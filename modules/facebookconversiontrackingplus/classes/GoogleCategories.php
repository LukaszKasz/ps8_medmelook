<?php
/**
 * Facebook Conversion Pixel Tracking Plus
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2014
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category Marketing & Advertising
 * Registered Trademark & Property of smart-modules.com
 * ***************************************************
 * *     Facebook Conversion Trackcing Pixel Plus    *
 * *          http://www.smart-modules.com           *
 * ***************************************************
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class GoogleCategories
{
    const MODULE_NAME = 'facebookconversiontrackingplus';
    private static $lang_code;
    private static $fctp;
    private static $local_path;
    private static $context;

    private $lang;

    public function __construct()
    {
        self::init();
    }

    public static function init()
    {
        if (!isset(self::$fctp)) {
            self::loadModuleInstance();
        }
        if (!isset(self::$local_path)) {
            self::$local_path = _PS_MODULE_DIR_ . self::MODULE_NAME . '/';
        }
        if (!isset(self::$lang_code)) {
            // Always assign an array of potential language codes
            self::$lang_code = self::getLangCode();
        }
        if (!isset(self::$context)) {
            self::$context = Context::getContext();
        }

        // Ensure the downloads directory path is used when checking for the file
        $langCodes = self::$lang_code; // Expected to be an array
        $downloadPath = self::$local_path . 'downloads/';

        if (is_array($langCodes)) {
            // Check if the primary language file exists; if not, download it.
            $google_categories_file = $downloadPath . $langCodes[0] . '.txt';
            if (!file_exists($google_categories_file) || filesize($google_categories_file) == 0) {
                self::downloadGPT($langCodes);
            }
            // Iterate over the possible codes until a file is found.
            foreach ($langCodes as $code) {
                $file = $downloadPath . $code . '.txt';
                if (file_exists($file) && filesize($google_categories_file) > 0) {
                    // Set the primary language code as a string for later use.
                    self::$lang_code = $code;
                    break;
                }
            }
        }
    }

    private static function loadModuleInstance()
    {
        self::$fctp = Module::getInstanceByName(self::MODULE_NAME);
    }

    private static function getLangCode($forced_code = '')
    {
        // Determine the base language code
        if ($forced_code !== '') {
            $lang_code = $forced_code;
        } else {
            $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
            $lang_code = self::isPS17() ? $lang->locale : $lang->language_code;
        }

        // If we couldn’t compute a language code, return a default array.
        if (empty($lang_code)) {
            return ['en-US'];
        }

        // Normalize the language code.
        if ($lang_code !== 'en') {
            $parts = explode('-', $lang_code);
            if (!isset($parts[1])) {
                $parts[1] = $parts[0];
            }
            $parts[1] = Tools::strtoupper($parts[1]);
            $lang_code = implode('-', $parts);
        } else {
            $lang_code = 'en-US';
        }
        $lang_code = str_replace('_', '-', $lang_code);

        // Return an array with the computed code as primary and a fallback.
        return array_unique([$lang_code, 'en-US']);
    }

    private static function downloadGPT($lang_code)
    {
        $base_url = 'https://www.google.com/basepages/producttype/taxonomy-with-ids.';
        foreach ($lang_code as $lang) {
            $file_exists = self::checkHeaders($base_url . $lang . '.txt');

            //            echo 'Checking: ' . $base_url . $lang . '.txt >> ('.(int)$file_exists.')'."<br>\n";
            if ($file_exists !== false && $file_exists < 400) {
                $contents = Tools::file_get_contents($base_url . $lang . '.txt');
                if ($contents !== false && $contents != '') {
                    self::$lang_code = $lang;
                    break;
                }
            }
        }
        $file = self::$local_path . 'downloads/' . self::$lang_code . '.txt';
        if (!isset($contents)) {
            self::$context->controller->errors[] = self::$fctp->l('Error: Couldn\'t locate any file to download the Taxonomy List for the Google Categories');

            return;
        }
        $h = fopen($file, 'w+');
        if ($h === false) {
            self::$context->controller->errors[] = Tools::displayError(self::$fctp->l('Couldn\'t create the file:') . '<br/> downloads/' . self::$lang_code . '.txt. <br />' . self::$fctp->l('Please review the file and folder writting permissions.'));
        }
        if (fwrite($h, $contents) === false) {
            self::$context->controller->errors[] = Tools::displayError(self::$fctp->l('Couldn\'t write to') . ' ' . $file . ' ' . self::$fctp->l('please review the file permissions') . '.');
        }
    }

    private static function checkHeaders($url)
    {
        if (function_exists('curl_version')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (is_numeric($http_code)) {
                return $http_code;
            }
        } else {
            // If Curl is not available on the Server
            $headers = get_headers($url, 1);
            $code = explode(' ', $headers[0]);

            return $code[1];
        }
    }

    private static function isPS17()
    {
        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            return false;
        } else {
            return true;
        }
    }

    public function buildGoogleCategories()
    {
        $output = '';
        $file = self::$local_path . 'downloads/' . self::$lang_code . '.txt';
        if (file_exists($file)) {
            self::$context->smarty->assign([
                'fpf_file_exists' => file_exists($file),
                'fpf_file_date' => date('d-m-Y', filemtime($file)),
            ]);
            $output .= self::$fctp->display(self::$fctp->name, 'views/templates/admin/sm-subtree-vars.tpl');
            if (file_exists($file) !== false) {
                $output .= $this->buildCategoryTree();
                $output .= $this->displayAutoComplete();
            }
        }

        return $output;
    }

    /* Generate the autocomplete necessary data */
    private function displayAutoComplete()
    {
        self::$context->smarty->assign(
            [
                'google_categories' => json_encode($this->prepareGoogleTaxonomies(self::$lang_code)),
                'gpt_url' => trim(self::$local_path . '/json/get_products_taxonomy_list.php?lang_code=' . self::$lang_code),
            ]
        );
        $output = self::$fctp->display(self::$fctp->name, 'views/templates/admin/autocomplete.tpl');

        return $output;
    }

    public function buildCategoryTree()
    {
        $options = [];
        $type = 'google_categories';
        $catTree = $this->getCategoryTree();
        $inputName = 'categoryBox';
        $treeTitle = self::$fctp->l('Google Product Category Association');
        $category_box = $this->getCategoryAssigned();
        $mainId = 'tree_categories_panel';
        $submitName = 'assigngoogletaxonomies';
        self::$context->smarty->assign(
            [
                'main_id' => $mainId,
                'tree_submit' => $submitName,
                'tree_type' => $type,
                'fpf_catTree' => $catTree,
                'input_name' => $inputName,
                'tree_title' => $treeTitle,
                'is_ajax' => false, // Was $ajax,
            ]
        );
        self::$context->smarty->assign(
            [
                'google_cat' => $category_box,
                'select_options' => $options,
            ]
        );

        return self::$fctp->display(self::$fctp->name, 'views/templates/admin/_configure/helpers/tree/customtree.tpl');
    }

    private function getCategoryTree()
    {
        $cache_id = 'FPF_getCategoryTree';
        if (!Cache::isStored($cache_id)) {
            $sql = 'SELECT id_category, id_parent, level_depth, name, google_taxonomy_id, is_root_category, active, excluded FROM ' . _DB_PREFIX_ . 'category LEFT JOIN ' . _DB_PREFIX_ . 'category_lang AS cl USING (id_category) LEFT JOIN ' . _DB_PREFIX_ . 'category_shop AS cs USING (id_category)  LEFT JOIN ' . _DB_PREFIX_ . 'fpf_cat USING (id_category) WHERE cs.id_shop = ' . (int) self::$context->shop->id . ' AND cl.id_lang = ' . (int) self::$context->language->id . ' GROUP BY id_category ORDER BY `' . _DB_PREFIX_ . 'category`.`id_parent` ASC, `' . _DB_PREFIX_ . 'category`.`id_category` ASC';
            if (!($results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(pSQL($sql)))) {
                return self::$fctp->l('No categories Found');
            } else {
                $min_depth = '';
                $id_root = 0;
                $categories = [];
                $childs = [];
                // Look and remove categories prior to root
                foreach ($results as $result) {
                    if ($result['is_root_category'] == 1) {
                        $min_depth = $result['level_depth'];
                        $id_root = $result['id_parent'];
                        break;
                    }
                }
                // Look and remove categories prior to root
                foreach ($results as $result) {
                    if ($result['is_root_category'] == 1 || $result['level_depth'] > $min_depth) {
                        $categories[] = (object) $result;
                    }
                }
                // Free memory
                unset($results);
                foreach ($categories as $category) {
                    $childs[$category->id_parent][] = $category;
                }
                foreach ($categories as $category) {
                    if (isset($childs[$category->id_category])) {
                        $category->childs = $childs[$category->id_category];
                    }
                }
                $ret = (object) $childs[$id_root];
                Cache::store($cache_id, $ret);
            }
        }

        return Cache::retrieve($cache_id);
    }

    private function getCategoryAssigned($column = 'google_taxonomy_id')
    {
        $sql = 'SELECT id_category, ' . $column . ' FROM ' . _DB_PREFIX_ . 'fpf_cat WHERE id_shop = ' . (int) self::$context->shop->id;
        $results = Db::getInstance()->executeS($sql);
        if (count($results) > 0) {
            $ret = [];
            $google_taxonomy = false;
            if ($column == 'google_taxonomy_id') {
                $google_taxonomy = $this->prepareGoogleTaxonomies(self::$lang_code, 'associative');
            }
            foreach ($results as $result) {
                if ($google_taxonomy !== false) {
                    if (isset($result[$column]) && $result[$column] != '' && isset($google_taxonomy[$result[$column]])) {
                        $ret[$result['id_category']] = ['name' => $result[$column] . ' - ' . $google_taxonomy[$result[$column]], 'id' => $result[$column], 'categoryname' => $google_taxonomy[$result[$column]]];
                    } else {
                        $ret[$result['id_category']] = ['name' => '', 'id' => ''];
                    }
                } else {
                    $ret[$result['id_category']] = [$column => $result[$column]];
                }
            }

            return $ret;
        } elseif ($results === false) {
            self::$context->controller->errors[] = 'Error: ' . Db::getInstance()->getMsgError();
        }

        return false;
    }

    private function prepareGoogleTaxonomies($lang_code, $return_type = '')
    {
        if (file_exists(self::$local_path . 'downloads/' . $lang_code . '.txt')) {
            $i = 0;
            $handle = fopen(self::$local_path . 'downloads/' . $lang_code . '.txt', 'r');
            if ($handle) {
                $taxonomies = [];
                while (($line = fgets($handle)) !== false) {
                    // Skip the First line
                    if ($i > 0) {
                        // process the line read.
                        $line = explode(' - ', $line);
                        if ($return_type == 'associative') {
                            // Return values like google_taxonomy_id > Description
                            $taxonomies[$line['0']] = $line['1'];
                        } else {
                            $taxonomies[$i - 1]['label'] = str_replace("\n", '', $line['1']);
                            $taxonomies[$i - 1]['value'] = str_replace("\n", '', $line['0']);
                        }
                    }
                    ++$i;
                }
                fclose($handle);

                return $taxonomies;
            } else {
                // error opening the file.
                return 'Error';
            }
        } else {
            self::$context->controller->_errors[] = self::$fctp->l('Can\'t find the Google Taxonomy file');

            return [];
        }
    }

    public function assignGoogleTaxonomies()
    {
        if (isset(self::$fctp)) {
            self::loadModuleInstance();
        }
        $id_shop = (int) self::$context->shop->id;
        $google_cat_id = Tools::getValue('google_cat_id');
        $mcategories = Tools::getValue('categoryBox');
        $categories = [];
        $values = [];
        if (Tools::getIsset('google_cat')) {
            $categories = $this->getGoogleTaxonomiesFromFields(Tools::getValue('google_cat'));
            foreach ($google_cat_id as $key => $value) {
                // If not in the massive update add it
                if ($value != '') {
                    $categories[] = ['id_category' => $key, 'google_taxonomy_id' => $value];
                }
            }
        }
        // The Massive update, if set.
        $mass_id = Tools::getValue('massiveupdate_id');
        if ($mass_id != '' && Tools::getIsset('categoryBox')) {
            if (is_array($mcategories)) {
                foreach ($mcategories as $mcategory) {
                    $categories[] = ['id_category' => $mcategory, 'google_taxonomy_id' => $mass_id];
                }
            }
        }
        // $categories = implode(', ', $categories);
        foreach ($categories as $category) {
            $values[] = '(' . (int) $category['id_category'] . ', ' . (int) $id_shop . ', ' . ($category['google_taxonomy_id'] != '' ? (int) $category['google_taxonomy_id'] : '0') . ')';
        }
        if (!empty($values) && count($values) > 0) {
            $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'fpf_cat (id_category, id_shop, google_taxonomy_id) VALUES ' . implode(', ', $values) . ' ON DUPLICATE KEY UPDATE google_taxonomy_id = VALUES(google_taxonomy_id)';
            if (!Db::getInstance()->execute($sql)) {
                self::$context->controller->errors[] = Db::getInstance()->getMsgError();
            }
        }
    }

    private function getGoogleTaxonomiesFromFields($fields)
    {
        $return = [];
        $google_taxonomies = $this->prepareGoogleTaxonomies(self::$lang_code, 'associative');
        $google_cat = array_keys($google_taxonomies);
        if (count($google_cat) > 0) {
            foreach ($fields as $id_cat => $id_google_cat) {
                preg_match("/^[^\d]*(\d+)/", $id_google_cat, $cat_id);
                if (isset($cat_id[0]) && in_array($cat_id[0], $google_cat)) {
                    $return[] = ['id_category' => $id_cat, 'google_taxonomy_id' => $cat_id[0]];
                } elseif ($id_google_cat == '') {
                    $return[] = ['id_category' => $id_cat, 'google_taxonomy_id' => ''];
                }
            }

            return $return;
        }
    }

    public static function getCategoryNameById($id_category)
    {
        return self::getGoogleProductCategoryId($id_category);
    }

    private static function getGoogleProductCategoryId($id_category)
    {
        return Db::getInstance()->getValue('SELECT google_taxonomy_id FROM ' . _DB_PREFIX_ . 'fpf_cat WHERE id_category = ' . (int) $id_category);
    }
}
