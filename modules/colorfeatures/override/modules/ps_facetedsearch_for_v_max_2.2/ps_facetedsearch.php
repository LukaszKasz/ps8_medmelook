<?php
/**
 * ColorFeatures
 *
 * @author    silbersaiten <info@silbersaiten.de>
 * @copyright 2019 silbersaiten
 * @license   See joined file licence.txt
 * @category  Module
 * @support   silbersaiten <support@silbersaiten.de>
 * @version   1.0.1
 * @link      http://www.silbersaiten.de
 */

require_once implode(DIRECTORY_SEPARATOR, array(
    _PS_MODULE_DIR_, 'colorfeatures', 'override_src', 'Ps_FacetedsearchProductSearchProviderCustom.php'
));

class Ps_FacetedsearchOverride extends Ps_Facetedsearch
{
    public function hookProductSearchProvider($params)
    {
        $query = $params['query'];

        if ($query->getIdCategory()) {
            return new Ps_FacetedsearchProductSearchProviderCustom($this);
        } else {
            return null;
        }
    }
}
