{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $status == 'ok'}
	<p class="alert alert-success">{l s='Your order on %s is complete.' sprintf=$shop_name mod='cheque'}</p>
	<div class="box">
		{l s='Thank you for your order!' mod='bankwire'}
		<br/>{l s='We will process it immediately and send you the goods.' mod='bankwire'}
		<br/>{l s='If you have any questions, please contact.' mod='bankwire'} <strong><a href="mailto:orders@imegagen.de">orders@imegagen.de</a></strong> oder <strong><a href="tel:062214551140">06221 - 455 1140.</a></strong>
	</div>
{else}
	<p class="alert alert-warning">
		{l s='We noticed a problem with your order. If you think this is an error, feel free to contact our' mod='bankwire'}
		<a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='customer service department.' mod='bankwire'}</a>.
	</p>
{/if}
