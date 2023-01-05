<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Tools extends ToolsCore
{

	/**
	 * Set cookie currency from POST or default currency
	 *
	 * @return Currency object
	 */
	public static function setCurrency($cookie)
	{
		if (file_exists('modules/locationdetection/locationdetection.php'))
		{
			include_once('modules/locationdetection/locationdetection.php');
			$ld = new LocationDetection();
			if ($ld->active)
			{
				// Force Language -> Currency connection
				if ($ld->_ld_force_lr == 1 && $id_cur = $ld->getCurrencyIdByLang((int)$cookie->id_lang))
				{
					if (isset($_POST['id_currency']))
						$_POST['id_currency'] = $id_cur;
					else
						$cookie->id_currency = $id_cur;
				}
				// Link Language -> Currency connection (only on language change).
				if (isset($cookie->ld_last_lang) && $cookie->ld_last_lang != (int)$cookie->id_lang && $id_cur = $ld->getCurrencyIdByLang((int)$cookie->id_lang))
				{
					if (isset($_POST['id_currency']))
						$_POST['id_currency'] = $id_cur;
					else
						$cookie->id_currency = $id_cur;
				}
				$cookie->ld_last_lang = (int)$cookie->id_lang;
			}
		}
		if (Tools::isSubmit('SubmitCurrency'))
			if (isset($_POST['id_currency']) && is_numeric($_POST['id_currency']))
			{
				$currency = Currency::getCurrencyInstance($_POST['id_currency']);
				if (is_object($currency) && $currency->id && !$currency->deleted && $currency->isAssociatedToShop())
					$cookie->id_currency = (int)$currency->id;
			}
		
		$currency = null;
		if ((int)$cookie->id_currency)
			$currency = Currency::getCurrencyInstance((int)$cookie->id_currency);
		if (!Validate::isLoadedObject($currency) || (bool)$currency->deleted || !(bool)$currency->active)
			$currency = Currency::getCurrencyInstance(Configuration::get('PS_CURRENCY_DEFAULT'));

		$cookie->id_currency = (int)$currency->id;
		if ($currency->isAssociatedToShop())
			return $currency;
		else
		{
			// get currency from context
			$currency = Shop::getEntityIds('currency', Context::getContext()->shop->id, true, true);
			if (isset($currency[0]) && $currency[0]['id_currency'])
			{
				$cookie->id_currency = $currency[0]['id_currency'];
				return Currency::getCurrencyInstance((int)$cookie->id_currency);
			}
		}
		return $currency;
	}
}