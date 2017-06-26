<?php
include_once dirname( __DIR__ ).'/configs/config.php';
include_once 'utils.php';
require_once 'es.php';

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

}
$docId =  getStringFromPostById( 'docId' );


$artObj = new Article( $artFieldPrefix, $eSClient );
$params = [
    'index' => $eSArticleIndex['index'],
    'type' => $eSArticleIndex['type'],
    'id' => $docId
];

$art['article'] = $artObj->getArticleContent( $params );
header('Content-Type:application/json;charset=UTF-8');
echo json_encode( $art );