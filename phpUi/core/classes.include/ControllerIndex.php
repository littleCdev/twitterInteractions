<?php

class ControllerIndex
{
    /**
     * @return bool
     * @throws SmartyException
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public static function get_index(){
        header('Content-Type: text/html;charset=utf-8');

        $oTLogin = lcTwitterLogin::getInstance();
        try{
            $oTLogin->login();
        }catch (Exception $exception){
            print_r($exception);
        }



        $Smarty = lcSmarty::getInstance();

        if($oTLogin->twitter_id>0){
            $Smarty->assign("tlogin",$oTLogin);
            $Smarty->display("loading.tpl");
        }else{

            $CollagenCount = lcMongo::collection("users")->countDocuments(["status" => ['$ne'=>99]]);
            $Smarty->assign("imagecount",$CollagenCount);
            $Smarty->display("index.tpl");
        }

        return false;
    }
}