<?php
require_once '../configs/config.php';
require_once 'es.php';
require_once 'utils.php';
require_once 'Views/indexView.php';

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

if( empty($_POST) ){
    $inputFormData =[];
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


try{
    $response = $eSClient->index($params);
} catch ( \Exception $e ){
    print_r( $e->getMessage() );
    showInputForm( $_POST );
    die();
}

print_r($response);
die('done');

