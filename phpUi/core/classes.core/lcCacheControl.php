<?php

class lcCacheControl
{
    /**
     * @var lcCacheControl
     */
    private static $instance;

    /**
     * @var array
     */
    private $aFiles = [];

    /**
     * lcCacheControl constructor.
     * @param int $iMaxDiff in hours
     */
    public function __construct(int $iMaxDiff = 24)
    {
        if(!Cfg::CACHE_CONTROL)
            return;

        if(file_exists(__LcConfig__."cache.php")){
            $this->aFiles = unserialize(file_get_contents(__LcConfig__."cache.php"));

            if((time() - $this->aFiles["time"] ) > 60*60*$iMaxDiff){
                lcLogger::Info("Updating cachefiles");
                self::indexFiles();
            }
        }else
            self::indexFiles();
    }

    /**
     * @param int $iMaxDiff in hours
     * @return lcCacheControl
     */
    public static function init(int $iMaxDiff = 24){
        return self::getInstance()->__construct($iMaxDiff);
    }

    public static function getInstance(): lcCacheControl
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * creates an urlpath with cachecontrol
     * @param $sFile
     * @return string
     */
    public static function getFile($sFile):string {
        $o = self::getInstance();

        if(isset($o->aFiles[$sFile])){
            return $sFile."?v=".$o->aFiles[$sFile]["time"];
        }

        return $sFile;
    }

    /**
     * recursive-scandir
     * @param $path
     * @return array
     */
    private static function getDirContents($path) {
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

        $files = array();
        foreach ($rii as $file)
            if (!$file->isDir())
                $files[] = $file->getPathname();

        return $files;
    }

    /**
     * loads files from /static/ and creates a cache-file with timestamps
     */
    public static function indexFiles()
    {
        $aFiles = self::getDirContents(__LcRoot__."static/");

        $aFileInfos = [];
        foreach ($aFiles as $sFile){
            $iFileTime = filemtime($sFile);
            $sShortFile = str_replace(__LcRoot__,"",$sFile);
            $sFile = str_replace("\\","/",$sFile);
            $sShortFile = str_replace("\\","/",$sShortFile);
            $aFileInfos[$sShortFile] = [
                "time"  => $iFileTime,
                "file"  => $sShortFile,
                "fileFull"  => $sFile
            ];
        }

        $aFileInfos["time"]=time();
        lcLogger::Debug("Updating cache for:",$aFileInfos);

        file_put_contents(__LcConfig__."cache.php",serialize($aFileInfos));
    }
}