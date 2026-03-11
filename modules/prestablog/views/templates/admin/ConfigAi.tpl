{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<div class='col-md-5'>
{if !$demo_mode}
    {$prestablog->get_displayFormOpen('ai.png', "{l s='Artificial intelligence' d='Modules.Prestablog.Ai'}", $confpath)}
    <div class="form-group">
        <label class="control-label col-lg-3" for="prestablog_chatgpt_api_key">
            <i class="material-icons">vpn_key</i> {l s='ChatGPT API key' d='Modules.Prestablog.Ai'}
        </label>
        <div class="col-lg-6">
            <input type="password" name="prestablog_chatgpt_api_key" id="prestablog_chatgpt_api_key" placeholder="{l s='Enter your API key' d='Modules.Prestablog.Ai'}">
            {if $chatgpt_api_key}
                <small>{l s='Leave this field empty to keep the current API key.' d='Modules.Prestablog.Ai'}</small>
            {/if}       
        </div>    
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3" for="prestablog_chatgpt_model">
            <i class="material-icons">smart_toy</i> {l s='ChatGPT Model' d='Modules.Prestablog.Ai'}
        </label>
        <div class="col-lg-6">
            <select name="prestablog_chatgpt_model" id="prestablog_chatgpt_model">
                {foreach from=$chatgpt_models item=model}
                    <option value="{$model}" {if $chatgpt_model == $model}selected{/if}>{$model}</option>
                {/foreach}
            </select>
        </div>
    </div>

    {$prestablog->get_displayFormSubmit('submitAiConfig', 'icon-save', "{l s='Update' d='Modules.Prestablog.Ai'}")}
    {$prestablog->get_displayFormClose()}
    
{/if}
{if $demo_mode}{$prestablog->displayWarning({l s='Feature disabled on the demo mode' d='Modules.Prestablog.Prestablog'})}{/if}
    
    {if $api_verification.success}
        <div class="alert alert-success">{$api_verification.message}</div>
    {else}
        <div class="alert alert-danger">{$api_verification.message}</div>
    {/if}
</div>
<div class='col-md-7'>
    <div class="blocmodule">
        <legend>{l s='Your feedback matters!' d='Modules.Prestablog.Ai'}</legend>
        <p>{l s='Try out the idea generation and article writing assistance with ChatGPT, and let us know your thoughts. Your input is valuable to us.' d='Modules.Prestablog.Ai'}</p>
    </div>
    <div class="blocmodule">
        <legend>{l s='How to configure' d='Modules.Prestablog.Ai'}</legend>
        <p>{l s='To get your API key, follow these steps:' d='Modules.Prestablog.Ai'}</p>
        <ol>
            <li><a href="https://platform.openai.com/api-keys" target="_blank">{l s='Click here' d='Modules.Prestablog.Ai'}</a> {l s='and create or log in to your OpenAI account' d='Modules.Prestablog.Ai'} </li>
            <li>{l s='Generate a new API key or copy your existing API key.' d='Modules.Prestablog.Ai'}</li>
            <li>{l s='Save your API key securely.' d='Modules.Prestablog.Ai'}</li>
            <li>{l s='Choose the type of GPT you want to use. We suggest you start with the GPT-4 option.' d='Modules.Prestablog.Ai'}</li>
        </ol>
        <p>{l s='For more detailed instructions, visit the' d='Modules.Prestablog.Ai'} <a href="https://openai.com" target="_blank">{l s='OpenAI documentation' d='Modules.Prestablog.Ai'}</a>.</p>
    </div>
    <div class="blocmodule">
        <legend>{l s='How to Use the ChatGPT Integration on PrestaBlog' d='Modules.Prestablog.Ai'}</legend>
        <ol>
            <li>{l s='Navigate to the article of your choice or create a new one in PrestaBlog.' d='Modules.Prestablog.Ai'}</li>
            <li>{l s='Click on, ' d='Modules.Prestablog.Ai'} 
                {l s='Create your article with the help of AI' d='Modules.Prestablog.Ai'}
            </li>
            <li>{l s='Explore the 3 available fields:' d='Modules.Prestablog.Ai'}
             </li>
        </ol>
        <ul>
            <li>
                <strong>{l s='AI conversation interface' d='Modules.Prestablog.Ai'}</strong> 
                <p>{l s='This area displays the ongoing conversation with ChatGPT and indicates available actions.' d='Modules.Prestablog.Ai'}</p>
            </li>
            <li>
                <strong>{l s='Select the type of discussion' d='Modules.Prestablog.Ai'}</strong>
                <p>{l s='Choose the purpose of your interaction with the AI, such as idea generation, writing article or summary, etc.' d='Modules.Prestablog.Ai'}</p>
            </li>
            <li>
                <strong>{l s='Input Area:' d='Modules.Prestablog.Ai'}</strong>
                <p>{l s='Use this area to enter your messages or requests based on the selected type of discussion. If needed, you can also fine-tune your inputs with specific prompts. ChatGPT’s responses will appear in the conversation interface.' d='Modules.Prestablog.Ai'}</p>
            </li>
        </ul>
    </div>

</div>
