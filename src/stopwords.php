<?php

//@todo may be move stopwords to array if its faster
// keeping backup option on file
class Stopwords{
    
    private $stopWordsStr = '';
    private $stopWordsArr = [];
    private $lang = '';
    private $rscPath = '';
    const stopWordDirPrefix = 'stopwords_';
    const stopWordFileName = 'stopwords.txt';

    public function __construct( ){
        $this->rscPath = dirname( __DIR__ ) . '/resources';
    }

    private function readStopWordsFromFile( $lang ){
        $filePath = $this->rscPath . '/' . self::stopWordDirPrefix . $lang . '/' .self::stopWordFileName;
        $this->stopWordsStr = file_get_contents( $filePath );
        $this->stopWordsArr = explode(PHP_EOL, $this->stopWordsStr );
    }

    public function getStopWordsArr( $lang = 'hi' ){
        if( empty( $this->stopWordsArr ) && empty($this->lang) ){
            $this->readStopWordsFromFile();
        }
        return $this->stopWordsArr;
    }

}