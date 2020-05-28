<head>

    <title>
        {if isset($title)}
            {$title|escape:htmlall}
        {else}
            twitterinteractions.icu
        {/if}
    </title>

    <meta charset="utf-8" />
    <meta name="description" content="simple way to create a map of users you interacted with" />
    <meta name="apple-mobile-web-app-capable" content="yes" />


    <link href="{$sRelativePath}static/css/icon.css.php" rel="stylesheet">

    <link type="text/css" rel="stylesheet" href="{$sRelativePath}{cacheFile file="static/css/materialize.min.css"}" />
    <link type="text/css" rel="stylesheet" href="{$sRelativePath}{cacheFile file="static/css/style.css"}" />

    <link rel="shortcut icon" href="{$sMainDomain}static/img/logo.png">
    <link rel="icon" type="image/png" href="{$sMainDomain}static/img/logo.png" sizes="32x32">
    <link rel="icon" type="image/png" href="{$sMainDomain}static/img/logo.png" sizes="96x96">
    <link rel="apple-touch-icon" sizes="180x180" href="{$sMainDomain}static/img/logo.png">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{$sMainDomain}static/img/logo.png">

    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    {if !isset($twittercard)}
        <meta name="twitter:site" content="@InteractionsMaps" />
        <meta name="twitter:card" content="summary" />
        <meta name="twitter:title" content="twitterinteractions.icu - create your own map of users you interacted with!" />
        <meta name="twitter:description" content="twitterinteractions.icu - create your own map of users you interacted with!" />
        <meta name="twitter:widgets:csp" content="on">
        <meta name="twitter:image" content="{$sMainDomain}static/img/logo.png">
    {else}
        <meta name="twitter:site" content="@InteractionsMaps" />
        <meta name="twitter:card" content="summary" />
        <meta name="twitter:title" content="InteractionsMap for {$twittercard->username}" />
        <meta name="twitter:description" content="twitterinteractions.icu - create your own map of users you interacted with!" />
        <meta name="twitter:widgets:csp" content="on">
        <meta name="twitter:image" content="{$twittercard->image}">
    {/if}

</head>
