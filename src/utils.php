<?php
include_once '../configs/config.php';

function getConfigsDirPath( ){
    return getRootPath() . '/configs';
}


function getResourceDirPath( $withRootPath = false ){
    return $withRootPath ? getRootPath() . '/resources' : '/resources';
}

function getSrcDirPath( $withRootPath = false ){
    return $withRootPath ? getRootPath() . '/src' : '';
}

function getJsDirPath( $withRootPath = false ){
    return getSrcDirPath( $withRootPath ) . '/JS';
}

function getCssDirPath( $withRootPath = false ){
    return getSrcDirPath( $withRootPath ) . '/CSS';
}

function getExtDirPath( $withRootPath = false ){
    return $withRootPath ? getRootPath().'/ext' : '/ext';
}

function getVendorDirPath( $withRootPath = false ){
    return $withRootPath ? getRootPath() . '/vendor' : '/vendor';
}

function getPluginInExtDirPath( $withRootPath = false, $name ){
    return $withRootPath ? getRootPath() . '/ext/'.$name : '/ext/'.$name;
}

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

class Utils{

    public function __construct(){

    }

    public static function getArtId( $mId ){
        return $mId;
    }

    public static function getMongoDocId( $artId ){
        return $artId;
    }

    public static function getUserId( ){
        return 1;
    }


    //use: www.google.com
    public static function getRootDomain($domain)
    {
        if( empty($domain) ){
            $domain = $_SERVER['HTTP_HOST'];
        }

        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }
        return false;
    }

}