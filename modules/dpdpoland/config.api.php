<?php
/**
* 2019 DPD Polska Sp. z o.o.
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* prestashop@dpd.com.pl so we can send you a copy immediately.
*
*  @author    DPD Polska Sp. z o.o.
*  @copyright 2019 DPD Polska Sp. z o.o.
*  @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*  International Registered Trademark & Property of DPD Polska Sp. z o.o.
*/

if (!defined('_PS_VERSION_'))
	exit;

/* URI constants */

if (!defined('_DPDPOLAND_MODULE_URI_'))
    /**
     * URL to module directory
     */
	define('_DPDPOLAND_MODULE_URI_', _MODULE_DIR_.'dpdpoland/');

if (!defined('_DPDPOLAND_LIBRARIES_DIR_'))
    /**
     * Path to module libraries directory
     */
	define('_DPDPOLAND_LIBRARIES_DIR_', _PS_MODULE_DIR_.'dpdpoland/libraries/');

if (!defined('_DPDPOLAND_CSS_URI_'))
    /**
     * URL to module CSS files
     */
	define('_DPDPOLAND_CSS_URI_', _DPDPOLAND_MODULE_URI_.'css/');

if (!defined('_DPDPOLAND_JS_URI_'))
    /**
     * URL to module JS files
     */
	define('_DPDPOLAND_JS_URI_', _DPDPOLAND_MODULE_URI_.'js/');

if (!defined('_DPDPOLAND_IMG_URI_'))
    /**
     * URL to module images
     */
	define('_DPDPOLAND_IMG_URI_', _DPDPOLAND_MODULE_URI_.'img/');

if (!defined('_DPDPOLAND_AJAX_URI_'))
    /**
     * URL to module AJAX file
     */
	define('_DPDPOLAND_AJAX_URI_', _DPDPOLAND_MODULE_URI_.'dpdpoland.ajax.php');

if (!defined('_DPDPOLAND_PDF_URI_'))
    /**
     * URL to module file used for PDF files printing
     */
	define('_DPDPOLAND_PDF_URI_', _DPDPOLAND_MODULE_URI_.'dpdpoland.pdf.php');

/* Directories constants */

if (!defined('_DPDPOLAND_CONTROLLERS_DIR_'))
    /**
     * Path to module controllers directory
     */
	define('_DPDPOLAND_CONTROLLERS_DIR_', dirname(__FILE__).'/controllers/');

if (!defined('_DPDPOLAND_SERVICES_DIR_'))
    /**
     * Path to module services directory
     */
    define('_DPDPOLAND_SERVICES_DIR_', dirname(__FILE__).'/services/');

if (!defined('_DPDPOLAND_TPL_DIR_'))
    /**
     * Path to module templates directory
     */
	define('_DPDPOLAND_TPL_DIR_', dirname(__FILE__).'/views/templates/');

if (!defined('_DPDPOLAND_CLASSES_DIR_'))
    /**
     * Path to module classes directory
     */
	define('_DPDPOLAND_CLASSES_DIR_', dirname(__FILE__).'/classes/');

if (!defined('_DPDPOLAND_MODULE_DIR_'))
    /**
     * Path to module directory
     */
	define('_DPDPOLAND_MODULE_DIR_', _PS_MODULE_DIR_.'dpdpoland/');

if (!defined('_DPDPOLAND_IMG_DIR_'))
    /**
     * Path to module images directory
     */
	define('_DPDPOLAND_IMG_DIR_', _DPDPOLAND_MODULE_DIR_.'img/');

/*  */

if (!defined('_DPDPOLAND_DEBUG_MODE_'))
    /**
     * Is debug mode enabled or not
     */
	define('_DPDPOLAND_DEBUG_MODE_', false);

if (!defined('_DPDPOLAND_PRICE_RULE_DB_'))
    /**
     * Database table name for price rules data
     */
	define('_DPDPOLAND_PRICE_RULE_DB_', 'dpdpoland_price_rule');

if (!defined('_DPDPOLAND_PAYER_NUMBERS_DB_'))
    /**
     * Database table name for payer numbers
     */
	define('_DPDPOLAND_PAYER_NUMBERS_DB_', 'dpdpoland_payer_number');

if (!defined('_DPDPOLAND_COUNTRY_DB_'))
    /**
     * Database table name for available countries data
     */
	define('_DPDPOLAND_COUNTRY_DB_', 'dpdpoland_country');

if (!defined('_DPDPOLAND_MANIFEST_DB_'))
    /**
     * Database table name for manifests data
     */
	define('_DPDPOLAND_MANIFEST_DB_', 'dpdpoland_manifest');

if (!defined('_DPDPOLAND_SENDER_ADDRESS_DB_'))
    /**
     * Database table name for sender address data
     */
    define('_DPDPOLAND_SENDER_ADDRESS_DB_', 'dpdpoland_sender_address');

if (!defined('_DPDPOLAND_PACKAGE_DB_'))
    /**
     * Database table name for packages data
     */
	define('_DPDPOLAND_PACKAGE_DB_', 'dpdpoland_package');

if (!defined('_DPDPOLAND_PARCEL_DB_'))
    /**
     * Database table name for parcels data
     */
	define('_DPDPOLAND_PARCEL_DB_', 'dpdpoland_parcel');

if (!defined('_DPDPOLAND_PARCEL_PRODUCT_DB_'))
    /**
     * Database table name for parcel products data
     */
	define('_DPDPOLAND_PARCEL_PRODUCT_DB_', 'dpdpoland_parcel_product');


if (!defined('_DPDPOLAND_CARRIER_DB_'))
    /**
     * Database table name for carriers assignations to their references on PS 1.4
     */
	define('_DPDPOLAND_CARRIER_DB_', 'dpdpoland_carrier');

if (!defined('_DPDPOLAND_PICKUP_HISTORY_DB_'))
    /**
     * Database table name for sender address data
     */
    define('_DPDPOLAND_PICKUP_HISTORY_DB_', 'dpdpoland_pickup_history');

if (!defined('_DPDPOLAND_CSV_DELIMITER_'))
    /**
     * CSV file delimiter
     */
	define('_DPDPOLAND_CSV_DELIMITER_', ';');

if (!defined('_DPDPOLAND_CSV_FILENAME_'))
    /**
     * CSV file name
     */
	define('_DPDPOLAND_CSV_FILENAME_', 'dpdpoland');

if (!defined('_DPDPOLAND_STANDARD_ID_'))
    /**
     * Standard service (carrier) ID
     */
	define('_DPDPOLAND_STANDARD_ID_', 1);

if (!defined('_DPDPOLAND_STANDARD_COD_ID_'))
    /**
     * Standard COD service (carrier) ID
     */
	define('_DPDPOLAND_STANDARD_COD_ID_', 2);

if (!defined('_DPDPOLAND_CLASSIC_ID_'))
    /**
     * Classic / international service (carrier) ID
     */
	define('_DPDPOLAND_CLASSIC_ID_', 3);

if (!defined('_DPDPOLAND_PUDO_ID_'))
    /**
     * Pickup point service (carrier) ID
     */
    define('_DPDPOLAND_PUDO_ID_', 4);

if (!defined('_DPDPOLAND_PUDO_COD_ID_'))
    /**
     * Pickup point service (carrier) with COD ID
     */
    define('_DPDPOLAND_PUDO_COD_ID_', 5);

if (!defined('_DPDPOLAND_CURRENCY_ISO_'))
    /**
     * DPD Poland currency
     */
	define('_DPDPOLAND_CURRENCY_ISO_', 'PLN');

if (!defined('_DPDPOLAND_DEFAULT_WEIGHT_UNIT_'))
    /**
     * Weight unit
     */
	define('_DPDPOLAND_DEFAULT_WEIGHT_UNIT_', 'kg');

if (!defined('_DPDPOLAND_DEFAULT_DIMENSION_UNIT_'))
    /**
     * Dimension unit
     */
	define('_DPDPOLAND_DEFAULT_DIMENSION_UNIT_', 'cm');

if (!defined('_DPDPOLAND_DIMENTION_WEIGHT_DIVISOR_'))
    /**
     * Weight divisor
     */
	define('_DPDPOLAND_DIMENTION_WEIGHT_DIVISOR_', 6000);

if (!defined('_DPDPOLAND_TRACKING_URL_'))
    /**
     * Tracking URL
     */
	define('_DPDPOLAND_TRACKING_URL_',
		'https://tracktrace.dpd.com.pl/parcelDetails?typ=1&p1=@');


if (!defined('_DPDPOLAND_TRACKING_URL_WITHOUT_AT_'))
    /**
     * Tracking URL
     */
    define('_DPDPOLAND_TRACKING_URL_WITHOUT_AT_',
        'https://tracktrace.dpd.com.pl/parcelDetails?typ=1&p1=');

if (!defined('_DPDPOLAND_PRICES_ZIP_URL_'))
    /**
     * Information about prices ZIP file URL address
     */
	define('_DPDPOLAND_PRICES_ZIP_URL_', 'https://www.dpd.com/pl/pl/oferta-dla-firm/warunki-wysylki/warunki-wykonywania-uslug-krajowych/');

if (!defined('_DPDPOLAND_CONTENT_HEADER_URL_'))
    /**
     * Link to DPD website
     */
	define('_DPDPOLAND_CONTENT_HEADER_URL_', 'http://www.dpd.com.pl');

if (!defined('_DPDPOLAND_SUPPORT_URL_'))
    /**
     * Support form URL address
     */
	define('_DPDPOLAND_SUPPORT_URL_', 'https://addons.prestashop.com/en/write-to-developper?id_product=17924');

if (!defined('_DPDPOLAND_REFERENCE3_'))
    /**
     * Reference #3 content
     */
	define('_DPDPOLAND_REFERENCE3_', 'PSMODUL#');

if (!defined('_DPDPOLAND_COOKIE_'))
    /**
     * DPD module cookie name
     */
	define('_DPDPOLAND_COOKIE_', 'dpdpoland_cookie');