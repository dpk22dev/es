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

$searchJson = '
{
    "query": {
    "multi_match": {
        "query": "'.$current_text.'",
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

$url = 'http://'.$eSHosts[0].'/'.$eSArticleIndex['index'].'/_search';
$response = curl_post($url, $searchJson );

print_r($response);