<?php
include_once "../configs/config.php";

function getRedisConnection()
{
    global $redisConfigs;
    $redis = new Redis();
    $redis->connect($redisConfigs['redisHost'], $redisConfigs['redisPort']);
    $redis->auth($redisConfigs['auth']);
    return $redis;
}

//check whether server is running or not
function pingRedis( $redis ){
    $redis->ping();
}