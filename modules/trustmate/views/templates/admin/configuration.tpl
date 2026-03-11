{*
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  @copyright  2007-2021 PrestaShop SA
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $output != ''}
    <div class="bootstrap">
        <div class="module_confirmation conf confirm alert alert-{$outputType|escape:'htmlall':'UTF-8'}">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {$output|escape:'htmlall':'UTF-8'}
        </div>
    </div>
{/if}

<div class="row">
    <div class="col-md-12">
        {if $have_account}
        <ul class="nav nav-tabs" style="padding-left: 15px">
            <li {if $current_active == 'account'} class="active" {/if}><a href="#account" data-toggle="tab">{l s='TrustMate’s account' mod='trustmate'}</a></li>
            <li {if $current_active == 'invitations'} class="active" {/if}><a href="#invites" data-toggle="tab">{l s='Invitations' mod='trustmate'}</a></li>
            <li {if $current_active == 'widgets'} class="active" {/if}><a href="#widgets" data-toggle="tab">{l s='Widgets' mod='trustmate'}</a></li>
        </ul>
        {/if}
        <div class="tab-content">
            <div class="tab-pane panel {if $current_active == 'account'} active {/if} " id="account">
                {if $display_create_account && $have_account == false}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel">
                                <div class="panel-body">
                                    <p class="text-center" style="font-size: 1.5em; margin-bottom: 24px;">
                                        {l s='To facilitate your account registration, we will use the data you provided in your PrestaShop store. You can change them later in the TrustMate panel. In case of problems, please contact support@trustmate.io.' mod='trustmate'}
                                    </p>
                                    <form method="POST" action="" id="accout_form">
                                        <div  style="width: min(300px, 100%)">
                                            <small style="font-weight: bold; display: block; margin-bottom: 4px;" class="form-text text-muted">{l s="Account data" mod='trustmate'}</small>
                                            <div class="form-group" style="width: min(300px, 100%)">
                                                <label for="trustmate-url">{l s="Website address" mod='trustmate'}</label>
                                                <input
                                                    type="text"
                                                    name="url"
                                                    class="form-control"
                                                    id="trustmate-url"
                                                    aria-describedby="website"
                                                    value="{$account_data.url}"
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label for="trustmate-email">{l s="Owner e-mail" mod='trustmate'}</label>
                                                <input
                                                    type="email"
                                                    name="email"
                                                    class="form-control"
                                                    id="trustmate-email"
                                                    aria-describedby="e-mail"
                                                    value="{$account_data.email}"
                                                >
                                            </div>
                                            <small style="font-weight: bold; display: block; margin-bottom: 4px;" class="form-text text-muted">{l s="Company registration data" mod='trustmate'}</small>
                                            <div class="form-group">
                                                <label for="trustmate-company-name">{l s="Company name" mod='trustmate'}</label>
                                                <input
                                                    type="text"
                                                    name="name"
                                                    class="form-control"
                                                    id="trustmate-company-name"
                                                    aria-describedby="company name"
                                                    value="{$account_data.name}"
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label for="trustmate-street">{l s="Street" mod='trustmate'}</label>
                                                <input
                                                    type="text"
                                                    name="street"
                                                    class="form-control"
                                                    id="trustmate-street"
                                                    aria-describedby="street"
                                                    value="{$account_data.street}"
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label for="trustmate-city">{l s="City" mod='trustmate'}</label>
                                                <input
                                                    type="text"
                                                    name="city"
                                                    class="form-control"
                                                    id="trustmate-city"
                                                    aria-describedby="city"
                                                    value="{$account_data.city}"
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label for="trustmate-zip-code">{l s="Zip code" mod='trustmate'}</label>
                                                <input
                                                    type="text"
                                                    name="zip_code"
                                                    class="form-control"
                                                    id="trustmate-zip-code"
                                                    aria-describedby="zip-code"
                                                    value="{$account_data.zip_code}"
                                                >
                                            </div>
                                            <div class="form-group">
                                                <label for="trustmate-country">{l s="Country" mod='trustmate'}</label>
                                                <select class="form-control" name="country" id="trustmate-country">
                                                    {foreach from=$countries item=country}
                                                        <option value="{$country.iso_code}"{if ($country.iso_code == $account_data.country)} selected="selected"{/if}>{$country.name|escape:'html':'UTF-8'}</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="trustmate-nip">{l s="Tax identification number" mod='trustmate'}</label>
                                                <input
                                                    type="text"
                                                    name="nip"
                                                    class="form-control"
                                                    id="trustmate-nip"
                                                    aria-describedby="tax-identification-number"
                                                    value="{$account_data.nip}"
                                                >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="agreement_1">
                                                <input type="checkbox" class="agreement" id="agreement_1">
                                                {l s='I agree to electronic and telephone communication (thanks to this consent we can contact you and provide you with necessary advice) *' mod='trustmate'}
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label for="agreement_2">
                                                <input type="checkbox" class="agreement" id="agreement_2">
                                                {l s='I agree to receive commercial information by electronic means about TrustMate products (you will receive e-mails regarding your TrustMate account) *' mod='trustmate'}
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label for="agreement_3">
                                                <input type="checkbox" class="agreement" id="agreement_3">
                                                {l s='I accept the regulations, privacy policy and ' mod='trustmate'}<a href="{if $current_iso_lang == 'pl'}https://cdn.trustmate.io/TrustMate_Personal_Data_Processing_Contract_PL.pdf{else}https://cdn.trustmate.io/TrustMate_Personal_Data_Processing_Contract_EN.pdf{/if}">{l s='personal data entrustment agreement' mod='trustmate'}</a>*
                                            </label>
                                        </div>
                                        <div class="form-group text-center">
                                            <button name="have_account" type="submit" id="have_account" class="btn btn-default">{l s='I ALREADY HAVE AN ACCOUNT AT TRUSTMATE' mod='trustmate'}</button>
                                            <button name="create_account" type="submit" id="create_account" class="btn btn-success">{l s='CREATE AN ACCOUNT' mod='trustmate'}</button>
                                        </div>
                                    </form>
                                    <p class="text-muted text-center">{l s='By clicking the button Create an account with TrustMate, you confirm that you have read the information on the processing of personal data and accept the terms of the ' mod='trustmate'}<a href="{if $current_iso_lang == 'pl'}https://trustmate.io{else}https://en.trustmate.io{/if}/regulations">{l s='regulations' mod='trustmate'}</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
                {if $have_account}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel">
                                <div class="panel-body">
                                    <h2 style="margin-top: 0;">{l s='Thank you for choosing TrustMate! Gather reviews for free or choose one of our paid packages.' mod='trustmate'}</h2>
                                    <p>{l s='Collect appropriate reviews and join “The clients love us!” programme.' mod='trustmate'}</p>
                                    <p>{l s='In various tabs of the plugin, you may enable invitation sending and the widgets which will display reviews. You may also create a subpage with reviews which can help with the online positioning of your store.' mod='trustmate'}</p>
                                    <p>{l s='You can access reviews, mediations, email configurations and other services of TrustMate in your store’s panel → ' mod='trustmate'} <a href="{if $current_iso_lang == 'pl'}https://trustmate.io{else}https://en.trustmate.io{/if}/panel">{l s='TrustMate’s Panel' mod='trustmate'}</a></p>
                                </div>
                                {if isset($form_account)}
                                    {$form_account}
                                {/if}
                            </div>
                            <form method="post" action="">
                                <button name="reset_account" type="submit" style="background: transparent; border: none;" title="Reset module configuration">&nbsp;</button>
                            </form>
                            <form method="post" action="">
                                <button name="sandbox_mode" type="submit" style="background: transparent; border: none;" title="Enable sandbox mode">&nbsp;</button>
                            </form>
                        </div>
                    </div>
                {/if}
            </div>
            {if $have_account}
                <div class="tab-pane {if $current_active == 'invitations'} active {/if}" id="invites">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel">
                                <div class="panel-body">
                                    <h2>{l s='Invitations to writing a review' mod='trustmate'}</h2>
                                    <p>{l s='In this tab, you may enable email invitation sending to your clients. The moment of invitation creating can be configured below, the invitations are created right after the purchase by default.' mod='trustmate'}</p>
                                    <p>{l s='After enabling automated sending, TrustMate will send review invitations asking the clients about a firm’s mark and product’s mark.' mod='trustmate'}</p>
                                    <p>{l s='TrustMate’s panel enables you to configure among others:' mod='trustmate'}</p>
                                    <ul>
                                        <li>{l s='the amount of days after an invitation is sent (by default it’s 7 days, however you may change this value for example to 2 days, right after a package is delivered),' mod='trustmate'}</li>
                                        <li>{l s='how many times should a client be reminded about writing a review (we suggest to remind twice),' mod='trustmate'}</li>
                                        <li>{l s='invitation email colorway,' mod='trustmate'}</li>
                                        <li>{l s='design of the stars in the invitations.' mod='trustmate'}</li>
                                    </ul>

                                    <h2>{l s='Gathering product reviews' mod='trustmate'}</h2>
                                    <p>{l s='You just need to enable the sending to begin collecting product reviews.' mod='trustmate'}</p>
                                    <p>
                                        {l s='The invitations can be created based on one of the available order statuses. No matter what the status is, remember to set an appropriate email sending delay in TrustMate panel' mod='trustmate'}.
                                        {l s='Try to set the invitation sending to a day in which a client received a package or a day after that. The better fit of the sending date, the more reviews can be gathered' mod='trustmate'}.
                                    </p>

                                    {if isset($form_invitations)}
                                        {$form_invitations}
                                    {/if}

                                    {if isset($compatibility_warning)}
                                        <div class="alert alert-warning">{$compatibility_warning}</div>
                                    {/if}
                                    <h3 style="margin: 20px 0 10px -15px">{l s='Approval of reviews gathering' mod='trustmate'}</h3>
                                    {l s='Remember to give us the data according to law. If you don’t ask your clients for the approval directly, we suggest to put the attachment’s content to the store’s ' mod='trustmate'} <a href="{if $current_iso_lang == 'pl'}https://cdn.trustmate.io/platforms/regulamin-sklep-zgoda-2023.pdf{else}https://cdn.trustmate.io/platforms/store_agreement_consent_2023.pdf{/if}">{l s='terms and conditions' mod='trustmate'}</a>.

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane {if $current_active == 'widgets'} active {/if}" id="widgets">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel">
                                <div class="form-wrapper">
                                    <div class="alert alert-warning">{l s='In the case of a store based on the PRESTASHOP 1.6. * System, it is required to place a special hook in the place where the widget is embedded. The hook should be added in the appropriate template file:' mod='trustmate'}<br><code>&#123;hook h='displayCustomWidgetPosition' mod='trustmate'&#125;</code></div>
                                    {if isset($form_widgets)}
                                        {$form_widgets}
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    </div>
</div>


<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#create_account').on('click', function(e) {
            var agreement_1 = $('#agreement_1');
            var agreement_2 = $('#agreement_2');
            var agreement_3 = $('#agreement_3');

            var stopped = false;
            if( !agreement_1.prop('checked') ) {
                agreement_1.css({ outline: "1px solid red" })
                stopped = true;
            }

            if( !agreement_2.prop('checked') ) {
                agreement_2.css({ outline: "1px solid red" })
                stopped = true;
            }

            if( !agreement_3.prop('checked') ) {
                agreement_3.css({ outline: "1px solid red" })
                stopped = true;
            }

            if(stopped) {
                $('#account').prepend(`
                    <div class="bootstrap">
                        <div class="module_confirmation conf confirm alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {l s='You must confirm all agreements to create an account.' mod='trustmate'}
                        </div>
                    </div>
                `);

                e.stopPropagation();
                e.preventDefault();
            }
        });

        $('.agreement').each(function() {
            $(this).change(function() {
                $(this).css({ outline: "none" });
            });
        });
    });
</script>