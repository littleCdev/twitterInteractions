<?php


class lcTwitterLogin
{
    public $twitter_id = 0;
    public $name = "";
    public $profile_picture = "";
    public $cookie = "";
    public $oauth_token = "";
    public $oauth_token_secret = "";
    public $status = 0;
    public $tweetCount = 0;
    public $tweetsCrawled = 0;
    public $outputs = [];

    /**
     * @var self
     */
    protected static $oInstance = null;

    /**
     * @return int
     */
    public function getTwitterId(): int
    {
        return $this->twitter_id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return lcTwitterLogin
     */
    public static function getInstance(): lcTwitterLogin
    {
        if (null === self::$oInstance) {
            self::$oInstance = new self;
        }
        return self::$oInstance;
    }

    /**
     * lcCuriousLogin constructor.
     */
    private function __construct()
    {
    }

    public function isError(){
        if($this->status == 99)
            return true;
        return false;
    }

    public function canStartOver():bool{
        // users with error always can try again
        if($this->status == 99)
            return true;

        // users that are still in working queue are not allowed to create another one
        if($this->status != 7)
            return false;



        $maxImageAge = time()-(60*60*24*31); // 1 month


        foreach ($this->outputs as $image) {
            if($image->date > $maxImageAge)
                return false;
        }

        return true;
    }

    public function startOver(){
        if(!$this->canStartOver())
            return false;

        $currentTweetCount = 0;
        // get current tweet count
        lcTwitterApi::initUser($this->oauth_token, $this->oauth_token_secret);
        $user = lcTwitterApi::get("users/show",["user_id"=>$this->twitter_id]);

        $currentTweetCount = $user->statuses_count ?? 0;

        $tweets = new stdClass();
        $tweets->total = $currentTweetCount;
        $tweets->current = 0;

        $collection = lcMongo::collection("users");
        $collection->updateOne(
            ["twitterId"=>$this->twitter_id],
            ['$set' => [
                'status'=>0,
                'tweets'=>$tweets
            ],
                '$unset'=>[
                    'interactions'=>"",
                    'download'=>'',
                    'files'=>"",
                    'collage'=>""
                ]]
        );

        return true;
    }

    private static function createCookie()
    {

        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $length = 64;
        $collection = lcMongo::collection("users");
        do {
            $input_length = strlen($permitted_chars);
            $random_string = '';
            for ($i = 0; $i < $length; $i++) {
                $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
                $random_string .= $random_character;
            }


            $result = $collection->findOne(["cookie" => $random_string]);
        } while ($result != null);


        return $random_string;
    }


    /**
     * @throws \MongoDB\Driver\Exception\Exception
     */
    public function login(): void
    {
        if ($this->twitter_id > 0)
            return;

        $cookie = $_COOKIE["rm"] ?? "";

        if (strlen($cookie) != 64) {
            setcookie("rm", "", time() - 3600, "/");
            return;
        }

        $collection = lcMongo::collection("users");
        $doc = $collection->findOne(["cookie" => $cookie]);

        if ($doc === null) {
            setcookie("rm", "", time() - 3600, "/");
            return;
        }

        $this->oauth_token_secret = $doc["oauth_token_secret"];
        $this->oauth_token = $doc["oauth_token"];
        $this->name = $doc["tname"];
        $this->twitter_id = $doc["twitterId"];
        $this->profile_picture = $doc["profile_picture"];
        $this->status = $doc["status"];
        $this->tweetCount = $doc["tweets"]->total;
        $this->tweetsCrawled = $doc["tweets"]->current;
        $this->outputs = $doc["outputs"]??[];

    }


    /**
     * oauth-login. user returning from self::oAuthUrl
     * @throws Exception
     * @return bool
     */
    public function oAuthLogin(): bool
    {

        $access_token = lcTwitterApi::oAuthLogin();
        $this->oauth_token = $access_token["oauth_token"];
        $this->oauth_token_secret = $access_token["oauth_token_secret"];

        $this->_oAuthVerifyCredentials();

        return true;
    }

    /**
     * for returning users
     * @throws Exception
     */
    private function _oAuthVerifyCredentials(): void
    {

        lcTwitterApi::initUser($this->oauth_token, $this->oauth_token_secret);

        $oUser = lcTwitterApi::get(
            'account/verify_credentials',
            ['tweet_mode' => 'extended',
                'include_entities' => 'true']);

        if (!$this->updateCredentials($oUser->id, $this->oauth_token, $this->oauth_token_secret)) {
            $this->addUser($oUser, $this->oauth_token, $this->oauth_token_secret);
        }

        $collection = lcMongo::collection("users");


        $doc = $collection->findOne(["twitterId" => $oUser->id]);

        if ($doc === null)
            throw new Exception("could not find user: " . $oUser->id);

        $this->oauth_token_secret = $doc["oauth_token_secret"];
        $this->oauth_token = $doc["oauth_token"];
        $this->name = $doc["tname"];
        $this->twitter_id = $doc["twitterId"];
        $this->profile_picture = $doc["profile_picture"];
        $this->cookie = $doc["cookie"];
        $this->status = $doc["status"];
        $this->tweetCount = $doc["tweets"]->total;
        $this->tweetsCrawled = $doc["tweets"]->current;
        $this->outputs = $doc["outputs"]??[];

        // 1 month
        setcookie("rm", $this->cookie, time() + 3600 * 24 * 30, "/");

    }

    /**
     * @param int $tid
     * @param $token
     * @param $secret
     * @return bool
     * @throws Exception
     */
    private function updateCredentials(int $tid, $token, $secret): bool
    {

        $set = ['$set' => [
            'oauth_token' => $token,
            'oauth_token_secret' => $secret,
        ]
        ];
        $query = [
            'twitterId' => $tid
        ];

        $result = lcMongo::collection("users")->updateOne($query, $set);

        if ($result->getMatchedCount() != 1) {
            lcLogger::Debug("user does not exist in database", [$query, $set]);

            return false;
        }

        return true;
    }

    /**
     * @param $oUser
     * @param string $token
     * @param string $secret
     */
    private function addUser($oUser, string $token, string $secret): void
    {

        $tweets = new stdClass();
        $tweets->total = $oUser->statuses_count;
        $tweets->current = 0;

        $aParams = [
            "twitterId" => $oUser->id,
            "tname" => $oUser->name,
            "cookie" => self::createCookie(),
            "profile_picture" => $oUser->profile_image_url_https,
            "oauth_token" => $token,
            "oauth_token_secret" => $secret,
            "status" => 0,
            "tweets" => $tweets,
            "creation_time" => date("Y-m-d H:i:s")
        ];

        lcMongo::collection("users")->insertOne($aParams);

        $this->oauth_token_secret = $secret;
        $this->oauth_token = $token;
        $this->name = $oUser->name;
        $this->twitter_id = $oUser->id;
        $this->status = 0;
        $this->cookie = $aParams["cookie"];
        $this->tweetCount = $oUser->statuses_count;
    }

    // deletes all files and databaseentries for this user
    function deleteAll()
    {
        $doc = lcMongo::collection("users")->findOne(["twitterId" => $this->twitter_id]);

        if ($doc === null) {
            lcLogger::Critical("could not find user: ", $this);
            throw new Exception("could not find user");
        }

        $collageFiles = $doc->outputs ?? [];

        foreach ($collageFiles as $collageFile) {
            if (!@unlink(Cfg::ConfigEntry("OutputPath") . $this->twitter_id . "/" . $collageFile->file)) {
                lcLogger::Warn("failed to delete file: " . Cfg::ConfigEntry("OutputPath") . $this->twitter_id . "/" . $collageFile->file, error_get_last());
            }
        }
        if (is_dir(Cfg::ConfigEntry("OutputPath") . $this->twitter_id . "/")) {
            if (!@rmdir(Cfg::ConfigEntry("OutputPath") . $this->twitter_id . "/")) {
                lcLogger::Warn("failed to delete dir: " . Cfg::ConfigEntry("OutputPath") . $this->twitter_id . "/", error_get_last());
            }
        }

        lcMongo::collection("users")->deleteOne(["twitterId" => $this->twitter_id]);
    }

}