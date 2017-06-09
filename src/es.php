<?php
require_once '../configs/config.php';

require_once '../vendor/autoload.php';

use Elasticsearch\ClientBuilder;

$eSClient = ClientBuilder::create()
    ->setHosts($eSHosts)
    ->build();