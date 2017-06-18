<?php
include_once 'utils.php';

function getuserId(){
    return getStringFromPostById( 'userId' );
}

function getArtId(){
    return getStringFromPostById( 'artId' );
}

