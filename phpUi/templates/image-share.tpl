<!DOCTYPE html>
<html xml:lang="en" lang="en">
{include file="header.tpl"}

<body>
<input type="hidden" id="domain" value="{$sMainDomain}">
{include file="navbar.tpl"}

<main class="bg valign-wrapper">
    <div class="section p100">
        <div class="row">
            <div class="col s12">
                <img class="center" src="{$sMainDomain}images/{$image->file|replace:".jpg":""}/show">
            </div>
            <div class="col s12 center">
                <a href="{$sMainDomain}images/{$image->file|replace:".jpg":""}/show" class="btn-flat white-text"><i class="material-icons left">link</i> Direct link</a>
                <a href="{$sMainDomain}images/{$image->file|replace:".jpg":""}/download" class="btn-flat white-text"><i class="material-icons left">cloud_download</i> Download
                    image</a>
                <a href="http://twitter.com/share?text=look+at+my+interactionsmap!&url={$sMainDomain}images/{$image->file|replace:".jpg":""}/share&hashtags=interactionsMap" class="btn-flat white-text">
                    <img class="twittershare" src="{$sMainDomain}static/img/twitter-icon.png" alt="login with twitter">
                    Share on Twitter
                </a>
            </div>
        </div>
    </div>
</main>

{include file="footer.tpl"}
</body>
</html>

