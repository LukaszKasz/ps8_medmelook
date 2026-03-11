{*
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  @copyright  2007-2021 PrestaShop SA
*  International Registered Trademark & Property of PrestaShop SA
*}
{if isset($fields.title)}<h3>{$fields.title|escape:'htmlall':'UTF-8'}</h3>{/if}

{if isset($tabs) && $tabs|count}
  <script type="text/javascript">
    var helper_tabs = {$tabs|json_encode};
    var unique_field_id = '';
  </script>
{/if}
{block name="defaultForm"}
    {if isset($identifier_bk) && $identifier_bk == $identifier}{capture name='identifier_count'}{counter name='identifier_count'}{/capture}{/if}
    {assign var='identifier_bk' value=$identifier scope='parent'}
    {if isset($table_bk) && $table_bk == $table}{capture name='table_count'}{counter name='table_count'}{/capture}{/if}
    {assign var='table_bk' value=$table scope='parent'}
    <form id="{if isset($fields.form.form.id_form)}Test 1{$fields.form.form.id_form|escape:'html':'UTF-8'}{else}{if $table == null}configuration_form{else}{$table|escape:'htmlall':'UTF-8'}_form{/if}{if isset($smarty.capture.table_count) && $smarty.capture.table_count}_{$smarty.capture.table_count|intval}{/if}{/if}"
          class="defaultForm form-horizontal{if isset($name_controller) && $name_controller} {$name_controller|escape:'htmlall':'UTF-8'}{/if}"{if isset($current) && $current} action="{$current|escape:'html':'UTF-8'}{if isset($token) && $token}&amp;token={$token|escape:'html':'UTF-8'}{/if}"{/if}
          method="post" enctype="multipart/form-data"{if isset($style)} style="{$style|escape:'htmlall':'UTF-8'}"{/if}
          novalidate>
        {if $form_id}
            <input type="hidden" name="{$identifier|escape:'htmlall':'UTF-8'}"
                   id="{$identifier|escape:'htmlall':'UTF-8'}{if isset($smarty.capture.identifier_count) && $smarty.capture.identifier_count}_{$smarty.capture.identifier_count|intval}{/if}"
                   value="{$form_id|escape:'htmlall':'UTF-8'}"/>
        {/if}
        {if !empty($submit_action)}
            <input type="hidden" name="{$submit_action|escape:'htmlall':'UTF-8'}" value="1"/>
        {/if}

        <!-- FERRET2 -->
        <div class="row">
          <div class="row mt-5">
            <div class="col-xs-12 col-sm-12"><div class="h2">Ferret</div></div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
              <div id="ferret2Carousel" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                  <li data-target="#ferret2Carousel" data-slide-to="0" class="active"></li>
                  <li data-target="#ferret2Carousel" data-slide-to="1"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                  <div class="item active">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/ferret2_01.png">
                  </div>
                  <div class="item">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/ferret2_02.png">
                  </div>
                </div>
                <a class="left carousel-control" href="#ferret2Carousel" role="button" data-slide="prev">
                  <span class="icon-prev" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#ferret2Carousel" role="button" data-slide="next">
                  <span class="icon-next" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
                </a>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
              <p>{l s='Type' mod='trustmate'}: <strong>{l s='Company' mod='trustmate'}</strong></p>
              <p>{l s='Name' mod='trustmate'}: <strong>{l s='Carousel - company reviews' mod='trustmate'}</strong></p>
              <br />
              <p>
                <p><strong>{l s='Catch your customers’ attention with this immersive widget!’ attention with this dynamic widget.' mod='trustmate'}</strong></p>
                <p><strong>{l s='Showcase your average rating and the most recent reviews on auto-scrolling tiles and make your website look even cooler!' mod='trustmate'}</strong></p>
              </p>
              <br />
              <hr>
              <strong>{l s='Widget settings' mod='trustmate'}:</strong>
              <div class="form-group">
                <label class="control-label col-lg-2">{l s='Widget enabled' mod='trustmate'}?</label>
                <div class="col-lg-4">
                  <span class="switch prestashop-switch">
                    <input type="radio" name="TRUSTMATE_FERRET2" id="TRUSTMATE_FERRET2_on" value="1" {if $TRUSTMATE_FERRET2 == 1}checked{/if}>
                    <label for="TRUSTMATE_FERRET2_on">{l s='Yes' mod='trustmate'}</label>
                    <input type="radio" name="TRUSTMATE_FERRET2" id="TRUSTMATE_FERRET2_off" value="0" {if $TRUSTMATE_FERRET2 == 0}checked{/if}>
                    <label for="TRUSTMATE_FERRET2_off">{l s='No' mod='trustmate'}</label>
                    <a class="slide-button btn"></a>
                  </span>
                </div>
                <div class="col-lg-6"></div>
              </div>
              <hr>
              <div class="col-lg-10"></div>
              <div class="col-lg-2"><button type="submit" value="1" name="save_widget" class="btn btn-default"><i class="process-icon-save"></i>{l s='Save' mod='trustmate'}</button></div>
            </div>
          </div>
        </div>

        <!-- PRODUCT_FERRET2 -->
        <div class="row">
          <div class="row mt-5">
            <div class="col-xs-12 col-sm-12"><div class="h2">Product ferret</div></div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
              <div id="productFerret2Carousel" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                  <li data-target="#productFerret2Carousel" data-slide-to="0" class="active"></li>
                  <li data-target="#productFerret2Carousel" data-slide-to="1"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                  <div class="item active">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/productFerret2_01.png">
                  </div>
                  <div class="item">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/productFerret2_02.png">
                  </div>
                </div>
                <a class="left carousel-control" href="#productFerret2Carousel" role="button" data-slide="prev">
                  <span class="icon-prev" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#productFerret2Carousel" role="button" data-slide="next">
                  <span class="icon-next" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
                </a>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
              <p>{l s='Type' mod='trustmate'}: <strong>{l s='Product' mod='trustmate'}</strong></p>
              <p>{l s='Name' mod='trustmate'}: <strong>{l s='Carousel - product reviews' mod='trustmate'}</strong></p>
              <br />
              <p>
                <p><strong>{l s='Attract your customers’ attention with this dynamic widget.' mod='trustmate'}</strong></p>
                <p><strong>{l s='Average rating, recent reviews, customer images - everything in one place!' mod='trustmate'}</strong></p>
                <p><strong>{l s='Fits like a glove and works like a dream!' mod='trustmate'}</strong></p>
              </p>
              <br />
              <hr>
              <strong>{l s='Widget settings' mod='trustmate'}:</strong>
              <div class="form-group">
                <label class="control-label col-lg-2">{l s='Widget enabled' mod='trustmate'}?</label>
                <div class="col-lg-4">
                  <span class="switch prestashop-switch">
                    <input type="radio" name="TRUSTMATE_PRODUCT_FERRET2" id="TRUSTMATE_PRODUCT_FERRET2_on" value="1" {if $TRUSTMATE_PRODUCT_FERRET2 == 1}checked{/if}>
                    <label for="TRUSTMATE_PRODUCT_FERRET2_on">{l s='Yes' mod='trustmate'}</label>
                    <input type="radio" name="TRUSTMATE_PRODUCT_FERRET2" id="TRUSTMATE_PRODUCT_FERRET2_off" value="0" {if $TRUSTMATE_PRODUCT_FERRET2 == 0}checked{/if}>
                    <label for="TRUSTMATE_PRODUCT_FERRET2_off">{l s='No' mod='trustmate'}</label>
                    <a class="slide-button btn"></a>
                  </span>
                </div>
                <div class="col-lg-6"></div>
              </div>
              <hr>
              <div class="col-lg-10"></div>
              <div class="col-lg-2"><button type="submit" value="1" name="save_widget" class="btn btn-default"><i class="process-icon-save"></i>{l s='Save' mod='trustmate'}</button></div>
            </div>
          </div>
        </div>

        <!-- BADGER2 -->
        <div class="row">
          <div class="row mt-5">
            <div class="col-xs-12 col-sm-12"><div class="h2">Badger</div></div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
              <div id="badger2Carousel" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                  <li data-target="#badger2Carousel" data-slide-to="0" class="active"></li>
                  <li data-target="#badger2Carousel" data-slide-to="1"></li>
                  <li data-target="#badger2Carousel" data-slide-to="2"></li>
                  <li data-target="#badger2Carousel" data-slide-to="3"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                  <div class="item active">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/badger2_01.png">
                  </div>
                  <div class="item">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/badger2_02.png">
                  </div>
                  <div class="item">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/badger2_03.png">
                  </div>
                  <div class="item">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/badger2_04.png">
                  </div>
                </div>
                <a class="left carousel-control" href="#badger2Carousel" role="button" data-slide="prev">
                  <span class="icon-prev" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#badger2Carousel" role="button" data-slide="next">
                  <span class="icon-next" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
                </a>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
              <p>{l s='Type' mod='trustmate'}: <strong>{l s='Product' mod='trustmate'}</strong></p>
              <p>{l s='Name' mod='trustmate'}: <strong>{l s='Badge - product rating (edge)' mod='trustmate'}</strong></p>
              <br />
              <p>
                <p><strong>{l s='Small but mighty!' mod='trustmate'}</strong></p>
                <p><strong>{l s='Bring your average rating to view and showcase the full review content by expanding this little widget.' mod='trustmate'}</strong></p>
              </p>
              <br />
              <hr>
              <strong>{l s='Widget settings' mod='trustmate'}:</strong>
              <div class="form-group">
                <label class="control-label col-lg-2">{l s='Widget enabled' mod='trustmate'}?</label>
                <div class="col-lg-4">
                  <span class="switch prestashop-switch">
                    <input type="radio" name="TRUSTMATE_BADGER2" id="TRUSTMATE_BADGER2_on" value="1" {if $TRUSTMATE_BADGER2 == 1}checked{/if}>
                    <label for="TRUSTMATE_BADGER2_on">{l s='Yes' mod='trustmate'}</label>
                    <input type="radio" name="TRUSTMATE_BADGER2" id="TRUSTMATE_BADGER2_off" value="0" {if $TRUSTMATE_BADGER2 == 0}checked{/if}>
                    <label for="TRUSTMATE_BADGER2_off">{l s='No' mod='trustmate'}</label>
                    <a class="slide-button btn"></a>
                  </span>
                </div>
                <div class="col-lg-6"></div>
              </div>
              <hr>
              <div class="col-lg-10"></div>
              <div class="col-lg-2"><button type="submit" value="1" name="save_widget" class="btn btn-default"><i class="process-icon-save"></i>{l s='Save' mod='trustmate'}</button></div>
            </div>
          </div>
        </div>

        <!-- MUSKRAT2 -->
        <div class="row">
          <div class="row mt-5">
            <div class="col-xs-12 col-sm-12"><div class="h2">Muskrat</div></div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
              <div id="muskrat2" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                  <li data-target="#muskrat2" data-slide-to="0" class="active"></li>
                  <li data-target="#muskrat2" data-slide-to="1"></li>
                  <li data-target="#muskrat2" data-slide-to="2"></li>
                  <li data-target="#muskrat2" data-slide-to="3"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                  <div class="item active">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/muskrat2_01.png">
                  </div>
                  <div class="item">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/muskrat2_02.png">
                  </div>
                  <div class="item">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/muskrat2_03.png">
                  </div>
                  <div class="item">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/muskrat2_04.png">
                  </div>
                </div>
                <a class="left carousel-control" href="#muskrat2" role="button" data-slide="prev">
                  <span class="icon-prev" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#muskrat2" role="button" data-slide="next">
                  <span class="icon-next" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
                </a>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
              <p>{l s='Type' mod='trustmate'}: <strong>{l s='Company' mod='trustmate'}</strong></p>
              <p>{l s='Name' mod='trustmate'}: <strong>{l s='Badge with the company\'s rating and grades distribution (edge)' mod='trustmate'}</strong></p>
              <br />
              <p>
                <p><strong>{l s='Amaze your customers with this modern, interactive widget!' mod='trustmate'}</strong></p>
                <p><strong>{l s='Display your average rating and review count, showcase the most recent reviews and collect new ones with just a few clicks.' mod='trustmate'}</strong></p>
                <p><strong>{l s='Simply and intuitively.' mod='trustmate'}</strong></p>
              </p>
              <br />
              <hr>
              <strong>{l s='Widget settings' mod='trustmate'}:</strong>
              <div class="form-group">
                <label class="control-label col-lg-2">{l s='Widget enabled' mod='trustmate'}?</label>
                <div class="col-lg-4">
                  <span class="switch prestashop-switch">
                    <input type="radio" name="TRUSTMATE_MUSKRAT2" id="TRUSTMATE_MUSKRAT2_on" value="1" {if $TRUSTMATE_MUSKRAT2 == 1}checked{/if}>
                    <label for="TRUSTMATE_MUSKRAT2_on">{l s='Yes' mod='trustmate'}</label>
                    <input type="radio" name="TRUSTMATE_MUSKRAT2" id="TRUSTMATE_MUSKRAT2_off" value="0" {if $TRUSTMATE_MUSKRAT2 == 0}checked{/if}>
                    <label for="TRUSTMATE_MUSKRAT2_off">{l s='No' mod='trustmate'}</label>
                    <a class="slide-button btn"></a>
                  </span>
                </div>
                <div class="col-lg-6"></div>
              </div>
              <hr>
              <div class="col-lg-10"></div>
              <div class="col-lg-2"><button type="submit" value="1" name="save_widget" class="btn btn-default"><i class="process-icon-save"></i>{l s='Save' mod='trustmate'}</button></div>
            </div>
          </div>
        </div>

        <!-- CHUPACABRA -->
        <div class="row">
          <div class="row mt-5">
            <div class="col-xs-12 col-sm-12"><div class="h2">Chupacabra</div></div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
              <div id="chupacabra" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                  <li data-target="#chupacabra" data-slide-to="0" class="active"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                  <div class="item active">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/chupacabra_01.png?1">
                  </div>
                </div>
                <a class="left carousel-control" href="#chupacabra" role="button" data-slide="prev">
                  <span class="icon-prev" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#chupacabra" role="button" data-slide="next">
                  <span class="icon-next" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
                </a>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
              <p>{l s='Type' mod='trustmate'}: <strong>{l s='Company' mod='trustmate'} / {l s='Product' mod='trustmate'}</strong></p>
              <p>{l s='Name' mod='trustmate'}: <strong>{l s='Social Proof (big box)' mod='trustmate'}</strong></p>
              <br />
              <p>
                <p><strong>{l s='Average rating, recent reviews and customer photos displayed on static cards directly on your website.' mod='trustmate'}</strong></p>
                <p><strong>{l s='Neat and simple.' mod='trustmate'}</strong></p>
              </p>
              <br />
              <hr>
              <strong>{l s='Widget settings' mod='trustmate'}:</strong>
              <div class="form-group">
                <label class="control-label col-lg-2">{l s='Widget enabled' mod='trustmate'}?</label>
                <div class="col-lg-4">
                  <span class="switch prestashop-switch">
                    <input type="radio" name="TRUSTMATE_CHUPACABRA" id="TRUSTMATE_CHUPACABRA_on" value="1" {if $TRUSTMATE_CHUPACABRA == 1}checked{/if}>
                    <label for="TRUSTMATE_CHUPACABRA_on">{l s='Yes' mod='trustmate'}</label>
                    <input type="radio" name="TRUSTMATE_CHUPACABRA" id="TRUSTMATE_CHUPACABRA_off" value="0" {if $TRUSTMATE_CHUPACABRA == 0}checked{/if}>
                    <label for="TRUSTMATE_CHUPACABRA_off">{l s='No' mod='trustmate'}</label>
                    <a class="slide-button btn"></a>
                  </span>
                </div>
                <div class="col-lg-6"></div>
              </div>
              <hr>
              <div class="col-lg-10"></div>
              <div class="col-lg-2"><button type="submit" value="1" name="save_widget" class="btn btn-default"><i class="process-icon-save"></i>{l s='Save' mod='trustmate'}</button></div>
            </div>
          </div>
        </div>

        <!-- ALPACA -->
        <div class="row">
          <div class="row mt-5">
            <div class="col-xs-12 col-sm-12"><div class="h2">Alpaca</div></div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
              <div id="alpaca" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                  <li data-target="#alpaca" data-slide-to="0" class="active"></li>
                  <li data-target="#alpaca" data-slide-to="1"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                  <div class="item active">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/alpaca_01.png">
                  </div>
                  <div class="item">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/alpaca_02.png">
                  </div>
                </div>
                <a class="left carousel-control" href="#alpaca" role="button" data-slide="prev">
                  <span class="icon-prev" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#alpaca" role="button" data-slide="next">
                  <span class="icon-next" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
                </a>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
              <p>{l s='Type' mod='trustmate'}: <strong>{l s='Company' mod='trustmate'}</strong></p>
              <p>{l s='Name' mod='trustmate'}: <strong>{l s='Type' mod='trustmate'}Social Proof (pop-up reviews)</strong></p>
              <br />
              <p>
                <p><strong>{l s='Attention grabber like no other.' mod='trustmate'}</strong></p>
                <p><strong>{l s='Display your average rating, number of reviews and present the most recent ones with this unmissable widget.' mod='trustmate'}</strong></p>
                <p><strong>{l s='Let Alpaca give your website that wow factor!' mod='trustmate'}</strong></p>
              </p>
              <br />
              <hr>
              <strong>{l s='Widget settings' mod='trustmate'}:</strong>
              <div class="form-group">
                <label class="control-label col-lg-2">{l s='Widget enabled' mod='trustmate'}?</label>
                <div class="col-lg-4">
                  <span class="switch prestashop-switch">
                    <input type="radio" name="TRUSTMATE_ALPACA" id="TRUSTMATE_ALPACA_on" value="1" {if $TRUSTMATE_ALPACA == 1}checked{/if}>
                    <label for="TRUSTMATE_ALPACA_on">{l s='Yes' mod='trustmate'}</label>
                    <input type="radio" name="TRUSTMATE_ALPACA" id="TRUSTMATE_ALPACA_off" value="0" {if $TRUSTMATE_ALPACA == 0}checked{/if}>
                    <label for="TRUSTMATE_ALPACA_off">{l s='No' mod='trustmate'}</label>
                    <a class="slide-button btn"></a>
                  </span>
                </div>
                <div class="col-lg-6"></div>
              </div>
              <hr>
              <div class="col-lg-10"></div>
              <div class="col-lg-2"><button type="submit" value="1" name="save_widget" class="btn btn-default"><i class="process-icon-save"></i>{l s='Save' mod='trustmate'}</button></div>
            </div>
          </div>
        </div>

        <!-- HYDRA -->
        <div class="row">
          <div class="row mt-5">
            <div class="col-xs-12 col-sm-12"><div class="h2">Hydra</div></div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
              <div id="hydra" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                  <li data-target="#hydra" data-slide-to="0" class="active"></li>
                  <li data-target="#hydra" data-slide-to="1"></li>
                  <li data-target="#hydra" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                  <div class="item active">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/hydra_01.png">
                  </div>
                  <div class="item">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/hydra_02.png">
                  </div>
                  <div class="item">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/hydra_03.png">
                  </div>
                </div>
                <a class="left carousel-control" href="#hydra" role="button" data-slide="prev">
                  <span class="icon-prev" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#hydra" role="button" data-slide="next">
                  <span class="icon-next" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
                </a>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
              <p>{l s='Type' mod='trustmate'}: <strong>{l s='Product' mod='trustmate'}</strong></p>
              <p>{l s='Name' mod='trustmate'}: <strong>{l s='Modular widget - product reviews, expert reviews, Q&A' mod='trustmate'}</strong></p>
              <br />
              <p>
                <p><strong>{l s='Wow your customers with this versatile, modular widget!' mod='trustmate'}</strong></p>
                <p><strong>{l s='Average rating, results of product surveys, up to 50 recent reviews, expert evaluations, and the Q&A module - Hydra has everything in one place.' mod='trustmate'}</strong></p>
                <p><strong>{l s='Type' mod='trustmate'}</strong></p>
              </p>
              <br />
              <hr>
              <strong>{l s='Widget settings' mod='trustmate'}:</strong>
              <div class="form-group">
                <label class="control-label col-lg-2">{l s='Widget enabled' mod='trustmate'}?</label>
                <div class="col-lg-4">
                  <span class="switch prestashop-switch">
                    <input type="radio" name="TRUSTMATE_HYDRA" id="TRUSTMATE_HYDRA_on" value="1" {if $TRUSTMATE_HYDRA == 1}checked{/if}>
                    <label for="TRUSTMATE_HYDRA_on">{l s='Yes' mod='trustmate'}</label>
                    <input type="radio" name="TRUSTMATE_HYDRA" id="TRUSTMATE_HYDRA_off" value="0" {if $TRUSTMATE_HYDRA == 0}checked{/if}>
                    <label for="TRUSTMATE_HYDRA_off">{l s='No' mod='trustmate'}</label>
                    <a class="slide-button btn"></a>
                  </span>
                </div>
                <div class="col-lg-6"></div>
              </div>
              <hr>
              <div class="col-lg-10"></div>
              <div class="col-lg-2"><button type="submit" value="1" name="save_widget" class="btn btn-default"><i class="process-icon-save"></i>{l s='Save' mod='trustmate'}</button></div>
            </div>
          </div>
        </div>

        <!-- HORNET -->
        <div class="row">
          <div class="row mt-5">
            <div class="col-xs-12 col-sm-12"><div class="h2">Hornet</div></div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
              <div id="hornet" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                  <li data-target="#hornet" data-slide-to="0" class="active"></li>
                  <li data-target="#hornet" data-slide-to="1"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                  <div class="item active">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/hornet_01.png">
                  </div>
                  <div class="item">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/hornet_02.png">
                  </div>
                </div>
                <a class="left carousel-control" href="#hornet" role="button" data-slide="prev">
                  <span class="icon-prev" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#hornet" role="button" data-slide="next">
                  <span class="icon-next" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
                </a>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
              <p>{l s='Type' mod='trustmate'}: <strong>{l s='Product' mod='trustmate'}</strong></p>
              <p>{l s='Name' mod='trustmate'}: <strong>{l s='Widget that displays the rating and reviews' mod='trustmate'}</strong></p>
              <br />
              <p>
                <p><strong>{l s='A minimalistic widget displaying average rating next to a product description.' mod='trustmate'}</strong></p>
                <p><strong>{l s='Hornet allows customers to read reviews.' mod='trustmate'}</strong></p>
                <p><strong>{l s='Simple and effective.' mod='trustmate'}</strong></p>
              </p>
              <br />
              <hr>
              <strong>{l s='Widget settings' mod='trustmate'}:</strong>
              <div class="form-group">
                <label class="control-label col-lg-2">{l s='Widget enabled' mod='trustmate'}?</label>
                <div class="col-lg-4">
                  <span class="switch prestashop-switch">
                    <input type="radio" name="TRUSTMATE_HORNET" id="TRUSTMATE_HORNET_on" value="1" {if $TRUSTMATE_HORNET == 1}checked{/if}>
                    <label for="TRUSTMATE_HORNET_on">{l s='Yes' mod='trustmate'}</label>
                    <input type="radio" name="TRUSTMATE_HORNET" id="TRUSTMATE_HORNET_off" value="0" {if $TRUSTMATE_HORNET == 0}checked{/if}>
                    <label for="TRUSTMATE_HORNET_off">{l s='No' mod='trustmate'}</label>
                    <a class="slide-button btn"></a>
                  </span>
                </div>
                <div class="col-lg-6"></div>
              </div>
              <label>{l s='Position' mod='trustmate'}</label>
              <p>
                <em>{l s='Please choose the best option experimentally. Depending on your theme, some options may not show widget at all' mod='trustmate'}</em>.
                <em>{l s='In case of problems, please refer to the author of your theme and ask about supported types of displayProductPriceBlock hook'}</em>.
              </p>
              <select name="TRUSTMATE_HORNET_POSITION">
                <option value="DisplayProductPriceBlock@price" {if $TRUSTMATE_HORNET_POSITION == 'DisplayProductPriceBlock@price'}selected{/if}>price block - price</option>
                <option value="DisplayProductPriceBlock@after_price" {if $TRUSTMATE_HORNET_POSITION == 'DisplayProductPriceBlock@after_price'}selected{/if}>price block - after price</option>
                <option value="DisplayProductPriceBlock@before_price" {if $TRUSTMATE_HORNET_POSITION == 'DisplayProductPriceBlock@before_price'}selected{/if}>price block - before price</option>
                <option value="DisplayProductPriceBlock@custom_price" {if $TRUSTMATE_HORNET_POSITION == 'DisplayProductPriceBlock@custom_price'}selected{/if}>price block - custom price</option>
                <option value="DisplayProductPriceBlock@old_price" {if $TRUSTMATE_HORNET_POSITION == 'DisplayProductPriceBlock@old_price'}selected{/if}>price block - old price</option>
                <option value="DisplayProductPriceBlock@unit_price" {if $TRUSTMATE_HORNET_POSITION == 'DisplayProductPriceBlock@unit_price'}selected{/if}>price block - unit price</option>
                <option value="DisplayProductPriceBlock@weight" {if $TRUSTMATE_HORNET_POSITION == 'DisplayProductPriceBlock@weight'}selected{/if}>price block - weight</option>
                <option value="DisplayProductPriceBlock@custom_hook" {if $TRUSTMATE_HORNET_POSITION == 'DisplayProductPriceBlock@custom_hook'}selected{/if}>price block - custom hook</option>
              </select>
              <hr>
              <div class="col-lg-10"></div>
              <div class="col-lg-2"><button type="submit" value="1" name="save_widget" class="btn btn-default"><i class="process-icon-save"></i>{l s='Save' mod='trustmate'}</button></div>
            </div>
          </div>
        </div>

        <!-- MULTI_HORNET -->
        <div class="row">
          <div class="row mt-5">
            <div class="col-xs-12 col-sm-12"><div class="h2">Multihornet</div></div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
              <div id="multihornet" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                  <li data-target="#multihornet" data-slide-to="0" class="active"></li>
                  <li data-target="#multihornet" data-slide-to="1"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                  <div class="item active">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/multihornet_01.png">
                  </div>
                  <div class="item">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/multihornet_02.png">
                  </div>
                </div>
                <a class="left carousel-control" href="#multihornet" role="button" data-slide="prev">
                  <span class="icon-prev" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#multihornet" role="button" data-slide="next">
                  <span class="icon-next" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
                </a>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
              <p>{l s='Type' mod='trustmate'}: <strong>{l s='Product' mod='trustmate'}</strong></p>
              <p>{l s='Name' mod='trustmate'}: <strong>{l s='Widget that displays the rating and reviews' mod='trustmate'}</strong></p>
              <br />
              <p>
                <p><strong>{l s='Display product average rating on category pages and other product listings.' mod='trustmate'}</strong></p>
                <p><strong>{l s='Create a star constellation in your product lists!' mod='trustmate'}</strong></p>
              </p>
              <br />
              <hr>
              <strong>{l s='Widget settings' mod='trustmate'}:</strong>
              <div class="form-group">
                <label class="control-label col-lg-2">{l s='Widget enabled' mod='trustmate'}?</label>
                <div class="col-lg-4">
                  <span class="switch prestashop-switch">
                    <input type="radio" name="TRUSTMATE_MULTIHORNET" id="TRUSTMATE_MULTIHORNET_on" value="1" {if $TRUSTMATE_MULTIHORNET == 1}checked{/if}>
                    <label for="TRUSTMATE_MULTIHORNET_on">{l s='Yes' mod='trustmate'}</label>
                    <input type="radio" name="TRUSTMATE_MULTIHORNET" id="TRUSTMATE_MULTIHORNET_off" value="0" {if $TRUSTMATE_MULTIHORNET == 0}checked{/if}>
                    <label for="TRUSTMATE_MULTIHORNET_off">{l s='No' mod='trustmate'}</label>
                    <a class="slide-button btn"></a>
                  </span>
                </div>
                <div class="col-lg-6"></div>
              </div>
              <label>{l s='Pages' mod='trustmate'}</label>
              <p>
                <em>
                  {l s='Widget is preconfigured for standard PrestaShop theme.' mod='trustmate'}
                  {l s='If it does not show, you may need to configure it in TrustMate panel -> Integrations -> Widgets -> Multihornet.' mod='trustmate'}
                  {l s='You can also change widget placement there.' mod='trustmate'}
                </em>
              </p>
              <select name="TRUSTMATE_MULTIHORNET_PAGES">
                <option value="DisplayFooterCategory" {if $TRUSTMATE_MULTIHORNET_PAGES == 'DisplayFooterCategory'}selected{/if}>
                  {l s='category pages' mod='trustmate'} (DisplayFooterCategory hook)
                </option>
                <option value="DisplayFooter" {if $TRUSTMATE_MULTIHORNET_PAGES == 'DisplayFooter'}selected{/if}>
                  {l s='all product pages' mod='trustmate'} (DisplayFooter hook)
                </option>
              </select>
              <hr>
              <div class="col-lg-10"></div>
              <div class="col-lg-2"><button type="submit" value="1" name="save_widget" class="btn btn-default"><i class="process-icon-save"></i>{l s='Save' mod='trustmate'}</button></div>
            </div>
          </div>
        </div>

        <!-- LEMUR -->
        <div class="row">
          <div class="row mt-5">
            <div class="col-xs-12 col-sm-12"><div class="h2">Lemur</div></div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
              <div id="lemur" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                  <li data-target="#lemur" data-slide-to="0" class="active"></li>
                  <li data-target="#lemur" data-slide-to="1"></li>
                  <li data-target="#lemur" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                  <div class="item active">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/lemur_01.png">
                  </div>
                  <div class="item">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/lemur_02.png">
                  </div>
                  <div class="item">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/lemur_03.png">
                  </div>
                </div>
                <a class="left carousel-control" href="#lemur" role="button" data-slide="prev">
                  <span class="icon-prev" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#lemur" role="button" data-slide="next">
                  <span class="icon-next" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
                </a>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
              <p>{l s='Type' mod='trustmate'}: <strong>{l s='Company' mod='trustmate'}</strong></p>
              <p>{l s='Name' mod='trustmate'}: <strong>{l s='Sliding out widget with reviews' mod='trustmate'}</strong></p>
              <br />
              <p>
                <p><strong>{l s='Get your reviews noticed with this sliding widget.' mod='trustmate'}</strong></p>
                <p><strong>{l s='Lemur is a perfect way to demonstrate your average rating, and when expanded, also latest reviews and TrustMate-granted badges.' mod='trustmate'}</strong></p>
                <p><strong>{l s='What’s not to love about it?' mod='trustmate'}</strong></p>
              </p>
              <br />
              <hr>
              <strong>{l s='Widget settings' mod='trustmate'}:</strong>
              <div class="form-group">
                <label class="control-label col-lg-2">{l s='Widget enabled' mod='trustmate'}?</label>
                <div class="col-lg-4">
                  <span class="switch prestashop-switch">
                    <input type="radio" name="TRUSTMATE_LEMUR" id="TRUSTMATE_LEMUR_on" value="1" {if $TRUSTMATE_LEMUR == 1}checked{/if}>
                    <label for="TRUSTMATE_LEMUR_on">{l s='Yes' mod='trustmate'}</label>
                    <input type="radio" name="TRUSTMATE_LEMUR" id="TRUSTMATE_LEMUR_off" value="0" {if $TRUSTMATE_LEMUR == 0}checked{/if}>
                    <label for="TRUSTMATE_LEMUR_off">{l s='No' mod='trustmate'}</label>
                    <a class="slide-button btn"></a>
                  </span>
                </div>
                <div class="col-lg-6"></div>
              </div>
              <hr>
              <div class="col-lg-10"></div>
              <div class="col-lg-2"><button type="submit" value="1" name="save_widget" class="btn btn-default"><i class="process-icon-save"></i>{l s='Save' mod='trustmate'}</button></div>
            </div>
          </div>
        </div>

        <!-- OWL -->
        <div class="row">
          <div class="row mt-5">
            <div class="col-xs-12 col-sm-12"><div class="h2">Owl</div></div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
              <div id="owl" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                  <li data-target="#owl" data-slide-to="0" class="active"></li>
                  <li data-target="#owl" data-slide-to="1"></li>
                  <li data-target="#owl" data-slide-to="2"></li>
                  <li data-target="#owl" data-slide-to="3"></li>
                  <li data-target="#owl" data-slide-to="4"></li>
                  <li data-target="#owl" data-slide-to="5"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                  <div class="item active">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/owl_01.png">
                  </div>
                  <div class="item">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/owl_02.png">
                  </div>
                  <div class="item">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/owl_03.png">
                  </div>
                  <div class="item">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/owl_04.png">
                  </div>
                  <div class="item">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/owl_05.png">
                  </div>
                  <div class="item">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/owl_06.png">
                  </div>
                </div>
                <a class="left carousel-control" href="#owl" role="button" data-slide="prev">
                  <span class="icon-prev" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#owl" role="button" data-slide="next">
                  <span class="icon-next" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
                </a>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
              <p>{l s='Name' mod='trustmate'}: <strong>{l s='Widget with badges' mod='trustmate'}</strong></p>
              <br />
              <p>
                <p><strong>{l s='Want to present TrustMate-granted awards in style?' mod='trustmate'}</strong></p>
                <p><strong>{l s='Owl’s here to help!' mod='trustmate'}</strong></p>
              </p>
              <br />
              <hr>
              <strong>{l s='Widget settings' mod='trustmate'}:</strong>
              <div class="form-group">
                <label class="control-label col-lg-2">{l s='Widget enabled' mod='trustmate'}?</label>
                <div class="col-lg-4">
                  <span class="switch prestashop-switch">
                    <input type="radio" name="TRUSTMATE_OWL" id="TRUSTMATE_OWL_on" value="1" {if $TRUSTMATE_OWL == 1}checked{/if}>
                    <label for="TRUSTMATE_OWL_on">{l s='Yes' mod='trustmate'}</label>
                    <input type="radio" name="TRUSTMATE_OWL" id="TRUSTMATE_OWL_off" value="0" {if $TRUSTMATE_OWL == 0}checked{/if}>
                    <label for="TRUSTMATE_OWL_off">{l s='No' mod='trustmate'}</label>
                    <a class="slide-button btn"></a>
                  </span>
                </div>
                <div class="col-lg-6"></div>
              </div>
              <hr>
              <div class="col-lg-10"></div>
              <div class="col-lg-2"><button type="submit" value="1" name="save_widget" class="btn btn-default"><i class="process-icon-save"></i>{l s='Save' mod='trustmate'}</button></div>
            </div>
          </div>
        </div>

        <!-- BEE -->
        <div class="row">
          <div class="row mt-5">
            <div class="col-xs-12 col-sm-12"><div class="h2">Bee</div></div>
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
              <div id="bee" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                  <li data-target="#bee" data-slide-to="0" class="active"></li>
                  <li data-target="#bee" data-slide-to="1"></li>
                  <li data-target="#bee" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                  <div class="item active">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/bee_01.png">
                  </div>
                  <div class="item">
                    <img src="https://cdn.trustmate.io/platforms/v2/en/bee_02.png">
                  </div>
                </div>
                <a class="left carousel-control" href="#bee" role="button" data-slide="prev">
                  <span class="icon-prev" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#bee" role="button" data-slide="next">
                  <span class="icon-next" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
                </a>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
              <p>{l s='Type' mod='trustmate'}: <strong>{l s='Company' mod='trustmate'}</strong></p>
              <p>{l s='Name' mod='trustmate'}: <strong>{l s='Company rating - top bar' mod='trustmate'}</strong></p>
              <br />
              <p>
                <p><strong>{l s='Show off the number of reviews and display the average rating with this simple widget.' mod='trustmate'}</strong></p>
                <p><strong>{l s='Buyers will know right away that they are dealing with a trustworthy company when they enter your store.' mod='trustmate'}</strong></p>
              </p>
              <br />
              <hr>
              <strong>{l s='Widget settings' mod='trustmate'}:</strong>
              <div class="form-group">
                <label class="control-label col-lg-2">{l s='Widget enabled' mod='trustmate'}?</label>
                <div class="col-lg-4">
                  <span class="switch prestashop-switch">
                    <input type="radio" name="TRUSTMATE_BEE" id="TRUSTMATE_BEE_on" value="1" {if $TRUSTMATE_BEE == 1}checked{/if}>
                    <label for="TRUSTMATE_BEE_on">{l s='Yes' mod='trustmate'}</label>
                    <input type="radio" name="TRUSTMATE_BEE" id="TRUSTMATE_BEE_off" value="0" {if $TRUSTMATE_BEE == 0}checked{/if}>
                    <label for="TRUSTMATE_BEE_off">{l s='No' mod='trustmate'}</label>
                    <a class="slide-button btn"></a>
                  </span>
                </div>
                <div class="col-lg-6"></div>
              </div>
              <hr>
              <div class="col-lg-10"></div>
              <div class="col-lg-2"><button type="submit" value="1" name="save_widget" class="btn btn-default"><i class="process-icon-save"></i>{l s='Save' mod='trustmate'}</button></div>
            </div>
          </div>
        </div>
    </form>
{/block}
{block name="after"}{/block}
