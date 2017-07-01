<?php
namespace Dpk\Src\Srv;

require_once dirname( dirname( __DIR__) ). '/configs/config.php';
require_once getRootPath().'/src/utils.php';
require_once getVendorDirPath( false ).'/autoload.php';

class MongoClient{

    private $client;
    //@todo make it singleton
    public function __construct( $mongoConfigs ){
        $conStr = "mongodb://".$mongoConfigs['host'].':'.$mongoConfigs['port'];
        $this->client = new MongoDB\Client( $conStr );
    }
    
}

