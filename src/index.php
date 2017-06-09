<?php
require_once '../configs/config.php';
require_once 'es.php';
require_once 'utils.php';

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

function showInputForm(){
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
    <form method="post" action="/index.php">
        <table>
            <tbody>
                <tr>
                    <td>content:</td>
                    <td><textarea type="text" id="content" name="content" rows="5" cols="50"></textarea></td>
                </tr>
                <tr>
                    <td>tags:</td>
                    <td><input type="text" name="tags"></td>
                </tr>
                <tr>
                    <td>authors:</td>
                    <td><input type="text" name="authors"></td>
                </tr>
                <tr>
                    <td>movie_name:</td>
                    <td><input type="text" name="movie_name"></td>
                </tr>
                <tr>
                    <td>book_name:</td>
                    <td><input type="text" name="book_name"></td>
                </tr>
                <tr><td>
                    <input type="submit">
                </td></tr>
            </tbody>
        </table>
    </form>
    <?php
}

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

