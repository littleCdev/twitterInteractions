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