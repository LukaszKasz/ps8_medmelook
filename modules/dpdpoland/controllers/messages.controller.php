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

/**
 * Class DpdPolandMessagesController Responsible for flash messages management
 */
class DpdPolandMessagesController extends DpdPolandController
{
    /**
     * Name of success message saved in cookie
     */
	const DPD_POLAND_SUCCESS_MESSAGE 		= 'dpd_poland_success_message';

    /**
     * Name of error message saved in cookie
     */
	const DPD_POLAND_ERROR_MESSAGE 			= 'dpd_poland_error_message';

    /**
     * @var Cookie Cookie object instance
     */
	private $cookie;

    /**
     * DpdPolandMessagesController class constructor
     */
	public function __construct()
	{
		parent::__construct();
		$this->cookie = new Cookie(_DPDPOLAND_COOKIE_);
	}

    /**
     * Saves success message into Cookie
     *
     * @param string $message Success message text
     */
	public function setSuccessMessage($message)
	{
		if (!is_array($message))
			$this->cookie->{self::DPD_POLAND_SUCCESS_MESSAGE} = $message;
	}

    /**
     * Saves error message into Cookie
     *
     * @param string $message Error message text
     */
	public function setErrorMessage($message)
	{
		$old_message = $this->cookie->{self::DPD_POLAND_ERROR_MESSAGE};
		if ($old_message && Validate::isSerializedArray($old_message))
		{
			if (version_compare(_PS_VERSION_, '1.5', '<'))
				$old_message = unserialize($old_message);
			else
				$old_message = Tools::unSerialize($old_message);
			$message = array_merge($message, $old_message);
		}

		if (is_array($message))
			$this->context->cookie->{self::DPD_POLAND_ERROR_MESSAGE} = serialize($message);
		else
			$this->cookie->{self::DPD_POLAND_ERROR_MESSAGE} = $message;
	}

    /**
     * Collects and returns success message
     * Removes success message from Cookie
     *
     * @return string Success message
     */
	public function getSuccessMessage()
	{
		$message = $this->cookie->{self::DPD_POLAND_SUCCESS_MESSAGE};
		$this->cookie->__unset(self::DPD_POLAND_SUCCESS_MESSAGE);
		return $message ? $message : '';
	}

    /**
     * Collects and returns error message
     * Removes error message from Cookie
     *
     * @return array|string Error message(s)
     */
	public function getErrorMessage()
	{
		$message = $this->cookie->{self::DPD_POLAND_ERROR_MESSAGE};
		if (Validate::isSerializedArray($message))
			if (version_compare(_PS_VERSION_, '1.5', '<'))
				$message = unserialize($message);
			else
				$message = Tools::unSerialize($message);
		$this->cookie->__unset(self::DPD_POLAND_ERROR_MESSAGE);
		if (is_array($message))
			return array_unique($message);
		return $message ? array($message) : '';
	}
}