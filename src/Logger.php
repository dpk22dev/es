<?php
require_once dirname(__DIR__).'/vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class FileLogger{

    private static $logger;

    public static function getLogger( $configs )
    {
        if(is_null(self::$logger))
        {
            $name = $configs['fileLogger']['name'];
            $fileDebug = $configs['fileLogger']['pathDebug'];
            $fileWarning = $configs['fileLogger']['pathWarning'];
            $fileCritical = $configs['fileLogger']['pathCritical'];
            $minLevel = $configs['fileLogger']['minLevel'];

            self::$logger = new Logger( $name );
            self::$logger->pushHandler(new StreamHandler( $fileDebug, Logger::DEBUG) );
            self::$logger->pushHandler(new StreamHandler( $fileWarning, Logger::WARNING) );
            self::$logger->pushHandler(new StreamHandler( $fileCritical, Logger::CRITICAL) );
        }
        return self::$logger;
    }
    private function __construct( $configs )
    {

    }
}