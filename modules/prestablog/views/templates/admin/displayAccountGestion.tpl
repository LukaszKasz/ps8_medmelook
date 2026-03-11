{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
{$prestablog->get_displayFormOpen('icon-edit', {l s='Edit your profile' d='Modules.Prestablog.Prestablog'}, $confpath)}
    {if $img_author['is_true'] == true}
        <img class='item' src='{$img_author['src_value']|escape:'html':'UTF-8'}' style='margin-left: 16%; margin-bottom:10px;' />
    {else}
        <img class='item' src='{$img_author['src_value']|escape:'html':'UTF-8'}' style='margin-left: 16%; margin-bottom:10px;' />
    {/if}


<div class='form-group'>
    <label class='control-label col-lg-2'></label>
    <div class='col-lg-7'>
        <div id='image'>
        <input type='file' name='load_img' id='load_img' value='' />
        </div>
    </div>
</div>


<div class='form-group'>
    <label class='control-label col-lg-2'>{l s='Your pseudo' d='Modules.Prestablog.Prestablog'} </label>
    <div class='col-lg-7'>
        <div id='pseudo'>
            <input type='text' name='pseudo' id='pseudo_author' {if isset($pseudo)} value="{$pseudo|escape:'htmlall':'UTF-8'}" {/if} />
        </div>
    </div>

</div>


<div class='form-group'>
    <label class='control-label col-lg-2'>{l s='Biography' d='Modules.Prestablog.Prestablog'} </label>
    <div class='col-lg-7'>
        <div id='bio'>
            <textarea class='autoload_rte' id='biography' name='biography'>{if isset($biography)}
                   {$biography|escape:'html':'UTF-8'}
        {/if}</textarea>
        </div>
    </div>

</div>

<div class='form-group'>
    <label class='control-label col-lg-2'>{l s='Email' d='Modules.Prestablog.Prestablog'} </label>
    <div class='col-lg-7'>
        <div id='mail'>
            <input type='text' name='email' id='email' {if isset($email)} value="{$email|escape:'htmlall':'UTF-8'}" {/if} />
        </div>
    </div>

</div>


<div class='form-group'>
    <label class='control-label col-lg-2'>{l s='Meta Title' d='Modules.Prestablog.Prestablog'} </label>
    <div class='col-lg-7'>
        <div id='metaTitle'>
            <input type='text' name='meta_title' id='meta_title' maxlength='60' {if isset($meta_title)}
                value="{$meta_title|escape:'htmlall':'UTF-8'}" {/if} />
        </div>
    </div>
</div>

<div class='form-group'>
    <label class='control-label col-lg-2'>{l s='Meta Description' d='Modules.Prestablog.Prestablog'} </label>
    <div class='col-lg-7'>
        <div id='metaDescription'>
            <input type='text' name='meta_description' id='meta_description' maxlength='160'
                {if isset($Meta_Description)} value="{$Meta_Description|escape:'htmlall':'UTF-8'}" {/if} />
        </div>
    </div>
</div>

<div id='display_author' style='display: none;'>
    <input type='text' name='author_id' id='author_id' value='{$id|escape:'htmlall':'UTF-8'}' />
</div>
<button class='btn btn-primary' name='submitEditAuthor' type='submit'>
    <i class='icon-plus'></i>&nbsp;{l s='Edit your profile' d='Modules.Prestablog.Prestablog'}
</button>

{$prestablog->get_displayFormClose()}
