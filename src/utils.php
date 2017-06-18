<?php
include_once '../configs/config.php';

function getStringFromPostById( $id ){
    $val = trim( $_POST[ $id ] );
    return !empty( $val ) ? $val : "";
}

function getStringFromRequestById( $id ){
    $val = trim( $_POST[ $id ] );
    return !empty( $val ) ? $val : "";
}

function getContentLines( $content ){
    global $artFieldPrefix;
    $cntArr = explode( PHP_EOL, $content);
    $temp = [];
    foreach ( $cntArr as $k => $val ){
        $trimVal = trim( $val );
        if( !empty($trimVal) ) {
            $temp[$artFieldPrefix . $k] = $trimVal;
        }
    }

    return $temp;
}

function getArrAfterExplode( $del, $str ){
    if( empty($str) ) return [];
    else return explode($del, $str);
}