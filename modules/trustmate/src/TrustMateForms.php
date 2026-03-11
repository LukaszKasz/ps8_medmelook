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

class TrustMateForms
{
    public function __construct($module, $language)
    {
        $this->module = $module;
        $this->language = $language;
    }

    public function getHelperForm($submit_action, $form_template_dir = null, $form_template = null)
    {
        $helper = new HelperForm();
        $language = (int)Configuration::get('PS_LANG_DEFAULT');

        // Module, token and currentIndex
        $helper->module = $this->module;
        $helper->name_controller = $this->module->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->module->name;

        // Language
        $helper->default_form_language = $language;
        $helper->allow_employee_form_lang = $language;

        // Title and toolbar
        $helper->title = $this->module->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = $submit_action;
        $helper->toolbar_btn = array(
            'save' => array(
                'desc' => $this->module->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->module->name . '&save' . $this->module->name .
                    '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->module->l('Back to list')
            ),
        );

        if ($form_template !== null && $form_template_dir !== null) {
            $helper->base_folder = $form_template_dir;
            $helper->base_tpl = $form_template;
        }

        return $helper;
    }

    public function getInvitationsFormFields()
    {
        $fields = array();
        $fields[0]['form'] = array(
            'input' => array(
                array(
                    'type' => 'radio',
                    'label' => $this->module->l('Automated TrustMate invitation sending'),
                    'name' => 'TRUSTMATE_INVITATIONS',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'disabled',
                            'value' => 0,
                            'label' => "<strong>{$this->module->l('Disabled')}</strong>",
                        ),
                        array(
                            'id' => 'via_api',
                            'value' => 3,
                            'label' => "
                                <strong>{$this->module->l('Enable using Webservice (API) - recommended')}</strong>
                                <p>
                                {$this->module->l('Creates API key with permissions necessary to create review invitation')}.
                                {$this->module->l('TrustMate checks for new orders periodically using your shop webservice and creates invitations on its own')}.
                                {$this->module->l('Works well in all scenarios, even if order statuses are changed by external modules or systems')}.
                                {$this->module->l('Invitations are not created instantly')}.
                                {$this->module->l('If you block external traffic to shop API, please whitelist our IP: 3.120.215.90')}.
                                </p>
                            ",
                        ),
                        array(
                            'id' => 'shop_and_products',
                            'value' => 2,
                            'label' => "
                                {$this->module->l('Using TrustMate module')}
                                - {$this->module->l('deprecated, may be removed in further versions')}
                                <p>
                                {$this->module->l('Review invitations are created after order status change in PrestaShop')}.
                                {$this->module->l('Works well if you change order statuses manually')}.
                                </p>
                            ",
                        ),
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->module->l('When should an invitation be sent?'),
                    'name' => 'TRUSTMATE_DISPATCH_TRIGGERED_BY',
                    'options' => array(
                        'query' => self::getDispatchTriggerChoices($this->language),
                        'id' => 'value',
                        'name' => 'name'
                )),
                array(
                    'type' => 'select',
                    'label' => $this->module->l('Language (multistore only)'),
                    'name' => 'TRUSTMATE_LANGUAGE',
                    'options' => array(
                        'query' => self::getLanguageChoices($this->module->l('default')),
                        'id' => 'value',
                        'name' => 'name'
                )),
                array(
                    'type' => 'checkbox',
                    'label' => $this->module->l('Instant company review form'),
                    'name' => 'TRUSTMATE',
                    'values' => array(
                        'query' => array(array('id' => 'INSTANT_REVIEW', 'name' => $this->module->l('Enable on thank you page (do not use with Processing in progress status)'), 'val' => 1)),
                        'id' => 'id',
                        'name' => 'name',
                        'value' => Configuration::get('TRUSTMATE_INSTANT_REVIEW')
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->module->l('Save'),
                'class' => 'btn btn-default pull-right'
            ),
        );

        return $fields;
    }

    public function getAccountFormFields()
    {
        $fields = array();
        $fields[0]['form'] = array(

            'input' => array(
                array(
                    'type' => 'text',
                    'name' => 'TRUSTMATE_UUID',
                    'label' => $this->module->l('Your UUID number (required for configuring invitation sending and widgets)'),
                    'size' => 20,
                    'required' => true
                ),
            ),
            'submit' => array(
                'title' => $this->module->l('Save'),
                'class' => 'btn btn-default pull-right'
            ),
        );

        return $fields;
    }

    private static function getDispatchTriggerChoices($language)
    {
        $result = array();
        $states = OrderState::getOrderStates($language->id);
        foreach ($states as $state) {
            if (in_array($state['id_order_state'], array(
                Configuration::get('PS_OS_PAYMENT'),
                Configuration::get('PS_OS_PREPARATION'),
                Configuration::get('PS_OS_SHIPPING'),
                Configuration::get('PS_OS_DELIVERED'),
            ))) {
                $result[] = array(
                    'value' => $state['id_order_state'],
                    'name' => $state['name'],
                    'selected' => false,
                );
            }
        }

        return $result;
    }

    private static function getLanguageChoices($defaultLabel)
    {
        $result = array();
        $result[] = array(
            'value' => '',
            'name' => $defaultLabel,
            'selected' => !Configuration::get('TRUSTMATE_LANGUAGE'),
        );

        $languages = Language::getLanguages(true);
        foreach ($languages as $language) {
            $result[] = array(
                'value' => $language['id_lang'],
                'name' => $language['name'],
                'selected' => Configuration::get('TRUSTMATE_LANGUAGE') == $language['id_lang'],
            );
        }

        return $result;
    }
}
