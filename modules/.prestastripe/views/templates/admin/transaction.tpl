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
*	@author PrestaShop SA <contact@prestashop.com>
*	@copyright	2007-2015 PrestaShop SA
*	@license		http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*}
 <table class="table">
	<tr>
		<th>{l s='Date (last update)' mod='prestastripe'}</th>
	   	<th>{l s='Stripe Payment ID' mod='prestastripe'}</th>
	   	<th>{l s='Name' mod='prestastripe'}</th>
      <th>{l s='Card type' mod='prestastripe'}</th>
	   	<th>{l s='Amount Paid' mod='prestastripe'}</th>
	   	<th>{l s='Balance' mod='prestastripe'}</th>
	   	<th>{l s='Result' mod='prestastripe'}</th>
	</tr>
	{foreach from=$tenta key=k item=v}
	<tr>
		<td>{$v.date|escape:'html'}</td>
		<td>{$v.id_stripe|escape:'html'}</td>
		<td>{$v.name|escape:'html'}</td>
		<td><img src="{$module_dir|escape:html:'UTF-8'}/views/img/cc-{$v.type|escape:'html'}.png" alt="card type" /></td>
		<td>{$v.amount|escape:'html'} {$v.currency|escape:'html'}</td>
		<td>{$v.refund|escape:'html'} {$v.currency|escape:'html'}</td>
		{if $v.result == 2}
			<td>Refund</td>
		{elseif $v.result == 3}
			<td>Partial Refund</td>
		{else}
			<td><img src="{$module_dir|escape:html:'UTF-8'}/views/img/{$v.result|escape:'html'}ok.gif" alt="result" /></td>
		{/if}
	</tr>
	{/foreach}
</table>
