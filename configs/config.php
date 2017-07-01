<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'] . '/../';

$project = "kavitarth";

$eSArticleIndex = [
    'index' => 'article_index',
    'type' => 'article',
];

$eSHosts = [
    '127.0.0.1:9200'
];

$artFieldPrefix = 'content_line_';

$redisConfigs = [
    "redisHost" => "127.0.0.1",
    "redisPort" => "6379",
    "auth" => "times11!"
];

$prevNextLineSeparator = 'qqqqqqqqqqq';

function getRootPath(){
    global $rootPath;
    return $rootPath;
}

$mongoConfigs = [
    "host" => '127.0.0.1',
    "port" => '27017',
    "db" => 'aston',
    "collection" => 'articles'
];

$loggerConfigs = [
    "fileLogger" => array(
        "name" => "es-logging",
        "pathDebug" => "/var/log/es.debug",
        "pathWarning" => "/var/log/es.warning",
        "pathCritical" => "/var/log/es.critical",
        "minLevel" => 100
    ),
];