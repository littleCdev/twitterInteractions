<?php

// complete root of this project
define("__LcRoot__"     ,dirname(__DIR__)."/");

// core-path
define("__LcCore__"  ,__LcRoot__."core/");

// system classes
define("__LcCoreClasses__",__LcCore__."classes.core/");

// include-path
define("__LcInclude__"  ,__LcCore__."classes.include/");

// config files
define("__LcConfig__"   ,__LcCore__."config/");

// templates for smarty
define("__LcTemplates__",__LcCore__."templates/");

// smarty root
define("__LcSmartyRoot__",__LcCoreClasses__."smarty/");

