<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SAS Comptoir du Code
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SAS Comptoir du Code is strictly forbidden.
 * In order to obtain a license, please contact us: contact@comptoirducode.com
 *
 * @author    Vincent - Comptoir du Code
 * @copyright Copyright(c) 2015-2023 SAS Comptoir du Code
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * @package   cdc_googletagmanager
 */

if (!defined('_PS_VERSION_')) { exit; }

include_once(_CDCGTM_DIR_.'/services/Gtm_Product.php');

/**
 * Represent GTM Datalayer Product
 */
class Gtm_DataLayerProduct
{
	public $name;
	public $id;
    public $reference;
	public $price;
	public $price_tax_exc;
    public $price_tax_inc;
	public $brand;
	public $category;
	public $item_category;
    public $item_category2;
    public $item_category3;
    public $item_category4;
	public $variant;
    public $list;
    public $position;
	public $quantity;

    /**
     * Gtm_DataLayerProduct constructor.
     * @param $product
     * @param int $id_product_attribute
     */
	public function __construct($gtm, $product, $list = null) {
        $product = $product instanceof Product ? $product : (object)$product;
        $variant = null;

        // set correct ID
        if(empty($product->id)) {
            $product->id = !empty($product->id_product) ? $product->id_product : 0;
            if(!$product->id || !is_numeric($product->id)) {
                // error no product id
                throw new Exception("[DataLayerProduct] Given product is not valid: ".json_encode($product));
            }
        }

        $id_product_attribute = !empty($product->id_product_attribute) ? $product->id_product_attribute : PrestashopUtils::getProductAttributeId($product);
        if($id_product_attribute) {
            $variant = new Combination($id_product_attribute);
            if(!Validate::isLoadedObject($variant)) {
                $variant = null;
                $id_product_attribute = 0;
            }
        }

        // product ID
        $this->id = (string) Gtm_Product::getInstance()->getProductId($product, $variant);


        // reference
        if(!empty($variant->reference)) {
            $this->reference = (string) $variant->reference;
        } else {
            $this->reference = (string) $product->reference;
        }

        // product name
        $this->name = Gtm_Product::getInstance()->getProductName($product);
        $this->link = Gtm_Product::getInstance()->getProductName($product, "link_rewrite");

        // variant name
        if($id_product_attribute) {
            $this->variant = $gtm->cleanString(PrestashopUtils::getAttributeSmall($id_product_attribute, $gtm->getDataLanguage()));
        }

        // if list is set use it
        $this->list = !empty($list['name']) ? $list['name'] : null;
        $this->position = !empty($list['index']) ? $list['index'] : null;

        // category
        if(!empty($list['name'])) {
            $this->category = $list['name'];
        } else {
            $this->category = $gtm->getCategoryName($product->id_category_default);
        }

        // categories (by level)
        $category = new Category($product->id_category_default);
        $this->item_category = $gtm->getPageCategoryNameHierarchy($category, 1);
        $this->item_category2 = $gtm->getPageCategoryNameHierarchy($category, 2);
        $this->item_category3 = $gtm->getPageCategoryNameHierarchy($category, 3);
        $this->item_category4 = $gtm->getPageCategoryNameHierarchy($category, 4);

        // manufacturer
        if($product->id_manufacturer) {
            $manufacturer_name = $gtm->cleanString(Manufacturer::getNameById((int)$product->id_manufacturer));
            $this->brand = $manufacturer_name;
        }

        // order quantity
        if(!empty($product->product_quantity)) {
            $this->quantity = (int) $product->product_quantity;
        }
        // cart quantity
        elseif(!empty($product->cart_quantity)) {
            $this->quantity = (int) $product->cart_quantity;
        }
        // stock
        elseif($gtm->display_product_stock) {
            if(!empty($product->quantity)) {
                $this->quantity = (int)$product->quantity;
            } else {
                $this->quantity = (int) StockAvailable::getQuantityAvailableByProduct($product->id, $id_product_attribute);
            }
        }
        // fallback
        if(empty($this->quantity)) {
            $this->quantity = 1;
        }

        // product price
        if(!empty($product->id_order) && !empty($product->unit_price_tax_excl)) {
            // price from order
            $this->price_tax_inc = (string) $product->unit_price_tax_incl;
            $this->price_tax_exc = (string) $product->unit_price_tax_excl;
        } else {
            // compute price
            $qtityForPrice = (!empty($product->product_quantity) || !empty($product->cart_quantity)) ? $this->quantity : 1;
            //$this->price = (string) Product::getPriceStatic($product->id, true, $id_product_attribute, _CDCGTM_PRICE_DECIMAL_, null, false, true, $qtityForPrice);
            $this->price_tax_inc = (string) Product::getPriceStatic($product->id, true, $id_product_attribute, _CDCGTM_PRICE_DECIMAL_, null, false, true, $qtityForPrice);
            $this->price_tax_exc = (string) Product::getPriceStatic($product->id, false, $id_product_attribute, _CDCGTM_PRICE_DECIMAL_, null, false, true, $qtityForPrice);
        }

        $this->price = $gtm->main_price_with_tax ? $this->price_tax_inc : $this->price_tax_exc;
        if(!$gtm->display_product_price_tax_detail) {
            $this->price_tax_inc = null;
            $this->price_tax_exc = null;
        }

        // wholesale price
        if($gtm->display_wholesale_price) {
            if ($variant && $variant->wholesale_price > 0) {
                $wholesale_price = $variant->wholesale_price;
            } else {
                $wholesale_price = $product->wholesale_price;
            }
            $this->wholesale_price = (string)round((float)$wholesale_price, _CDCGTM_PRICE_DECIMAL_);
        }

        return $this;
    }

	public function removeNull()
	{
		$properties = get_object_vars($this);
		foreach ($properties as $p_key => $p_val) {
			if(is_null($p_val)) {
				unset($this->$p_key);
			}
		}
	}
}
