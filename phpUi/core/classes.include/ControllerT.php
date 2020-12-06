<?php
/**
 * Created by PhpStorm.
 * User: littlecheetah
 * Date: 07.10.2019
 * Time: 17:23
 */

class ControllerT
{
    /**
     * @throws Exception
     */
    public static function get_login(){
        session_start();
        $sUrl = lcTwitterApi::oAuthUrl();
        header('Location: '.$sUrl);
    }

    /**
     * @throws Exception
     */
    public static function get_auth(){
        session_start();
        if(!isset($_GET["oauth_verifier"],$_GET["oauth_verifier"])){

            lcLogger::Info("not oauth_verifier or oauth_token_secret");
            setcookie( session_name(), "", time()-3600, "/" );
            session_destroy();
            header('Location: '.Cfg::sMainDomain());
            return;
        }


        if(!lcTwitterLogin::getInstance()->oAuthLogin()){
            setcookie( session_name(), "", time()-3600, "/" );
            session_destroy();
            header("location: ".Cfg::sMainDomain());

            exit();
        }

        setcookie( session_name(), "", time()-3600, "/" );
        session_destroy();

        if(lcTwitterLogin::getInstance()->canStartOver()){
            header("location: ".Cfg::sMainDomain()."index/startOver");
        }else{
            header("location: ".Cfg::sMainDomain()."");
        }
    }


    /**
     * forwards and user with twitter-id to twitter user-page
     * @param $ID
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public static function get_f($ID){
        header('Content-Type: text/html;charset=utf-8');

        $oTLogin = lcTwitterLogin::getInstance();
        $oTLogin->login();

        if($oTLogin->twitter_id <= 0){
            header("location: ".Cfg::sMainDomain()."");
            return;
        }

        $oTUser = new lcTwitterUsers($oTLogin);

        $Username = $oTUser->getFollowerLive($ID);
        $url = "https://twitter.com/".urlencode($Username);
        header("location: ".$url);

        exit();
    }


}