{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<div id="configuration_blog">
	<nav>
        <ul>
        <li>
        <a href= "{$homepath|escape:'html':'UTF-8'}">
        <i class="material-icons mi-home">home</i>
        {l s='Home' d='Modules.Prestablog.Prestablog'}
        </a>
        </li>
        <li>
        <a href="#">
        <i class="material-icons mi-collections_bookmark">collections_bookmark</i>
        {l s='Manage content' d='Modules.Prestablog.Prestablog'}
        </a>
        <ul>
            <li>
            <a href="{$newspath|escape:'html':'UTF-8'}">
            <i class="material-icons mi-art_track">art_track</i>
            {l s='News' d='Modules.Prestablog.Prestablog'}
            </a>
            </li>
            {if $can_manage_comments}            
            <li>
            <a href="{$commentspath|escape:'html':'UTF-8'}">
            <i class="material-icons mi-forum">forum</i>
            {l s='Comments' d='Modules.Prestablog.Prestablog'}
            </a>
            </li>
            {/if}
            
            <li>
            <a href="{$categoriespath|escape:'html':'UTF-8'}">
            <i class="material-icons mi-dns">dns</i>
            {l s='Categories' d='Modules.Prestablog.Prestablog'}
            </a>
            </li>
            
            {if $can_manage_personalised_list}
            <li>
            <a href="{$customizenewspath|escape:'html':'UTF-8'}">
            <i class="material-icons mi-description">description</i>
            {l s='Customize news list' d='Modules.Prestablog.Prestablog'}
            </a>
            </li>
            {/if}

            {if not $prestaboost}
            {if $can_manage_popup}
            <li>
            <a href="{$popuppath|escape:'html':'UTF-8'}">
            <i class="material-icons mi-filter_none">filter_none</i>
            {l s='Popup' d='Modules.Prestablog.Prestablog'}
            </a>
            </li>
            {/if}
            {/if}

            {if $can_manage_slide}
            {if $languagecount == 1}
            <li>
            <a href="{$multilangslidepath|escape:'html':'UTF-8'}">
            <i class="material-icons mi-add_to_queue">add_to_queue</i>
            {l s='Slide' d='Modules.Prestablog.Prestablog'}
            </a>
            </li>
            {else}
            <li>
            <a href="{$slidepath|escape:'html':'UTF-8'}">
            <i class="material-icons mi-add_to_queue">add_to_queue</i>
            {l s='Slide' d='Modules.Prestablog.Prestablog'}
            </a>
            </li>
            {/if}
            {/if}
        </ul>
        </li>

        {if $can_use_tool}
        <li>
        <a href="#">
        <i class="material-icons mi-build">build</i>
        {l s='Tools' d='Modules.Prestablog.Prestablog'}
        </a>
            <ul>
                <li>
                    <a href="{$configaipath|escape:'html':'UTF-8'}">
                    <i class="material-icons mi-palette">memory</i>
                    {l s='Artificial intelligence' d='Modules.Prestablog.Ai'}
                    </a>
                </li>
                <li>
                    <a href="{$antispampath|escape:'html':'UTF-8'}">
                    <i class="material-icons mi-beenhere">beenhere</i>
                    {l s='Anti-spam' d='Modules.Prestablog.Prestablog'}
                    </a>
                </li>
                <li>
                    <a href="{$importpath|escape:'html':'UTF-8'}">
                    <i class="material-icons mi-arrow_downward">arrow_downward</i>
                    {l s='Import WordPress XML' d='Modules.Prestablog.Prestablog'}
                    </a>
                </li>
                <li>
                    <a href="{$sitemappath|escape:'html':'UTF-8'}">
                    <i class="material-icons mi-transform">transform</i>
                    {l s='Sitemap' d='Modules.Prestablog.Prestablog'}
                    </a>
                </li>                
            </ul>
        </li>
        {/if}

        {if $can_configure_module}
        <li>
        <a href="#">
        <i class="material-icons mi-settings">settings</i>
        {l s='Configuration' d='Modules.Prestablog.Prestablog'}
        </a>
            <ul>
                <li>
                    <a href="{$configthemepath|escape:'html':'UTF-8'}">
                    <i class="material-icons mi-aspect_ratio">aspect_ratio</i>
                    {l s='Theme' d='Modules.Prestablog.Prestablog'}
                    </a>
                </li>
                <li>
                    <a href="{$configblogpath|escape:'html':'UTF-8'}">
                    <i class="material-icons mi-dashboard">dashboard</i>
                    {l s='Blog page' d='Modules.Prestablog.Prestablog'}
                    </a>
                </li>
                <li>
                    <a href="{$configmodulepath|escape:'html':'UTF-8'}">
                    <i class="material-icons mi-settings_applications">settings_applications</i>
                    {l s='Global' d='Modules.Prestablog.Prestablog'}
                    </a>
                </li>
                <li>
                    <a href="{$configblocspath|escape:'html':'UTF-8'}">
                    <i class="material-icons mi-picture_in_picture">picture_in_picture</i>
                    {l s='Blocks' d='Modules.Prestablog.Prestablog'}
                    </a>
                </li>
                <li>
                    <a href="{$configcategoriespath|escape:'html':'UTF-8'}">
                    <i class="material-icons mi-list">list</i>
                    {l s='Categories' d='Modules.Prestablog.Prestablog'}
                    </a>
                </li>
                <li>
                    <a href="{$configcommentspath|escape:'html':'UTF-8'}">
                    <i class="material-icons mi-forum">forum</i>
                    {l s='Comments' d='Modules.Prestablog.Prestablog'}
                    </a>
                </li>
                <li>
                    <a href="{$configcolorpath|escape:'html':'UTF-8'}">
                    <i class="material-icons mi-palette">palette</i>
                    {l s='Design' d='Modules.Prestablog.Prestablog'}
                    </a>
                </li>
            </ul>
        </li>
        {/if}

        <li>
        <a href="#">
        <i class="material-icons mi-person_outline">person_outline</i>
        {l s='Author' d='Modules.Prestablog.Prestablog'}
        </a>
        <ul>
        <li>
        <a href="{$authorpath|escape:'html':'UTF-8'}">
        <i class="material-icons mi-person_add">person_add</i>
        {l s='Author List' d='Modules.Prestablog.Prestablog'}
        </a>
        </li>
        {if $hasaccount}
        <li>
        <a href="{$myprofilepath|escape:'html':'UTF-8'}">
        <i class="material-icons mi-person">person</i>
        {l s='My profile' d='Modules.Prestablog.Prestablog'}
        </a>
        </li>
        {/if}
        {if $can_configure_module}
           <li>
            <a href="{$configauthorpath|escape:'html':'UTF-8'}">
            <i class="material-icons mi-settings_applications">settings_applications</i>
            {l s='Configuration' d='Modules.Prestablog.Prestablog'}
            </a>
            </li>
        {/if}
        </ul>
        </li>
        <li>
        <a href="#">
        <i class="material-icons mi-live_help">live_help</i>&nbsp;
        {l s='Help' d='Modules.Prestablog.Prestablog'}
        </a>
        <ul>
        <li>
        <a href="{$documentationpath|escape:'html':'UTF-8'}">
        <i class="material-icons mi-library_books">library_books</i>
        {l s='Documentation' d='Modules.Prestablog.Prestablog'}
        </a>
        </li>
        <li>
        <a href="{$contactpath|escape:'html':'UTF-8'}">
        <i class="material-icons mi-info">info</i>
        {l s='Contact' d='Modules.Prestablog.Prestablog'}
        </a>
        </li>
        </ul>
        </li>

        <li id="nav-version">
        {l s='Version' d='Modules.Prestablog.Prestablog'} : {$version|escape:'html':'UTF-8'}{if $isdemomode}/ {l s='Demo mode' d='Modules.Prestablog.Prestablog'}{/if}
        </li>
        <li class="nav-extra-link">
        <a href="{$blogpageurl|escape:'html':'UTF-8'}" target="_blank">
        <i class="material-icons mi-search">search</i>
        {l s='View blog page' d='Modules.Prestablog.Prestablog'}
        </a>
        </li>
        </ul>
	</nav>
<div id="contenu_config_prestablog">