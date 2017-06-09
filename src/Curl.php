<?php
function curl_post( $url, $fields ){

    $ch = curl_init();
    $fields_string = "";

    if( is_array($fields) ) {
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');
        $cnt = count($fields);
    } elseif ( is_string($fields) ){
        $fields_string = $fields;
        $cnt = 1;
    }

    $header = array(
        "content-type: application/x-www-form-urlencoded; charset=UTF-8"
    );

//set the url, number of POST vars, POST data
    curl_setopt($ch,CURLOPT_URL, $url);
    //curl_setopt($ch,CURLOPT_POST,$cnt );
    curl_setopt($ch,CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
//execute post
    $result = curl_exec($ch);

//close connection
    curl_close($ch);
    return $result;
}