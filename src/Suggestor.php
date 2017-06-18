<?php
require_once '../configs/config.php';
require_once 'es.php';
require_once 'utils.php';
require_once 'Curl.php';
require_once 'redis.php';
require_once 'Params.php';
require_once 'Views/suggestorView.php';
require_once 'stopwords.php';

if( empty($_POST) ){
    showSuggestorForm();
    die();
}

$current_text = getStringFromRequestById( 'current_text' );

function searchIndex( $current_text )
{
    /*
     * below not allowing fields to have *
     */
    /*
    $params = [
        'index' => 'my_index',
        'type' => 'my_type',
        'body' => [
            'query' => [
                'multi_match' => [
                    'query' => $current_text,
                    'fields' => [ $artFieldPrefix . '*' ]
                ]
            ],
            'highlight' => [
                'fields' => [
                    $artFieldPrefix . '*' => []
                ]
            ]
        ]
    ];
    try {
        $response = $eSClient->search($params);
    } catch ( \Exception $e ){
        echo json_encode( $params );
        print_r( $e->getMessage() );
    }
    */
//try multi match prefix
    $searchJson = '
{
    "query": {
    "multi_match": {
        "query": "' . $current_text . '",
      "fields": ["content_line_*"]
    }
  },
  "highlight" : {
    "fields" : {
        "content_line_*" : {}
        }
    }
}
';

    global $eSHosts, $eSArticleIndex;
    try {
        $url = 'http://' . $eSHosts[0] . '/' . $eSArticleIndex['index'] . '/_search';
        $response = curl_post($url, $searchJson);
    }catch ( Exception $e ){
        $e->getMessage();
        showSuggestorForm();
    }
    return $response;
}

function getStrongWords( $str ){

    $params = '
    {
        "text" : "'.$str.'",
        "analyzer" : "hindi_stopper"
    }
    ';

    global $eSHosts, $eSArticleIndex;
    $url = 'http://'.$eSHosts[0].'/'.$eSArticleIndex['index'].'/_analyze';
    $response = curl_post($url, $params  );
    return $response;

}

$response = searchIndex($current_text);

if( !empty($response) ) {
    $keywordsHashMap = [];
    $proximalCrudeWordsStr = "";
    $resObj = json_decode($response);
    $resArr = getResultArr( $resObj );
/*    $neighLinesArr = getNeighbourLines($resArr);
    if( !empty($neighLinesArr) ){
        $proximalCrudeWordsStr = implode(',', $neighLinesArr);
    }
    $strongWordsJson = getStrongWords( $proximalCrudeWordsStr );
    $strongWordsArr = json_decode($strongWordsJson);*/

    getDataForHash( $resArr );
    //var_dump( $keywordsHashMap );
    createZSet( $keywordsHashMap  );
    $topXStrongWordsJsonArr = getTopStrongWordsArr( 5 );
    $topXStrongWordsArr = getTopXStrongWordsArr( $topXStrongWordsJsonArr );
    var_dump( $topXStrongWordsArr );
}

function getTopXStrongWordsArr( $topXStrongWordsJsonArr ){
    $res = [];
    foreach ( $topXStrongWordsJsonArr as $content => $cnt ){
        $res[] = json_decode($content);
    }
    return $res;
}

function getHashMapName(){
    //@todo retun name with userid, artid init
    $userId = getuserId();
    $artId = getArtId();
    $lineNum = getStringFromPostById( 'lineNum' );
    return 'hashmap_'.$userId.'_'.$artId.'_'.$lineNum;
}

function createZSet( $map ){
    $redis = getRedisConnection();
    $zAddArr = [];
    $zAddArr[] = getHashMapName();
    foreach ( $map as $k => $valArr ){
        $zAddArr[] = $valArr['cnt'];
        $valArr['word'] = $k;
        $zAddArr[] = json_encode( $valArr );
    }

    call_user_func_array( array( $redis, 'zAdd'), $zAddArr);
}


function getSelfIndexesForMatches( $matches ){
    $res = [];
    foreach ( $matches as $k => $val ){
        $res[] = getIndexForCurrentLine( $val );
    }
    return $res;
}

function insertNeighbourKeywordsForMatch( $src, $field, $val ){
    $matchInx = getIndexForCurrentLine( $field );

}

function getFieldFromIndex( $inx, $offset ){
    global $artFieldPrefix;
    return $artFieldPrefix.( string )($inx + $offset);
}

function getKeywordsFromString( $str ){
    global $stopWordsObj;
    $inputArr = explode(' ', $str );
    $stopWordsArr = $stopWordsObj->getStopWordsArr( 'hi' );
    $res = array_diff($inputArr, $stopWordsArr );
    return $res;
}

//field can be prev or next
//keyword['info']
//keyword['cnt']
function insertFieldKeywords( $docObj, $field, $currentFieldValue, $currentIsNext ){
    global $prevNextLineSeparator, $keywordsHashMap;
    $src = getSourceFromHitObj($docObj);
    $docId = $docObj->_id;
    if( isset( $src->{$field} ) ){
        $keyArr = getKeywordsFromString( $src->{$field} );
        foreach ( $keyArr as $k => $val ){
            $str = $currentIsNext == true ? $val.$prevNextLineSeparator.$currentFieldValue : $currentFieldValue.$prevNextLineSeparator.$val;
            $temp = [];
            $temp['docId'] = $docId;
            $temp['str'] = $str;
            if( isset($keywordsHashMap[$val] ) ){
                $keywordsHashMap[$val]['info'][] = $temp;
                ++$keywordsHashMap[$val]['cnt'];
            } else {
                $keywordsHashMap[$val]['info'] = [];
                $keywordsHashMap[$val]['info'][] = $temp;
                $keywordsHashMap[$val]['cnt'] = 1;
            }
        }
    }
}

function insertNeighbourKeywords( $docObj, $matches ){
    //$matchesIndexes = getSelfIndexesForMatches( $matches );
    foreach ( $matches as $field => $val ){
        //insertNeighbourKeywordsForMatch( $src, $field, $val);
        // in single line multiple words can match resulting in $val as array
        // using $val[0], as we need just to display it
        if( is_array($val) ){
            $val = $val[0];
        }
        $matchInx = getIndexForCurrentLine( $field );
        
        $prevField = getFieldFromIndex($matchInx, -1);
        insertFieldKeywords( $docObj, $prevField, $val, true );

        $nextField = getFieldFromIndex($matchInx, 1);

        insertFieldKeywords( $docObj, $nextField, $val, false );
    }
}

/*function getProximalKeywords( $src, $matches ){
    //get self index for matches

    //if neighbours index not in matches, get insert its keywords into hash
    insertNeighbourKeywords( $src, $matches );

}*/


function getDataForHash( $arr ){
    $res = [];
    global $stopWordsObj;
    $stopWordsObj = new Stopwords( );
    foreach ( $arr as $k => $docObj ){
        //$src = getSourceFromHitObj( $docObj );
        $highlightObj = getHightLightObj( $docObj );
        $matches = get_object_vars( $highlightObj );
        /*$proximalLines = getProximalLines( $src, $matches );
        $proxStr = getProximalString($proximalLines);*/
        insertNeighbourKeywords( $docObj, $matches );
        //$res[] = $proxStr;
    }
    return $res;

}

function usort_callback($a, $b)
{
    if ( $a['cnt'] == $b['cnt'] )
        return 0;

    return ( $a['cnt'] > $b['cnt'] ) ? -1 : 1;
}

function getTopStrongWordsArr( $x ){
    $redis = getRedisConnection();
    $arr = $redis->zRevRange( getHashMapName(), 0, $x, true );
    return $arr;
}

function getResultArr( $obj ){
    $res = [];
    if( !empty($obj) && $obj->timed_out == false ){
        $res = $obj->hits->hits;
    }
    return $res;
}

function getSourceFromHitObj( $obj ){
    return $obj->_source;
}

function getHightLightObj( $obj ){
    return $obj->highlight;
}

function getIndexForCurrentLine( $str ){
    global $artFieldPrefix;
    $temp = explode($artFieldPrefix, $str );
    $inx = (int)$temp[1];
    return $inx;
}

function getIndexForProximalLines( $arr ){
    global $artFieldPrefix;
    $res = [];
    foreach ( $arr as $k => $val ){
        if( strstr($k, $artFieldPrefix) != false ){
            $inx = getIndexForCurrentLine( $k );
            $res[$inx-1] = $artFieldPrefix.( string )($inx-1);
            $res[$inx+1] = $artFieldPrefix.( string )($inx+1);
        }
    }
    return $res;
}

function getProximalLines( $src, $matches, $curMatchInx ){
    global $keywordsHashMap;
    $proxArr = getIndexForProximalLines( $matches );
    foreach ( $proxArr as $k => $lineInx ){
        //@todo proxarr index should not be in current line or $matches indexes
        // probably its ok to use it
        if( isset( $src->{$lineInx} ) ){
            $res[$lineInx] = $src->{$lineInx};
        }
    }
}

function getProximalString($proximalLines){
    $res = "";
    foreach ($proximalLines as $k => $val ){
        $res .= $val;
    }
    return $res;
}

function getNeighbourLines( $arr ){
    $res = [];
    foreach ( $arr as $k => $obj ){
        $src = getSourceFromHitObj($obj);
        $highlightObj = getHightLightObj($obj);
        $matches = get_object_vars($highlightObj);
        $proximalLines = getProximalLines( $src, $matches );
        $proxStr = getProximalString($proximalLines);
        $res[] = $proxStr;
    }
    return $res;
}



