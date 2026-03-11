{*
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 *}
<div class="form-group">
    <label class="control-label col-lg-2"><img src="{$module_dir}views/img/ai.png" alt="{l s='Create your article with the help of AI' d='Modules.Prestablog.Ai'}" /></label>
    <div class="col-lg-10" style="line-height: 35px;">
        <span onclick="$('#gptGroup').slideToggle(); $('#seo').slideDown();" style="cursor: pointer; display: flex;" class="link">
        <i class="material-icons" style="line-height: 35px;">settings</i>
        {l s='Create your article with the help of AI' d='Modules.Prestablog.Ai'}
        </span>
    </div>
</div>
<div id="gptGroup" style="10px; display:none;">
    <div class="form-group">
        <label class="control-label col-lg-2">{l s='AI conversation interface' d='Modules.Prestablog.Ai'}</label>
        <div id="gptMessages"></div>
    </div>
    <div id="actionButtons" class="form-group">
        <div class="row">
            <div class="col-lg-3">
                <select id="actionSelect" class="custom-select form-control action-select">
                    <option value="insertTitle">{l s='Insert as Title' d='Modules.Prestablog.Ai'}</option>
                    <option value="insertSummary">{l s='Insert as Summary' d='Modules.Prestablog.Ai'}</option>
                    <option value="insertContent">{l s='Insert as Content' d='Modules.Prestablog.Ai'}</option>
                    <option value="insertMetaTitle">{l s='Insert as Meta Title' d='Modules.Prestablog.Ai'}</option>
                    <option value="insertMetaDescription">{l s='Insert as Meta Description' d='Modules.Prestablog.Ai'}</option>
                </select>
            </div>
            <div class="col-lg-2">
               <button id="performAction" type="button" class="btn btn-primary perform-action">{l s='Perform Action' d='Modules.Prestablog.Ai'}</button>
            </div>
            <div class="col-lg-3">
                <select id="languageSelect" class="custom-select form-control language-select">
                    {foreach from=$languages item=language}
                        <option value="{$language.iso_code|escape:'html':'UTF-8'}">{$language.name|escape:'html':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
            <div class="col-lg-1">
                <button id="translateText" type="button" class="btn btn-primary translate-text">{l s='Translate' d='Modules.Prestablog.Ai'}</button>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-2">{l s='Select the type of discussion' d='Modules.Prestablog.Ai'}</label>
        <div class="col-lg-3">
            <select id="discussionType" class="custom-select form-control">
                <option value="free_discussion">{l s='Free Discussion' d='Modules.Prestablog.Ai'}</option>
                <option value="find_topic">1 - {l s='Find Topic ideas' d='Modules.Prestablog.Ai'}</option>
                <option value="seo_variation">2 - {l s='Find SEO Variations ideas' d='Modules.Prestablog.Ai'}</option>
                <option value="write_article">3 - {l s='Write Article' d='Modules.Prestablog.Ai'}</option>
                <option value="create_summary">4 - {l s='Create Summary' d='Modules.Prestablog.Ai'}</option>
                <option value="create_title">5 - {l s='Create Title' d='Modules.Prestablog.Ai'}</option>
                <option value="create_meta_title">6 - {l s='Create Meta Title' d='Modules.Prestablog.Ai'}</option>
                <option value="create_meta_description">7 - {l s='Create Meta Description' d='Modules.Prestablog.Ai'}</option>
            </select>
        </div>
        <div class="col-lg-3">
            <select id="writingStyle" class="custom-select form-control" style="display:none;">
                <option value="">{l s='Choose a writing style for your article...' d='Modules.Prestablog.Ai'}</option>
                <option value="casual_friendly">{l s='Casual and Friendly' d='Modules.Prestablog.Ai'}</option>
                <option value="emotional_personal">{l s='Emotional and Personal' d='Modules.Prestablog.Ai'}</option>
                <option value="professional_formal">{l s='Professional and Formal' d='Modules.Prestablog.Ai'}</option>
                <option value="sarcastic_humorous">{l s='Sarcastic or Humorous' d='Modules.Prestablog.Ai'}</option>
                <option value="inspiring_motivating">{l s='Inspiring and Motivating' d='Modules.Prestablog.Ai'}</option>
                <option value="confident_assertive">{l s='Confident and Assertive' d='Modules.Prestablog.Ai'}</option>
                <option value="thoughtful_reflective">{l s='Thoughtful and Reflective' d='Modules.Prestablog.Ai'}</option>
                <option value="trustworthy_serious">{l s='Trustworthy and Serious' d='Modules.Prestablog.Ai'}</option>
                <option value="innovative_ahead">{l s='Innovative and Forward-thinking' d='Modules.Prestablog.Ai'}</option>
                <option value="inclusive_diverse">{l s='Inclusive and Diverse' d='Modules.Prestablog.Ai'}</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-2">{l s='Input Area:' d='Modules.Prestablog.Ai'}</label>
        <div class="col-lg-6">
            <div id="gptError" class="text-danger" style="display:none; margin-bottom: 10px;"></div>

            <input type="text" id="themeInput" class="form-control" placeholder="{l s='Enter your theme...' d='Modules.Prestablog.Ai'}" style="margin-bottom: 10px; display:none;" />

            <textarea id="gptInput" rows="3" class="form-control" placeholder="{l s='Type your message here...' d='Modules.Prestablog.Ai'}" style="margin-bottom: 10px;"></textarea>

            <button id="sendGptMessage" class="btn btn-primary" style="margin-top: 10px;">{l s='Send' d='Modules.Prestablog.Ai'}</button>
        </div>
    </div>
</div>
{literal}
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function () {
    var discussionTypeSelect = document.getElementById('discussionType');
    var themeInput = document.getElementById('themeInput');
    var gptInput = document.getElementById('gptInput');
    var gptError = document.getElementById('gptError');
    var writingStyle = document.getElementById('writingStyle');

    discussionTypeSelect.addEventListener('change', function () {
        gptError.style.display = 'none';

        if (this.value === 'find_topic' || this.value === 'seo_variation' || this.value === 'write_article' || this.value === 'create_summary' || this.value === 'create_title' || this.value === 'create_meta_title' || this.value === 'create_meta_description') {
            themeInput.style.display = 'block';
            gptInput.style.display = 'none';
            writingStyle.style.display = this.value === 'write_article' ? 'block' : 'none';

            if (this.value === 'seo_variation') {
                themeInput.placeholder = '{/literal}{l s="Enter your subject..." d="Modules.Prestablog.Ai"}{literal}';
            } else if (this.value === 'write_article') {
                themeInput.placeholder = '{/literal}{l s="Enter your article topic..." d="Modules.Prestablog.Ai"}{literal}';
            } else if (this.value === 'create_summary') {
                themeInput.placeholder = '{/literal}{l s="Enter the theme to summarize..." d="Modules.Prestablog.Ai"}{literal}';
            } else if (this.value === 'create_title') {
                themeInput.placeholder = '{/literal}{l s="Enter the topic for the title..." d="Modules.Prestablog.Ai"}{literal}';
            } else if (this.value === 'create_meta_title') {
                themeInput.placeholder = '{/literal}{l s="Enter the topic for the meta title..." d="Modules.Prestablog.Ai"}{literal}';
            } else if (this.value === 'create_meta_description') {
                themeInput.placeholder = '{/literal}{l s="Enter the topic for the meta description..." d="Modules.Prestablog.Ai"}{literal}';
            } else {
                themeInput.placeholder = '{/literal}{l s="Enter your theme..." d="Modules.Prestablog.Ai"}{literal}';
            }
        } else {
            themeInput.style.display = 'none';
            gptInput.style.display = 'block';
            writingStyle.style.display = 'none';
        }
    });
});

var lastGptMessageContent = '';

document.getElementById('sendGptMessage').addEventListener('click', function(event) {
    event.preventDefault();

    var message = document.getElementById('gptInput').value.trim();
    var theme = document.getElementById('themeInput').value.trim();
    var prompt = document.getElementById('discussionType').value;
    var gptError = document.getElementById('gptError');
    var messagesDiv = document.getElementById('gptMessages');
    var style = document.getElementById('writingStyle').value;

    var originalMessage = message;

    if (prompt === 'free_discussion' && lastGptMessageContent) {
        message = lastGptMessageContent + "\n\n" + message;
    }

    var promptsRequiringTheme = [
        'find_topic', 
        'seo_variation', 
        'write_article', 
        'create_summary', 
        'create_title', 
        'create_meta_title', 
        'create_meta_description'
    ];

    if ((prompt === 'free_discussion' && originalMessage.length === 0) || 
        (promptsRequiringTheme.includes(prompt) && theme.length === 0)) {
        gptError.innerHTML = '{/literal}{l s="This field cannot be empty" d="Modules.Prestablog.Ai"}{literal}';
        gptError.style.display = 'block';
        return;
    }

    document.getElementById('gptInput').value = '';
    document.getElementById('themeInput').value = '';
    gptError.style.display = 'none';

    var loadingMessage = document.createElement('div');
    loadingMessage.className = 'loading-message';
    loadingMessage.innerHTML = '{/literal}{l s="Processing your request, this might take a little time..." d="Modules.Prestablog.Ai"}{literal}';
    messagesDiv.appendChild(loadingMessage);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;

    fetch('index.php?controller=AdminPrestaBlogChatGPT&ajax=1&action=chatWithGpt&token={/literal}{$admin_token}{literal}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            message: message,
            prompt: prompt,
            theme: theme,
            style: style
        })
    })
    .then(response => response.json())
    .then(data => {
        messagesDiv.removeChild(loadingMessage);
        if (data.success) {
            var userMessage = document.createElement('div');
            userMessage.className = 'user-message';
            userMessage.innerHTML = '{/literal}{l s="Your message" d="Modules.Prestablog.Ai"}{literal}: ' + originalMessage;
            messagesDiv.appendChild(userMessage);

            addGptMessage(data.response, messagesDiv);
            lastGptMessageContent = data.response;
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        } else {
            var errorMessage = document.createElement('div');
            errorMessage.className = 'error-message';
            errorMessage.innerHTML = '{/literal}{l s="Error" d="Modules.Prestablog.Ai"}{literal}: ' + data.message;
            messagesDiv.appendChild(errorMessage);
        }
    })
    .catch(error => {
        messagesDiv.removeChild(loadingMessage);
        var errorMessage = document.createElement('div');
        errorMessage.className = 'error-message';
        errorMessage.innerHTML = '{/literal}{l s="Error" d="Modules.Prestablog.Ai"}{literal}: ' + error.message;
        messagesDiv.appendChild(errorMessage);
    });
});
function addGptMessage(response, messagesDiv) {
    var gptMessageContainer = document.createElement('div');
    gptMessageContainer.className = 'gpt-message-container';

    var gptMessage = document.createElement('div');
    gptMessage.className = 'gpt-message';
    gptMessage.innerHTML = 'GPT: ' + response;
    gptMessage.setAttribute('data-response', response);
    gptMessageContainer.appendChild(gptMessage);

    var actionButton = document.createElement('button');
    actionButton.className = 'btn btn-secondary action-button';
    actionButton.innerHTML = '{/literal}{l s="Actions" d="Modules.Prestablog.Ai"}{literal}';
    actionButton.type = 'button';
    actionButton.onclick = function() {
        toggleActionOptions(actionOptionsClone);
    };
    gptMessageContainer.appendChild(actionButton);

    var actionOptionsClone = document.getElementById('actionButtons').cloneNode(true);
    actionOptionsClone.style.display = 'none'; 
    actionOptionsClone.querySelector('.perform-action').onclick = function() {
        var selectedAction = actionOptionsClone.querySelector('#actionSelect').value;
        performAction(selectedAction, response);
    };
    actionOptionsClone.querySelector('.translate-text').onclick = function() {
        var selectedLanguage = actionOptionsClone.querySelector('#languageSelect').value;
        performTranslation(selectedLanguage, response, gptMessageContainer);
    };

    gptMessageContainer.appendChild(actionOptionsClone);
    messagesDiv.appendChild(gptMessageContainer);
}

function toggleActionOptions(container) {
    container.style.display = container.style.display === 'none' ? 'block' : 'none';
}

function performAction(action, content) {
    var messagesDiv = document.getElementById('gptMessages');
    var successMessage = document.createElement('div');
    successMessage.className = 'success-message';
    successMessage.innerHTML = '{/literal}{l s="Content inserted successfully." d="Modules.Prestablog.Ai"}{literal}';

    switch(action) {
        case 'insertTitle':
            insertIntoVisibleTitleField(content);
            break;
        case 'insertSummary':
            insertIntoVisibleSummaryField(content);
            break;
        case 'insertContent':
            insertIntoVisibleContentField(content);
            break;
        case 'insertMetaTitle':
            insertIntoVisibleMetaTitleField(content);
            break;
        case 'insertMetaDescription':
            insertIntoVisibleMetaDescriptionField(content);
            break;
        default:
            var errorMessage = document.createElement('div');
            errorMessage.className = 'error-message';
            errorMessage.innerHTML = '{/literal}{l s="Error inserting content." d="Modules.Prestablog.Ai"}{literal}';
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
            return; 
    }
    
    messagesDiv.appendChild(successMessage);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}
function performTranslation(language, content, container) {
    var messagesDiv = document.getElementById('gptMessages');
    var loadingMessage = document.createElement('div');
    loadingMessage.className = 'loading-message';
    loadingMessage.innerHTML = '{/literal}{l s="Processing your request, this might take a little time..." d="Modules.Prestablog.Ai"}{literal}';
    messagesDiv.appendChild(loadingMessage);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;

    fetch('index.php?controller=AdminPrestaBlogChatGPT&ajax=1&action=translateMessage&token={/literal}{$admin_token}{literal}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({message: content, language: language})
    })
    .then(response => response.json())
    .then(data => {
        messagesDiv.removeChild(loadingMessage);
        if (data.success) {
            var translationLabel = document.createElement('div');
            translationLabel.className = 'user-message';
            translationLabel.innerHTML = '{/literal}{l s="Translation" d="Modules.Prestablog.Ai"}{literal}:';
            messagesDiv.appendChild(translationLabel);

            addGptMessage(data.translation, messagesDiv);
        } else {
            var errorMessage = document.createElement('div');
            errorMessage.className = 'error-message';
            errorMessage.innerHTML = '{/literal}{l s="Error" d="Modules.Prestablog.Ai"}{literal}: ' + data.message;
            messagesDiv.appendChild(errorMessage);
        }
    })
    .catch(error => {
        messagesDiv.removeChild(loadingMessage);
        var errorMessage = document.createElement('div');
        errorMessage.className = 'error-message';
        errorMessage.innerHTML = '{/literal}{l s="Error" d="Modules.Prestablog.Ai"}{literal}: ' + error.message;
        messagesDiv.appendChild(errorMessage);
    });
}

function insertIntoVisibleTitleField(content) {
    var titleFields = document.querySelectorAll('input[id^="title_"]');
    var fieldFound = false;
    titleFields.forEach(function(field) {
        if (field.offsetParent !== null) {
            field.value = content;
            field.dispatchEvent(new Event('input', { bubbles: true }));
            field.dispatchEvent(new Event('change', { bubbles: true }));
            fieldFound = true;
        }
    });
    if (!fieldFound) {
        var messagesDiv = document.getElementById('gptMessages');
        var errorMessage = document.createElement('div');
        errorMessage.className = 'error-message';
        errorMessage.innerHTML = '{/literal}{l s="Error: No visible field found." d="Modules.Prestablog.Ai"}{literal}';
        messagesDiv.appendChild(errorMessage);
    }
}

function insertIntoVisibleSummaryField(content) {
    var summaryFields = document.querySelectorAll('textarea[id^="paragraph_"]');
    var fieldFound = false;
    summaryFields.forEach(function(field) {
        if (field.offsetParent !== null) {
            field.value = content;
            field.dispatchEvent(new Event('input', { bubbles: true }));
            field.dispatchEvent(new Event('change', { bubbles: true }));
            fieldFound = true;
        }
    });
    if (!fieldFound) {
        var messagesDiv = document.getElementById('gptMessages');
        var errorMessage = document.createElement('div');
        errorMessage.className = 'error-message';
        errorMessage.innerHTML = '{/literal}{l s="Error: No visible field found." d="Modules.Prestablog.Ai"}{literal}';
        messagesDiv.appendChild(errorMessage);
    }
}

function insertIntoVisibleContentField(content) {
    var activeEditor = null;
    tinymce.editors.forEach(function(editor) {
        var editorContainer = document.getElementById(editor.id + '_ifr');
        if (editorContainer && editorContainer.offsetParent !== null) {
            activeEditor = editor;
        }
    });

    if (activeEditor) {
        activeEditor.setContent(content);
        activeEditor.fire('change');
    } else {
        var messagesDiv = document.getElementById('gptMessages');
        var errorMessage = document.createElement('div');
        errorMessage.className = 'error-message';
        errorMessage.innerHTML = '{/literal}{l s="Error: No visible TinyMCE editor found." d="Modules.Prestablog.Ai"}{literal}';
        messagesDiv.appendChild(errorMessage);
    }
}

function insertIntoVisibleMetaTitleField(content) {
    var titleFields = document.querySelectorAll('input[id^="meta_title_"]');
    var fieldFound = false;
    titleFields.forEach(function(field) {
        if (field.offsetParent !== null) {
            field.value = content;
            field.dispatchEvent(new Event('input', { bubbles: true }));
            field.dispatchEvent(new Event('change', { bubbles: true }));
            fieldFound = true;
        }
    });
    if (!fieldFound) {
        var messagesDiv = document.getElementById('gptMessages');
        var errorMessage = document.createElement('div');
        errorMessage.className = 'error-message';
        errorMessage.innerHTML = '{/literal}{l s="Error: No visible meta title field found." d="Modules.Prestablog.Ai"}{literal}';
        messagesDiv.appendChild(errorMessage);
    }
}

function insertIntoVisibleMetaDescriptionField(content) {
    var descriptionFields = document.querySelectorAll('input[id^="meta_description_"]');
    var fieldFound = false;
    descriptionFields.forEach(function(field) {
        if (field.offsetParent !== null) {
            field.value = content;
            field.dispatchEvent(new Event('input', { bubbles: true }));
            field.dispatchEvent(new Event('change', { bubbles: true }));
            fieldFound = true;
        }
    });
    if (!fieldFound) {
        var messagesDiv = document.getElementById('gptMessages');
        var errorMessage = document.createElement('div');
        errorMessage.className = 'error-message';
        errorMessage.innerHTML = '{/literal}{l s="Error: No visible meta description field found." d="Modules.Prestablog.Ai"}{literal}';
        messagesDiv.appendChild(errorMessage);
    }
}
</script>
{/literal}
