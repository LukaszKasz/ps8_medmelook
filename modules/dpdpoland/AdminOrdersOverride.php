<?php

require_once(_PS_ADMIN_DIR_.'/tabs/AdminOrders.php');

/**
 * Class AdminOrdersOverride used to make modifications in orders list on PS 1.4
 */
class AdminOrdersOverride extends AdminOrders
{
    /**
     * AdminOrdersOverride class constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->delete = true;
    }

    /**
     * Displays additional buttons in orders list
     *
     * @param null|string $token BackOffice token
     */
    public function displayListFooter($token = null)
    {
        echo '</table>';
        echo '<p>
            <input type="submit" class="button" name="submitBulkprint_a4order" value="'.$this->l('Print A4 format labels').'" onclick="return confirm(\''.$this->l('Print A4 format labels', __CLASS__, true, false).'\');" />
            <input type="submit" class="button" name="submitBulkprint_labelorder" value="'.$this->l('Print A4 format labels').'" onclick="return confirm(\''.$this->l('Print A4 format labels', __CLASS__, true, false).'\');" />
        </p>';

        echo '
				</td>
			</tr>
		</table>
		<input type="hidden" name="token" value="'.($token ? $token : $this->token).'" />
		</form>';
        if (isset($this->_includeTab) && count($this->_includeTab))
            echo '<br /><br />';
    }

    /**
     * Displays orders list
     *
     * @param null|string $token BackOffice token
     */
    public function displayListContent($token = null)
    {
        global $currentIndex, $cookie;
        $currency = new Currency(_PS_CURRENCY_DEFAULT_);

        $id_category = 1; // default categ

        $irow = 0;
        if ($this->_list AND isset($this->fieldsDisplay['position']))
        {
            $positions = array_map(create_function('$elem', 'return (int)($elem[\'position\']);'), $this->_list);
            sort($positions);
        }
        if ($this->_list)
        {
            $isCms = false;

            Module::getInstanceByName('dpdpoland');
            $exceptions = DpdPolandPackage::getLabelExceptions();

            if (preg_match('/cms/Ui', $this->identifier))
                $isCms = true;
            $keyToGet = 'id_'.($isCms ? 'cms_' : '').'category'.(in_array($this->identifier, array('id_category', 'id_cms_category')) ? '_parent' : '');
            foreach ($this->_list as $tr)
            {
                $id = $tr[$this->identifier];
                echo '<tr'.(array_key_exists($this->identifier,$this->identifiersDnd) ? ' id="tr_'.(($id_category = (int)(Tools::getValue('id_'.($isCms ? 'cms_' : '').'category', '1'))) ? $id_category : '').'_'.$id.'_'.$tr['position'].'"' : '').($irow++ % 2 ? ' class="alt_row"' : '').' '.((isset($tr['color']) AND $this->colorOnBackground) ? 'style="background-color: '.$tr['color'].'"' : '').'>
							<td class="center">';
                if ($this->delete AND (!isset($this->_listSkipDelete) OR !in_array($id, $this->_listSkipDelete)))
                    echo '<input type="checkbox" name="'.$this->table.'Box[]" value="'.$id.'" class="noborder" />';
                echo '</td>';
                foreach ($this->fieldsDisplay as $key => $params)
                {
                    $tmp = explode('!', $key);
                    $key = isset($tmp[1]) ? $tmp[1] : $tmp[0];
                    echo '
					<td '.(isset($params['position']) ? ' id="td_'.(isset($id_category) AND $id_category ? $id_category : 0).'_'.$id.'"' : '').' class="'.((!isset($this->noLink) OR !$this->noLink) ? 'pointer' : '').((isset($params['position']) AND $this->_orderBy == 'position')? ' dragHandle' : ''). (isset($params['align']) ? ' '.$params['align'] : '').'" ';
                    if (!isset($params['position']) AND (!isset($this->noLink) OR !$this->noLink))
                        echo ' onclick="document.location = \''.$currentIndex.'&'.$this->identifier.'='.$id.($this->view? '&view' : '&update').$this->table.'&token='.($token != null ? $token : $this->token).'\'">'.(isset($params['prefix']) ? $params['prefix'] : '');
                    else
                        echo '>';
                    if (isset($params['active']) AND isset($tr[$key]))
                        $this->_displayEnableLink($token, $id, $tr[$key], $params['active'], Tools::getValue('id_category'), Tools::getValue('id_product'));
                    elseif (isset($params['activeVisu']) AND isset($tr[$key]))
                        echo '<img src="../img/admin/'.($tr[$key] ? 'enabled.gif' : 'disabled.gif').'"
						alt="'.($tr[$key] ? $this->l('Enabled') : $this->l('Disabled')).'" title="'.($tr[$key] ? $this->l('Enabled') : $this->l('Disabled')).'" />';
                    elseif (isset($params['position']))
                    {
                        if ($this->_orderBy == 'position' AND $this->_orderWay != 'DESC')
                        {
                            echo '<a'.(!($tr[$key] != $positions[sizeof($positions) - 1]) ? ' style="display: none;"' : '').' href="'.$currentIndex.
                                '&'.$keyToGet.'='.(int)($id_category).'&'.$this->identifiersDnd[$this->identifier].'='.$id.'
									&way=1&position='.(int)($tr['position'] + 1).'&token='.($token != null ? $token : $this->token).'">
									<img src="../img/admin/'.($this->_orderWay == 'ASC' ? 'down' : 'up').'.gif"
									alt="'.$this->l('Down').'" title="'.$this->l('Down').'" /></a>';

                            echo '<a'.(!($tr[$key] != $positions[0]) ? ' style="display: none;"' : '').' href="'.$currentIndex.
                                '&'.$keyToGet.'='.(int)($id_category).'&'.$this->identifiersDnd[$this->identifier].'='.$id.'
									&way=0&position='.(int)($tr['position'] - 1).'&token='.($token != null ? $token : $this->token).'">
									<img src="../img/admin/'.($this->_orderWay == 'ASC' ? 'up' : 'down').'.gif"
									alt="'.$this->l('Up').'" title="'.$this->l('Up').'" /></a>';						}
                        else
                            echo (int)($tr[$key] + 1);
                    }
                    elseif (isset($params['image']))
                    {
                        // item_id is the product id in a product image context, else it is the image id.
                        $item_id = isset($params['image_id']) ? $tr[$params['image_id']] : $id;
                        // If it's a product image
                        if (isset($tr['id_image']))
                        {
                            $image = new Image((int)$tr['id_image']);
                            $path_to_image = _PS_IMG_DIR_.$params['image'].'/'.$image->getExistingImgPath().'.'.$this->imageType;
                        }else
                            $path_to_image = _PS_IMG_DIR_.$params['image'].'/'.$item_id.(isset($tr['id_image']) ? '-'.(int)($tr['id_image']) : '').'.'.$this->imageType;

                        echo cacheImage($path_to_image, $this->table.'_mini_'.$item_id.'.'.$this->imageType, 45, $this->imageType);
                    }
                    elseif (isset($params['icon']) AND (isset($params['icon'][$tr[$key]]) OR isset($params['icon']['default'])))
                        echo '<img src="../img/admin/'.(isset($params['icon'][$tr[$key]]) ? $params['icon'][$tr[$key]] : $params['icon']['default'].'" alt="'.$tr[$key]).'" title="'.$tr[$key].'" />';
                    elseif (isset($params['price']))
                        echo Tools::displayPrice($tr[$key], (isset($params['currency']) ? Currency::getCurrencyInstance((int)($tr['id_currency'])) : $currency), false);
                    elseif (isset($params['float']))
                        echo rtrim(rtrim($tr[$key], '0'), '.');
                    elseif (isset($params['type']) AND $params['type'] == 'date')
                        echo Tools::displayDate($tr[$key], (int)$cookie->id_lang);
                    elseif (isset($params['type']) AND $params['type'] == 'datetime')
                        echo Tools::displayDate($tr[$key], (int)$cookie->id_lang, true);
                    elseif (isset($tr[$key]))
                    {
                        $echo = ($key == 'price' ? round($tr[$key], 2) : isset($params['maxlength']) ? Tools::substr($tr[$key], 0, $params['maxlength']).'...' : $tr[$key]);
                        echo isset($params['callback']) ? call_user_func_array(array($this->className, $params['callback']), array($echo, $tr)) : $echo;
                    }
                    else
                        echo '--';

                    echo (isset($params['suffix']) ? $params['suffix'] : '').
                        '</td>';
                }

                if ($this->edit OR $this->delete OR ($this->view AND $this->view !== 'noActionColumn'))
                {
                    echo '<td class="center" style="white-space: nowrap;">';
                    if ($this->view)
                        $this->_displayViewLink($token, $id);

                    if (!in_array($id, $exceptions)) {
                        $this->_displayPrinta4Link($token, $id);
                        $this->_displayPrintlabelLink($token, $id);
                    }

                    echo '</td>';
                }
                echo '</tr>';
            }
        }
    }

    /**
     * Displays link used to print A4 format label
     *
     * @param string $token BackOffice token
     * @param int $id Order identifier
     */
    private function _displayPrinta4Link($token, $id)
    {
        global $currentIndex;

        $_cacheLang['printa4'] = $this->l('Print A4 format label');
        Module::getInstanceByName('dpdpoland');

        echo '
			<a href="'.$currentIndex.'&'.$this->identifier.'='.$id.'&printa4formatlabel&token='.($token ? $token : $this->token).'">
			<img src="'._DPDPOLAND_IMG_URI_.'download.gif" alt="'.$_cacheLang['printa4'].'" title="'.$_cacheLang['printa4'].'" /></a>';
    }

    /**
     * Displays link used to print label format label
     *
     * @param string $token BackOffice token
     * @param int $id Order identifier
     */
    private function _displayPrintlabelLink($token, $id)
    {
        global $currentIndex;

        $_cacheLang['printlabel'] = $this->l('Print label format label');
        Module::getInstanceByName('dpdpoland');

        echo '
			<a href="'.$currentIndex.'&'.$this->identifier.'='.$id.'&printlabelformatlabel&token='.($token ? $token : $this->token).'">
			<img src="'._DPDPOLAND_IMG_URI_.'download.gif" alt="'.$_cacheLang['printlabel'].'" title="'.$_cacheLang['printlabel'].'" /></a>';
    }

    /**
     * Main controller (tab) function used to make actions in page
     */
    public function postProcess()
    {
        parent::postProcess();

        if (Tools::isSubmit('submitBulkprint_a4order')) {
            $module_instance = Module::getInstanceByName('dpdpoland');
            
            if ($errors = $module_instance->printMultipleLabels(DpdPolandConfiguration::PRINTOUT_FORMAT_A4)) {
                $this->_errors = $errors;
            }
        }

        if (Tools::isSubmit('submitBulkprint_labelorder')) {
            $module_instance = Module::getInstanceByName('dpdpoland');

            if ($errors = $module_instance->printMultipleLabels(DpdPolandConfiguration::PRINTOUT_FORMAT_LABEL)) {
                $this->_errors = $errors;
            }
        }

        if (Tools::isSubmit('printa4formatlabel')) {
            $module_instance = Module::getInstanceByName('dpdpoland');

            if ($error = $module_instance->printSingleLabel(DpdPolandConfiguration::PRINTOUT_FORMAT_A4)) {
                $this->_errors[] = $error;
            }
        }

        if (Tools::isSubmit('printlabelformatlabel')) {
            $module_instance = Module::getInstanceByName('dpdpoland');

            if ($error = $module_instance->printSingleLabel(DpdPolandConfiguration::PRINTOUT_FORMAT_LABEL)) {
                $this->_errors[] = $error;
            }
        }
    }
}
