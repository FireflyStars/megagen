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

{assign var=tplArr value=[3, 31, 310, 311, 312, 313, 314, 315,316,318,322,327,328,329,330,331,332,333,334,335,336,337,338,339,340,341,342,344,345,317,347, 352,353,354,355,356,357,358,359,360,361,362,363,365,370,371,372,373,374,375,376,377,378,379,380,408,410,411,412,413]}

{if isset($category) && isset($category->id) && in_array($category->id, $tplArr) && !$mobile_device}
 {if $discount != ''}
        <div style="    background: #4e4e4e;color: #fff;margin: 5px;padding: 11px;">Discount: {$discount} %</div>
    {/if}
    
    {include file="./category-`$category->id`.tpl"}
{else}
 {if $discount != ''}
        <div style="    background: #4e4e4e;color: #fff;margin: 5px;padding: 11px;">Discount: {$discount} %</div>
    {/if}

    {include file="./category-default.tpl"}
{/if}