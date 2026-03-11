{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<div class="modal fade js-product-images-modal" id="product-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title">{l s='Tap to zoom' d='Shop.Theme.Catalog'}</span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {assign var=imagesCount value=$product.images|count}
                <div class="easyzoom easyzoom-modal">
                {if $product.cover}   
                    <a href="{if !empty($product.default_image.bySize.large_default.sources.webp)}{$product.default_image.bySize.large_default.sources.webp}{elseif !empty($product.default_image.bySize.large_default.sources.avif)}{$product.default_image.bySize.large_default.sources.avif}{else}{$product.default_image.bySize.large_default.url}{/if}" 
                        class="js-modal-product-cover-easyzoom" rel="nofollow">
                    <picture>
                        {if !empty($product.default_image.bySize.large_default.sources.avif)}<source srcset="{$product.default_image.bySize.large_default.sources.avif}" type="image/avif">{/if}
                        {if !empty($product.default_image.bySize.large_default.sources.webp)}<source srcset="{$product.default_image.bySize.large_default.sources.webp}" type="image/webp">{/if}
                        <img class="js-modal-product-cover product-cover-modal img-fluid"
                             width="{$product.default_image.bySize.large_default.width}"  height="{$product.default_image.bySize.large_default.height}" src="{$product.default_image.bySize.large_default.url}"
                             {if isset($product.cover.legend) && !empty($product.cover.legend)}
                                alt="{$product.cover.legend}"
                                {else}
                                alt="{$product.name}"
                             {/if}
                           >
                    </picture>
                    </a>
                    {else}
                        <a href="{$urls.no_picture_image.bySize.large_default.url}" class="js-modal-product-cover-easyzoom" rel="nofollow">
                        <picture>
                            {if !empty($urls.no_picture_image.bySize.large_default.sources.avif)}<source srcset="{$urls.no_picture_image.bySize.large_default.sources.avif}" type="image/avif">{/if}
                            {if !empty($urls.no_picture_image.bySize.large_default.sources.webp)}<source srcset="{$urls.no_picture_image.bySize.large_default.sources.webp}" type="image/webp">{/if}
                            <img class="js-modal-product-cover product-cover-modal img-fluid"
                            width="{$urls.no_picture_image.bySize.large_default.width}" height="{$urls.no_picture_image.bySize.large_default.height}" src="{$urls.no_picture_image.bySize.large_default.url}"   alt="{$product.name}">
                        </picture>
                         </a>


                      {/if}
                </div>
                <aside id="thumbnails" class="thumbnails js-thumbnails text-xs-center">
                    {block name='product_images'}
                        {if $product.images|@count gt 1}

                        <div class="js-modal-mask mask {if $imagesCount <= 5} nomargin {/if}">
                            <div id="modal-product-thumbs" class="product-images js-modal-product-images swiper-cls-fix desktop-swiper-cls-fix-10 swiper-cls-row-fix-1 tablet-swiper-cls-fix-6 mobile-swiper-cls-fix-6 swiper-container">
                                <div class="swiper-wrapper">
                                {foreach from=$product.images item=image}
                                    <div class="swiper-slide">
                                        <div class="thumb-container">
                                            <picture>
                                            {if !empty($image.bySize.medium_default.sources.avif)}<source srcset="{$image.bySize.medium_default.sources.avif}" type="image/avif">{/if}
                                            {if !empty($image.bySize.medium_default.sources.webp)}<source srcset="{$image.bySize.medium_default.sources.webp}" type="image/webp">{/if}
                                            <img data-image-large-src="{$image.large.url}" {if !empty($image.large.sources)}data-image-large-sources="{$image.large.sources|@json_encode}"{/if} class="thumb js-modal-thumb img-fluid swiper-lazy"
                                                 src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 {$image.bySize.medium_default.width} {$image.bySize.medium_default.height}'%3E%3C/svg%3E"
                                                 data-src="{$image.bySize.medium_default.url}"
                                                 {if isset($image.legend) && !empty($image.legend)}
                                                    alt="{$image.legend}"
                                                    title="{$image.legend}"
                                                  {else}
                                                    alt="{$product.name}"
                                                  {/if}
                                                 width="{$image.bySize.medium_default.width}"
                                                 height="{$image.bySize.medium_default.height}"

                                                 itemprop="image">
                                            </picture>
                                        </div>
                                    </div>
                                {/foreach}
                                </div>
                                <div class="swiper-button-prev swiper-button-inner-prev swiper-button-arrow"></div>
                                <div class="swiper-button-next swiper-button-inner-next swiper-button-arrow"></div>
                            </div>
                        </div>

                        {/if}
                    {/block}
                </aside>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
