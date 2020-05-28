<?php

class ControllerImages
{
    public static function get_show($id){

        if(strlen($id) !== 34)
            exit();

        $doc = lcMongo::collection("users")->findOne(["outputs.file"=>$id.".jpg"]);

        $file = __LcRoot__."../images/output/".$doc["twitterId"]."/".$id.".jpg";

        $type = 'image/jpeg';
        header('Content-Type:'.$type);
        header('Content-Length: ' . filesize($file));

        readfile($file);
        exit();
    }

    public static function get_share($id){

        if(strlen($id) !== 34)
            exit();

        $doc = lcMongo::collection("users")->findOne(["outputs.file"=>$id.".jpg"]);

        $aImages = $doc->outputs??[];
        $image = null;
        foreach ($aImages as $_image){
            if($_image->file !== $id.".jpg")
                continue;
            $image = $_image;
        }
        if($image === null)
            die();


        $oTLogin = lcTwitterLogin::getInstance();
        try {
            $oTLogin->login();
        } catch (Exception $exception) {
        }



        $smarty = lcSmarty::getInstance();

        if ($oTLogin->twitter_id > 0) {
            $smarty->assign("tlogin",$oTLogin);
        }

        $twittercard = new stdClass();
        $twittercard->image = Cfg::sMainDomain()."images/".str_replace(".jpg","",$image->file)."/show";
        $twittercard->username = $doc["tname"];

        $smarty->assign("title","InteractionsMap for ".$doc["tname"]);
        $smarty->assign("twittercard",$twittercard);

        $smarty->assign("image",$image);
        $smarty->display("image-share.tpl");

    }

    public static function get_download($id){

        if(strlen($id) !== 34)
            exit();

        $doc = lcMongo::collection("users")->findOne(["outputs.file"=>$id.".jpg"]);

        $file = __LcRoot__."../images/output/".$doc["twitterId"]."/".$id.".jpg";


        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . basename($file) . "\"");
        readfile($file);

        exit();
    }

    public static function get_all(){
        $oTLogin = lcTwitterLogin::getInstance();
        try {
            $oTLogin->login();
        } catch (Exception $exception) {
            exit();
        }

        if ($oTLogin->twitter_id == 0) {
            exit();
        }

        $doc = lcMongo::collection("users")->findOne(["twitterId" => $oTLogin->twitter_id]);

        if ($doc === null) {
            throw new Exception("something went wrong");
        }


        $smarty = lcSmarty::getInstance();
        $smarty->assign("images",$doc["outputs"]);
        $smarty->display("images.tpl");

    }
}