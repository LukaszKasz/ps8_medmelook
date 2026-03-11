{**
* ColorFeatures
*
* @category  Module
* @author    silbersaiten <info@silbersaiten.de>
* @support   silbersaiten <support@silbersaiten.de>
* @copyright 2022 silbersaiten
* @version   1.0.11
* @link      https://www.silbersaiten.de
* @license   See joined file licence.txt
*}

{if $color_feature.is_texture}
    <span class="color_feature_item">
        <span class="color_feature_texture" style="background-image:url({$image_path}{$color_feature.id_color_feature}.{$color_feature.texture_extension})"></span>
        <span class="color_feature_name">{$feature.value}</span>
    </span>
{else}
    <span class="color_feature_item">
        <span class="color_feature_color" style="background-color:{$color_feature.value}"></span>
        <span class="color_feature_name">{$feature.value}</span>
    </span>
{/if}
