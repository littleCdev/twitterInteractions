<?php
class Cfg {

    private static $instance;

    // system
    const       SysTimeZone     = "Europe/Berlin";
    const       SysShowPhpError = true;
    const       SysMbEncoding   = "UTF-8";
    const       SysSmartyDebug  = false;
    const       SysSmartyCache  = false;
    const       UrlSubFolder    = "";

    private $config = null;

    private function __construct( )
    {
        $filecontent = file_get_contents(__LcRoot__."../config.json");
        $this->config = json_decode($filecontent);
    }


    public static function getInstance( )
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self( );
        }
        return self::$instance;
    }

    public static function ConfigEntry(string $entry){
        $config = self::getInstance()->config;
        if(!property_exists($config,$entry)){
            lcLogger::Critical("entry ".$entry." does not exist in config",$config);
            return "does not exist :(";
        }

        return $config->$entry;
    }
}
