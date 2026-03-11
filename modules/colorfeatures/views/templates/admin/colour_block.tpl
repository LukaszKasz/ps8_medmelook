{*
 * ColorFeatures
 *
 * @author    silbersaiten <info@silbersaiten.de>
 * @copyright 2021 silbersaiten
 * @license   See joined file licence.txt
 * @link      http://www.silbersaiten.de
 * @support   silbersaiten <support@silbersaiten.de>
 * @category  Module
 * @version   1.0.7
 *}

<div class="form-group">
    <label class="control-label col-lg-3">
        {l s='Is colour feature' mod='colorfeatures'}
    </label>
    <div class="col-lg-9">
        <span class="switch prestashop-switch fixed-width-lg">
            <input type="radio" name="is_colour_feature" id="is_colour_feature_on" value="1"
                   {if isset($feature.value) && $feature.value}checked="checked"{/if}>
            <label for="is_colour_feature_on">{l s='Yes' mod='colorfeatures'}</label>
            <input type="radio" name="is_colour_feature" id="is_colour_feature_off" value="0"
                   {if !isset($feature.value) || !$feature.value}checked="checked"{/if}>
            <label for="is_colour_feature_off">{l s='No' mod='colorfeatures'}</label>
            <a class="slide-button btn"></a>
        </span>
        <p class="help-block">
            {l s="Opened colour properties" mod='colorfeatures'}
        </p>
    </div>
</div>
<div class="colorAttributeProperties">
    <input type="hidden" id="id_color_feature" name="id_color_feature" value="{if isset($feature.id_color_feature)}{$feature.id_color_feature}{/if}">
    <div class="form-group">
        <label class="control-label col-lg-3">
            <span>
                {l s="Colour" mod='colorfeatures'}
            </span>
        </label>
        <div class="col-lg-9">
            <div class="form-group">
                <div class="col-lg-2">
                    <div class="row">
                        <div class="input-group">
                            <input type="text" class="color" name="color"
                                   value="{if isset($feature.value) && $feature.value}{$feature.value}{else}#000000{/if}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3"></label>
        <div class="col-lg-9">
            <p>
                {l s="or you can add a file with a texture." mod='colorfeatures'}
                <b>{l s="If a file is selected, it will be used first." mod='colorfeatures'}</b>
            </p>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3">{l s="Texture" mod='colorfeatures'}</label>
        <div class="col-lg-9">
            <div class="form-group">
                <div class="col-sm-6">
                    <input id="texture" type="file" name="texture" class="hide">
                    <div class="dummyfile input-group">
                        <span class="input-group-addon"><i class="icon-file"></i></span>
                        <input id="texture-name" type="text" name="texture" readonly="">
                        <span class="input-group-btn">
                            <button id="texture-selectbutton" type="button" name="submitAddAttachments"
                                    class="btn btn-default">
                                <i class="icon-folder-open"></i>
                                {l s="Add file" mod='colorfeatures'}
                            </button>
                        </span>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                $(document).ready(function () {
                    $('#texture-selectbutton').click(function (e) {
                        $('#texture').trigger('click');
                    });

                    $('#texture-name').click(function (e) {
                        $('#texture').trigger('click');
                    });

                    $('#texture-name').on('dragenter', function (e) {
                        e.stopPropagation();
                        e.preventDefault();
                    });

                    $('#texture-name').on('dragover', function (e) {
                        e.stopPropagation();
                        e.preventDefault();
                    });

                    $('#texture-name').on('drop', function (e) {
                        e.preventDefault();
                        var files = e.originalEvent.dataTransfer.files;
                        $('#texture')[0].files = files;
                        $(this).val(files[0].name);
                    });

                    $('#texture').change(function (e) {
                        if ($(this)[0].files !== undefined) {
                            var files = $(this)[0].files;
                            var name = '';

                            $.each(files, function (index, value) {
                                name += value.name + ', ';
                            });

                            $('#texture-name').val(name.slice(0, -2));
                        } else {
                            // Internet Explorer 9 Compatibility
                            var name = $(this).val().split(/[\\/]/);
                            $('#texture-name').val(name[name.length - 1]);
                        }
                    });

                    if (typeof texture_max_files !== 'undefined') {
                        $('#texture').closest('form').on('submit', function (e) {
                            if ($('#texture')[0].files.length > texture_max_files) {
                                e.preventDefault();
                                alert('You can upload a maximum of files');
                            }
                        });
                    }
                });
            </script>
        </div>
    </div>
    {if $image}
        <div class="form-group">
            <label class="control-label col-lg-3">
                {l s="Current texture" mod='colorfeatures'}
            </label>
            <div class="col-lg-9">
                <img src="{$image_path}{$image}" alt="Texture" class="img-thumbnail">
                <p>
                    <a id="remove_texture_image" class="btn btn-default">
                        <i class="icon-trash"></i>
                        {l s="Delete" mod='colorfeatures'}
                    </a>
                </p>
            </div>
        </div>
    {/if}

</div>
