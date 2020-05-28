<!DOCTYPE html>
<html xml:lang="en" lang="en">
{assign "title" "settings"}
{include file="header.tpl"}

<body>
<input type="hidden" id="domain" value="{$sMainDomain}">
{include file="navbar.tpl"}


<main class="bg">
    <form method="post" action="{$sMainDomain}settings/tweet">

        <div class="row">
            <div class="col m4 offset-m2">
                <blockquote class="white-text">
                    currently logged in as {$tlogin->name|escape:htmlall}
                </blockquote>
            </div>
            <div class="col s12 m4 notify">
                <a class="btn-flat blue" href="{$sMainDomain}settings/logout">Logout</a>
            </div>
        </div>
        <div class="row">
            <div class="col m4 offset-m2">
                <blockquote class="white-text">
                    Delete all my data
                </blockquote>
            </div>
            <div class="col s12 m4 notify">
                <a class="btn-flat blue" href="{$sMainDomain}settings/delete">Delete</a>
            </div>
        </div>
    </form>
</main>

{include file="footer.tpl"}
</body>
</html>

