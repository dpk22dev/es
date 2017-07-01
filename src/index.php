<?php
require_once '../configs/config.php';
require_once 'es.php';
require_once 'utils.php';
require_once 'Views/indexView.php';
require_once 'MongoService.php';
require_once 'Logger.php';

/*$params = [
    'index' => 'my_index',
    'body' => [
        'settings' => [
            'number_of_shards' => 2,
            'number_of_replicas' => 0
        ]
    ]
];

$response = $eSClient->indices()->create($params);
print_r($response);*/

$logger = FileLogger::getLogger( $loggerConfigs );
$mongoSrv = new MongoService($mongoConfigs, $logger );

if( empty($_POST) ){
    $inputFormData =[];

    $doc['uId'] = Utils::getUserId();
    $mId = $mongoSrv->createEmptyMongoDoc( $doc );
    if( $mId == -1 ){
        die('error in creating doc in M');
    }
    $artId = Utils::getArtId($mId);
    $inputFormData['artId'] = $artId;
    showInputForm( $inputFormData );
    die();
}

//$id = getStringFromPostById( 'id' );
$content = getStringFromPostById( 'content' );
$cats = getStringFromPostById( 'categories' );
$lang = getStringFromPostById( 'language' );
$tags = getStringFromPostById( 'tags' );
$writer = getStringFromPostById( 'writer' );
$movie_name = getStringFromPostById( 'movie_name' );
$book_name = getStringFromPostById( 'book_name' );
$uId = getStringFromPostById( 'uId' );
$artId = getStringFromPostById( 'artId' );

$tagsArr = getArrAfterExplode( ',', $tags );
$catArr =  getArrAfterExplode( ',', $cats );

if( empty($content) ){
    die('empty content');
}

$contentArr = getContentLines($content);

$params = [
    'index' => $eSArticleIndex['index'],
    'type' => $eSArticleIndex['type'],
    'body' => [
        'categories' => $catArr,
        'language' => $lang,
        'tags' => $tagsArr,
        'writer' => $writer,
        'song' => [ "movie_name" => $movie_name ],
        'book' => [ "name" => $book_name ]
    ] + $contentArr
];

/*function insertIntoMongo( $mongoArt ){
    global $mongoSrv;
    //$mongoSrv = new MongoService($mongoConfigs);

    $mongoObj = $mongoSrv->insertArticle( $mongoArt );
    var_dump($mongoObj);

    if( $mongoObj->isAcknowledged() ){
        $x = $mongoObj->getInsertedId();
        return $x->oid;
    } else {
        return -1;
    }
}*/

function saveArticleInMongo( $mDocIdArr, $mongoArt ){
    global $mongoSrv;
    //$mongoSrv = new MongoService($mongoConfigs);

    $mongoObj = $mongoSrv->saveArticleInMongo( $mDocIdArr, $mongoArt );
    //@todo here upserted doc ids are not returned, should be better returned 
    //$id = $mongoObj['value']['_id'];
/*    $x = $mongoObj->getModifiedCount();
    $y = $mongoObj->getInsertedCount();
    if( $mongoObj->getModifiedCount() > 0 ){
        return true;
    } else {
        return false;
    }*/
}

$mongoArt = [
    "categories" => $catArr,
    "language" => $lang,
    "tags" => $tagsArr,
    'writer' => $writer,
    'song' => [ "movie_name" => $movie_name ],
    'book' => [ "name" => $book_name ],
    'content' => $content,
    '_id' => new \MongoDB\BSON\ObjectID( $docId ),
    'uId' => $uId
];

$mongoObjId = saveArticleInMongo( [ '_id' => $mongoArt['_id'] ], [ '$set' => $mongoArt ] );

try{
    $response = $eSClient->index($params);
} catch ( \Exception $e ){
    print_r( $e->getMessage() );
    showInputForm( $_POST );
    die();
}

//if( $mongoObjId > 0 ) {
    $mongoObj = $mongoSrv->updateMongoArticleWithEsId($mongoObjId, $response['_id']);
//}

die('done');

