<?php

if (version_compare(PHP_VERSION, '7.1.0','<') ) {
    echo "given php version: " . PHP_VERSION . " - needed: at least 7.1.0\n";
    die();
}
set_time_limit(0);



if(!extension_loaded ( "mongodb" )){
    echo "mongodb not installed";
    die();
}

//ini_set('mongodb.debug', 'stderr');

// load directorie-defines
include_once(__DIR__."/directories.inc");

require_once __LcRoot__ . "/vendor/autoload.php";

// autoload-function for autoloading classes
spl_autoload_register("LcCore::Autoload");

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
        }
    }

}
