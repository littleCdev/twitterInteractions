<div class="navbar-fixed">
    <nav>
        <div class="nav-wrapper blue">
            <a href="{$sMainDomain}" class="brand-logo left valign-wrapper">
                <img src="{$sMainDomain}static/img/logo.png" alt="Logo L">
                <span class="">witterinteractions</span>
            </a>
            {if isset($tlogin) && $tlogin->twitter_id > 0}
                <ul id="nav-mobile" class="right">
                    <li>
                        <a href="{$sMainDomain}settings" class="icon valign-wrapper" title="your account">
                            <img src="{$tlogin->profile_picture}" class="circle" alt="your twitter logo">
                            {$tlogin->name|escape:htmlall}
                        </a>
                    </li>
                    <li>
                        <a href="{$sMainDomain}settings" title="settings">
                            <i class="material-icons">settings</i>
                        </a>
                    </li>
                </ul>
            {/if}
        </div>
    </nav>
</div>
