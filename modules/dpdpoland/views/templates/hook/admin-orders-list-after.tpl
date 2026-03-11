<div class="panel col-lg-12">
    <div class="panel-heading">
        <i class="icon-truck"></i>
        {$moduleDisplayName|escape:'html':'UTF-8'}
    </div>
    <div class="panel-body">
        <div class="btn-group dropup">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                {l s='For the selected orders' mod='dpdpoland'}
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                {foreach $bulkActions as $bulkAction}
                    <li>
                        <a href="#" class="{$bulkAction.class|escape:'html':'UTF-8'}" data-action="{$bulkAction.action|escape:'html':'UTF-8'}">
                            <i class="icon-{$bulkAction.icon|escape:'html':'UTF-8'}"></i>
                            {$bulkAction.label|escape:'html':'UTF-8'}
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>
</div>
