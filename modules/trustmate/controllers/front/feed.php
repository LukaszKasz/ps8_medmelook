<?php
/**
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
 *  @copyright  2007-2021 PrestaShop SA
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  @version  Release: $Revision$
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class TrustMateFeedModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $language = $this->context->language;
        $shop = $this->context->shop;

        $products = Product::getSimpleProducts($language->id);

        foreach ($products as &$product) {
            $cover = Image::getCover($product['id_product']);
            $product['image_link'] = $this->context->link->getImageLink(
                Tools::link_rewrite($product['name']),
                $cover['id_image'],
                ImageType::getFormattedName('small')
            );
        }

        $this->context->smarty->assign(array(
            'title' => $shop->name,
            'products' => $products,
        ));

        $template = version_compare(_PS_VERSION_, '1.7.0', '>=')
            ? 'module:trustmate/views/templates/front/feed.tpl'
            : 'feed.tpl';

        $this->setTemplate($template);
    }

    public function display()
    {
        header("Content-Type:text/xml; charset=utf-8");
        echo $this->context->smarty->fetch($this->template);
    }
}
