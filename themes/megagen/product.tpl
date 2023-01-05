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

{assign var=tpl2Arr value=[2, 2077,2277,2175]}
{assign var=tpl3Arr value=[2272]}
{assign var=tpl4Arr value=[2153]}


{if isset($product) && isset($product->id) && in_array($product->id, $tpl4Arr)}
    {include file="./product-2153.tpl"}
{elseif isset($product) && isset($product->id) && in_array($product->id, $tpl2Arr)}
    {include file="./product-2.tpl"}
{elseif isset($product) && isset($product->id) && in_array($product->id, $tpl3Arr)}
    {include file="./product-type.tpl"}
{else}

{*include file="./product-default.tpl"*}


 {if count($combinations) == 0}
  {include file="./product-default2.tpl"}
 {else}
  {include file="./product-3.tpl"}
 {/if}


   
{/if}