<?php

class lcStatus
{
    public $status = 0;
    public $stageTweets = 0;
    public $stageImages = 0;
    public $stageCollage = 0;
    public $message = "";
    public $images = [];


    public function __construct($doc)
    {
        $this->images = $doc["outputs"] ?? [];
        switch ($doc["status"])
        {
            case 0:
                $this->status = 1;
                // wait for user crawling
                break;

            case 2:
                $this->status = 1;

                if($doc["tweets"]->total == 0)
                    $doc["tweets"]->total = 3200;

                if($doc["tweets"]->current > 0)
                    $this->stageTweets = round(100*($doc["tweets"]->current/$doc["tweets"]->total));

                if($this->stageTweets > 100)
                    $this->stageTweets = 100;

                // crawling users
                break;
            case 3:
                $this->status = 2;
                $this->stageTweets = 100;
                // wait for image crawling
                break;
            case 4:
                $this->status = 2;
                $this->stageTweets = 100;
                if($doc["download"]->current > 0)
                    $this->stageImages = round(100*($doc["download"]->current/$doc["download"]->total));
                // crawling images
                break;
            case 5:
                // wait for imagecreation
                $this->status = 2;
                $this->stageTweets = 100;
                $this->stageImages = 100;
                break;
            case 6:
                // image creationg
                $this->status = 2;
                $this->stageTweets = 100;
                $this->stageImages = 100;
                if($doc["collage"]->current > 0)
                    $this->stageCollage = round(100*($doc["collage"]->current/$doc["collage"]->total));

                // creating images
                break;
            case 7:

                $this->status = 3;
                $this->stageTweets = 100;
                $this->stageImages = 100;
                $this->stageCollage = 100;
                // done
                break;


            case 99:
                $this->message = $doc["message"];
                $this->status = 99;

                break;
        }
    }
}