<?php

use Abraham\TwitterOAuth\TwitterOAuth ;

define('OAUTH_CALLBACK', Cfg::sMainDomain()."t/auth/");

class lcTwitterApi
{
    /**
     * timeout for twitter-api
     * @var int
     */
    private static $TIMEOUT = 15;

    /**
     * @var null|lcTwitterApi
     */
    private static $instance = null;

    /**
     * @var TwitterOAuth
     */
    private $Connection = null;


    /**
     * @var string
     */
    private $UserToken = "";

    private  $UserSecret = "";

    /**
     * @return lcTwitterApi
     */
    public static function getInstance():lcTwitterApi{
        if(self::$instance == null)
            self::$instance = new self();

        return self::$instance;
    }

    /**
     * creates an instance without user
     */
    public static function initEmpty(){


        self::getInstance()->Connection = new TwitterOAuth(Cfg::ConfigEntry("ApiKey"),  Cfg::ConfigEntry("ApiSecret"));
        self::getInstance()->Connection->setTimeouts(self::$TIMEOUT,self::$TIMEOUT);
    }

    public static function initUser($token,$secret){
        self::getInstance()->Connection = new TwitterOAuth(Cfg::ConfigEntry("ApiKey"),  Cfg::ConfigEntry("ApiSecret"),$token, $secret);
        self::getInstance()->Connection->setTimeouts(self::$TIMEOUT,self::$TIMEOUT);
        self::getInstance()->UserSecret = $secret;
        self::getInstance()->UserToken = $token;
    }

    /**
     * creates an oauth-url to forward the user to twitter for login
     * oauth-data will be stored in session, make sure it's started
     * @throws Exception
     * @return string
     */
    public static function oAuthUrl():string {
        self::initEmpty();

        try {
            $request_token = self::getInstance()->Connection->oauth('oauth/request_token', ['oauth_callback' => OAUTH_CALLBACK]);
        }catch (Exception $e){
            lcLogger::Warn("Connection failed",$e);
            throw new Exception("Hickup! An wild error appeared!",500);
        }
        $_SESSION['oauth_token'] = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
        $url = self::getInstance()->Connection->url('oauth/authorize', ['oauth_token' => $request_token['oauth_token']]);

        return $url;
    }

    /**
     * gets an access token from firstly generated oaut-url
     * @return array [oauth_token,oauth_token_secret]
     * @throws Exception
     */
    public static function oAuthLogin():array {
        $request_token = [];
        $request_token['oauth_token']        = $_SESSION['oauth_token']??"invalidstringihope";
        $request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret']??"invalidstringihope";

        if(!isset($_REQUEST["oauth_token"])){
            lcLogger::Critical("_REQUEST[\"oauth_token\"] was not set");
            throw new Exception("Server error",500);
        }

        if (isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']) {
            lcLogger::Critical("oauth_token didn't match",[$request_token['oauth_token'],$_REQUEST['oauth_token']]);
            throw new Exception("Server error",500);
        }

        self::initUser($request_token['oauth_token'], $request_token['oauth_token_secret']);

        try {
            $access_token = self::getInstance()->Connection->oauth("oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]);
        }catch (Exception $e){
            lcLogger::Critical("Connection failed",$e);
            throw new Exception("Server error",500);
        }

        return $access_token;
    }

    /**
     * @param string $path
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public static function get(string $path,array $params){
        try {
            $data = self::getInstance()->Connection->get($path, $params);
        }catch (Exception $e){
            lcLogger::Warn("Connection failed",$e);
            throw new Exception("Hickup! An wild error appeared!",500);
        }
        self::checkRateLimitAndError(self::getInstance()->Connection,$data);

        return $data;
    }
    /**
     * @param string $path
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public static function post(string $path,array $params){
        try {
            $data = self::getInstance()->Connection->post($path, $params);
        }catch (Exception $e){
            lcLogger::Warn("Connection failed",$e);
            throw new Exception("Hickup! An wild error appeared!",500);
        }
        self::checkRateLimitAndError(self::getInstance()->Connection,$data);

        return $data;
    }

    /**
     * @param TwitterOAuth $Connection
     * @param $Data
     * @throws Exception
     */
    public static function checkRateLimitAndError(TwitterOAuth $Connection,$Data){
        if($Connection->getLastHttpCode() > 500){
            lcLogger::Warn("Twitter error: ".$Connection->getLastHttpCode(),["Con"=>$Connection,"Data"=>$Data]);
            throw new Exception("Twitterserver are throwing errors :(");
        }
        if($Connection->getLastHttpCode() == 410 || $Connection->getLastHttpCode() == 429){
            lcLogger::Info("User reached ratelimit: ",["Con"=>$Connection,"Data"=>$Data]);
            throw new Exception("Ratelimit reached, try again later",410);
        }
        if($Connection->getLastHttpCode() == 401){
            lcLogger::Warn("Invalid login, deleting session");
        }

        if($Connection->getLastHttpCode() == 404){
            throw  new lcNotFoundException();
        }

        if($Connection->getLastHttpCode() != 200){
            lcLogger::Warn("Twitter error: ".$Connection->getLastHttpCode(),["Con"=>$Connection,"Data"=>$Data]);
            throw new Exception("Hickup! An wild error appeared!");
        }
    }
}