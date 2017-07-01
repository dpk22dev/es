<?php
include_once dirname( __DIR__ ).'/vendor/autoload.php';

class MongoService{

    private $client;
    private $configs;
    //@todo make it singleton
    public function __construct( $mongoConfigs ){
        $this->configs = $mongoConfigs;
        $conStr = "mongodb://".$mongoConfigs['host'].':'.$mongoConfigs['port'];
        $this->client = new MongoDB\Client( $conStr );
    }

    public function insertArticle( $art ){
        $db = $this->configs['db'];
        $col = $this->configs['collection'];
        $mongoDb = $this->client->$db;
        $mongoCol = $mongoDb->$col;
        $ret = $mongoCol->insertOne($art);
        return $ret;
    }

    public function updateMongoArticleWithEsId( $docId, $esId ){
        $db = $this->configs['db'];
        $col = $this->configs['collection'];
        $mongoDb = $this->client->$db;
        $mongoCol = $mongoDb->$col;

        return $mongoCol->updateOne( array( '_id'=>  new \MongoDB\BSON\ObjectID( $docId ) ),
            array('$set'=>array("esId"=> $esId )));
    }

}