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
    showInputForm();
    die();
}

//$id = getStringFromPostById( 'id' );
$content = getStringFromPostById( 'content' );
$tags = getStringFromPostById( 'tags' );
$authors = getStringFromPostById( 'authors' );
$movie_name = getStringFromPostById( 'movie_name' );
$book_name = getStringFromPostById( 'book_name' );

if( empty($content) ){
    die('empty content');
}

$contentArr = getContentLines($content);

$params = [
    'index' => $eSArticleIndex['index'],
    'type' => $eSArticleIndex['type'],
    'body' => [
        $contentArr,
        'tags' => $tags,
        'author' => $authors,
        'movie_name' => $movie_name,
        'book_name' => $book_name
    ]
];

try{
    $response = $eSClient->index($params);
} catch ( \Exception $e ){
    print_r( $e->getMessage() );
}

print_r($response);
die('done');

