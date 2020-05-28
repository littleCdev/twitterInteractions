<?php

class lcSmartyHelper {
    /*
     * @function for Smarty only!
     *  checks if a post-value isset and returns value="value" or value="default"
     * @param name
     * @param default [optional]
     */
    public static function postValue($params, $smarty): string
    {
        if (empty($params["name"]))
            return "";

        if (!isset($_POST[$params["name"]])) {
            if (!empty($params["default"]))
                return "value=\"" . htmlentities($params["default"]) . "\"";
            else
                return "";
        } else {
            return "value=\"" . htmlentities($_POST[$params["name"]]) . "\"";
        }
    }
    /*
     * @function for Smarty only!
     * checks if $_POST[post] is equals $option, return selected if true
     */
    public static function postSelected($params, $smary): string
    {
        if(empty($params["post"])||empty($params["option"]))
            return "post-variable or option not set";

        if(!isset($_POST[$params["post"]]))
            return "";

        if($_POST[$params["post"]] == $params["option"])
            return "selected";

        return "";
    }

    /*
     * @function for Smarty only!
     * checks if $_POST[post] is equals $value
     * of if $_POST[post][] (array) contains $value
     * return "" or "checked"
     */
    public static function postChecked($params, $smarty): string
    {
        if(!isset($_POST[$params["post"]],$params["value"]))
            return "";

        if(preg_match("/\[\]$/",$params["post"])){ //array
            $params["post"] = str_replace("[]","",$params["post"]);
            foreach($_POST[$params["post"]] as $key=>$value){
                if($params["value"] == $value)
                    return "checked";
            }
        }else{
            if($_POST[$params["post"]]==$params["value"])
                return "checked";
        }
        return "";
    }

    public static function getFile($params, $smarty):string
    {
        if(!isset($params["file"])){
            return "";
        }

        if(!Cfg::CACHE_CONTROL)
            return $params["file"];

        return lcCacheControl::getFile($params["file"]);
    }

}