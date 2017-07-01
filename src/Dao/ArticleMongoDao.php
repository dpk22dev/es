<?php
namespace Dpk\Src\Dao;

class ArticleMongoDao{

    private $client;
    public function __construct( $client ){
        $this->client = $client;
    }
    //save/retreive article to mongodb
    public function saveArticle( $artModel ){

    }

}