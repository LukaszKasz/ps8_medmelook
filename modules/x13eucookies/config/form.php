<?php

use x13eucookies\Config\ConfigKeys;

$fields_form = [];

$customHookInfoClass = $this->fieldDependsOn(
    $this->fieldDependsOnValueEqualTo(),
    [
        ConfigKeys::NAVIGATION_HOOK => 'displayX13EuCookiesNav',
    ]
);

$blockedIframesNetworks = [
    'Twitter',
    'Facebook',
    'LinkedIn',
    'Pinterest',
    'YouTube',
    'Vimeo',
];

$consentsHookInfo = '<div class="alert alert-info">';
$consentsHookInfo .= $this->l('Some modules, like cdc_googletagmanager, require our module to be displayed in the "displayAfterTitleTag" hook. For known modules, we take care of this automatically. If you have a module which loads before consent is sent to Google, try to change this option to "displayAfterTitleTag".', 'form');
$consentsHookInfo .= '<br><br>';
$consentsHookInfo .= $this->l('If the module set this option to "displayAfterTitleTag" and you still do not see consents being sent to Google, make sure to check your theme\'s head.tpl file to see if there is a correct hook after the title tag. If not, you can add it manually:', 'form');
$consentsHookInfo .= '<br><br>';
$consentsHookInfo .= '<pre>';
$consentsHookInfo .= '{block name=\'hook_after_title_tag\'}' . PHP_EOL;
$consentsHookInfo .= '{hook h=\'displayAfterTitleTag\'}' . PHP_EOL;
$consentsHookInfo .= '{/block}' . PHP_EOL;
$consentsHookInfo .= '</pre>';
$consentsHookInfo .= '</div>';

$hasSpecialHookModulesEnabled = false;

if (Module::isEnabled('cdc_googletagmanager')) {
    $hasSpecialHookModulesEnabled = true;
    $consentsHookInfo .= '<div class="alert alert-success">';
    $consentsHookInfo .= $this->l('You have the "CDC Google Tag Manager" module enabled. This module requires our module to be displayed in the "displayAfterTitleTag" hook. We have changed this option automatically. The option below is ignored.', 'form');
    $consentsHookInfo .= '</div>';
}

if (Module::isEnabled('pshowconversion')) {
    $hasSpecialHookModulesEnabled = true;
    $consentsHookInfo .= '<div class="alert alert-success">';
    $consentsHookInfo .= $this->l('You have the "Prestashow Conversion" module enabled. This module requires our module to be displayed in the "displayAfterTitleTag" hook. We have changed this option automatically. The option below is ignored.', 'form');
    $consentsHookInfo .= '</div>';
}

$fields_form[] = [
    'form' => [
        'legend' => [
            'title' => $this->l('Settings', 'form'),
            'icon' => 'icon-cogs',
        ],
        'input' => [
            [
                'name' => 'separator',
                'ignore' => true,
                'type' => 'html',
                'html_content' => '<h2   style="font-size:20px;">' . $this->l('General settings', 'form') . '</h2>',
            ],
            [
                'type' => 'text',
                'label' => $this->l('Module cookie name', 'form'),
                'name' => ConfigKeys::COOKIE_NAME,
                'default' => '_x13eucookie',
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Display module in front office', 'form'),
                'desc' => $this->l('If you want, you can hide the cookie choice from visitors.', 'form'),
                'name' => ConfigKeys::DISPLAY_FRONT,
                'values' => [
                    [
                        'id' => 'display_front_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'display_front_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 1,
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Force module to display for store employees or provided IPs', 'form'),
                'desc' => $this->l('If you want, you can display cookie choice form for selected users.', 'form'),
                'name' => ConfigKeys::FORCE_DISPLAY_FRONT,
                'values' => [
                    [
                        'id' => 'force_display_front_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'force_display_front_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueEqualTo(),
                    [
                        ConfigKeys::DISPLAY_FRONT => 0,
                    ]
                ),
                'default' => 0,
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Display for all employees', 'form'),
                'name' => ConfigKeys::FORCE_DISPLAY_FRONT_EMPLOYEES,
                'values' => [
                    [
                        'id' => 'force_display_front_employees_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'force_display_front_employees_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueEqualTo(),
                    [
                        ConfigKeys::FORCE_DISPLAY_FRONT => 1,
                    ]
                ),
                'default' => 0,
            ],
            // [
            //     'type' => 'text',
            //     'label' => $this->l('Type IP addresses separated by comma', 'form'),
            //     'name' => ConfigKeys::FORCE_DISPLAY_FRONT_IPS,
            //     'form_group_class' => $this->fieldDependsOn(
            //         $this->fieldDependsOnValueEqualTo(),
            //         [
            //             ConfigKeys::FORCE_DISPLAY_FRONT => 1,
            //         ]
            //     ),
            //     'default' => '',
            // ],
            [
                'name' => 'separator',
                'ignore' => true,
                'type' => 'html',
                'html_content' => '<h2 style="font-size:17px; margin:0px;">' . $this->l('Google Consent Mode V2 integration', 'form') . '</h2>',
            ],
            [
                'name' => 'separator',
                'ignore' => true,
                'type' => 'html',
                'html_content' => '<div class="alert alert-info">' . $this->l('You can check if Consent Mode v2 is correctly implemented using Tag Assistant from Google (tagassistant.google.com). Consents signals should be sent before any other tag fired. If you see an error message saying " A tag read consent state before a default was set" it means something has not been properly configured. If the "Consent" is on the position "1" on the list of events, it is probably something inside Google Tag Manager.', 'form') . '</div>',
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Send Consent Mode V2 consents', 'form'),
                'desc' => $this->l('Send consent to Google services according to the user\'s acceptance of cookies.', 'form') . '<br>' . $this->l('Option sends consents signals to GTM (Google Tag Manager) and other Google services.', 'form'),
                'name' => ConfigKeys::GTM_CONSENTS,
                'values' => [
                    [
                        'id' => 'gtm_consents_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'gtm_consents_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 1,
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Value for "url_passthrough" parameter', 'form'),
                'desc' => sprintf($this->l('More information: %s.', 'form'), '<a target="_blank" href="https://developers.google.com/tag-platform/security/guides/consent?consentmode=advanced&hl=pl">https://developers.google.com/tag-platform/security/guides/consent?consentmode=advanced&hl=pl</a>'),
                'name' => ConfigKeys::GTM_CONSENTS_URL_PASSTHROUGH,
                'values' => [
                    [
                        'id' => 'url_passthrough_on',
                        'value' => 1,
                        'label' => 'true',
                    ],
                    [
                        'id' => 'url_passthrough_off',
                        'value' => 0,
                        'label' => 'false',
                    ],
                ],
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueEqualTo(),
                    [
                        ConfigKeys::GTM_CONSENTS => 1,
                    ]
                ),
                'default' => 0,
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Value for "ads_data_redaction" parameter', 'form'),
                'desc' => sprintf($this->l('More information: %s.', 'form'), '<a target="_blank" href="https://developers.google.com/tag-platform/security/guides/consent?consentmode=advanced&hl=pl#redact_ads_data">https://developers.google.com/tag-platform/security/guides/consent?consentmode=advanced&hl=pl#redact_ads_data</a>'),
                'name' => ConfigKeys::GTM_CONSENTS_ADS_DATA_REDACTION,
                'values' => [
                    [
                        'id' => 'ads_data_redaction_on',
                        'value' => 1,
                        'label' => 'true',
                    ],
                    [
                        'id' => 'ads_data_redaction_off',
                        'value' => 0,
                        'label' => 'false',
                    ],
                ],
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueEqualTo(),
                    [
                        ConfigKeys::GTM_CONSENTS => 1,
                    ]
                ),
                'default' => 1,
            ],
            [
                'name' => 'separator',
                'ignore' => true,
                'type' => 'html',
                'html_content' => '<h2 style="font-size:17px; margin:0px;">' . $this->l('Microsoft Advertising consents', 'form') . '</h2>',
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Microsoft Advertising consents', 'form'),
                'desc' => $this->l('Send consent to Microsoft services according to the user\'s acceptance of cookies. According to Microsoft "This feature is currently available in an open beta.".', 'form'),
                'name' => ConfigKeys::MICROSOFT_CONSENTS,
                'values' => [
                    [
                        'id' => 'microsoft_consents_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'microsoft_consents_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 0,
            ],
            // [
            //     'name' => 'separator',
            //     'ignore' => true,
            //     'type' => 'html',
            //     'html_content' => '<h2 style="font-size:17px; margin:0px;">' . $this->l('Block iframes', 'form') . ' - ' . $this->l('experimental feature', 'form') . '</h2>',
            //     'form_group_class' => version_compare(_PS_VERSION_, '1.7.1', '<') ? 'hidden' : '',
            // ],
            // [
            //     'type' => 'switch',
            //     'label' => $this->l('Block iframes by default', 'form'),
            //     'desc' => sprintf($this->l('If enabled, iframes from %s will be blocked until user consent for marketing cookies. Consider this feature as experimental.', 'form'), implode(', ', $blockedIframesNetworks)),
            //     'name' => ConfigKeys::BLOCK_IFRAMES,
            //     'values' => [
            //         [
            //             'id' => 'block_iframes_on',
            //             'value' => 1,
            //             'label' => $this->l('Yes', 'form'),
            //         ],
            //         [
            //             'id' => 'block_iframes_off',
            //             'value' => 0,
            //             'label' => $this->l('No', 'form'),
            //         ],
            //     ],
            //     'default' => 0,
            //     // only for PrestaShop 1.7+
            //     'form_group_class' => version_compare(_PS_VERSION_, '1.7.1', '<') ? 'hidden' : '',
            // ],
            // [
            //     'type' => 'text',
            //     'lang' => true,
            //     'label' => $this->l('Text for blocked iframes', 'form'),
            //     'name' => ConfigKeys::BLOCKED_IFRAMES_TEXT,
            //     'desc' => $this->l('This text will be displayed instead of blocked iframes. You can use some tags to format the text. It is important to put button and link tags to the text.', 'form'),
            //     'form_group_class' => $this->fieldDependsOn(
            //         $this->fieldDependsOnValueEqualTo(),
            //         [
            //             ConfigKeys::BLOCK_IFRAMES => 1,
            //         ]
            //     ),
            //     'default' => [
            //         'en' => '[button]Agree to marketing cookies[/button] to see the content or see it on [link]',
            //         'pl' => '[button]Zgoda na marketingowe pliki cookie[/button] jest wymagana, aby zobaczyć treść lub zobacz to na [link]',
            //     ],
            //     'validate_custom' => [$this, 'validateBlockedIframesText'],
            //     'validate_custom_message' => $this->l('You need to add [button] and [link] tags to the text for blocked iframes.'),
            //     'form_group_class' => version_compare(_PS_VERSION_, '1.7.1', '<') ? 'hidden' : '',
            // ],
            [
                'name' => 'separator',
                'ignore' => true,
                'type' => 'html',
                'html_content' => '<h2 style="font-size:17px; margin:0px;">' . $this->l('Module behavior', 'form') . '</h2>',
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Block scrolling website before choosing cookies', 'form'),
                'name' => ConfigKeys::BLOCK_WEBSITE,
                'values' => [
                    [
                        'id' => 'block_website_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'block_website_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 0,
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Reload the website after choosing cookies', 'form'),
                'name' => ConfigKeys::RELOAD_WEBSITE,
                'values' => [
                    [
                        'id' => 'reload_website_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'reload_website_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 0,
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Set all switches in cookie category groups active by default', 'form'),
                'desc' => $this->l('If you enable this option, the visitor to your store will have all switches set as active by default. However, before accepting selected cookies or all cookies, they will not automatically be active.', 'form'),
                'name' => ConfigKeys::ENABLE_THIRD_PARTY_COOKIES_BY_DEFAULT,
                'values' => [
                    [
                        'id' => 'third_party_by_default_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'third_party_by_default_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 0,
            ],
            [
                'name' => 'separator',
                'ignore' => true,
                'type' => 'html',
                'html_content' => '<h2   style="font-size:20px;">' . $this->l('Appearance', 'form') . '</h2>',
            ],
            [
                'name' => 'separator',
                'ignore' => true,
                'type' => 'html',
                'html_content' => '<h2 style="font-size:17px; margin:0px;">' . $this->l('Cookie selection box - initial consent settings', 'form') . '</h2>',
            ],
            [
                'type' => 'select',
                'label' => $this->l('Layout', 'form'),
                'name' => ConfigKeys::LAYOUT,
                'validation' => 'isAnything',
                'options' => [
                    'query' => [
                        [
                            'id' => 'box',
                            'name' => $this->l('Box', 'form'),
                        ],
                        [
                            'id' => 'cloud',
                            'name' => $this->l('Cloud', 'form'),
                        ],
                        [
                            'id' => 'cloud_full_height',
                            'name' => $this->l('Cloud full height', 'form'),
                        ],
                        [
                            'id' => 'infobar',
                            'name' => $this->l('Info bar (small version)', 'form'),
                        ],
                        [
                            'id' => 'infobar_extra',
                            'name' => $this->l('Info bar (with 3 button)', 'form'),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
                'default' => 'box',
                'class' => 'fixed-width-xxl',
            ],
            [
                'type' => 'select',
                'label' => $this->l('Position', 'form'),
                'name' => ConfigKeys::BOX_POSITION,
                'validation' => 'isAnything',
                'options' => [
                    'query' => [
                        [
                            'id' => 'center',
                            'name' => $this->l('Center', 'form'),
                        ],
                        [
                            'id' => 'top',
                            'name' => $this->l('Top', 'form'),
                        ],
                        [
                            'id' => 'bottom',
                            'name' => $this->l('Bottom', 'form'),
                        ],
                        [
                            'id' => 'left',
                            'name' => $this->l('Left', 'form'),
                        ],
                        [
                            'id' => 'right',
                            'name' => $this->l('Right', 'form'),
                        ],
                        [
                            'id' => 'left_bottom',
                            'name' => $this->l('Left bottom', 'form'),
                        ],
                        [
                            'id' => 'left_top',
                            'name' => $this->l('Left top', 'form'),
                        ],
                        [
                            'id' => 'right_bottom',
                            'name' => $this->l('Right bottom', 'form'),
                        ],
                        [
                            'id' => 'right_top',
                            'name' => $this->l('Right top', 'form'),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
                'default' => 'center',
                'class' => 'fixed-width-xxl',
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueEqualTo(),
                    [
                        ConfigKeys::LAYOUT => ['box'],
                    ]
                ),
            ],
            [
                'type' => 'select',
                'label' => $this->l('Position', 'form'),
                'name' => ConfigKeys::CLOUD_POSITION,
                'validation' => 'isAnything',
                'options' => [
                    'query' => [
                        [
                            'id' => 'left_bottom',
                            'name' => $this->l('Left bottom', 'form'),
                        ],
                        [
                            'id' => 'left_top',
                            'name' => $this->l('Left top', 'form'),
                        ],
                        [
                            'id' => 'right_bottom',
                            'name' => $this->l('Right bottom', 'form'),
                        ],
                        [
                            'id' => 'right_top',
                            'name' => $this->l('Right top', 'form'),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
                'default' => 'left_bottom',
                'class' => 'fixed-width-xxl',
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueEqualTo(),
                    [
                        ConfigKeys::LAYOUT => ['cloud'],
                    ]
                ),
            ],
            [
                'type' => 'select',
                'label' => $this->l('Position', 'form'),
                'name' => ConfigKeys::CLOUD_FULL_POSITION,
                'validation' => 'isAnything',
                'options' => [
                    'query' => [
                        [
                            'id' => 'left',
                            'name' => $this->l('Left', 'form'),
                        ],
                        [
                            'id' => 'right',
                            'name' => $this->l('Right', 'form'),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
                'default' => 'left',
                'class' => 'fixed-width-xxl',
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueEqualTo(),
                    [
                        ConfigKeys::LAYOUT => ['cloud_full_height'],
                    ]
                ),
            ],
            [
                'type' => 'select',
                'label' => $this->l('Position', 'form'),
                'name' => ConfigKeys::INFOBAR_POSITION,
                'validation' => 'isAnything',
                'options' => [
                    'query' => [
                        [
                            'id' => 'top',
                            'name' => $this->l('Top', 'form'),
                        ],
                        [
                            'id' => 'bottom',
                            'name' => $this->l('Bottom', 'form'),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
                'default' => 'bottom',
                'class' => 'fixed-width-xxl',
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueEqualTo(),
                    [
                        ConfigKeys::LAYOUT => ['infobar', 'infobar_extra'],
                    ]
                ),
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Display icon under cloud', 'form'),
                'name' => ConfigKeys::DISPLAY_ICON_UNDER_CLOUD,
                'values' => [
                    [
                        'id' => 'display_icon_under_cloud_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'display_icon_under_cloud_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 0,
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueEqualTo(),
                    [
                        ConfigKeys::LAYOUT => 'cloud',
                    ]
                ),
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Display shop logo', 'form'),
                'name' => ConfigKeys::DISPLAY_SHOP_LOGO,
                'values' => [
                    [
                        'id' => 'center_shop_logo_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'center_shop_logo_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 1,
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Center shop logo', 'form'),
                'name' => ConfigKeys::CENTER_LOGO,
                'values' => [
                    [
                        'id' => 'display_shop_logo_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'display_shop_logo_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 1,
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueEqualTo(),
                    [
                        ConfigKeys::DISPLAY_SHOP_LOGO => 1,
                        ConfigKeys::DISPLAY_TITLE => 0,
                    ]
                ),
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Display title', 'form'),
                'name' => ConfigKeys::DISPLAY_TITLE,
                'values' => [
                    [
                        'id' => 'display_title_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'display_title_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 1,
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Display close button', 'form'),
                'name' => ConfigKeys::DISPLAY_CLOSE_BUTTON,
                'values' => [
                    [
                        'id' => 'display_close_button_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'display_close_button_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 0,
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueEqualTo(),
                    [
                        ConfigKeys::LAYOUT => ['box', 'infobar', 'infobar_extra'],
                    ]
                ),
            ],
            [
                'type' => 'select',
                'label' => $this->l('Display style', 'form'),
                'name' => ConfigKeys::BOX_DISPLAY_STYLE,
                'validation' => 'isAnything',
                'options' => [
                    'query' => [
                        [
                            'id' => 'tabs',
                            'name' => $this->l('Tabs', 'form'),
                        ],
                        [
                            'id' => 'list',
                            'name' => $this->l('List (elements underneath)', 'form'),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
                'default' => 'tabs',
                'class' => 'fixed-width-xxl',
            ],
            [
                'type' => 'color',
                'label' => $this->l('Color tabs underlined', 'form'),
                'name' => ConfigKeys::COLOR_TABS_UNDERLINED,
                'default' => '#24b9d7',
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueEqualTo(),
                    [
                        ConfigKeys::BOX_DISPLAY_STYLE => 'tabs',
                    ]
                ),
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Rounded corners (buttons and box)', 'form'),
                'name' => ConfigKeys::BOX_ROUNDED,
                'values' => [
                    [
                        'id' => 'display_rounded_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'display_rounded_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 1,
            ],
            [
                'type' => 'text',
                'label' => $this->l('Font size', 'form'),
                'name' => ConfigKeys::FONT_SIZE,
                'default' => 13,
                'class' => 'fixed-width-xs',
                'suffix' => 'px',
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Display backdrop', 'form'),
                'name' => ConfigKeys::DISPLAY_BACKDROP,
                'values' => [
                    [
                        'id' => 'display_backdrop_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'display_backdrop_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'desc' => $this->l('If set to OFF, options', 'form') . ' "' . $this->l('Block scrolling website before choosing cookies', 'form') . '" ' . $this->l('will be skipped', 'form'),
                'default' => 1,
            ],
            [
                'type' => 'select',
                'label' => $this->l('Backdrop layer opacity', 'form'),
                'name' => ConfigKeys::BACKDROP_OPACITY,
                'validation' => 'isAnything',
                'options' => [
                    'query' => [
                        [
                            'id' => '0.1',
                            'name' => '10% ' . $this->l('opacity', 'form'),
                        ],
                        [
                            'id' => '0.2',
                            'name' => '20% ' . $this->l('opacity', 'form'),
                        ],
                        [
                            'id' => '0.3',
                            'name' => '30% ' . $this->l('opacity', 'form'),
                        ],
                        [
                            'id' => '0.4',
                            'name' => '40% ' . $this->l('opacity', 'form'),
                        ],
                        [
                            'id' => '0.5',
                            'name' => '50% ' . $this->l('opacity', 'form'),
                        ],
                        [
                            'id' => '0.6',
                            'name' => '60% ' . $this->l('opacity', 'form'),
                        ],
                        [
                            'id' => '0.7',
                            'name' => '70% ' . $this->l('opacity', 'form'),
                        ],
                        [
                            'id' => '0.8',
                            'name' => '80% ' . $this->l('opacity', 'form'),
                        ],
                        [
                            'id' => '0.9',
                            'name' => '90% ' . $this->l('opacity', 'form'),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
                'default' => '0.5',
                'class' => 'fixed-width-xxl',
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueEqualTo(),
                    [
                        ConfigKeys::DISPLAY_BACKDROP => 1,
                    ]
                ),
            ],
            [
                'type' => 'color',
                'label' => $this->l('Backdrop color', 'form'),
                'name' => ConfigKeys::BACKDROP_COLOR,
                'default' => '#000000',
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueEqualTo(),
                    [
                        ConfigKeys::DISPLAY_BACKDROP => 1,
                    ]
                ),
            ],
            [
                'name' => 'separator',
                'ignore' => true,
                'type' => 'html',
                'html_content' => '<h2 style="font-size:17px; margin:0px;">' . $this->l('Cookies group settings', 'form') . '</h2>',
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Show number of cookies in a single group', 'form'),
                'name' => ConfigKeys::GROUPS_COUNTER_ENABLED,
                'values' => [
                    [
                        'id' => 'counter_enabled_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'counter_enabled_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 0,
            ],
            [
                'type' => 'select',
                'label' => $this->l('Display style group', 'form'),
                'name' => ConfigKeys::GROUPS_DISPLAY_STYLE,
                'validation' => 'isAnything',
                'options' => [
                    'query' => [
                        [
                            'id' => 'groups_details',
                            'name' => $this->l('Groups with detailed information about each cookie', 'form'),
                        ],
                        [
                            'id' => 'groups_flat',
                            'name' => $this->l('Groups without detailed infromation', 'form'),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
                'default' => 'groups_details',
                'class' => 'fixed-width-xxl',
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Display information about empty cookies in the group', 'form'),
                'name' => ConfigKeys::GROUPS_EMPTY_COOKIES,
                'values' => [
                    [
                        'id' => 'empty_cookies_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'empty_cookies_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 0,
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueEqualTo(),
                    [
                        ConfigKeys::GROUPS_DISPLAY_STYLE => 'groups_details',
                    ]
                ),
            ],
            [
                'type' => 'select',
                'label' => $this->l('The effect of expanding groups of cookies', 'form'),
                'name' => ConfigKeys::GROUPS_CLOSING_EFFECT,
                'validation' => 'isAnything',
                'options' => [
                    'query' => [
                        [
                            'id' => 'slide',
                            'name' => $this->l('Slide', 'form'),
                        ],
                        [
                            'id' => 'none',
                            'name' => $this->l('None', 'form'),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
                'default' => 'slide',
                'class' => 'fixed-width-xxl',
            ],
            [
                'type' => 'color',
                'label' => $this->l('Switch color', 'form'),
                'name' => ConfigKeys::SWITCH_COLOR_BACKGROUND,
                'default' => '#b3c7cd',
            ],
            [
                'type' => 'color',
                'label' => $this->l('Active switch color', 'form'),
                'name' => ConfigKeys::SWITCH_ACTIVE_COLOR_BACKGROUND,
                'default' => '#000000',
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Invert colors to switch', 'form'),
                'desc' => $this->l('Enabling this option will set the color to border instead of background.', 'form'),
                'name' => ConfigKeys::SWITCH_INVERT_COLORS,
                'values' => [
                    [
                        'id' => 'invert',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'no_invert',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 0,
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Display "select all"', 'form'),
                'name' => ConfigKeys::DISPLAY_SELECT_ALL,
                'values' => [
                    [
                        'id' => 'display_select_all_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'display_select_all_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 0,
            ],
            [
                'type' => 'textarea',
                'lang' => true,
                'autoload_rte' => true,
                'label' => $this->l('Information text', 'form'),
                'name' => ConfigKeys::INFORMATION_TEXT,
                'validate' => 'isAnything',
                'default' => [
                    'en' => 'This site uses first party cookies to give you the best experience on our site. We also use third party cookies to improve our services, analyze and then display advertisements related to your preferences based on the analysis of your browsing behavior.',
                    'pl' => 'Ta witryna korzysta z własnych plików cookie, aby zapewnić Ci najwyższy poziom doświadczenia na naszej stronie . Wykorzystujemy również pliki cookie stron trzecich w celu ulepszenia naszych usług, analizy a nastepnie wyświetlania reklam związanych z Twoimi preferencjami na podstawie analizy Twoich zachowań podczas nawigacji.',
                ],
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Display text about cookies', 'form'),
                'name' => ConfigKeys::DISPLAY_ABOUT_COOKIES,
                'values' => [
                    [
                        'id' => 'display_about_cookies_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'display_about_cookies_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 1,
            ],
            [
                'type' => 'textarea',
                'lang' => true,
                'autoload_rte' => true,
                'label' => $this->l('About cookies text', 'form'),
                'name' => ConfigKeys::ABOUT_COOKIES_TEXT,
                'validate' => 'isAnything',
                'default' => [
                    'en' => 'Cookies are small text files that are saved on your computer or mobile device by the websites you visit. They are used for a variety of purposes, such as remembering user login information, tracking user behavior for advertising purposes, and personalizing the user\'s browsing experience. There are two types of cookies: session and persistent. The former are deleted after the end of the browser session, while the latter remain on the device for a certain period of time or until they are manually deleted.',
                    'pl' => 'Pliki cookie to niewielkie pliki tekstowe, które są zapisywane na komputerze lub urządzeniu mobilnym przez strony internetowe, które odwiedzasz.  Służą do różnych celów, takich jak zapamiętywanie informacji o logowaniu użytkownika, śledzenie zachowania użytkownika w celach reklamowych i personalizacji doświadczenia przeglądania użytkownika. Istnieją dwa rodzaje plików cookie: sesyjne i trwałe. Te pierwsze są usuwane po zakończeniu sesji przeglądarki, podczas gdy te drugie pozostają na urządzeniu przez określony czas lub do momentu ich ręcznego usunięcia.',
                ],
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueNotEqualTo(),
                    [
                        ConfigKeys::DISPLAY_ABOUT_COOKIES => 0,
                    ]
                ),
            ],
            [
                'name' => 'separator',
                'ignore' => true,
                'type' => 'html',
                'html_content' => '<h2 style="font-size:17px; margin:0px;">' . $this->l('Button settings', 'form') . ' - <strong>' . $this->l('accept all cookies', 'form') . '</strong></h2>',
            ],
            [
                'type' => 'text',
                'lang' => true,
                'label' => $this->l('Text', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_ACCEPT_ALL_TEXT,
                'default' => [
                    'en' => 'Accept all',
                    'pl' => 'Zaakceptuj wszystkie',
                ],
            ],
            [
                'type' => 'color',
                'label' => $this->l('Text color', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_ACCEPT_ALL_TEXT_COLOR,
                'default' => '#ffffff',
            ],
            [
                'type' => 'color',
                'label' => $this->l('Background', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_ACCEPT_ALL_BACKGROUND,
                'default' => '#24B9D7',
            ],
            [
                'type' => 'color',
                'label' => $this->l('Border color', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_ACCEPT_ALL_BORDER_COLOR,
                'default' => '#24B9D7',
            ],
            [
                'type' => 'color',
                'label' => $this->l('Text color on hover', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_ACCEPT_ALL_HOVER_TEXT_COLOR,
                'default' => '#ffffff',
            ],
            [
                'type' => 'color',
                'label' => $this->l('Background on hover', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_ACCEPT_ALL_HOVER_BACKGROUND,
                'default' => '#20a3bd',
            ],
            [
                'type' => 'color',
                'label' => $this->l('Border color on hover', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_ACCEPT_ALL_HOVER_BORDER_COLOR,
                'default' => '#20a3bd',
            ],
            [
                'name' => 'separator',
                'ignore' => true,
                'type' => 'html',
                'html_content' => '<h2 style="font-size:17px; margin:0px;">' . $this->l('Button settings', 'form') . ' - <strong>' . $this->l('accept selected cookies', 'form') . '</strong></h2>',
            ],
            [
                'type' => 'text',
                'lang' => true,
                'label' => $this->l('Text', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_ACCEPT_SELECTED_TEXT,
                'default' => [
                    'en' => 'Accept selected',
                    'pl' => 'Zaakceptuj wybrane',
                ],
            ],
            [
                'type' => 'color',
                'label' => $this->l('Text color', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_ACCEPT_SELECTED_TEXT_COLOR,
                'default' => '#000000',
            ],
            [
                'type' => 'color',
                'label' => $this->l('Background', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_ACCEPT_SELECTED_BACKGROUND,
                'default' => '#ffffff',
            ],
            [
                'type' => 'color',
                'label' => $this->l('Border color', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_ACCEPT_SELECTED_BORDER_COLOR,
                'default' => '#24B9D7',
            ],
            [
                'type' => 'color',
                'label' => $this->l('Text color on hover', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_ACCEPT_SELECTED_HOVER_TEXT_COLOR,
                'default' => '#ffffff',
            ],
            [
                'type' => 'color',
                'label' => $this->l('Background on hover', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_ACCEPT_SELECTED_HOVER_BACKGROUND,
                'default' => '#24B9D7',
            ],
            [
                'type' => 'color',
                'label' => $this->l('Border color on hover', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_ACCEPT_SELECTED_HOVER_BORDER_COLOR,
                'default' => '#24B9D7',
            ],
            [
                'name' => 'separator',
                'ignore' => true,
                'type' => 'html',
                'html_content' => '<h2 style="font-size:17px; margin:0px;">' . $this->l('Button settings', 'form') . ' - <strong>' . $this->l('settings', 'form') . '</strong></h2>',
            ],
            [
                'type' => 'text',
                'lang' => true,
                'label' => $this->l('Text', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_SETTINGS_TEXT,
                'default' => [
                    'en' => 'Customize',
                    'pl' => 'Dostosuj',
                ],
            ],
            [
                'type' => 'color',
                'label' => $this->l('Text color', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_SETTINGS_TEXT_COLOR,
                'default' => '#000000',
            ],
            [
                'type' => 'color',
                'label' => $this->l('Background', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_SETTINGS_BACKGROUND,
                'default' => '#ffffff',
            ],
            [
                'type' => 'color',
                'label' => $this->l('Border color', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_SETTINGS_BORDER_COLOR,
                'default' => '#24B9D7',
            ],
            [
                'type' => 'color',
                'label' => $this->l('Text color on hover', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_SETTINGS_HOVER_TEXT_COLOR,
                'default' => '#ffffff',
            ],
            [
                'type' => 'color',
                'label' => $this->l('Background on hover', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_SETTINGS_HOVER_BACKGROUND,
                'default' => '#24B9D7',
            ],
            [
                'type' => 'color',
                'label' => $this->l('Border color on hover', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_SETTINGS_HOVER_BORDER_COLOR,
                'default' => '#24B9D7',
            ],
            [
                'name' => 'separator',
                'ignore' => true,
                'type' => 'html',
                'html_content' => '<h2 style="font-size:17px; margin:0px;">' . $this->l('Button settings', 'form') . ' - <strong>' . $this->l('other cookies button', 'form') . '</strong></h2>',
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Display "Deny" button', 'form'),
                'desc' => $this->l('If you disable this option, the customer will have fewer buttons to choose from.', 'form'),
                'name' => ConfigKeys::SWITCH_DENY_BUTTON,
                'values' => [
                    [
                        'id' => 'display_deny_button',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'no_deny_button',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 1,
            ],
            [
                'type' => 'text',
                'lang' => true,
                'label' => $this->l('Deny text', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_DENY_TEXT,
                'default' => [
                    'en' => 'Deny',
                    'pl' => 'Odrzuć',
                ],
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueNotEqualTo(),
                    [
                        ConfigKeys::SWITCH_DENY_BUTTON => 0,
                    ]
                ),
            ],
            [
                'type' => 'text',
                'lang' => true,
                'label' => $this->l('Close text', 'form'),
                'name' => ConfigKeys::BOX_BUTTON_CLOSE_TEXT,
                'default' => [
                    'en' => 'Close',
                    'pl' => 'Zamknij',
                ],
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueEqualTo(),
                    [
                        ConfigKeys::LAYOUT => ['cloud', 'cloud_full_height'],
                    ]
                ),
            ],
            [
                'name' => 'separator',
                'ignore' => true,
                'type' => 'html',
                'html_content' => '<h2 style="font-size:17px; margin:0px;">' . $this->l('Cookie selection widget - settings', 'form') . '</h2>',
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Allow to change the settings after initial consent', 'form'),
                'name' => ConfigKeys::CHANGE_AFTER_CONSENT,
                'values' => [
                    [
                        'id' => 'change_after_consent_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'change_after_consent_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 1,
            ],
            [
                'type' => 'select',
                'label' => $this->l('Display widget', 'form'),
                'desc' => $this->l('This option allows you to display a button thanks to which the customer can change cookie settings at any time.', 'form'),
                'name' => ConfigKeys::WIDGET_DISPLAY_STYLE,
                'validation' => 'isAnything',
                'options' => [
                    'query' => [
                        [
                            'id' => 'icon',
                            'name' => $this->l('Icon', 'form'),
                        ],
                        [
                            'id' => 'navbar',
                            'name' => $this->l('Navbar', 'form'),
                        ],
                        [
                            'id' => 'icon_navbar',
                            'name' => $this->l('Icon + Navbar', 'form'),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
                'default' => 'icon',
                'class' => 'fixed-width-xxl',
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueEqualTo(),
                    [
                        ConfigKeys::CHANGE_AFTER_CONSENT => '1',
                    ]
                ),
            ],
            [
                'type' => 'select',
                'label' => $this->l('Navigation hook', 'form'),
                'name' => ConfigKeys::NAVIGATION_HOOK,
                'validation' => 'isAnything',
                'options' => [
                    'query' => [
                        [
                            'id' => 'displayNav',
                            'name' => 'displayNav',
                        ],
                        [
                            'id' => 'displayNav2',
                            'name' => 'displayNav2',
                        ],
                        [
                            'id' => 'displayX13EuCookiesNav',
                            'name' => 'displayX13EuCookiesNav',
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
                'default' => 'icon',
                'class' => 'fixed-width-xxl',
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueEqualTo(),
                    [
                        ConfigKeys::CHANGE_AFTER_CONSENT => '1',
                        ConfigKeys::WIDGET_DISPLAY_STYLE => ['navbar', 'icon_navbar'],
                    ]
                ),
            ],
            [
                'name' => 'separator',
                'ignore' => true,
                'type' => 'html',
                'html_content' => '<div class="alert alert-info ' . $customHookInfoClass . '">' . sprintf($this->l('%s You can use this hook in the place where you want to display information.', 'form'), '<pre>{hook h="displayX13EuCookiesNav"}</pre>') . '</div>',
            ],
            [
                'type' => 'select',
                'label' => $this->l('Position icon', 'form'),
                'name' => ConfigKeys::WIDGET_DISPLAY_POSITION,
                'validation' => 'isAnything',
                'options' => [
                    'query' => [
                        [
                            'id' => 'left_bottom',
                            'name' => $this->l('Left bottom', 'form'),
                        ],
                        [
                            'id' => 'left_top',
                            'name' => $this->l('Left top', 'form'),
                        ],
                        [
                            'id' => 'right_bottom',
                            'name' => $this->l('Right bottom', 'form'),
                        ],
                        [
                            'id' => 'right_top',
                            'name' => $this->l('Right top', 'form'),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
                'default' => 'left_bottom',
                'class' => 'fixed-width-xxl',
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueEqualTo(),
                    [
                        ConfigKeys::WIDGET_DISPLAY_STYLE => ['icon', 'icon_navbar'],
                        ConfigKeys::CHANGE_AFTER_CONSENT => '1',
                    ]
                ),
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Hide icon on mobile', 'form'),
                'name' => ConfigKeys::WIDGET_HIDE_ON_MOBILE,
                'values' => [
                    [
                        'id' => 'hide_widget_on_mobile_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'hide_widget_on_mobile_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 0,
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueEqualTo(),
                    [
                        ConfigKeys::WIDGET_DISPLAY_STYLE => ['icon', 'icon_navbar'],
                        ConfigKeys::CHANGE_AFTER_CONSENT => '1',
                    ]
                ),
            ],
            [
                'name' => 'separator',
                'ignore' => true,
                'type' => 'html',
                'html_content' => '<h2   style="font-size:20px;">' . $this->l('Advanced settings', 'form') . '</h2>',
            ],
            [
                'name' => 'separator',
                'ignore' => true,
                'type' => 'html',
                'html_content' => '<h2 style="font-size:17px; margin:0px;">' . $this->l('Appearance', 'form') . '</h2>',
            ],
            [
                'type' => 'textarea',
                'rows' => 3,
                'label' => $this->l('Extra CSS', 'form'),
                'name' => ConfigKeys::EXTRA_CSS,
                'default' => '',
            ],
            [
                'name' => 'separator',
                'ignore' => true,
                'type' => 'html',
                'html_content' => '<h2 style="font-size:17px; margin:0px;">' . $this->l('Advanced cookie settings', 'form') . '</h2>',
            ],
            [
                'type' => 'text',
                'label' => $this->l('Cookie time for user selection', 'form'),
                'name' => ConfigKeys::COOKIE_TIME,
                'suffix' => $this->l('days', 'form'),
                'class' => 'fixed-width-xl',
                'default' => 365,
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Let search index bots through the consent box', 'form'),
                'name' => ConfigKeys::LET_IN_BOTS,
                'values' => [
                    [
                        'id' => 'let_in_bots_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'let_in_bots_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 1,
            ],
            [
                'type' => 'textarea',
                'rows' => 3,
                'label' => $this->l('Allowed bots', 'form'),
                'name' => ConfigKeys::ENABLED_BOTS,
                'default' => 'ADmantX|Applebot|Baidu|Baiduspider|Bing|Bingbot|Butterfly|Cookiebot|cognitiveSEO|crawler|DuckDuckGo|DuckDuckBot|Evaliant|Exabot|Facebook|Firefly|froogle|Genieo|Gigabot|Google|Googlebot|Grapeshot|inktomi|InfoSeek|Lighthouse|Lumar|MJ12bot|Mediapartners-Google|MeanPath|MSN|NationalDirectory|Oncrawl|OpenSiteExplorer|Pinterest|Proximic|Rankivabot|Scooter|Sogou|Sogouwebspider|Sosospider|Slackbot|Slurp|Squider|TechnoratiSnoop|Teoma|TwitterBot|TweetMeme|TweetmemeBot|Twiceler|Twitturls|WebAltaCrawler|WebFindBot|Yahoo|YandexAhrefs|YandexBot|YodaoBot|360Spider',
                'form_group_class' => $this->fieldDependsOn(
                    $this->fieldDependsOnValueEqualTo(),
                    [
                        ConfigKeys::LET_IN_BOTS => 1,
                    ]
                ),
            ],
            [
                'name' => 'separator',
                'ignore' => true,
                'type' => 'html',
                'html_content' => '<h2 class="h3 ' . ($this->ps_version < 1.7 ? 'x13-hide-element' : '') . '">' . $this->l('Troubleshooting', 'form') . '</h2>',
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Disapearing cookie navbar on mobile', 'form'),
                'name' => ConfigKeys::DISAPEARING_NAV_ON_MOBILE,
                'values' => [
                    [
                        'id' => 'disapearing_nav_on_mobile_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'disapearing_nav_on_mobile_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 0,
                'form_group_class' => ($this->ps_version < 1.7 ? 'x13-hide-element' : ''),
                'desc' => $this->l('If your theme doesn\'t toggle between desktop and mobile navigation style using Javascript - turn it on', 'form'),
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Move modal right before end of the body tag using JavaScript', 'form'),
                'name' => ConfigKeys::MOVE_MODAL_TO_END_BODY,
                'values' => [
                    [
                        'id' => 'move_modal_before_body_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'move_modal_before_body_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 0,
                'desc' => $this->l('If your theme doesn\'t display the cookie consent box correctly, you can try to enable this option.', 'form'),
            ],
            [
                'name' => 'separator',
                'ignore' => true,
                'type' => 'html',
                'html_content' => '<h2 style="font-size:17px; margin:0px;">' . $this->l('Consents are not being sent one of the first events', 'form') . '</h2>',
            ],
            [
                'name' => 'separator',
                'ignore' => true,
                'type' => 'html',
                'html_content' => $consentsHookInfo,
            ],
            [
                'type' => 'select',
                'label' => $this->l('Hook to render consents in the template', 'form'),
                'desc' => $this->l('Some modules or GTM implementation may require to render consents in a different place than the default one.', 'form'),
                'name' => ConfigKeys::CONSENTS_HOOK,
                'validation' => 'isAnything',
                'options' => [
                    'query' => [
                        [
                            'id' => 'displayHeader',
                            'name' => $this->l('displayHeader', 'form'),
                        ],
                        [
                            'id' => 'displayAfterTitleTag',
                            'name' => $this->l('displayAfterTitleTag', 'form'),
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
                'disabled' => $hasSpecialHookModulesEnabled,
                'default' => 'displayHeader',
                'class' => 'fixed-width-xxl',
            ],
            [
                'type' => 'switch',
                'label' => $this->l('Render Consent Mode choices directly inside the template', 'form'),
                'name' => ConfigKeys::SYNCHRONOUS_CONSENTS,
                'values' => [
                    [
                        'id' => 'synchronous_consents_on',
                        'value' => 1,
                        'label' => $this->l('Yes', 'form'),
                    ],
                    [
                        'id' => 'synchronous_consents_off',
                        'value' => 0,
                        'label' => $this->l('No', 'form'),
                    ],
                ],
                'default' => 0,
                'desc' => $this->l('If you have problems with asynchronous method of getting user consents and your container does not receive any data, this might help you. Please note that this setting is not recommended and if you are forced to use it, you probably have some issues with your tracking or Tag Manager in general.', 'form'),
            ],
        ],
        'submit' => [
            'title' => $this->l('Save', 'form'),
        ],
    ],
];

return $fields_form;
