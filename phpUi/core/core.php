<?php

if (version_compare(PHP_VERSION, '7.1.0','<') ) {
    echo "given php version: " . PHP_VERSION . " - needed: at least 7.1.0\n";
    die();
}
if(!extension_loaded ( "imagick" )){
    echo "imagick not installed";
    die();
}


/*
if(!extension_loaded ( "mongodb" )){
    echo "mongodb not installed";
    die();
}
*/

// load directorie-defines
include_once(__DIR__."/directories.inc");

require_once __LcRoot__ . "/vendor/autoload.php";

// autoload-function for autoloading classes
spl_autoload_register("LcCore::Autoload");
// autoload for classes
include_once  __LcInclude__."twitteroauth-master/autoload.php";


if(!file_exists(__LcRoot__."../config.json"))
    die("config.json does not exist");

/*
 * Set things in the php.ini for system
 */
error_reporting(E_ALL);
mb_internal_encoding(       Cfg::SysMbEncoding  );
ini_set("display_errors",   Cfg::SysShowPhpError);
ini_set('date.timezone',    Cfg::SysTimeZone    );
ini_set("memory_limit", "256M");

ini_set("session.use_cookies", 1);
ini_set("session.use_only_cookies", 1);
ini_set("session.use_trans_sid", 0);
ini_set("session.cache_limiter", "");

/*
 * Init logger
 */
lcLogger::Init("file",["File"=>date("my").".log","Path"=>__LcRoot__]);
lcLogger::SetLogLevel("debug");

lcLogger::Debug("Started logging");

lcCacheControl::init(Cfg::CACHE_CONTROL_TIME);


/**
 * Class lcCore
 * core-functions for lc
 */
class lcCore {

    /**
     * @function    autoloads files to classes
     * @param       $sClassName
     * @return      void
     */
    public static function Autoload($sClassName) {
        $sClassName =  str_replace("\\","/",$sClassName);

        if(substr($sClassName,0,3) === "Phr" && file_exists(__LcCoreClasses__.$sClassName.".php")){
            include_once __LcCoreClasses__.$sClassName.".php";

            return;
        }

        switch ( $sClassName )
        {

            default:
                if(file_exists(__LcInclude__.$sClassName.".php"))
                    include_once __LcInclude__.$sClassName.".php";

                if(file_exists(__LcCoreClasses__.$sClassName.".php"))
                    include_once __LcCoreClasses__.$sClassName.".php";
            break;

            // log
            case "lcLogger":
                include_once __LcCoreClasses__ . "lcLogger/lcLogger/lcLogger.php";
                break;

            case "Cfg":
                include_once __LcConfig__."lc.inc";
                break;

            // core functions
            case "lcSmarty":
            case "Smarty":
                include_once  __LcCoreClasses__."lcSmarty.php";
                include_once __LcCoreClasses__."smarty/Smarty.class.php";
                break;

            case "lcSmartyHelper":
                include_once __LcCoreClasses__."lcSmartyHelper.php";
                break;
        }
    }
    /**
     * @function    check if "string" is a number is really numeric
     * (+-)[0-9]((,.)[0-9])
     * @param       int $iNum
     * @return      bool
     */
    public static function IsNumeric ( $iNum )
    {
        if(preg_match( "/^([-,+]{0,1})[0-9]{1,}([,,.]{0,1}[0-9]{0,})$/" ,$iNum))
            return true;
        return false;
    }

    /**
     * @function    deletes a file if it exists
     * @param       $sFile
     * @return      bool
     */
    public static function deleteFile( $sFile ) {
        if( !file_exists( $sFile ))
            return true;

        if( !unlink($sFile) ) {
            return false;
        }
        return true;
    }

    /**
     * @function    checks is request is an ajax-call
     * @return bool
     */
    public static function isAjax(){
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        }

        return false;
    }

    /**
     * @function    checks if the request is an POST-request
     * @return bool
     */
    public static function isPost(){
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * @function    checks if the request is an GET-request
     * @return bool
     */
    public static function isGet(){
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    public static function isHttps(){
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === "on";
    }

    public static function strReplaceAssoc(array $replace, $subject) {
        return str_replace(array_keys($replace), array_values($replace), $subject);
    }
    public static function striReplaceAssoc(array $replace, $subject) {
        return str_ireplace(array_keys($replace), array_values($replace), $subject);
    }

    /**
     * @function same as scandir but ignores . and ..
     * @param $sDir
     * @return array
     */
    public static function scanDir($sDir){
        $aRet = [];
        $aTmpFiles = scandir($sDir);
        foreach ($aTmpFiles as &$sFile)
            if($sFile != "." && $sFile != ".." ) $aRet[] = $sFile;

        return $aRet;
    }
}
