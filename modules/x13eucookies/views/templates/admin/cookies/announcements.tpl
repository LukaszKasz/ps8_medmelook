<div class="panel">
    <div class="panel-heading">
        {l s='Important announcement regarding new version' mod='x13eucookies'}
    </div>
    <div class="panel-body">
        {if $module_version == '1.3.0'}
        <div class="alert alert-info">
            <p>
                <span class="label label-info">{l s='Version:' mod='x13eucookies'}1.3.0</span>
            </p>
            <p>
                {l s='In version 1.3.0, we have made changes to the module\'s cookie deletion process. Now, you must explicitly select which cookies should be deleted if the user hasn\'t given consent to store them. This change is necessary because some cookies, like those created by Google, should not to be deleted automatically. Therefore, it\'s crucial to review the list of added and enabled cookies. If you find any cookies from Google, you probably should not to delete them, as Google\'s consent mode will take care of everything.' mod='x13eucookies'}
            </p>
        </div>
        {/if}
        <hr>
        {l s='Module documentation' mod='x13eucookies'}: <a href="https://x13.pl/doc/dokumentacja-eu-cookies-baner-z-blokowaniem-ciastek-dla-prestashop" target="_blank">https://x13.pl/doc/dokumentacja-eu-cookies-baner-z-blokowaniem-ciastek-dla-prestashop</a>
    </div>
    <div class="panel-footer">
        <form method="post" action="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}">
            <button class="btn btn-success"  name="submitDiscardAnnouncement">
                <i class="icon-ok"></i>
                {l s='I understand. Close and never show again for this update.' mod='x13eucookies'}
            </button>
        </form>
        
    </div>
</div>
