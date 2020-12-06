{if $cancreatenew}
<div class="row">
    <!--
    <div class="col offset-l2 s8 center-align">
            <div class="card horizontal " id="warning">
                <div class="card-image">
                    <img src="{Cfg::sMainDomain()}static/img/alert.png" alt="alert">
                </div>
                <div class="card-stacked">
                    <div class="card-content white-text">
                        <p>Your last map is older than two month old, do you want to create a new one?</p>
                    </div>
                    <div class="card-action">
                        <a href="{Cfg::sMainDomain()}index/startOver">Create a new one</a>
                    </div>
                </div>
            </div>
            -->

    <div class="col offset-l2 s8 center-align">

        <h5>
            <a class="white-text" href="{Cfg::sMainDomain()}index/startOver">
                <i class="material-icons">notification_important</i>
                You last interactionsmap is old, click here to create a new map!
            </a>
        </h5>

    </div>

</div>
{/if}

</div>
{foreach from=$images item=$image}
    <div class="row">
        <div class="col s12">
            <a href="{$sMainDomain}images/{$image->file|replace:".jpg":""}/share">
                <h4 class="white-text">{$image->date|date_format:"%D"}</h4>
                <div class="card">
                    <div class="card-image">
                        <img src="{$sMainDomain}images/{$image->file|replace:".jpg":""}/show"
                             alt="your twitter collage"/>
                    </div>
                    <div class="card-action">
                        <a href="{$sMainDomain}images/{$image->file|replace:".jpg":""}/show" class="btn-flat white-text"><i class="material-icons left">link</i> Direct link</a>
                        <a href="{$sMainDomain}images/{$image->file|replace:".jpg":""}/download" class="btn-flat white-text"><i class="material-icons left">cloud_download</i> Download
                            image</a>
                        <a href="http://twitter.com/share?text=look+at+my+interactionsmap!&url={$sMainDomain}images/{$image->file|replace:".jpg":""}/share&hashtags=interactionsMap" class="btn-flat white-text">
                            <img class="twittershare" src="{$sMainDomain}static/img/twitter-icon.png" alt="login with twitter">
                                Share on Twitter
                        </a>
                    </div>
                </div>
            </a>
        </div>
    </div>
{/foreach}