{*
 * Facebook Conversion Pixel Tracking Plus
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2014
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category Marketing & Advertising
 * Registered Trademark & Property of smart-modules.com
 *
 * ***************************************************
 * *     Facebook Conversion Trackcing Pixel Plus    *
 * *          http://www.smart-modules.com           *
 * ***************************************************
 *
*}

{if isset($oldps) && $oldps == 1}
<fieldset id="configuration_fieldset">
    <legend>{l s='CAPI Logs' mod='facebookconversiontrackingplus'}</legend>
    {else}
    <div id="pixel_plus_logs" class="panel">
        <div class="panel-heading"><i class="icon-list"> </i> {l s='CAPI Logs' mod='facebookconversiontrackingplus'}</div>
        {/if}
        <div class="col-lg-12">
            <div class="alert alert-info">
                <h3 class="modal-title text-info" style="margin-top: 0">{l s='Last events captured in the logs' mod='facebookconversiontrackingplus'} - (CAPI)</h3>
                <p>{l s='Here you will see the data stored in the logs in a readable format' mod='facebookconversiontrackingplus'}.</p>
                <p>{l s='There are two types of logs, the regular events and the payload' mod='facebookconversiontrackingplus'}.</p>
                <br>
                <h4>{l s='Regular Events' mod='facebookconversiontrackingplus'}</h4>
                <p>{l s='In the events logs you will see several information as the event name, the pixel, event and external IDs. The url that sent the event and even the facebook trace Id' mod='facebookconversiontrackingplus'}.</p>
                <p>{l s='The message field, will only contain text if there is any error' mod='facebookconversiontrackingplus'}.</p>
                <br>
                <h4>{l s='Events Payload' mod='facebookconversiontrackingplus'}</h4>
                <p>{l s='The Payload is the recollection of data sent to Facebook, this can be useful for debugging' mod='facebookconversiontrackingplus'}.</p>
            </div>
            <h3 class="modal-title text-info" style="margin-top: 0">{l s='Displaying the last %d captured events' mod='facebookconversiontrackingplus' sprintf=[$pp_logs|count]}</h3>
            <hr>
            {foreach from=$pp_logs item=log}
                <br>
                <div class="col-lg-12">
                    <h4 class="text-info">
                        {l s='Log Type' mod='facebookconversiontrackingplus'}: {if $log.is_json}Payload{else}Event{/if}
                    </h4>
                    <h4 class="text-info">
                        {l s='Log time' mod='facebookconversiontrackingplus'}: {$log.date_add|escape:'htmlall':'UTF-8'}
                    </h4>
                    <br>
                    {if $log.is_json}
                    <pre>
                        {foreach from=$log.message item=message}
                            {if !empty($message)}{$message|escape:'htmlall':'UTF-8'}<br>{/if}
                        {/foreach}
                    </pre>
                    {else}
                        {foreach from=$log.message item=data}
                            {if !empty($data)}{$data|escape:'htmlall':'UTF-8'}<br>{/if}
                        {/foreach}
                    {/if}
                    <hr>
                </div>
            {/foreach}
            {capture assign=logs_link}<a href="{$logs_link|escape:'htmlall':'UTF-8'}" target="_blank">{/capture}
            <h4>{l s='To see all the events registered in the logs [1]click here[/1]' mod='facebookconversiontrackingplus' tags=[$logs_link]}</h4>
        </div>

        <!-- Add the Delete Logs button -->
        <div class="col-lg-12">
            <button id="delete-pixel-plus-logs" class="btn btn-danger">
                {l s='Delete Pixel Plus Logs' mod='facebookconversiontrackingplus'}
            </button>
        </div>

        <script type="text/javascript">
            document.getElementById('delete-pixel-plus-logs').addEventListener('click', function (e) {
                e.preventDefault();
                if (confirm('{l s='Are you sure you want to delete all Pixel Plus logs?' mod='facebookconversiontrackingplus'}')) {
                    // Reload the page and append the URL parameter to trigger the delete function
                    window.location.href += '&deletePixelPlusLogs=1';
                }
            });
        </script>



        <div class="clearfix"></div>
{if isset($oldps) && $oldps == 1}
</fieldset>
{else}
    </div>
{/if}