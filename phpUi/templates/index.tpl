<!DOCTYPE html>
<html xml:lang="en" lang="en">
{include file="header.tpl"}

<body>
<input type="hidden" id="domain" value="{$sMainDomain}">
{include file="navbar.tpl"}

<main class="diff bg">
    <div class="section valign-wrapper entertext">
        <div class="row">
            <div class="col s12 m6 white-text center-align ">
                <h5>Want to see how you interacted with on twitter?</h5>
                <h5>Get a cool looking visual overview of who you spoke to an with!</h5>

                <h5>We already created {$imagecount} #interactionmaps!</h5>
            </div>
            <div class="col s12 m6 valign-wrapper">
                <a href="{$sMainDomain}t/login" class="valign-wrapper twitter" rel="nofollow" title="login with twitter">
                    <img class="circle twitter" src="{$sMainDomain}static/img/twitter-icon.png" alt="login with twitter">
                    <p>
                        Login with twitter
                    </p>
                </a>
            </div>
        </div>
    </div>
    <div class="row hide-on-med-and-down" id="timeline">
        <div class="col s3 offset-l9">
            <a class="twitter-timeline" data-width="400" data-height="700" data-dnt="true" data-theme="dark" href="https://twitter.com/InteractionsMap/likes?ref_src=twsrc%5Etfw">Tweets Liked by @InteractionsMap</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
        </div>
    </div>
</main>

{include file="footer.tpl"}
</body>
</html>

