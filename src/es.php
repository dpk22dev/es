<?php
require_once '../configs/config.php';

require_once '../vendor/autoload.php';

use Elasticsearch\ClientBuilder;

//var_dump( $eSHosts );
$eSClient = ClientBuilder::create()
    ->setHosts($eSHosts)
    ->build();

//var_dump( $eSClient );