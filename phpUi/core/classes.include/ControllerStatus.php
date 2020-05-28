<?php
/**
 * Created by PhpStorm.
 * User: littlecheetah
 * Date: 20.05.2020
 * Time: 17:21
 */

class ControllerStatus
{
    public static function get_index()
    {
        header('Content-Type: application/json;charset=utf-8');
        $oTLogin = lcTwitterLogin::getInstance();
        try {
            $oTLogin->login();
        } catch (Exception $exception) {
            header("location: " . Cfg::sMainDomain() . "");
        }

        if ($oTLogin->twitter_id == 0) {
            header("location: " . Cfg::sMainDomain() . "");
        }

        $doc = lcMongo::collection("users")->findOne(["twitterId" => $oTLogin->twitter_id]);

        if ($doc === null) {
            throw new Exception("something went wrong");
        }

        $status = new lcStatus($doc);

        echo json_encode($status);
    }
}