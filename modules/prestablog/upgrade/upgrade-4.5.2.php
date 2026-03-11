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

function upgrade_module_4_5_2($object)
{
    // - Table "prestablog_news" colonne langues (ps8_prestablog_news)
    if ($results = Db::getInstance()->executeS('SELECT * FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_news`')) {
        foreach ($results as $row) {
            if (json_decode($row['langues']) != null) {
                continue;
            }

            $row['langues'] = json_encode(unserialize($row['langues']));
            if (!Db::getInstance()->execute('UPDATE `' . bqSQL(_DB_PREFIX_) . 'prestablog_news` SET `langues` = \'' . $row['langues'] . '\' WHERE `id_prestablog_news` = \'' . $row['id_prestablog_news'] . '\'')) {
                return false;
            }
        }
    }

    // - Table "prestablog_subblock" colonne langues (ps8_prestablog_subblock)
    if ($results = Db::getInstance()->executeS('SELECT * FROM `' . bqSQL(_DB_PREFIX_) . 'prestablog_subblock`')) {
        foreach ($results as $row) {
            if (json_decode($row['langues']) != null) {
                continue;
            }

            $row['langues'] = json_encode(unserialize($row['langues']));
            if (!Db::getInstance()->execute('UPDATE `' . bqSQL(_DB_PREFIX_) . 'prestablog_subblock` SET `langues` = \'' . $row['langues'] . '\' WHERE `id_prestablog_subblock` = \'' . $row['id_prestablog_subblock'] . '\'')) {
                return false;
            }
        }
    }

    // - Table "configuration" pour prestablog_sbr colonne value (ps8_configuration)
    // - Table "configuration" prestablog_sbl colonne value
    // - Table "configuration" prestablog_commentfb_modosId colonne value
    if ($results = Db::getInstance()->executeS('SELECT * FROM `' . bqSQL(_DB_PREFIX_) . 'configuration` WHERE name = \'prestablog_sbr\' OR name = \'prestablog_sbl\' OR name = \'prestablog_commentfb_modosId\'')) {
        foreach ($results as $row) {
            if (json_decode($row['value']) != null) {
                continue;
            }

            $row['value'] = json_encode(unserialize($row['value']));
            if (!Db::getInstance()->execute('UPDATE `' . bqSQL(_DB_PREFIX_) . 'configuration` SET `value` = \'' . $row['value'] . '\' WHERE `id_configuration` = \'' . $row['id_configuration'] . '\'')) {
                return false;
            }
        }
    }

    Tools::clearCache();

    return true;
}
