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
<!-- Block Newsletter module-->
<div id="newsletter_block_left" class="block">
	<h4>{l s='Newsletter' mod='blocknewsletter'}</h4>
	<div class="block_content">

		<!--
			<form action="http://domain.us11.list-manage.com/subscribe/post" method="POST">
			enter mailchimp subscribe here...
		-->
		<form action="/" method="POST">
			<div class="form-group">
				<input type="hidden" name="u" value="0ceef1d2fffb3f47b77f7dde8">
				<input type="hidden" name="id" value="24238b548f">
				<!-- <input class="form-control" type="text" name="MERGE1" id="MERGE1" size="25" value="" placeholder="First Name"> -->
				<!-- <input class="form-control" type="text" name="MERGE2" id="MERGE2" size="25" value="" placeholder="Last Name"> -->
				{if $lang_iso eq 'fr'}
					<input class="form-control" type="email" autocapitalize="off" autocorrect="off" name="MERGE0" id="MERGE0" size="18" value="" placeholder="Saisissez votre adresse e-mail">
				{else}
					<input class="form-control" type="email" autocapitalize="off" autocorrect="off" name="MERGE0" id="MERGE0" size="18" value="" placeholder="
Geben sie ihre E-Mail Adresse">
				{/if}
				<button class="btn btn-default button button-small" type="submit" name="submitNewsletter">
				<span>{l s='Ok' mod='blocknewsletter'}</span>
				</button>
			</div>
		</form>

	</div>
	{hook h="displayBlockNewsletterBottom" from='blocknewsletter'}
</div>
<!-- /Block Newsletter module-->
{strip}
{if isset($msg) && $msg}
{addJsDef msg_newsl=$msg|@addcslashes:'\''}
{/if}
{if isset($nw_error)}
{addJsDef nw_error=$nw_error}
{/if}
{addJsDefL name=placeholder_blocknewsletter}{l s='Enter your e-mail' mod='blocknewsletter' js=1}{/addJsDefL}
{if isset($msg) && $msg}
	{addJsDefL name=alert_blocknewsletter}{l s='Newsletter : %1$s' sprintf=$msg js=1 mod="blocknewsletter"}{/addJsDefL}
{/if}
{/strip}
