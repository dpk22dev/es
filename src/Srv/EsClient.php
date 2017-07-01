<?php
namespace Dpk\Src\Srv;

require_once dirname( dirname( __DIR__) ). '/configs/config.php';
require_once getRootPath().'/src/utils.php';
require_once getVendorDirPath( false ).'/autoload.php';

use Elasticsearch\ClientBuilder;

class EsClient{

    private $client;
    //@todo make it singleton
    public function __construct( $eSHosts ){
        $this->client = ClientBuilder::create()
            ->setHosts($eSHosts)
            ->build();
    }

}