<!DOCTYPE html>
<html xml:lang="en" lang="en">
{include file="header.tpl"}

<body>
<input type="hidden" id="domain" value="{$sMainDomain}">
{include file="navbar.tpl"}

<main class="bg">
    <div class="row hide" id="message">
        <div class="col s12 m6 offset-m3">
            <div class="center-align">
                <h5 class="red-text" id="errormessage"></h5>
            </div>
        </div>
    </div>
    <div class="row" id="loading">
        <div class="col s12 m6 offset-m3">
            <div class="center-align">
                <div class="preloader-wrapper big active">
                    <div class="spinner-layer spinner-blue-only">
                        <div class="circle-clipper left">
                            <div class="circle"></div>
                        </div><div class="gap-patch">
                            <div class="circle"></div>
                        </div><div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="white-text">Processing your tweets: <span id="statusTextTweets"></span></h5>
            <div class="progress">
                <div class="determinate" id="stageTweets" style="width: 1%"></div>
            </div>
            <h5 class="white-text">Getting usericons: <span id="statusTextImages"></span></h5>
            <div class="progress">
                <div class="determinate" id="stageImages" style="width: 1%"></div>
            </div>
            <h5 class="white-text">Creating your usermap: <span id="statusTextCollage"></span></h5>
            <div class="progress">
                <div class="determinate" id="stageCollage" style="width: 1%"></div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col s12 m6 offset-m3" id="images">
        <div>
    </div>
</main>

{include file="footer.tpl"}
<script type="text/javascript" src="{$sRelativePath}/static/js/jquery-3.4.1.js"></script>
<script type="text/javascript" src="{$sRelativePath}/static/js/materialize.min.js"></script>
<script type="text/javascript" src="{$sRelativePath}/static/js/initial.js"></script>
</body>
</html>

