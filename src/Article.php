<?php
include_once dirname( __DIR__ ).'/configs/config.php';
include_once 'utils.php';
include_once 'es.php';
require_once 'MongoService.php';
require_once 'Logger.php';

class Article{

    private $esClient;
    private $artFieldPrefix;

    function __construct( $artFieldPrefix, $esClient ){
        $this->esClient = $esClient;
        $this->artFieldPrefix = $artFieldPrefix;
    }

    function getArticle( $params ){
        $response = $this->esClient->get($params);
        return $response;
    }

    function getArticleContent( $params ){
        $esResponse = $this->getArticle( $params );
        //foreach content_lines merge into string and return back

        $str = "";
        $res = [];

        if( $esResponse['found'] ) {
            $srcArray = $esResponse['_source'];
            foreach ($srcArray as $k => $val) {
                if (strpos($k, $this->artFieldPrefix) !== false) {
                    $res[] = $val;
                }
            }
            $str = implode(PHP_EOL, $res);
        }
        return $str;
    }

    function getVerificationMatchingDocsJson( $hitsArr ){

        $res = [];
        foreach ( $hitsArr as $k => $hit ){
            $id = $hit['_id'];
            $content = $hit['_source'];
            $res[ $id ] = $content;
        }
        return $res;

    }

    function convertToBrowserFormat( $hits, $mappings ){
        $ret = [];
        if( !empty($hits) ){
            foreach ( $hits as $docId => $hit ){
                $arr = [];
                if( empty( $mappings[$docId] ) ){
                    continue;
                }
                $artId = (string)$mappings[$docId];
                $arr['artId'] = $artId;

                $arr['content'] = Utils::getContentLinesFromArr( $hit );

                $cats = '';
                if( !empty($hit['categories'] ) ){
                    $cats = implode(', ', $hit['categories'] );
                }
                $arr['categories'] = $cats;

                $arr['language'] = $hit['language'];

                $tags = '';
                if( !empty($hit['tags'] ) ){
                    $tags = implode(', ', $hit['tags'] );
                }
                $arr['tags'] = $tags;
                
                $arr['writer'] = $hit['writer'];
                $arr['movie_name'] = !empty($hit['song']['movie_name']) ? $hit['song']['movie_name'] : '';
                $arr['book_name']= !empty($hit['book']['name']) ? $hit['book']['name'] : '';
                
                $ret[] = $arr;
            }
        }
        return $ret;
    }

}

$docId =  getStringFromPostById( 'docId' );
$docTxt = getStringFromPostById( 'docText' );
//$artObj = new Article( $artFieldPrefix, $eSClient );


$artObj = new Article( $artFieldPrefix, $eSClient );

if( !empty($docId) ){
    $params = [
        'index' => $eSArticleIndex['index'],
        'type' => $eSArticleIndex['type'],
        'id' => $docId
    ];

    $art['article'] = $artObj->getArticleContent( $params );
    header('Content-Type:application/json;charset=UTF-8');
    echo json_encode( $art );
} else if( !empty($docTxt ) ){
    $docTxt = str_replace( PHP_EOL, ' ', $docTxt );
    $json = '{
    "query" : {
        "multi_match" : {
            "query" : "'.$docTxt.'",
            "fields" : [ "content_line_*" ]
        }
    }
}';

    $params = [
        'index' => $eSArticleIndex['index'],
        'type' => $eSArticleIndex['type'],
        'body' => $json
    ];

    $results = $eSClient->search($params);

    //get first article with id from content
    $hitsArr = $hits = [];
    if( $results['hits']['total'] > 0 ){
        $hits = $artObj->getVerificationMatchingDocsJson( $results['hits']['hits'] );
        $esIds = array_keys($hits);
        $logger = FileLogger::getLogger( $loggerConfigs );
        $mongoSrv = new MongoService($mongoConfigs, $logger );
        $esId2docIdMappings = $mongoSrv->getMongoIdsForEsIds( $esIds );
        $hitsArr = $artObj->convertToBrowserFormat( $hits, $esId2docIdMappings );
    }

    header('Content-Type:application/json;charset=UTF-8');
    echo json_encode( $hitsArr );

}



