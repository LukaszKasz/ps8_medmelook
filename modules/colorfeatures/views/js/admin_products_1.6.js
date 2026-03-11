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
        this.listeners();
    },
    listeners: function () {
        if (!is_multifeatures_enabled) {
            var tab = $('#product-tab-content-Features');
            /** #FIXME - to find a better way */
            tab.ajaxComplete(function () {
                if (!tab.hasClass('not-loaded') || !tab.hasClass('loading') || !$('.advanced_feature_wrapper').length) {
                    AdminColorFeaturesProducts.featureProductReplacer();
                }
            });
        }
    },
    featureProductReplacer: function () {
        var exist_af = advanced_features && advanced_features.length ? JSON.parse(advanced_features) : {};

        $.each(exist_af, function (id_feature, ids) {
            var replaced = $('#feature_' + id_feature + '_value');
            var selected_id = replaced.val();

            $.each(ids, function (id_feature_value, value) {
                var container = $(document.createElement('div'))
                    .addClass('radio af_replaced');
                var $label = $(document.createElement('label'))
                    .attr('for', 'feature_' + id_feature + '_value_' + value)
                    .attr('style', 'background: ' + value + ';');

                if(value.indexOf('.') > -1){
                    $label = $(document.createElement('label'))
                        .attr('for', 'feature_' + id_feature + '_value_' + value)
                        .attr('style', 'background-image: url(' + af_img_path + value + ');');
                }

                container.append(
                    $(document.createElement('input'))
                        .attr('type', 'radio')
                        .attr('name', 'feature_' + id_feature + '_value')
                        .attr('id', 'feature_' + id_feature + '_value_' + value)
                        .attr('value', id_feature_value)
                        .prop("checked", selected_id === id_feature_value)
                        .addClass('hidden')
                );
                container.append(
                    $label
                );

                replaced.parent('td').append(container);
            });

            replaced.first().remove();
        });
    },
};

$(document).ready(function () {
    AdminColorFeaturesProducts.init();
});
