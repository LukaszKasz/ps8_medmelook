/**
 * ColorFeatures
 *
 * @author    silbersaiten <info@silbersaiten.de>
 * @copyright 2021 silbersaiten
 * @license   See joined file licence.txt
 * @link      http://www.silbersaiten.de
 * @support   silbersaiten <support@silbersaiten.de>
 * @category  Module
 * @version   1.0.7
 */

var AdminColorFeatures = {
    init: function () {
        this.listeners();
        this.addColourBlock();
    },
    listeners: function () {
        $(document).on('click', 'input[name=is_colour_feature]', function () {
            AdminColorFeatures.propertiesTrigger(parseInt($(this).val()))
        });
        $(document).on('click', '#remove_texture_image', function (e) {
            e.preventDefault();
            AdminColorFeatures.removeTextureImage();
        });
    },
    propertiesTrigger: function (show) {
        if (show) {
            $('#feature_value_form .colorAttributeProperties').slideDown();
        } else {
            $('#feature_value_form .colorAttributeProperties').slideUp();
        }
    },
    addColourBlock: function () {
        $.post(af_tpl_path,
            {
                action: "GetTemplate",
                ajax: true,
                id_feature_value: $('#id_feature_value').val()
            })
            .done(function (data) {
                $('.form-wrapper').append(data);

                AdminColorFeatures.propertiesTrigger(parseInt($('input[name=is_colour_feature]:checked').val()));
                // Fixed the PS bug if the virtual path is too long
                if (typeof (admin_img_path) != 'undefined') {
                    $.fn.mColorPicker.defaults.imageFolder = admin_img_path;
                    $('#mColorPickerImg').css({'background-image':"url('" + admin_img_path + "colorpicker.png')"});
                    $('#mColorPickerImgGray').css({'background-image':"url('" + admin_img_path + "graybar.jpg')"});
                    $('#mColorPickerFooter').css({'background-image':"url('" + admin_img_path + "grid.gif')"});
                }
                $('input.color').mColorPicker($.fn.mColorPicker.defaults);
            });
    },
    removeTextureImage: function () {
        $.post(af_tpl_path,
            {
                action: "removeTextureImage",
                ajax: true,
                id_color_feature: $('#id_color_feature').val()
            })
            .done(function (data) {
                var res = JSON.parse(data);
                if(res.error){
                    $.growl.error({message: res.error_msg});
                } else {
                    $.growl.error({message: res.success_msg});
                    location.reload();
                }
            });
    }
};


$(document).ready(function () {
    if ($('#feature_value_form').length) {
        AdminColorFeatures.init();
    }
});
