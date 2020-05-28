<?php

class ControllerSettings
{
    /**
     * @throws SmartyException
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public static function get_index(){
        $oTLogin = lcTwitterLogin::getInstance();
        try {
            $oTLogin->login();
        } catch (Exception $exception) {
            header("location: " . Cfg::sMainDomain() . "");
            return;
        }

        if ($oTLogin->twitter_id == 0) {
            header("location: " . Cfg::sMainDomain() . "");
            return;
        }


        $smarty = lcSmarty::getInstance();
        $smarty->assign("tlogin",$oTLogin);
        $smarty->display("settings.tpl");
    }

    /**
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public static function get_logout(){
        $oTLogin = lcTwitterLogin::getInstance();
        try {
            $oTLogin->login();
        } catch (Exception $exception) {
            header("location: " . Cfg::sMainDomain() . "");
            return;
        }

        if ($oTLogin->twitter_id == 0) {
            header("location: " . Cfg::sMainDomain() . "");
            return;
        }

        setcookie("rm","",time()-3600,"/");
        header("location: " . Cfg::sMainDomain() . "");
    }

    /**
     * @throws SmartyException
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public static function get_delete(){
        $oTLogin = lcTwitterLogin::getInstance();
        try {
            $oTLogin->login();
        } catch (Exception $exception) {
            header("location: " . Cfg::sMainDomain() . "");
            return;
        }

        if ($oTLogin->twitter_id == 0) {
            header("location: " . Cfg::sMainDomain() . "");
            return;
        }

        $smarty = lcSmarty::getInstance();
        $smarty->assign("tlogin",$oTLogin);
        $smarty->display("settings-delete.tpl");
    }


    public static function post_delete(){
        $oTLogin = lcTwitterLogin::getInstance();
        try {
            $oTLogin->login();
        } catch (Exception $exception) {
            header("location: " . Cfg::sMainDomain() . "");
            return;
        }

        if ($oTLogin->twitter_id == 0) {
            header("location: " . Cfg::sMainDomain() . "");
            return;
        }

        $oTLogin->deleteAll();
        header("location: " . Cfg::sMainDomain() . "settings/logout");
    }
}