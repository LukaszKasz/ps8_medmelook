<div class="panel">
    <div class="panel-heading">
        {l s="Reset consents" mod='x13eucookies'}
    </div>
    <div class="alert alert-info">
        {l s="If you make changes to the cookie settings, it is necessary to obtain the customer's consent again. By clicking this button, the customers will be prompted to provide their cookie consent once more." mod='x13eucookies'}
    </div>
       
    <div class="text-center">
        <form method="post" action="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}">
            <button class="btn btn-warning" name="submitResetConsents">
                <i class="icon-trash"></i>
                {l s="Reset all cookie consents" mod='x13eucookies'}
            </button>
        </form>
        <p class="text-muted">{l s='Last updated on' mod='x13eucookies'}: {$consentModificationsDate|escape:'htmlall':'UTF-8'}</p>
    </div>
</div>
