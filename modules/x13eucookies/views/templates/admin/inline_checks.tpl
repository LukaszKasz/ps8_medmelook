{if empty($id_xeucookies_cookie_category)}
<div class="alert alert-info">
{l s='You need to save the category first.' mod='x13eucookies'}
</div>
{else}
<div class="x13eucookies-inline-checks">
    <div class="alert alert-info">
        {l s='There are two ways to block scripts added directly in Smarty template files and JavaScript.' mod='x13eucookies'}
    </div>
    <div class="panel">
        <div class="panel-heading">
            {l s='Block scripts added directly in Smarty template files' mod='x13eucookies'}
        </div>
        <div class="panel-body">
            <p>{l s='You can block scripts added directly in Smarty template files by adding the following code to the Smarty template file' mod='x13eucookies'}:</p>
            <pre>{strip}
            {literal}
{if !empty($x13eucookies_consents[{/literal}<span style="color:#FF0000">{$id_xeucookies_cookie_category}</span>{literal}])}

{/if}{/literal}</pre>
        </div>
    </div>
    <div class="panel">
        <div class="panel-heading">
            {l s='Block scripts inside JavaScript files' mod='x13eucookies'}
        </div>
        <div class="panel-body">
            <p>{l s='You can block code in JavaScript by adding the following code to your script' mod='x13eucookies'}:</p>
            <pre>{literal}
if (typeof x13eucookies_consents !== 'undefined' && x13eucookies_consents[{/literal}<span style="color:#FF0000">{$id_xeucookies_cookie_category}</span>{literal}]) {
    
}{/literal}</pre>
            </div>
        </div>
</div>
{/if}