<?php
include_once '../configs/config.php';

function getStringFromPostById( $id ){
    return !empty( $_POST[ $id ] ) ? $_POST[ $id ] : "";
}

function getStringFromRequestById( $id ){
    return !empty( $_REQUEST[ $id ] ) ? $_REQUEST[ $id ] : "";
}

function getContentLines( $content ){
    global $artFieldPrefix;
    $cntArr = explode( PHP_EOL, $content);
    $temp = [];
    foreach ( $cntArr as $k => $val ){
        $temp[ $artFieldPrefix.$k ] = $val;
    }

    return $temp;
}
