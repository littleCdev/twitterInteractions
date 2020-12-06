<?php

include_once("core/core.php");


function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
    exit(1);
}
set_error_handler("exception_error_handler");

$collection = lcMongo::collection("users");

$doc = $collection->findOne(["status"=>5]);

if($doc === null){
//    echo "nothing to do\n";
    return;
}

$collection->updateOne(
    ["twitterId"=>$doc["twitterId"]],
    ['$set' => [
        'status'=>6
    ]]
    );

$images = $doc->files;

$layout = count($images);

$sizes = [
    "50" => [
        1100,
        600
    ],
    "19" => [
        600,
        600
    ],
    "11" => [
        400,
        400
    ]
];

$infoPositions = [
    "50" => [
        ["x"=>0,"y"=>500]
    ],
    "19" => [
        ["x"=>200,"y"=>500]
    ],
    "11" => [
        ["x"=>100,"y"=>300]
    ]
];

$positions= [
    "50" => [
        ["x"=>400,"y"=>200,"size"=>300],

        ["x"=>100,"y"=>100,"size"=>200],
        ["x"=>800,"y"=>300,"size"=>200],

        ["x"=>0,"y"=>0,"size"=>100],
        ["x"=>100,"y"=>0,"size"=>100],
        ["x"=>200,"y"=>0,"size"=>100],
        ["x"=>300,"y"=>0,"size"=>100],
        ["x"=>400,"y"=>0,"size"=>100],
        ["x"=>500,"y"=>0,"size"=>100],
        ["x"=>600,"y"=>0,"size"=>100],
        ["x"=>700,"y"=>0,"size"=>100],
        ["x"=>800,"y"=>0,"size"=>100],
        ["x"=>900,"y"=>0,"size"=>100],
        ["x"=>1000,"y"=>0,"size"=>100],

        ["x"=>00,"y"=>100,"size"=>100],
        ["x"=>300,"y"=>100,"size"=>100],
        ["x"=>400,"y"=>100,"size"=>100],
        ["x"=>500,"y"=>100,"size"=>100],
        ["x"=>600,"y"=>100,"size"=>100],
        ["x"=>700,"y"=>100,"size"=>100],
        ["x"=>800,"y"=>100,"size"=>100],
        ["x"=>900,"y"=>100,"size"=>100],
        ["x"=>1000,"y"=>100,"size"=>100],

        ["x"=>00,"y"=>200,"size"=>100],
        ["x"=>300,"y"=>200,"size"=>100],
        ["x"=>700,"y"=>200,"size"=>100],
        ["x"=>800,"y"=>200,"size"=>100],
        ["x"=>900,"y"=>200,"size"=>100],
        ["x"=>1000,"y"=>200,"size"=>100],

        ["x"=>00,"y"=>300,"size"=>100],
        ["x"=>100,"y"=>300,"size"=>100],
        ["x"=>200,"y"=>300,"size"=>100],
        ["x"=>300,"y"=>300,"size"=>100],
        ["x"=>700,"y"=>300,"size"=>100],
        ["x"=>1000,"y"=>300,"size"=>100],

        ["x"=>00,"y"=>400,"size"=>100],
        ["x"=>100,"y"=>400,"size"=>100],
        ["x"=>200,"y"=>400,"size"=>100],
        ["x"=>300,"y"=>400,"size"=>100],
        ["x"=>700,"y"=>400,"size"=>100],
        ["x"=>1000,"y"=>400,"size"=>100],

        ["x"=>200,"y"=>500,"size"=>100],
        ["x"=>300,"y"=>500,"size"=>100],
        ["x"=>400,"y"=>500,"size"=>100],
        ["x"=>500,"y"=>500,"size"=>100],
        ["x"=>600,"y"=>500,"size"=>100],
        ["x"=>700,"y"=>500,"size"=>100],
        ["x"=>800,"y"=>500,"size"=>100],
        ["x"=>900,"y"=>500,"size"=>100],
        ["x"=>1000,"y"=>500,"size"=>100]
    ],

    "19" => [
        ["x"=>200,"y"=>200,"size"=>200],

        ["x"=>00,"y"=>00,"size"=>200],
        ["x"=>400,"y"=>00,"size"=>200],
        ["x"=>00,"y"=>400,"size"=>200],
        ["x"=>400,"y"=>400,"size"=>200],


        ["x"=>200,"y"=>00,"size"=>100],
        ["x"=>300,"y"=>00,"size"=>100],

        ["x"=>200,"y"=>100,"size"=>100],
        ["x"=>300,"y"=>100,"size"=>100],


        ["x"=>00,"y"=>200,"size"=>100],
        ["x"=>100,"y"=>200,"size"=>100],
        ["x"=>400,"y"=>200,"size"=>100],
        ["x"=>500,"y"=>200,"size"=>100],

        ["x"=>00,"y"=>300,"size"=>100],
        ["x"=>100,"y"=>300,"size"=>100],
        ["x"=>400,"y"=>300,"size"=>100],
        ["x"=>500,"y"=>300,"size"=>100],


        ["x"=>200,"y"=>400,"size"=>100],
        ["x"=>300,"y"=>400,"size"=>100]
    ],
    "11"=>[

        ["x"=>100,"y"=>100,"size"=>200],

        ["x"=>00,"y"=>00,"size"=>100],
        ["x"=>100,"y"=>00,"size"=>100],
        ["x"=>200,"y"=>00,"size"=>100],
        ["x"=>300,"y"=>00,"size"=>100],

        ["x"=>00,"y"=>100,"size"=>100],
        ["x"=>300,"y"=>100,"size"=>100],

        ["x"=>00,"y"=>200,"size"=>100],
        ["x"=>300,"y"=>200,"size"=>100],

        ["x"=>00,"y"=>300,"size"=>100],
        ["x"=>300,"y"=>300,"size"=>100],
    ]
];


$baseimage = new Imagick();
$baseimage->newImage($sizes[$layout][0],$sizes[$layout][1],new ImagickPixel('#1dcaff'));
$baseimage->setFormat("jpg");

$infoimage = new Imagick(__LcRoot__."static/placeholder.png");
$baseimage->compositeImage($infoimage,$infoimage->getImageCompose(),$infoPositions[$layout][0]["x"],$infoPositions[$layout][0]["y"]);
$infoimage->clear();

for ($i=0;$i<$layout;$i++){
    $icon = new Imagick(Cfg::ConfigEntry("DownloadPath").$doc["twitterId"]."/".$images[$i]->localpath);
    $icon->resizeImage( $positions[$layout][$i]["size"], $positions[$layout][$i]["size"], Imagick::FILTER_LANCZOS, 0.9);

    $baseimage->compositeImage($icon,$icon->getImageCompose(),$positions[$layout][$i]["x"],$positions[$layout][$i]["y"]);

    echo $i."/".$layout."\n";

    $collection->updateOne(
        ["twitterId"=>$doc["twitterId"]],
        ['$set' => [
            'collage.current'=>$i,
            'collage.total'=>$layout,
        ]]
    );

    $icon->clear();
}

$filename = $doc["_id"]->__toString().time().".jpg";
$dir = Cfg::ConfigEntry("OutputPath").$doc["twitterId"]."/";

if(!is_dir($dir))
    mkdir($dir,0775);
chown($dir,Cfg::ConfigEntry("fileUser"));
chgrp($dir,Cfg::ConfigEntry("fileUser"));

$baseimage->writeImage($dir.$filename);
chmod($dir.$filename,0777);
chown($dir.$filename,Cfg::ConfigEntry("fileUser"));
chgrp($dir.$filename,Cfg::ConfigEntry("fileUser"));

$collage = new stdClass();
$collage->date = time();
$collage->file = $filename;

$collection->updateOne(
    ["twitterId"=>$doc["twitterId"]],
    [
        '$push' => [
            'outputs'=>$collage
        ],
        '$set' => [
            'status'=>7
        ]
    ]
);
// cleanup

//delete files from disk
$dir = Cfg::ConfigEntry("DownloadPath").$doc["twitterId"]."/";
$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
$files = new RecursiveIteratorIterator($it,RecursiveIteratorIterator::CHILD_FIRST);
foreach($files as $file) {
    if ($file->isDir()){
        rmdir($file->getRealPath());
    } else {
        unlink($file->getRealPath());
    }
}
rmdir($dir);

// unset stuff in database
$collection->updateOne(
    ["twitterId"=>$doc["twitterId"]],
    [
        '$unset' => [
            'interactions'=>"",
            'files'=>"",
            'collage'=>"",
            'download'=>''
            ],
        '$set'=>[
            'tweets.total'=>0,
            'tweets.current'=>0,
        ]]
);

