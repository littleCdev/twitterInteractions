<?php

header('Content-Type: text/html;charset=utf-8');
header('Connection: close');

include_once("core/core.php");

use Phroute\Phroute\RouteCollector;
use Phroute\Phroute\Dispatcher;


function runControllerFunction($sClassName,$sFunction,$Param=""){
    if(strlen($sFunction)==0){
        $sFunction = "index";
    }

    if(lcCore::isPost()){
        $sMethod = 'post_'.$sFunction;
    }else{
        $sMethod = 'get_'.$sFunction;
    }

    $message = "";
    if(!class_exists($sClassName)){
        $message = "Controller ".$sClassName." does not exist -x (function: ".$sFunction.", param:".$Param.")";
        http_response_code(404);
    }else if(!method_exists($sClassName,$sMethod)){
        lcLogger::Warn("Route -".$sClassName."::".$sFunction."- does not exist -y");
        $message = "Route ".$sClassName."::".$sFunction." does not exist -y";
        http_response_code(404);
    }
    else
    {
        try{
            call_user_func($sClassName."::".$sMethod,$Param);
            return;
        }catch (Exception $exception){
            $message = $exception->getMessage();
            if($exception->getCode() > 300)
                http_response_code($exception->getCode());
        }
    }

    lcLogger::Warn($message);
    $smarty = lcSmarty::getInstance();
    $smarty->display("404.tpl");
}

$collector = new RouteCollector();

$collector->group([], function(RouteCollector $collector){
    $collector->any('Admin/{controller}/{id:a}/{function}', function($sController,$Id="",$sFunction=""){
        $sClassName = "ControllerAdmin".ucfirst($sController);
        runControllerFunction($sClassName,$sFunction,$Id);
    });

    $collector->any('Admin/{controller}/{function}', function($sController,$sFunction){
        $sClassName = "ControllerAdmin".ucfirst($sController);
        runControllerFunction($sClassName,$sFunction,"does not exist");
    });

    $collector->any('Index/{controller}/{id:a}/{function}', function($sController,$Id="",$sFunction=""){
        $sClassName = "ControllerIndex".ucfirst($sController);
        runControllerFunction($sClassName,$sFunction,$Id);
    });
    $collector->any('Backend/{controller}/{function}', function($sController,$sFunction=""){
        $sClassName = "ControllerBackend".ucfirst($sController);
        runControllerFunction($sClassName,$sFunction,"does not exist");
    });
    $collector->any('Backend/{controller}/{id:a}/{function}', function($sController,$Id="",$sFunction=""){
        $sClassName = "ControllerBackend".ucfirst($sController);
        runControllerFunction($sClassName,$sFunction,$Id);
    });
    $collector->any('{controller}/{id}/{function}', function($sController,$Id="",$sFunction=""){
        $sClassName = "Controller".ucfirst($sController);
        runControllerFunction($sClassName,$sFunction,$Id);
    });
    $collector->any('{controller}/{function}', function($sController,$sFunction){
        $sClassName = "Controller".ucfirst($sController);
        runControllerFunction($sClassName,$sFunction,"does not exist");
    });

    // like index/ function will be set to index if empty
    $collector->any('{controller}', function($sController,$sFunction=""){
        $sClassName = "Controller".ucfirst($sController);
        runControllerFunction($sClassName,$sFunction,"does not exist");
    });

});

$dispatcher =  new Dispatcher($collector->getData());
$sUrlPath = str_replace(Cfg::getHttRoot(),"",parse_url($_SERVER['REQUEST_URI'])["path"]);
if(strlen(Cfg::UrlSubFolder) > 0){
    $sUrlPath = str_replace(Cfg::UrlSubFolder,"",$sUrlPath);
}

$sUrlPath = str_replace("-","_",$sUrlPath);

if(strlen($sUrlPath) == 0 || $sUrlPath == "/")
    $sUrlPath = "Index";

try{
    $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $sUrlPath);
}catch (Exception $exception){
    header('Content-Type: text/html');
    echo $exception->getMessage()."+";
}

