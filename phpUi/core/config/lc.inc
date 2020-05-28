<?php
class Cfg {

    private static $instance;

    const       CACHE_CONTROL       = true;
    const       CACHE_CONTROL_TIME  = 2; // hours


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

    /**
     * @function    returns domainurl as string
     * @return      string
     */
    public static function sMainDomain () {
        $HTTPProtocol		= isset( $_SERVER['HTTPS'] ) ? "https://":"http://";

        $sDir = str_replace("\\","/",__DIR__); // replace \ with / . Happens when you use Windows
        $sDir = str_replace("/core/config","",$sDir)."/";
        $sys_root = str_replace($_SERVER['DOCUMENT_ROOT'], '/', $sDir);
        $sys_root = str_replace(['//', '///'], '/', $sys_root);

        $sPort = "";
        if($_SERVER["SERVER_PORT"] != 80 && $_SERVER["SERVER_PORT"] != 443)
            $sPort = ":".$_SERVER["SERVER_PORT"];

        $sMaindomain = $HTTPProtocol.$_SERVER["SERVER_NAME"].$sPort.$sys_root;


        return $sMaindomain;
    }

    public static function getHttRoot(){
        $sDir = str_replace("\\","/",__DIR__); // replace \ with / . Happens when you use Windows
        $sDir = str_replace("/core/config","",$sDir)."/";
        $sys_root = str_replace($_SERVER['DOCUMENT_ROOT'], '/', $sDir);
        $sys_root = str_replace(['//', '///'], '/', $sys_root);

        if($sys_root == "/")
            return "";

        return $sys_root;
    }
}
