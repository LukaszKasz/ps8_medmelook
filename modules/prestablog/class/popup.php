<?php
/**
 * 2008 - 2024 (c) Prestablog
 *
 * MODULE PrestaBlog
 *
 * @author    Prestablog
 * @copyright Copyright (c) permanent, Prestablog
 * @license   Commercial
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class PopupClass extends ObjectModel
{
    public $name = 'popup';
    public $id;
    public $id_prestablog_popup;
    public $date_start;
    public $date_stop;
    public $height = 460;
    public $width = 700;
    public $delay = 500;
    public $expire = 1;
    public $expire_ratio = 86400;
    public $theme = 'colorpicker';
    public $actif = 1;
    public $content;
    public $title;
    public $restriction_rules = 0;
    public $restriction_pages;
    public $footer = 1;
    public $pop_colorpicker_content;
    public $pop_colorpicker_modal;
    public $pop_colorpicker_btn;
    public $pop_colorpicker_btn_border;
    public $pop_opacity_content = 1;
    public $pop_opacity_modal = 1;
    public $pop_opacity_btn = 1;
    public $actif_home = 0;

    protected $table = 'prestablog_popup';
    protected $identifier = 'id_prestablog_popup';

    public static $definition = [
        'table' => 'prestablog_popup',
        'primary' => 'id_prestablog_popup',
        'multilang' => true,
        'fields' => [
            'date_start' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true],
            'date_stop' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true],
            'height' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true],
            'width' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true],
            'delay' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true],
            'expire' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true],
            'expire_ratio' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true],
            'theme' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true,
                'size' => 255,
            ],
            'restriction_rules' => [
                'type' => self::TYPE_INT,
                'validate' => 'isunsignedInt',
                'required' => true,
            ],
            'restriction_pages' => ['type' => self::TYPE_STRING,  'validate' => 'isString'],
            'footer' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'actif' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
            'content' => [
                'type' => self::TYPE_HTML,
                'validate' => 'isString',
                'required' => true,
                'lang' => true,
            ],
            'title' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true,
                'lang' => true,
                'size' => 255,
            ],
            'pop_colorpicker_content' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'lang' => false,
                'size' => 255,
            ],
            'pop_colorpicker_modal' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'lang' => false,
                'size' => 255,
            ],
            'pop_colorpicker_btn' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'lang' => false,
                'size' => 255,
            ],
            'pop_colorpicker_btn_border' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'lang' => false,
                'size' => 255,
            ],
            'pop_opacity_content' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'lang' => false,
                'size' => 255,
            ],
            'pop_opacity_modal' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'lang' => false,
                'size' => 255,
            ],
            'pop_opacity_btn' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'lang' => false,
                'size' => 255,
            ],
            'actif_home' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true],
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null, $translator = null)
    {
        parent::__construct($id, $id_lang, $id_shop, $translator);

        if (_PS_VERSION_ >= '1.7') {
            $this->translator = Context::getContext()->getTranslator();
        }
    }

    public function copyFromPost()
    {
        $object = $this;
        $table = $this->table;

        foreach ($_POST as $key => $value) {
            if (property_exists($object, $key) && $key != 'id_' . $table) {
                if ($key == 'passwd' && Tools::getValue('id_' . $table) && empty($value)) {
                    continue;
                }
                if ($key == 'passwd' && !empty($value)) {
                    $value = Tools::encrypt($value);
                }
                $object->{$key} = Tools::getValue($key);
            }
        }
        // Multilingual fields
        $rules = call_user_func([get_class($object), 'getValidationRules'], get_class($object));
        if (count($rules['validateLang'])) {
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                foreach (array_keys($rules['validateLang']) as $field) {
                    if (Tools::getIsset($field . '_' . (int) $language['id_lang'])) {
                        $object->{$field}[(int) $language['id_lang']] = Tools::getValue(
                            $field . '_' . (int) $language['id_lang']
                        );
                    }
                }
            }
        }
    }

    public static function popupContent($params)
    {
        if (isset($params['adminPreview']) && $params['adminPreview']) {
            return html_entity_decode($params['return'], ENT_QUOTES, 'UTF-8');
        }

        return $params['return'];
    }

    public function update($null_values = false)
    {
        $id = (int) $this->id_prestablog_popup;
        $context = Context::getContext();

        // EDIT FOR SHOP
        $liste_shop = [];
        $check_shop = [(int) $context->shop->id];
        foreach (self::getListeIdShop((int) $id) as $value) {
            $liste_shop[$value['id_shop']] = $value['id_shop'];
        }

        if (empty($check_shop)) {
            return false;
        }
        // a supp
        foreach (array_diff($liste_shop, $check_shop) as $id_shop) {
            if (!self::delAssoShop((int) $id, (int) $id_shop)) {
                return false;
            }
        }
        // a add
        foreach (array_diff($check_shop, $liste_shop) as $id_shop) {
            if (!self::addAssoShop((int) $id, (int) $id_shop)) {
                return false;
            }
        }
        // EDIT FOR SHOP

        // EDIT FOR GROUP
        $liste_group = [];
        $check_group = [];
        foreach (self::getListeIdGroup((int) $id) as $value) {
            $liste_group[$value['id_group']] = $value['id_group'];
        }
        if (!$check_group = Tools::getValue('groupBox')) {
            $check_group = [];
        }
        if (empty($check_group)) {
            return false;
        }
        // a supp
        foreach (array_diff($liste_group, $check_group) as $id_group) {
            if (!self::delAssoGroup((int) $id, (int) $id_group)) {
                return false;
            }
        }
        // a add
        foreach (array_diff($check_group, $liste_group) as $id_group) {
            if (!self::addAssoGroup((int) $id, (int) $id_group)) {
                return false;
            }
        }

        // EDIT FOR GROUP
        return parent::update($null_values);
    }

    public function add($autodate = true, $null_values = false)
    {
        parent::add($autodate, $null_values);
        $context = Context::getContext();
        $id = $this->id;
        // EDIT FOR SHOP
        $liste_shop = [];
        $check_shop = [(int) $context->shop->id];
        foreach (self::getListeIdShop((int) $id) as $value) {
            $liste_shop[$value['id_shop']] = $value['id_shop'];
        }

        if (empty($check_shop)) {
            return false;
        }
        // a supp
        foreach (array_diff($liste_shop, $check_shop) as $id_shop) {
            if (!self::delAssoShop((int) $id, (int) $id_shop)) {
                return false;
            }
        }
        // a add
        foreach (array_diff($check_shop, $liste_shop) as $id_shop) {
            if (!self::addAssoShop((int) $id, (int) $id_shop)) {
                return false;
            }
        }
        // EDIT FOR SHOP

        // EDIT FOR GROUP
        $liste_group = [];
        $check_group = [];
        foreach (self::getListeIdGroup((int) $id) as $value) {
            $liste_group[$value['id_group']] = $value['id_group'];
        }
        if (!$check_group = Tools::getValue('groupBox')) {
            $check_group = [];
        }
        if (empty($check_group)) {
            return false;
        }
        // a supp
        foreach (array_diff($liste_group, $check_group) as $id_group) {
            if (!self::delAssoGroup((int) $id, (int) $id_group)) {
                return false;
            }
        }
        // a add
        foreach (array_diff($check_group, $liste_group) as $id_group) {
            if (!self::addAssoGroup((int) $id, (int) $id_group)) {
                return false;
            }
        }

        // EDIT FOR GROUP
        return true;
    }

    public function deletepopup()
    {
        if (parent::delete()) {
            if (!Db::getInstance()->Execute('
               DELETE ps FROM `' . _DB_PREFIX_ . 'prestablog_popup_shop` AS ps
               WHERE ps.`id_prestablog_popup` = ' . (int) $this->id)) {
                return false;
            }
            if (!Db::getInstance()->Execute('
               DELETE pg FROM `' . _DB_PREFIX_ . 'prestablog_popup_group` AS pg
               WHERE pg.`id_prestablog_popup` = ' . (int) $this->id)) {
                return false;
            }
            if (!Db::getInstance()->Execute('
               DELETE pp FROM `' . _DB_PREFIX_ . 'prestablog_news_popuplink` AS pp
               WHERE pp.`id_prestablog_popup` = ' . (int) $this->id)) {
                return false;
            }
            if (!Db::getInstance()->Execute('
               DELETE pc FROM `' . _DB_PREFIX_ . 'prestablog_categorie_popuplink` AS pc
               WHERE pc.`id_prestablog_popup` = ' . (int) $this->id)) {
                return false;
            }

            return true;
        }
    }

    public static function getListeIdShop($id)
    {
        return Db::getInstance()->ExecuteS('
           SELECT id_shop
           FROM `' . _DB_PREFIX_ . 'prestablog_popup_shop`
           WHERE    `id_prestablog_popup`=' . (int) $id);
    }

    public static function getListePopup($lid, $shopId)
    {
        return Db::getInstance()->ExecuteS('
           SELECT p.`id_prestablog_popup`, pl.`title`
           FROM `' . _DB_PREFIX_ . 'prestablog_popup` p
           LEFT JOIN `' . _DB_PREFIX_ . 'prestablog_popup_lang` pl ON (p.`id_prestablog_popup` = pl.`id_prestablog_popup`)
           LEFT JOIN `' . _DB_PREFIX_ . 'prestablog_popup_shop` ps ON (p.`id_prestablog_popup` = ps.`id_prestablog_popup`)
           WHERE `id_lang` = ' . $lid . ' AND ps.id_shop = ' . $shopId);
    }

    public static function getListeIdGroup($id_prestablog_popup)
    {
        return Db::getInstance()->ExecuteS('
           SELECT fg.id_group
           FROM `' . _DB_PREFIX_ . 'prestablog_popup_group` as fg
           WHERE    fg.`id_prestablog_popup`=' . (int) $id_prestablog_popup);
    }

    public static function addAssoShop($id, $id_shop)
    {
        return Db::getInstance()->Execute('
           INSERT INTO `' . _DB_PREFIX_ . 'prestablog_popup_shop` (
           `id_prestablog_popup`,
           `id_shop`
           )
           VALUES (
           ' . (int) $id . ',
           ' . (int) $id_shop . '
       );');
    }

    public static function addAssoGroup($id, $id_group)
    {
        return Db::getInstance()->Execute('
           INSERT INTO `' . _DB_PREFIX_ . 'prestablog_popup_group` (
           `id_prestablog_popup`,
           `id_group`
           )
           VALUES (
           ' . (int) $id . ',
           ' . (int) $id_group . '
       );');
    }

    public static function delAssoShop($id, $id_shop)
    {
        if (!Db::getInstance()->Execute('
           DELETE   fs FROM
           `' . _DB_PREFIX_ . 'prestablog_popup_shop` AS fs
           WHERE    fs.`id_prestablog_popup` = ' . (int) $id . '
           AND   fs.`id_shop` = ' . (int) $id_shop)) {
            return false;
        }

        return true;
    }

    public static function getPopupActifHome()
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
       SELECT `id_prestablog_popup`
       FROM `' . _DB_PREFIX_ . 'prestablog_popup`
       WHERE `actif_home` = 1');

        if (PrestaBlog::isNotRestrictionHome()) {
            return $return1;
        }

        return false;
    }

    public static function getIdPopupActifHome()
    {
        $return1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
   SELECT `id_prestablog_popup`
   FROM `' . _DB_PREFIX_ . 'prestablog_popup`
   WHERE `actif_home` = 1');

        return $return1;
    }

    public static function delAssoGroup($id, $id_group)
    {
        if (!Db::getInstance()->Execute('
       DELETE fg FROM
       `' . _DB_PREFIX_ . 'prestablog_popup_group` AS fg
       WHERE    fg.`id_prestablog_popup` = ' . (int) $id . '
       AND   fg.`id_group` = ' . (int) $id_group)) {
            return false;
        }

        return true;
    }

    public function changeState($id)
    {
        return Db::getInstance()->Execute('
       UPDATE `' . _DB_PREFIX_ . 'prestablog_popup`
       SET `actif`=CASE `actif` WHEN 1 THEN 0 WHEN 0 THEN 1 END
       WHERE `id_prestablog_popup`=' . (int) $id);
    }

    public static function updateValuePopuphome($id)
    {
        return Db::getInstance()->Execute('
       UPDATE `' . _DB_PREFIX_ . 'prestablog_popup`
       SET `actif_home`=CASE `actif_home` WHEN 1 THEN 0 WHEN 0 THEN 1 END
       WHERE `id_prestablog_popup`=' . (int) $id);
    }

    public static function deleteAllValue($id)
    {
        return Db::getInstance()->Execute('
       UPDATE `' . _DB_PREFIX_ . 'prestablog_popup`
       SET `actif_home`= 0
       WHERE `id_prestablog_popup` !=' . (int) $id);
    }

    public function displayList()
    {
        $context = Context::getContext();
        $definition_lang = $this->definitionLang();
        $content_list = self::getListContent((int) $context->language->id, (int) $context->shop->id);
        $fields_list = [
            'id_prestablog_popup' => [
                'title' => $this->trans('Id', [], 'Modules.Prestablog.Popup'),
                'type' => 'text',
                'search' => false,
                'orderby' => false,
            ],
            'title' => [
                'title' => $this->trans('Title', [], 'Modules.Prestablog.Popup'),
                'required' => true,
                'type' => 'text',
                'search' => false,
                'orderby' => false,
                'class' => 'list-strong',
            ],
            'date_start' => [
                'title' => $this->trans('Date start', [], 'Modules.Prestablog.Popup'),
                'required' => true,
                'type' => 'text',
                'search' => false,
                'orderby' => false,
                'align' => 'center',
                'badge_warning' => true,
            ],
            'date_stop' => [
                'title' => $this->trans('Date stop', [], 'Modules.Prestablog.Popup'),
                'required' => true,
                'type' => 'text',
                'search' => false,
                'orderby' => false,
                'align' => 'center',
                'badge_danger' => true,
                'badge_success' => true,
            ],
            'verbose_interval' => [
                'title' => $this->trans('Duration', [], 'Modules.Prestablog.Popup'),
                'type' => 'text',
                'search' => false,
                'orderby' => false,
            ],
            'theme' => [
                'title' => $this->trans('Theme', [], 'Modules.Prestablog.Popup'),
                'type' => 'text',
                'search' => false,
                'orderby' => false,
            ],
            'actif' => [
                'title' => $this->trans('Status', [], 'Modules.Prestablog.Popup'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'center',
            ],
        ];
        $helper = new HelperList();

        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_prestablog_popup';
        // $helper->actions = array('edit', 'delete', 'duplicate');
        $helper->actions = ['edit', 'delete'];
        $helper->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->trans('Delete selected items?', [], 'Modules.Prestablog.Popup'),
            ],
        ];
        $helper->show_toolbar = true;
        $helper->imageType = 'jpg';
        $tk = Tools::getAdminTokenLite('AdminModules');
        $ppc = AdminController::$currentIndex . '&configure=prestablog';
        $helper->toolbar_btn['new'] = [
            'href' => $ppc . '&add' . $this->name . '&token=' . $tk . '&class=PopupClass',
            'desc' => $this->trans('Add new', [], 'Modules.Prestablog.Popup'),
        ];
        $helper->title = $definition_lang['tabListName'];
        $helper->table = $this->name;
        $helper->module = $this;
        // $helper->listTotal = (int)sizeof($content_list);
        $helper->listTotal = (int) count($content_list);
        $helper->token = $tk;
        $helper->currentIndex = $ppc . '&class=PopupClass';

        return $helper->generateList($content_list, $fields_list);
    }

    public static function imgPathFO()
    {
        return '../modules/prestablog/';
    }

    public function displayForm($do = 'add')
    {
        $context = Context::getContext();
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $definition_lang = $this->definitionLang();
        $popup_preview_link = '';
        $popup_preview_content = '';
        $content_restriction_pages = '';
        $helper = new HelperForm();
        $helper->id = (int) Tools::getValue('id_prestablog_popup');
        $fields_value = [];
        /* Include Colorpicker script */
        $popup = new PrestaBlog();
        $context->smarty->assign([
            'popup' => $popup,
        ]);
        $popup_scripts = $popup->display('prestablog.php', 'views/templates/admin/displayFormPopup.tpl');

        if (!Tools::isSubmit('editpopup') && !Tools::getIsset('error1') && !Tools::getIsset('error2') && !Tools::getIsset('error3') && !Tools::getIsset('error4')) {
            $current_class = Tools::getValue('class');
            $object_model = PrestaBlog::createDynInstance($current_class, [(int) $helper->id]);

            /* Include preview popup */
            if ((int) $helper->id > 0) {
                $popup = new PrestaBlog();
                $context->smarty->assign([
                    'languages' => Language::getLanguages(true),
                    'helper' => $helper,
                    'popup' => $popup,
                ]);
                $popup_preview_link = $popup->display('prestablog.php', 'views/templates/admin/popup_preview_link.tpl');
            }

            $fields_value = [
                'id_prestablog_popup' => (int) $object_model->id_prestablog_popup,
                'date_start' => $object_model->date_start,
                'date_stop' => $object_model->date_stop,
                'height' => (int) $object_model->height,
                'width' => (int) $object_model->width,
                'delay' => (int) $object_model->delay,
                'expire' => (int) $object_model->expire,
                'expire_ratio' => (int) $object_model->expire_ratio,
                'theme' => $object_model->theme,
                'restriction_rules' => (int) $object_model->restriction_rules,
                'restriction_pages' => $object_model->restriction_pages,
                'actif' => (bool) $object_model->actif,
                'footer' => (bool) $object_model->footer,
                'pop_colorpicker_content' => $object_model->pop_colorpicker_content,
                'pop_colorpicker_modal' => $object_model->pop_colorpicker_modal,
                'pop_colorpicker_btn' => $object_model->pop_colorpicker_btn,
                'pop_colorpicker_btn_border' => $object_model->pop_colorpicker_btn_border,
                'pop_opacity_content' => $object_model->pop_opacity_content,
                'pop_opacity_modal' => $object_model->pop_opacity_modal,
                'pop_opacity_btn' => $object_model->pop_opacity_btn,
            ];
            foreach (Language::getLanguages(false) as $lang) {
                if (!isset($object_model->content)) {
                    $fields_value['content'][(int) $lang['id_lang']] = '';
                } else {
                    $fields_value['content'][(int) $lang['id_lang']] = $object_model->content[(int) $lang['id_lang']];
                }

                if (!isset($object_model->title)) {
                    $fields_value['title'][(int) $lang['id_lang']] = '';
                } else {
                    $fields_value['title'][(int) $lang['id_lang']] = $object_model->title[(int) $lang['id_lang']];
                }
            }
            $groups = [];
            foreach (self::getListeIdGroup((int) $object_model->id_prestablog_popup) as $group) {
                $groups[] = $group['id_group'];
            }
            foreach (Group::getGroups($context->language->id) as $group) {
                $fields_value['groupBox_' . $group['id_group']] = (
                    in_array(
                        $group['id_group'],
                        $groups
                    ) ? $group['id_group'] : (
                        !Tools::getValue('id_prestablog_popup') && (int) $group['id_group'] == 1 ? true : false
                    )
                );
            }
        } elseif (!Tools::isSubmit('editpopup') && (Tools::getIsset('error1') || Tools::getIsset('error2') || Tools::getIsset('error3') || Tools::getIsset('error4'))) {
            $current_class = Tools::getValue('class');
            $object_model = PrestaBlog::createDynInstance($current_class, [(int) $helper->id]);

            if ((int) $helper->id > 0) {
                $popup = new PrestaBlog();
                $context->smarty->assign([
                    'languages' => Language::getLanguages(true),
                    'helper' => $helper,
                    'popup' => $popup,
                ]);
                $popup_preview_link = $popup->display('prestablog.php', 'views/templates/admin/popup_preview_link.tpl');
            }

            $fields_value = [
                'id_prestablog_popup' => (int) $object_model->id_prestablog_popup,
                'date_start' => (null !== Tools::getValue('1') ? Tools::getValue('1') : $object_model->date_start),
                'date_stop' => (null !== Tools::getValue('2') ? Tools::getValue('2') : $object_model->date_stop),
                'height' => (null !== Tools::getValue('3') ? Tools::getValue('3') : (int) $object_model->height),
                'width' => (null !== Tools::getValue('4') ? Tools::getValue('4') : (int) $object_model->width),
                'delay' => (null !== Tools::getValue('5') ? Tools::getValue('5') : (int) $object_model->delay),
                'expire' => (null !== Tools::getValue('6') ? Tools::getValue('6') : (int) $object_model->expire),
                'expire_ratio' => (int) $object_model->expire_ratio,
                'theme' => $object_model->theme,
                'restriction_rules' => (int) $object_model->restriction_rules,
                'restriction_pages' => $object_model->restriction_pages,
                'actif' => (bool) $object_model->actif,
                'footer' => (bool) $object_model->footer,
                'pop_colorpicker_content' => (null !== Tools::getValue('8') ? Tools::getValue('8') : $object_model->pop_colorpicker_content),
                'pop_colorpicker_modal' => (null !== Tools::getValue('9') ? Tools::getValue('9') : $object_model->pop_colorpicker_modal),
                'pop_colorpicker_btn' => (null !== Tools::getValue('10') ? Tools::getValue('10') : $object_model->pop_colorpicker_btn),
                'pop_colorpicker_btn_border' => (null !== Tools::getValue('11') ? Tools::getValue('11') : $object_model->pop_colorpicker_btn_border),
                'pop_opacity_content' => $object_model->pop_opacity_content,
                'pop_opacity_modal' => $object_model->pop_opacity_modal,
                'pop_opacity_btn' => $object_model->pop_opacity_btn,
            ];
            foreach (Language::getLanguages(false) as $lang) {
                $langId = (int) $lang['id_lang'];

                if (!empty($object_model->content[$langId])) {
                    $fields_value['content'][$langId] = $object_model->content[$langId];
                } else {
                    $fields_value['content'][$langId] = '';
                }

                if (!empty($object_model->title[$langId])) {
                    $fields_value['title'][$langId] = $object_model->title[$langId];
                } else {
                    $fields_value['title'][$langId] = '';
                }
            }
            $groups = [];
            foreach (self::getListeIdGroup((int) $object_model->id_prestablog_popup) as $group) {
                $groups[] = $group['id_group'];
            }
            foreach (Group::getGroups($context->language->id) as $group) {
                $fields_value['groupBox_' . $group['id_group']] = (
                    in_array(
                        $group['id_group'],
                        $groups
                    ) ? $group['id_group'] : (
                        !Tools::getValue('id_prestablog_popup') && (int) $group['id_group'] == 1 ? true : false
                    )
                );
            }
        } else {
            $fields_value = [
                'id_prestablog_popup' => (int) Tools::getValue('id_prestablog_popup'),
                'date_start' => Tools::getValue('date_start'),
                'date_stop' => Tools::getValue('date_stop'),
                'height' => (int) Tools::getValue('height'),
                'width' => (int) Tools::getValue('width'),
                'delay' => (int) Tools::getValue('delay'),
                'expire' => (int) Tools::getValue('expire'),
                'expire_ratio' => (int) Tools::getValue('expire_ratio'),
                'theme' => Tools::getValue('theme'),
                'restriction_rules' => (int) Tools::getValue('restriction_rules'),
                'restriction_pages' => Tools::getValue('restriction_pages'),
                'actif' => (bool) Tools::getValue('actif'),
                'footer' => (bool) Tools::getValue('footer'),
                'pop_colorpicker_content' => Tools::getValue('pop_colorpicker_content'),
                'pop_colorpicker_modal' => Tools::getValue('pop_colorpicker_modal'),
                'pop_colorpicker_btn' => Tools::getValue('pop_colorpicker_btn'),
                'pop_colorpicker_btn_border' => Tools::getValue('pop_colorpicker_btn_border'),
                'pop_opacity_content' => Tools::getValue('pop_opacity_content'),
                'pop_opacity_modal' => Tools::getValue('pop_opacity_modal'),
                'pop_opacity_btn' => Tools::getValue('pop_opacity_btn'),
            ];
            foreach (Language::getLanguages(false) as $lang) {
                $fields_value['content'][(int) $lang['id_lang']] = Tools::getValue(
                    'content_' . (int) $lang['id_lang'],
                    ''
                );
                $fields_value['title'][(int) $lang['id_lang']] = Tools::getValue(
                    'title_' . (int) $lang['id_lang'],
                    ''
                );
            }
            foreach (Group::getGroups($context->language->id) as $group) {
                $fields_value['groupBox_' . $group['id_group']] = Tools::getValue('groupBox_' . $group['id_group']);
            }
        }

        $l01 = $this->trans('If the content does not fit in, a scroll bar will appear', [], 'Modules.Prestablog.Popup');
        $ppc = AdminController::$currentIndex . '&configure=prestablog';
        $tk = Tools::getAdminTokenLite('AdminModules');

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => Tools::ucfirst($do) . ' ' . $this->trans('popup', [], 'Modules.Prestablog.Popup'),
                    'icon' => 'icon-edit',
                    'badge' => 'icon-edit',
                ],
                'input' => [
                    [
                        'type' => 'hidden',
                        'name' => 'id_prestablog_popup',
                    ],
                    [
                        'type' => 'html',
                        'name' => $popup_preview_link,
                    ],
                    [
                        'type' => 'html',
                        'name' => $popup_scripts,
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Status', [], 'Modules.Prestablog.Popup'),
                        'name' => 'actif',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->trans('Enabled', [], 'Modules.Prestablog.Popup'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->trans('Disabled', [], 'Modules.Prestablog.Popup'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->trans('Theme', [], 'Modules.Prestablog.Popup'),
                        'id' => 'select_theme',
                        'name' => 'theme',
                        'options' => [
                            'query' => PrestaBlog::scanThemeTpl(),
                            'id' => 'id',
                            'name' => 'name',
                        ],
                        'desc' => $this->trans('If you want a full customization of your popup, please select the "colorpicker" theme.', [], 'Modules.Prestablog.Popup'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Color picker background', [], 'Modules.Prestablog.Popup'),
                        'name' => 'pop_colorpicker_content',
                        'lang' => false,
                        'id' => 'pop_colorpicker_content',
                        'data-hex' => true,
                        'class' => 'mColorPicker',
                        'col' => '5',
                    ],
                    [
                        'label' => $this->trans('Background opacity', [], 'Modules.Prestablog.Popup'),
                        'type' => 'select',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 0,
                                    'value' => 0,
                                    'name' => '0',
                                ],
                                [
                                    'id' => 0.1,
                                    'value' => 0.1,
                                    'name' => '0.1',
                                ],
                                [
                                    'id' => 0.2,
                                    'value' => 0.2,
                                    'name' => '0.2',
                                ],
                                [
                                    'id' => 0.3,
                                    'value' => 0.3,
                                    'name' => '0.3',
                                ],
                                [
                                    'id' => 0.4,
                                    'value' => 0.4,
                                    'name' => '0.4',
                                ],
                                [
                                    'id' => 0.5,
                                    'value' => 0.5,
                                    'name' => '0.5',
                                ],
                                [
                                    'id' => 0.6,
                                    'value' => 0.6,
                                    'name' => '0.6',
                                ],
                                [
                                    'id' => 0.7,
                                    'value' => 0.7,
                                    'name' => '0.7',
                                ],
                                [
                                    'id' => 0.8,
                                    'value' => 0.8,
                                    'name' => '0.8',
                                ],
                                [
                                    'id' => 0.9,
                                    'value' => 0.9,
                                    'name' => '0.9',
                                ],
                                [
                                    'id' => 1,
                                    'value' => 1,
                                    'name' => '1',
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                        'id' => 'pop_opacity_content',
                        'name' => 'pop_opacity_content',
                        'col' => '3',
                        'desc' => $this->trans('0 is full transparent and 1 is full colored.', [], 'Modules.Prestablog.Popup'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Color picker content', [], 'Modules.Prestablog.Popup'),
                        'name' => 'pop_colorpicker_modal',
                        'lang' => false,
                        'id' => 'pop_colorpicker_modal',
                        'data-hex' => true,
                        'class' => 'mColorPicker',
                        'col' => '5',
                    ],
                    [
                        'label' => $this->trans('Content opacity', [], 'Modules.Prestablog.Popup'),
                        'type' => 'select',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 0,
                                    'value' => 0,
                                    'name' => '0',
                                ],
                                [
                                    'id' => 0.1,
                                    'value' => 0.1,
                                    'name' => '0.1',
                                ],
                                [
                                    'id' => 0.2,
                                    'value' => 0.2,
                                    'name' => '0.2',
                                ],
                                [
                                    'id' => 0.3,
                                    'value' => 0.3,
                                    'name' => '0.3',
                                ],
                                [
                                    'id' => 0.4,
                                    'value' => 0.4,
                                    'name' => '0.4',
                                ],
                                [
                                    'id' => 0.5,
                                    'value' => 0.5,
                                    'name' => '0.5',
                                ],
                                [
                                    'id' => 0.6,
                                    'value' => 0.6,
                                    'name' => '0.6',
                                ],
                                [
                                    'id' => 0.7,
                                    'value' => 0.7,
                                    'name' => '0.7',
                                ],
                                [
                                    'id' => 0.8,
                                    'value' => 0.8,
                                    'name' => '0.8',
                                ],
                                [
                                    'id' => 0.9,
                                    'value' => 0.9,
                                    'name' => '0.9',
                                ],
                                [
                                    'id' => 1,
                                    'value' => 1,
                                    'name' => '1',
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                        'id' => 'pop_opacity_modal',
                        'name' => 'pop_opacity_modal',
                        'col' => '3',
                        'desc' => $this->trans('0 is full transparent and 1 is full colored.', [], 'Modules.Prestablog.Popup'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Color picker button', [], 'Modules.Prestablog.Popup'),
                        'name' => 'pop_colorpicker_btn',
                        'lang' => false,
                        'id' => 'pop_colorpicker_btn',
                        'data-hex' => true,
                        'class' => 'mColorPicker',
                        'col' => '5',
                    ],
                    [
                        'label' => $this->trans('Button opacity', [], 'Modules.Prestablog.Popup'),
                        'type' => 'select',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 0,
                                    'value' => 0,
                                    'name' => '0',
                                ],
                                [
                                    'id' => 0.1,
                                    'value' => 0.1,
                                    'name' => '0.1',
                                ],
                                [
                                    'id' => 0.2,
                                    'value' => 0.2,
                                    'name' => '0.2',
                                ],
                                [
                                    'id' => 0.3,
                                    'value' => 0.3,
                                    'name' => '0.3',
                                ],
                                [
                                    'id' => 0.4,
                                    'value' => 0.4,
                                    'name' => '0.4',
                                ],
                                [
                                    'id' => 0.5,
                                    'value' => 0.5,
                                    'name' => '0.5',
                                ],
                                [
                                    'id' => 0.6,
                                    'value' => 0.6,
                                    'name' => '0.6',
                                ],
                                [
                                    'id' => 0.7,
                                    'value' => 0.7,
                                    'name' => '0.7',
                                ],
                                [
                                    'id' => 0.8,
                                    'value' => 0.8,
                                    'name' => '0.8',
                                ],
                                [
                                    'id' => 0.9,
                                    'value' => 0.9,
                                    'name' => '0.9',
                                ],
                                [
                                    'id' => 1,
                                    'value' => 1,
                                    'name' => '1',
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                        'id' => 'pop_opacity_btn',
                        'name' => 'pop_opacity_btn',
                        'col' => '3',
                        'desc' => $this->trans('0 is full transparent and 1 is full colored.', [], 'Modules.Prestablog.Popup'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Color picker button border', [], 'Modules.Prestablog.Popup'),
                        'name' => 'pop_colorpicker_btn_border',
                        'lang' => false,
                        'id' => 'pop_colorpicker_btn_border',
                        'data-hex' => true,
                        'class' => 'mColorPicker',
                        'col' => '5',
                    ],
                    [
                        'label' => $this->trans('Date start', [], 'Modules.Prestablog.Popup'),
                        'type' => 'datetime',
                        'name' => 'date_start',
                        'required' => true,
                    ],
                    [
                        'label' => $this->trans('Date stop', [], 'Modules.Prestablog.Popup'),
                        'type' => 'datetime',
                        'name' => 'date_stop',
                        'required' => true,
                    ],
                    [
                        'label' => $this->trans('Width', [], 'Modules.Prestablog.Popup'),
                        'type' => 'text',
                        'name' => 'width',
                        'suffix' => $this->trans('px', [], 'Modules.Prestablog.Popup'),
                        'col' => '1',
                        'required' => true,
                    ],
                    [
                        'label' => $this->trans('Height', [], 'Modules.Prestablog.Popup'),
                        'hint' => $l01,
                        'type' => 'text',
                        'name' => 'height',
                        'suffix' => $this->trans('px', [], 'Modules.Prestablog.Popup'),
                        'col' => '1',
                        'required' => true,
                    ],
                    [
                        'label' => $this->trans('Delay', [], 'Modules.Prestablog.Popup'),
                        'type' => 'text',
                        'name' => 'delay',
                        'suffix' => $this->trans('milliseconds', [], 'Modules.Prestablog.Popup'),
                        'col' => '2',
                    ],
                    [
                        'label' => $this->trans('Popup expire', [], 'Modules.Prestablog.Popup'),
                        'type' => 'text',
                        'name' => 'expire',
                        'suffix' => $this->trans('unit(s)', [], 'Modules.Prestablog.Popup'),
                        'hint' => $this->trans('Unit(s) related to selected expire ratio on the field below.', [], 'Modules.Prestablog.Popup'),
                        'col' => '2',
                        'required' => true,
                        'desc' => $this->trans('For the popup to appear constantly, turn it to 0', [], 'Modules.Prestablog.Popup'),
                    ],
                    [
                        'label' => $this->trans('Expire ratio', [], 'Modules.Prestablog.Popup'),
                        'type' => 'select',
                        'name' => 'expire_ratio',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 60,
                                    'name' => $this->trans('Minute', [], 'Modules.Prestablog.Popup'),
                                ],
                                [
                                    'id' => 3600,
                                    'name' => $this->trans('Hour', [], 'Modules.Prestablog.Popup'),
                                ],
                                [
                                    'id' => 86400,
                                    'name' => $this->trans('Day', [], 'Modules.Prestablog.Popup'),
                                ],
                                [
                                    'id' => 2678400,
                                    'name' => $this->trans('Month', [], 'Modules.Prestablog.Popup'),
                                ],
                                [
                                    'id' => 30758400,
                                    'name' => $this->trans('Year', [], 'Modules.Prestablog.Popup'),
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                            'required' => true,
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Title', [], 'Modules.Prestablog.Popup'),
                        'lang' => true,
                        'name' => 'title',
                        'class' => 'text-strong',
                        'required' => true,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->trans('Content', [], 'Modules.Prestablog.Popup'),
                        'lang' => true,
                        'name' => 'content',
                        'cols' => 40,
                        'rows' => 10,
                        'class' => 'rte',
                        'autoload_rte' => true,
                        'required' => true,
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Popup footer', [], 'Modules.Prestablog.Popup'),
                        'hint' => $this->trans('This option shows or hides the footer popup with the close button.', [], 'Modules.Prestablog.Popup') . '
                   <br />' . $this->trans('Its closing is also always possible without button.', [], 'Modules.Prestablog.Popup'),
                        'name' => 'footer',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'footer_on',
                                'value' => 1,
                                'label' => $this->trans('Enabled', [], 'Modules.Prestablog.Popup'),
                            ],
                            [
                                'id' => 'footer_off',
                                'value' => 0,
                                'label' => $this->trans('Disabled', [], 'Modules.Prestablog.Popup'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'group',
                        'label' => $this->trans('Group access restrictions', [], 'Modules.Prestablog.Popup'),
                        'name' => 'groupBox',
                        'values' => Group::getGroups($context->language->id),
                    ],
                    [
                        'type' => 'html',
                        'name' => $content_restriction_pages,
                    ],
                ],
                'submit' => [
                    'title' => Tools::ucfirst($do),
                    'name' => $do . 'popupsubmit',
                    'class' => 'btn btn-default pull-right',
                ],
                'buttons' => [
                    [
                        'href' => $ppc . '&token=' . $tk . '&class=PopupClass',
                        'title' => $this->trans('Back to list', [], 'Modules.Prestablog.Popup'),
                        'icon' => 'process-icon-back',
                    ],
                ],
            ],
        ];
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->title = $definition_lang['tabListName'];
        $helper->table = 'prestablog_popup';
        $helper->identifier = 'id_prestablog_popup';
        foreach (Language::getLanguages(false) as $lang) {
            $helper->languages[] = [
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0),
            ];
        }
        // $helper->toolbar_scroll = true;
        // $helper->show_toolbar = true;
        $helper->submit_action = $do . 'popupsubmit';
        // $helper->show_cancel_button = true;
        $helper->token = $tk;
        $cI = $ppc . '&class=PopupClass';
        $helper->currentIndex = $cI;
        $helper->tpl_vars = [
            'fields_value' => $fields_value,
            'languages' => $context->controller->getLanguages(),
            'id_language' => $context->language->id,
        ];

        return $helper->generateForm([$fields_form]) . $popup_preview_content;
    }

    public function definitionLang()
    {
        $className = $this->trans('popup', [], 'Modules.Prestablog.Popup');

        return [
            'className' => $className,
            'tabListName' => $this->trans('List of', [], 'Modules.Prestablog.Popup') . ' ' . $className,
            'tabAddName' => $this->trans('Add', [], 'Modules.Prestablog.Popup') . ' ' . $className,
            'tabEditName' => $this->trans('Edit', [], 'Modules.Prestablog.Popup') . ' ' . $className,
            'tabHelp' => $this->trans('Help', [], 'Modules.Prestablog.Popup'),
            'tableName' => $this->name, ];
    }

    public static function getIdFrontPopupCatePreFiltered($categorie)
    {
        $context = Context::getContext();
        if ($categorie == null) {
            $categorie = 0;
        }

        if ((int) $categorie != 0) {
            $id_popup = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
           SELECT p.`id_prestablog_popup`
           FROM `' . _DB_PREFIX_ . 'prestablog_categorie_popuplink` p
           LEFT JOIN `' . _DB_PREFIX_ . 'prestablog_popup` pp ON (p.`id_prestablog_popup` = pp.`id_prestablog_popup`)
           LEFT JOIN `' . _DB_PREFIX_ . 'prestablog_popup_lang` pl ON (p.`id_prestablog_popup` = pl.`id_prestablog_popup`)
           LEFT JOIN `' . _DB_PREFIX_ . 'prestablog_popup_shop` ps ON (p.`id_prestablog_popup` = ps.`id_prestablog_popup`)
           LEFT JOIN `' . _DB_PREFIX_ . 'prestablog_categorie` pn ON (pn.`id_prestablog_categorie` = ' . $categorie . ')
           WHERE
           p.`id_prestablog_categorie`         = ' . $categorie . '
           AND pl.`id_lang`      = ' . (int) $context->language->id . '
           AND   ps.`id_shop`      = ' . (int) $context->shop->id . '
           AND   pp.`actif`         = 1
           AND   NOW() BETWEEN pp.`date_start` AND pp.`date_stop`');
        }

        if (isset($id_popup[0]['id_prestablog_popup'])) {
            // if (PrestaBlog::isNotRestrictionCate($cate)) {

            $groups_popup_cate = [];
            $groups_customer = $context->customer->getGroups();

            foreach (self::getListeIdGroup((int) $id_popup[0]['id_prestablog_popup']) as $group) {
                $groups_popup[] = $group['id_group'];
            }
            if (count(array_intersect($groups_popup, $groups_customer)) > 0) {
                return (int) $id_popup[0]['id_prestablog_popup'];
            }
            // }
        }

        return false;
    }

    public static function getIdFrontPopupNewsPreFiltered($news)
    {
        $context = Context::getContext();
        if ((int) $news != 0) {
            $id_popup = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
           SELECT p.`id_prestablog_popup`
           FROM `' . _DB_PREFIX_ . 'prestablog_news_popuplink` p
           LEFT JOIN `' . _DB_PREFIX_ . 'prestablog_popup` pp ON (p.`id_prestablog_popup` = pp.`id_prestablog_popup`)
           LEFT JOIN `' . _DB_PREFIX_ . 'prestablog_popup_lang` pl ON (p.`id_prestablog_popup` = pl.`id_prestablog_popup`)
           LEFT JOIN `' . _DB_PREFIX_ . 'prestablog_popup_shop` ps ON (p.`id_prestablog_popup` = ps.`id_prestablog_popup`)
           LEFT JOIN `' . _DB_PREFIX_ . 'prestablog_news` pn ON (pn.`id_prestablog_news` = ' . $news . ')
           WHERE
           p.`id_prestablog_news`         = ' . $news . '
           AND pl.`id_lang`      = ' . (int) $context->language->id . '
           AND   ps.`id_shop`      = ' . (int) $context->shop->id . '
           AND   pp.`actif`         = 1
           AND   NOW() BETWEEN pp.`date_start` AND pp.`date_stop`');
        }

        if (isset($id_popup[0]['id_prestablog_popup'])) {
            //  if (PrestaBlog::isNotRestrictionNews($id)) {

            $groups_popup_news = [];
            $groups_customer = $context->customer->getGroups();

            foreach (self::getListeIdGroup((int) $id_popup[0]['id_prestablog_popup']) as $group) {
                $groups_popup[] = $group['id_group'];
            }
            if (count(array_intersect($groups_popup, $groups_customer)) > 0) {
                return (int) $id_popup[0]['id_prestablog_popup'];
            }
            // }
        }

        return false;
    }

    public static function getIdFrontPopupPreFiltered()
    {
        $context = Context::getContext();
        $list_popup = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
       SELECT p.`id_prestablog_popup`, p.`restriction_rules`, p.`restriction_pages`, pl.`id_lang`
       FROM `' . _DB_PREFIX_ . 'prestablog_popup` p
       LEFT JOIN `' . _DB_PREFIX_ . 'prestablog_popup_lang` pl ON (p.`id_prestablog_popup` = pl.`id_prestablog_popup`)
       LEFT JOIN `' . _DB_PREFIX_ . 'prestablog_popup_shop` ps ON (p.`id_prestablog_popup` = ps.`id_prestablog_popup`)
       WHERE
       pl.`id_lang`      = ' . (int) $context->language->id . '
       AND   ps.`id_shop`      = ' . (int) $context->shop->id . '
       AND   p.`actif`         = 1
       AND   NOW() BETWEEN p.`date_start` AND p.`date_stop`
       ORDER BY p.`date_stop` ASC;');

        if (count($list_popup) > 0) {
            foreach ($list_popup as $value) {
                if (PrestaBlog::isNotRestrictionPage($value['restriction_rules'], $value['restriction_pages'])) {
                    $groups_popup = [];
                    $groups_customer = $context->customer->getGroups();
                    foreach (self::getListeIdGroup((int) $value['id_prestablog_popup']) as $group) {
                        $groups_popup[] = $group['id_group'];
                    }
                    if (count(array_intersect($groups_popup, $groups_customer)) > 0) {
                        return (int) $value['id_prestablog_popup'];
                    }
                }
            }
        }

        return false;
    }

    public static function getListContent($id_lang, $id_shop = null)
    {
        $content = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
       SELECT p.*, pl.*, ps.*,
       DATEDIFF(p.`date_stop`,p.`date_start`) as date_interval,
       TIMEDIFF(p.`date_stop`,p.`date_start`) as time_interval,
       CONCAT(
       FLOOR(HOUR(TIMEDIFF(p.`date_stop`,p.`date_start`)) / 24), \' days \',
       MOD(HOUR(TIMEDIFF(p.`date_stop`,p.`date_start`)), 24), \' hours \',
       MINUTE(TIMEDIFF(p.`date_stop`,p.`date_start`)), \' minutes\'
       ) as verbose_interval,
       if (NOW() BETWEEN p.`date_start` AND p.`date_stop`, 1, 0) badge_success,
       if (NOW() > p.`date_stop`, 1, 0) badge_danger,
       if (NOW() < p.`date_start`, 1, 0) badge_warning
       FROM `' . _DB_PREFIX_ . 'prestablog_popup` p
       LEFT JOIN `' . _DB_PREFIX_ . 'prestablog_popup_lang` pl ON (p.`id_prestablog_popup` = pl.`id_prestablog_popup`)
       LEFT JOIN `' . _DB_PREFIX_ . 'prestablog_popup_shop` ps ON (p.`id_prestablog_popup` = ps.`id_prestablog_popup`)
       WHERE
       pl.`id_lang` = ' . (int) $id_lang . ($id_shop ? ' AND ps.`id_shop`=' . bqSQL((int) $id_shop) : '') . '
       ORDER BY p.`date_stop` ASC;');

        return $content;
    }

    public static function createTables()
    {
        $return = true;
        $return &= Db::getInstance()->execute('
       CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestablog_popup` (
       `id_prestablog_popup` INT UNSIGNED NOT NULL AUTO_INCREMENT,
       `date_start` datetime NOT NULL,
       `date_stop` datetime NOT NULL,
       `height` int(11) NOT NULL,
       `width` int(11) NOT NULL,
       `delay` int(11) NOT NULL,
       `expire` int(11) NOT NULL,
       `expire_ratio` int(11) NOT NULL,
       `theme` varchar(255) NOT NULL,
       `restriction_rules` int(11) NOT NULL,
       `restriction_pages` text NOT NULL,
       `footer` tinyint(1) NOT NULL DEFAULT \'1\',
       `actif` tinyint(1) NOT NULL DEFAULT \'1\',
       `pop_colorpicker_content` varchar(255),
       `pop_colorpicker_modal` varchar(255),
       `pop_colorpicker_btn` varchar(255),
       `pop_colorpicker_btn_border` varchar(255),
       `pop_opacity_content` varchar(255),
       `pop_opacity_modal` varchar(255),
       `pop_opacity_btn` varchar(255),
       `actif_home` tinyint(1) NOT NULL DEFAULT \'0\',




       PRIMARY KEY (`id_prestablog_popup`)
   ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 ;');

        $return &= Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('
       CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestablog_popup_lang` (
       `id_prestablog_popup` INT UNSIGNED NOT NULL AUTO_INCREMENT,
       `id_lang` int(10) unsigned NOT NULL ,
       `title` varchar(255) NOT NULL,
       `content` text NOT NULL,
       PRIMARY KEY (`id_prestablog_popup`, `id_lang`)
   ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 ;');

        $return &= Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('
       CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestablog_popup_shop` (
       `id_prestablog_popup` int(10) unsigned NOT NULL,
       `id_shop` int(10) unsigned NOT NULL,
       PRIMARY KEY (`id_prestablog_popup`, `id_shop`)
   ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 ;');

        $return &= Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('
       CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'prestablog_popup_group` (
       `id_prestablog_popup` int(10) unsigned NOT NULL,
       `id_group` int(10) unsigned NOT NULL,
       PRIMARY KEY (`id_prestablog_popup`, `id_group`)
   ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 ;');

        return $return;
    }

    public static function dropTables()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'prestablog_popup`')
   && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'prestablog_popup_lang`')
   && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'prestablog_popup_shop`')
   && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'prestablog_popup_group`');
    }
}
