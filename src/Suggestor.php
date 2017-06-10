<?php
require_once '../configs/config.php';
require_once 'es.php';
require_once 'utils.php';
require_once 'Curl.php';

if( empty($_REQUEST) ){
    ?>
    <style>
        tbody {
            display: table-row-group;
            vertical-align: middle;
            border-color: inherit;
        }
        tr {
            display: table-row;
            vertical-align: inherit;
            border-color: inherit;
        }

    </style>
    <form method="post" action="/Suggestor.php">
        <table>
            <tbody>
            <tr>
                <td>words:</td>
                <td><input type="text" name="current_text" id="current_text"></td>
            </tr>
            <tr><td>
                    <input type="submit">
                </td></tr>
            </tbody>
        </table>
    </form>
    <?php
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
    $url = 'http://'.$eSHosts[0].'/'.$eSArticleIndex['index'].'/_search';
    $response = curl_post($url, $searchJson );
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
    $proximalCrudeWordsStr = "";
    $resObj = json_decode($response);
    $resArr = getResultArr( $resObj );
    $neighLinesArr = getNeighbourLines($resArr);
    if( !empty($neighLinesArr) ){
        $proximalCrudeWordsStr = implode(',', $neighLinesArr);
    }
    $strongWordsJson = getStrongWords( $proximalCrudeWordsStr );
    $strongWordsArr = json_decode($strongWordsJson);
    $topXStrongWords = getTopStrongWords( $strongWordsArr->tokens, 5 );
    var_dump( $topXStrongWords );
}

function usort_callback($a, $b)
{
    if ( $a['cnt'] == $b['cnt'] )
        return 0;

    return ( $a['cnt'] > $b['cnt'] ) ? -1 : 1;
}

function getTopStrongWords( $arr, $limit ){
    $hashMap = [];
    foreach( $arr as $k => $tokenObj ){
        if( empty( $hashMap[$tokenObj->token]['cnt'] ) ){
            $hashMap[$tokenObj->token]['cnt'] = 1;
            $hashMap[$tokenObj->token]['token'] = $tokenObj->token;
        } else {
            ++$hashMap[$tokenObj->token]['cnt'];
        }
    }

    usort($hashMap, 'usort_callback');
    $topX = array_slice($hashMap, 0, $limit);
    return $topX;
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

function getIndexForProximalLines( $arr ){
    global $artFieldPrefix;
    $res = [];
    foreach ( $arr as $k => $val ){
        if( strstr($k, $artFieldPrefix) != false ){
            $temp = explode($artFieldPrefix, $k);
            $inx = (int)$temp[1];
            $res[$inx-1] = $artFieldPrefix.( string )($inx-1);
            $res[$inx+1] = $artFieldPrefix.( string )($inx+1);
        }
    }
    return $res;
}

function getProximalLines( $src, $matches ){
    $proxArr = getIndexForProximalLines( $matches );
    $res = [];
    foreach ( $proxArr as $k => $lineInx ){
        if( isset( $src->{$lineInx} ) ){
            $res[$lineInx] = $src->{$lineInx};
        }
    }
    return $res;
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



