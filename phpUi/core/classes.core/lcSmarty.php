<?php

class lcSmarty {
     // for single instance
    private static 	$oInstance 		= null	;

    public static function getInstance() {
        if ( self::$oInstance === null ) {
            self::$oInstance = new Smarty();
            self::init();
        }
        return self::$oInstance;
    }

    /**
     * @functions reads all aviable languages for the current user-template
     * @return array
     */
    public static function getAviableLanguages(){
        $aFiles = scandir(__LcTemplates__.lcUser::getUser()->sTemplate."/lang/");
        $aRet = [];

        foreach($aFiles as $sFile){
            $aExtension = pathinfo($sFile);
            if(isset($aExtension["extension"]) && $aExtension["extension"] == "conf" ){
                $aRet[] = $aExtension["filename"];
            }
        }

        return $aRet;
    }

    /**
     * @function Checks if a language exists
     * @param $sLanguage|string (De|Fr|Nl..)
     * @return bool
     */
    public static function checkLanguage($sLanguage){
        $sLanguage = str_replace(["/","\\","."],"",$sLanguage);

        $sFile = __LcTemplates__.lcUser::getUser()->sTemplate."/lang/".$sLanguage.".conf";
        if(file_exists($sFile))
            return true;

        return false;
    }

    /**
     * @function replaces (/or adds) a key/value to $_SEVER[REQUEST_URI]
     * @param $sKey
     * @param $sValue
     * @return string
     */
    public static function RequestUriReplaceGet($sKey,$sValue){
        $_GET[$sKey] = $sValue;
        return "?".http_build_query($_GET);
    }

    /**
     * @functions searches the relative path of for current page (mod_rewrite) (thanks to Jörg Nowak)
     * @return mixed|string
     */
    public static function relativeCssJsPath(){
        return Cfg::sMainDomain(); // don't repeat yourself...
    }

    public static function init(){
        $Smarty = self::getInstance();
        $Smarty->setCompileDir(     __LcSmartyRoot__."templates_c/");
        $Smarty->setCacheDir(       __LcSmartyRoot__."cache/");
        $Smarty->setConfigDir(      __LcSmartyRoot__."/configs/");
        $Smarty->setTemplateDir(__LcRoot__."templates/");
        $Smarty->caching            = false;
        $Smarty->debugging          = Cfg::SysSmartyDebug;
        $Smarty->force_compile      = true;
        // add meta-content
        $Smarty->assign("sMainDomain",  Cfg::sMainDomain());
        $Smarty->assign("sRelativePath",self::relativeCssJsPath());

        $Smarty->unregisterPlugin("function","postValue");
        $Smarty->unregisterPlugin("function","postSelected");
        $Smarty->unregisterPlugin("function","postChecked");
        $Smarty->registerPlugin("function","postValue","lcSmartyHelper::postValue");
        $Smarty->registerPlugin("function","postSelected","lcSmartyHelper::postSelected");
        $Smarty->registerPlugin("function","postChecked","lcSmartyHelper::postChecked");
        $Smarty->registerPlugin("function","cacheFile","lcSmartyHelper::getFile");

        // set language file from userconfig
 //       $Smarty->assign("sLangFile",__LcCore__."/templates/default/lang/".$User->getUserLanguage().".conf");

    }
}
