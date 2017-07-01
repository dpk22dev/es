<?php
include_once dirname( __DIR__ ).'/vendor/autoload.php';

class MongoService{

    private $client;
    private $configs;
    private $mongoDb;
    private $mongoCol;

    //@todo make it singleton
    public function __construct( $mongoConfigs, $logger ){
        $this->logger = $logger;
        $this->configs = $mongoConfigs;

        $conStr = "mongodb://".$mongoConfigs['host'].':'.$mongoConfigs['port'];
        $this->client = new MongoDB\Client( $conStr );

        $db = $this->configs['db'];
        $col = $this->configs['collection'];
        $this->setDBAndCollection( $db, $col );

    }

    public function setDBAndCollection( $db, $col ){
        $this->mongoDb =  $this->client->$db;
        $this->mongoCol = $this->mongoDb->$col;
    }

    public function insertArticle( $art ){
        $ret = $this->mongoCol->insertOne($art);
        return $ret;
    }

    public function updateMongoArticleWithEsId( $docId, $esId ){

        return $this->mongoCol->updateOne( array( '_id'=>  new \MongoDB\BSON\ObjectID( $docId ) ),
            array('$set'=>array("esId"=> $esId )));
    }

    public function saveArticleInMongo( $filter, $doc ){
        //return $this->mongoCol->save( $doc );
        /*$updateResult = $this->mongoCol->replaceOne( $filter, $doc, [ 'upsert' => true ] );
        return $updateResult;*/

        /*$result = $this->mongoDb->command( array(
            'findAndModify' => $this->mongoCol,
            'query' => $filter,
            'update' => $doc,
            'new' => true,        # To get back the document after the upsert
            'upsert' => true,
            'fields' => array( '_id' => 1 )   # Only return _id field
        ) );
        return $result;*/

        $updateResult = $this->mongoCol->updateOne( $filter, $doc,  [ 'upsert' => true ] );
        return $updateResult;
    }

    public function updateArticleInMongo( $docId, $esId ){

        return $this->mongoCol->updateOne( array( '_id'=>  new \MongoDB\BSON\ObjectID( $docId ) ),
            array('$set'=>array("esId"=> $esId )));
    }

    public function createEmptyMongoDoc( $doc ){
        try {
            $ret = $this->mongoCol->insertOne($doc);
            if ($ret->isAcknowledged()) {
                $x = $ret->getInsertedId();
                return (string)$x;
            } else {
                return -1;
            }
        }catch ( Exception $e ){
            $this->logger->critical( __FILE__.'['.__LINE__.']'.'failed to create empty mongo doc.'.$e->getMessage() );
            return -1;
        }
    }


}