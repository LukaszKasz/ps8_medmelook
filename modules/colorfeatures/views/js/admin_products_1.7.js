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

var AdminColorFeaturesProducts = {
    init: function () {
        if (!is_multifeatures_enabled) {
            this.listeners();
            this.featureProductReplacer();
        }
    },
    listeners: function () {
        $(document).on("ajaxComplete", function (e) {
            AdminColorFeaturesProducts.featureProductReplacer();
        });
    },
    featureProductReplacer: function () {
        var exist_af = advanced_features && advanced_features.length ? JSON.parse(advanced_features) : {};
        // var features_select = $('#features-content .row .feature-selector'); // for PS 1.7.2.2
        var features_select = $('#features-content .product-feature .feature-selector');

        $.each(exist_af, function (id_feature, ids) {
            features_select.each(function (index, current_feature) {
                if ($(current_feature).val() === id_feature) {
                    var replaced = $(current_feature).parents('.product-feature').find("select[id$='_value']");
                    var current_attr_name = replaced.attr('name');
                    var selected_id = replaced.val();
                    var container = $(document.createElement('div')).addClass('af_replaced_container');

                    $.each(ids, function (id_feature_value, value) {
                        var item = $(document.createElement('div')).addClass('radio af_replaced');
                        var $label = $(document.createElement('label'))
                            .attr('for', current_attr_name + '_' + id_feature_value)
                            .attr('style', 'background: ' + value + ';');

                        if(value.indexOf('.') > -1){
                            $label = $(document.createElement('label'))
                                .attr('for', current_attr_name + '_' + id_feature_value)
                                .attr('style', 'background-image: url(' + af_img_path + value + ');');
                        }

                        item.append(
                            $(document.createElement('input'))
                                .attr('type', 'radio')
                                .attr('name', current_attr_name)
                                .attr('id', current_attr_name + '_' + id_feature_value)
                                .attr('value', id_feature_value)
                                .prop("checked", selected_id === id_feature_value)
                                .addClass('hidden')
                        );
                        item.append(
                            $label
                        );

                        container.append(item);
                        replaced.parents('fieldset').append(container);
                    });
                    replaced.parents('fieldset').find('span.select2-container').remove();
                    replaced.first().remove();
                }
            });
        });
    },
};

$(document).ready(function () {
    AdminColorFeaturesProducts.init();
});
