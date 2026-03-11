{extends file="helpers/form/form.tpl"}
{block name="script"}
$(document).ready(function() {
    $('.iframe-upload').fancybox({
        'width'     : 900,
        'height'    : 600,
        'type'      : 'iframe',
        'autoScale' : false,
        'autoDimensions': false,
        'fitToView' : false,
        'autoSize' : false,
        onUpdate : function() {
            $('.fancybox-iframe').contents().find('a.link').data('field_id', $(this.element).data("input-name"));
            $('.fancybox-iframe').contents().find('a.link').attr('data-field_id', $(this.element).data("input-name"));
        },
        afterShow: function() {
            $('.fancybox-iframe').contents().find('a.link').data('field_id', $(this.element).data("input-name"));
            $('.fancybox-iframe').contents().find('a.link').attr('data-field_id', $(this.element).data("input-name"));
        },
        helpers: {
            overlay: {
                locked: false
            }
        }
    });
    handleFieldDependencies();
    let $fieldDependencies = getFieldDependencies();
    for (let i = 0; i < $fieldDependencies.length; i++) {
        $(document).off($fieldDependencies[i]).on('change', '[name="'+ $fieldDependencies[i] +'"]', function () {
            handleFieldDependencies($fieldDependencies[i]);
        }).bind(i);
    }
    function getFieldDependencies()
    {
        let fieldDependencies = [];
        $('.depends-on').each(function (index, node) {
            var $element = $(node);
            var $classes = $element.prop('class').split(/\s+/);
            for (var i = 0; i < $classes.length; i++) {
                let current = $classes[i];
                if (current.includes('depends-field')) {
                    let parts = current.replace('depends-field-', '').split(':');
                    fieldDependencies.push(parts[0]);
                }
            }
        });

        return fieldDependencies;
    }

    function inArray(needle, haystack) {
        var length = haystack.length;
        for(var i = 0; i < length; i++) {
            if(haystack[i] == needle) return true;
        }
        return false;
    }

    function handleFieldDependencies(specificFieldName)
    {
        let specificField = specificFieldName || false;
        $('.depends-on').each(function (index, node) {
            var $element = $(node);
            var $classes = $element.prop('class').split(/\s+/);
            let $method = 'match';
            let $fieldName = false,
                $fieldValue = false,
                $fieldType = false,
                $currentValue;
            if ($element.hasClass('depends-on-multiple')) {
                $fieldValue = [];
                $fieldName = [];
                $fieldType = [];
            }

            for (var i = 0; i < $classes.length; i++) {
                let current = $classes[i];
                if (current.includes('depends-where')) {
                    if (current == 'depends-where-is-not') {
                        $method = 'not_match';
                    }
                }
                if (current.includes('depends-field')) {
                    let parts = current.replace('depends-field-', '').split(':');
                    let $nameOfTheField = parts[0];
                    let $valueOfTheField = parts[1].split('--');
                    if ($element.hasClass('depends-on-multiple')) {
                        $fieldName.push($nameOfTheField);
                        $fieldValue.push($valueOfTheField);
                    } else {
                        $fieldName = $nameOfTheField;
                        $fieldValue = $valueOfTheField;
                    }

                    if($('input[name="'+ $nameOfTheField +'"]').length > 0){
                        $typeOfTheField = $('input[name="'+ $nameOfTheField +'"]').attr('type');
                    }else if($('textarea[name="'+ $nameOfTheField +'"]').length == 1){
                        $typeOfTheField = 'textarea';
                    }else if($('select[name="'+ $nameOfTheField +'"]').length == 1){
                        $typeOfTheField = 'select';
                    }

                    if ($element.hasClass('depends-on-multiple')) {
                        $fieldType.push($typeOfTheField);
                    } else {
                        $fieldType = $typeOfTheField;
                    }
                }
            }

            if ($element.hasClass('depends-on-multiple')) {
                var showBasedOnMultiple = true;
                for (var i = 0; i < $fieldName.length; i++) {
                    if ($fieldType[i] == 'checkbox' || $fieldType[i] == 'radio'){
                        $currentValue = $('[name="'+ $fieldName[i] +'"]:checked').val();
                    } else {
                        $currentValue = $('[name="'+ $fieldName[i] +'"]').val();
                    }

                    $searchedValues = $fieldValue[i];
                    if ($method == 'match') {
                        if (!inArray($currentValue, $searchedValues)) {
                            showBasedOnMultiple = false;
                        }
                    }
                    if ($method == 'not_match') {
                        if (inArray($currentValue, $searchedValues)) {
                            showBasedOnMultiple = false;
                        }
                    }
                }

                if (showBasedOnMultiple) {
                    $element.show();
                } else {
                    $element.hide();
                }
            } else {
                if (specificField && specificField != $fieldName) {
                    return;
                }

                if ($fieldType == 'checkbox' || $fieldType == 'radio'){
                    $currentValue = $('[name="'+ $fieldName +'"]:checked').val();
                } else {
                    $currentValue = $('[name="'+ $fieldName +'"]').val();
                }

                if ($method == 'not_match' && $fieldName && $fieldValue) {
                    if ($fieldValue.includes($currentValue)) {
                        $element.hide();
                    } else {
                        $element.show();
                    }
                }
                if ($method == 'match' && $fieldName && $fieldValue) {
                    if ($fieldValue.includes($currentValue)) {
                        $element.show();
                    } else {
                        $element.hide();
                    }
                }
            }
        });
    }
});
{/block}
{* {block name="label"}
    {if isset($input.errors) && !empty($input.errors)}
        <div class="col-lg-12">
            <div class="alert alert-danger">{$input.errors}</div>
        </div>
    {/if}
    {$smarty.block.parent}
{/block} *}
{block name="input"}
    {if $input.type == 'file_lang'}
        <div class="row">
            {foreach from=$languages item=language}
                {if $languages|count > 1}
                    <div class="translatable-field lang-{$language.id_lang|escape:'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                    {/if}
                    <div class="col-lg-9">
                        {*$fields_value|d*}
                        {if isset($fields_value['image']) && $fields_value['image'][$language.id_lang] != ''}
                            <img src="{$image_baseurl|escape:'UTF-8'}{$language.iso_code|escape:'UTF-8'}/{$fields_value['image'][$language.id_lang]|escape:'UTF-8'}" class="img-thumbnail" /><br><br>
                        {/if}
                        <input id="{$input.name|escape:'UTF-8'}_{$language.id_lang|escape:'UTF-8'}" type="file" name="{$input.name|escape:'UTF-8'}_{$language.id_lang|escape:'UTF-8'}" class="hide" />
                        <div class="dummyfile input-group">
                            <span class="input-group-addon"><i class="icon-file"></i></span>
                            <input id="{$input.name|escape:'UTF-8'}_{$language.id_lang|escape:'UTF-8'}-name" type="text" class="disabled" name="filename" readonly />
                            <span class="input-group-btn">
                                <button id="{$input.name|escape:'UTF-8'}_{$language.id_lang|escape:'UTF-8'}-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
                                    <i class="icon-folder-open"></i> {l s='Choose a file' mod='x13pricehistory'}
                                </button>
                            </span>
                        </div>
                    </div>
                    {if $languages|count > 1}
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                {$language.iso_code|escape:'UTF-8'}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=lang}
                                    <li><a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'UTF-8'});" tabindex="-1">{$lang.name}</a></li>
                                {/foreach}
                            </ul>
                        </div>
                    {/if}
                    {if $languages|count > 1}
                    </div>
                {/if}
                <script>
                $(document).ready(function() {
                    $('#{$input.name|escape:'UTF-8'}_{$language.id_lang|escape:'UTF-8'}-selectbutton').click(function(e) {
                        $('#{$input.name|escape:'UTF-8'}_{$language.id_lang|escape:'UTF-8'}').trigger('click');
                    });
                    $('#{$input.name|escape:'UTF-8'}_{$language.id_lang|escape:'UTF-8'}').change(function(e) {
                        var val = $(this).val();
                        var file = val.split(/[\\/]/);
                        $('#{$input.name|escape:'UTF-8'}_{$language.id_lang|escape:'UTF-8'}-name').val(file[file.length - 1]);
                    });
                });
                </script>
            {/foreach}
        </div>
    {elseif $input.type == 'selectImage'}
        <input name="{$input.name|escape:'html':'UTF-8'}" id="{$input.name|escape:'html':'UTF-8'}" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" type="text" />
        <p>
            <a href="filemanager/dialog.php?type=1&amp;field_id={$input.name|escape:'html':'UTF-8'}" class="btn btn-default iframe-upload" data-input-name="{$input.name|escape:'html':'UTF-8'}" type="button">{l s='Image selector' mod='x13pricehistory'} <i class="icon-angle-right"></i></a>
        </p>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}