<?php

class lcMongo
{
    private static $instance;

    /**
     * @var \MongoDB\Client
     */
    var $client;

    var $db;


    /**
     * lcMongo constructor.
     */
    private function __construct( )
    {
        $this->client = new MongoDB\Client(Cfg::ConfigEntry("MongoHost"));
        $this->db = $this->client->selectDatabase(Cfg::ConfigEntry("MongoDb"));
    }

    private function __clone( )
    {
    }

    /**
     * @param string $name
     * @return \MongoDB\Collection
     */
    public static function collection(string $name){
        return self::get()->db->selectCollection($name);
    }

    /**
     * @return lcMongo
     */
    public static function get( )
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self( );
        }
        return self::$instance;
    }
}