<!DOCTYPE html>
<html xml:lang="en" lang="en">
{assign "title" "settings"}
{include file="header.tpl"}

<body>
<input type="hidden" id="domain" value="{$sMainDomain}">
{include file="navbar.tpl"}

<main class="bg">
    <form method="post" action="{$sMainDomain}settings/delete">
        <div class="row">
            <div class="col s12 center-align">
                <p class="white-text">
                    Delete all my data.<br>
                    This includes all interactionmaps that where created for {$tlogin->name|escape:htmlall}<br>
                    and all data saved in the database.
                </p>
            </div>
            <div class="col s12 center-align">
                <button type="submit" class="btn-flat red">Delete</button>
            </div>
        </div>
    </form>
</main>

{include file="footer.tpl"}
</body>
</html>

