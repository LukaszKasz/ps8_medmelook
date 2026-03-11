{*
 * ColorFeatures
 *
 * @author    silbersaiten <info@silbersaiten.de>
 * @copyright 2019 silbersaiten
 * @license   See joined file licence.txt
 * @category  Module
 * @support   silbersaiten <support@silbersaiten.de>
 * @version   1.0.4
 * @link      http://www.silbersaiten.de
 *}

<div class="panel">
    <div class="panel-heading">{l s='Colorfeatures configuration' mod='colorfeatures'}</div>
    <div class="panel-body">
        <div class="form-group">
            {if $over_back}
                <label class="control-label col-lg-3">
                    <strong class="text-danger">
                        {l s='The ps_facetedsearch module file already replaced. Do you want to replace them back?' mod='colorfeatures'}
                    </strong>
                </label>
            {else}
                <label class="control-label col-lg-3">
                    {l s='Override the ps_facetedsearch module file?' mod='colorfeatures'}
                </label>
            {/if}
            <div class="col-lg-9">
                <a href="{$link_over}" class="btn {if $over_back}haveover btn-warning{else}btn-default{/if}">
                    <i class="icon-refresh"></i>
                    {if $over_back}
                        {l s='Remove the override' mod='colorfeatures'}
                    {else}
                        {l s='Override' mod='colorfeatures'}
                    {/if}
                </a>
                <p class="help-block">
                    <strong>
                        {l s='This is necessary if the ps_facetedsearch module version is higher than 3.0' mod='colorfeatures'}
                    </strong>
                </p>
            </div>
        </div>
    </div>
</div>
