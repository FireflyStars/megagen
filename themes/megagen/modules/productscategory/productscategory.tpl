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
{if count($categoryProducts) > 0 && $categoryProducts !== false}
<section class="page-product-box blockproductscategory">
	<h3 class="productscategory_h3 page-product-heading">
		{if $categoryProducts|@count == 1}
			<span>{l s='%s other product in the same category:' sprintf=[$categoryProducts|@count] mod='productscategory'}</span>
		{else}
			<span>{l s='%s other products in the same category:' sprintf=[$categoryProducts|@count] mod='productscategory'}</span>
		{/if}
	</h3>
	<div id="productscategory_list" class="clearfix">
		<ul id="bxslider1" class="bxslider clearfix product_list grid">
		{foreach from=$categoryProducts item='categoryProduct' name=categoryProduct}
			<li class="product-box item">


					<div class="product-container" itemscope itemtype="http://schema.org/Product">
				<div class="left-block">
					<div class="product-image-container">
						<a class="product_img_link" href="{$categoryProduct.link|escape:'html':'UTF-8'}" title="{$categoryProduct.name|escape:'html':'UTF-8'}" itemprop="url">
							<img class="replace-2x img-responsive" src="{$link->getImageLink($categoryProduct.link_rewrite, $categoryProduct.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($categoryProduct.legend)}{$categoryProduct.legend|escape:'html':'UTF-8'}{else}{$categoryProduct.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($categoryProduct.legend)}{$categoryProduct.legend|escape:'html':'UTF-8'}{else}{$categoryProduct.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
						</a>
						{if isset($quick_view) && $quick_view}
							<div class="quick-view-wrapper-mobile">
							<a class="quick-view-mobile" href="{$categoryProduct.link|escape:'html':'UTF-8'}" rel="{$categoryProduct.link|escape:'html':'UTF-8'}">
								<i class="icon-eye-open"></i>
							</a>
						</div>
						<a class="quick-view" href="{$categoryProduct.link|escape:'html':'UTF-8'}" rel="{$categoryProduct.link|escape:'html':'UTF-8'}">
							<span>{l s='Quick view'}</span>
						</a>
						{/if}
						{if (!$PS_CATALOG_MODE && ((isset($categoryProduct.show_price) && $categoryProduct.show_price) || (isset($categoryProduct.available_for_order) && $categoryProduct.available_for_order)))}
							<div class="content_price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
								{if isset($categoryProduct.show_price) && $categoryProduct.show_price && !isset($restricted_country_mode)}
									<span itemprop="price" class="price product-price">
                                        {hook h="displayProductPriceBlock" product=$categoryProduct type="before_price"}
										{if !$priceDisplay}{convertPrice price=$categoryProduct.price}{else}{convertPrice price=$categoryProduct.price_tax_exc}{/if}
									</span>
									<meta itemprop="priceCurrency" content="{$currency->iso_code}" />
									{if isset($categoryProduct.specific_prices) && $categoryProduct.specific_prices && isset($categoryProduct.specific_prices.reduction) && $categoryProduct.specific_prices.reduction > 0}
										{hook h="displayProductPriceBlock" product=$categoryProduct type="old_price"}
										<span class="old-price product-price">
											{displayWtPrice p=$categoryProduct.price_without_reduction}
										</span>
										{if $categoryProduct.specific_prices.reduction_type == 'percentage'}
											<span class="price-percent-reduction">-{$categoryProduct.specific_prices.reduction * 100}%</span>
										{/if}
									{/if}
									{if $PS_STOCK_MANAGEMENT && isset($categoryProduct.available_for_order) && $categoryProduct.available_for_order && !isset($restricted_country_mode)}
										<span class="unvisible">
											{if ($categoryProduct.allow_oosp || $categoryProduct.quantity > 0)}
													<link itemprop="availability" href="http://schema.org/InStock" />{if $categoryProduct.quantity <= 0}{if $categoryProduct.allow_oosp}{if isset($categoryProduct.available_later) && $categoryProduct.available_later}{$categoryProduct.available_later}{else}{l s='In Stock'}{/if}{else}{l s='Out of stock'}{/if}{else}{if isset($categoryProduct.available_now) && $categoryProduct.available_now}{$categoryProduct.available_now}{else}{l s='In Stock'}{/if}{/if}
											{elseif (isset($categoryProduct.quantity_all_versions) && $categoryProduct.quantity_all_versions > 0)}
													<link itemprop="availability" href="http://schema.org/LimitedAvailability" />{l s='Product available with different options'}

											{else}
													<link itemprop="availability" href="http://schema.org/OutOfStock" />{l s='Out of stock'}
											{/if}
										</span>
									{/if}
									{hook h="displayProductPriceBlock" product=$categoryProduct type="price"}
									{hook h="displayProductPriceBlock" product=$categoryProduct type="unit_price"}
								{/if}
							</div>
						{/if}
						{if isset($categoryProduct.new) && $categoryProduct.new == 1}
							<a class="new-box" href="{$categoryProduct.link|escape:'html':'UTF-8'}">
								<span class="new-label">{l s='New'}</span>
							</a>
						{/if}
						{if isset($categoryProduct.on_sale) && $categoryProduct.on_sale && isset($categoryProduct.show_price) && $categoryProduct.show_price && !$PS_CATALOG_MODE}
							<a class="sale-box" href="{$categoryProduct.link|escape:'html':'UTF-8'}">
								<span class="sale-label">{l s='Sale!'}</span>
							</a>
						{/if}
					</div>
					{if isset($categoryProduct.is_virtual) && !$categoryProduct.is_virtual}{hook h="displayProductDeliveryTime" product=$categoryProduct}{/if}
					{hook h="displayProductPriceBlock" product=$categoryProduct type="weight"}
				</div>
				<div class="right-block">
					<h5 itemprop="name">
						{if isset($categoryProduct.pack_quantity) && $categoryProduct.pack_quantity}{$categoryProduct.pack_quantity|intval|cat:' x '}{/if}
						<a class="product-name" href="{$categoryProduct.link|escape:'html':'UTF-8'}" title="{$categoryProduct.name|escape:'html':'UTF-8'}" itemprop="url" >
							{$categoryProduct.name|truncate:100:'...'|escape:'html':'UTF-8'} 
						</a>
					</h5>
					{hook h='displayProductListReviews' product=$categoryProduct}
					<p class="product-desc" itemprop="description">
						{$categoryProduct.description_short|strip_tags:'UTF-8'|truncate:360:'...'}
					</p>
					{if (!$PS_CATALOG_MODE AND ((isset($categoryProduct.show_price) && $categoryProduct.show_price) || (isset($categoryProduct.available_for_order) && $categoryProduct.available_for_order)))}
					{*
					<div class="content_price">
						{if isset($categoryProduct.show_price) && $categoryProduct.show_price && !isset($restricted_country_mode)}
                            {hook h="displayProductPriceBlock" product=$categoryProduct type='before_price'}


							<span class="price product-price">
								{if !$priceDisplay}{convertPrice price=$categoryProduct.price}{else}{convertPrice price=$categoryProduct.price_tax_exc}{/if}
							</span>


							{if isset($categoryProduct.specific_prices) && $categoryProduct.specific_prices && isset($categoryProduct.specific_prices.reduction) && $categoryProduct.specific_prices.reduction > 0}
								{hook h="displayProductPriceBlock" product=$categoryProduct type="old_price"}
								<span class="old-price product-price">
									{displayWtPrice p=$categoryProduct.price_without_reduction}
								</span>
								{hook h="displayProductPriceBlock" id_product=$categoryProduct.id_product type="old_price"}
								{if $categoryProduct.specific_prices.reduction_type == 'percentage'}
									<span class="price-percent-reduction">-{$categoryProduct.specific_prices.reduction * 100}%</span>
								{/if}
							{/if}
							{hook h="displayProductPriceBlock" product=$categoryProduct type="price"}
							{hook h="displayProductPriceBlock" product=$categoryProduct type="unit_price"}
                            {hook h="displayProductPriceBlock" product=$categoryProduct type='after_price'}
						{/if}
					</div>
					*}
					{/if}
					<div class="button-container">
						{*
						{if ($categoryProduct.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $categoryProduct.available_for_order && !isset($restricted_country_mode) && $categoryProduct.customizable != 2 && !$PS_CATALOG_MODE}
							{if (!isset($categoryProduct.customization_required) || !$categoryProduct.customization_required) && ($categoryProduct.allow_oosp || $categoryProduct.quantity > 0)}
								{capture}add=1&amp;id_product={$categoryProduct.id_product|intval}{if isset($static_token)}&amp;token={$static_token}{/if}{/capture}
								<a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart'}" data-id-product="{$categoryProduct.id_product|intval}" data-minimal_quantity="{if isset($categoryProduct.product_attribute_minimal_quantity) && $categoryProduct.product_attribute_minimal_quantity >= 1}{$categoryProduct.product_attribute_minimal_quantity|intval}{else}{$categoryProduct.minimal_quantity|intval}{/if}">
									<span>{l s='Add to cart'}</span>
								</a>
							{else}
								<span class="button ajax_add_to_cart_button btn btn-default disabled">
									<span>{l s='Add to cart'}</span>
								</span>
							{/if}
						{/if}
						*}
						<a class="button lnk_view btn btn-default" href="{$categoryProduct.link|escape:'html':'UTF-8'}" title="{l s='View'}">
							<span>{if (isset($categoryProduct.customization_required) && $categoryProduct.customization_required)}{l s='Customize'}{else}{l s='Produkt anzeigen'}{/if}</span>
						</a>
					</div>
					{if isset($categoryProduct.color_list)}
						<div class="color-list-container">{$categoryProduct.color_list}</div>
					{/if}
					<div class="product-flags">
						{if (!$PS_CATALOG_MODE AND ((isset($categoryProduct.show_price) && $categoryProduct.show_price) || (isset($categoryProduct.available_for_order) && $categoryProduct.available_for_order)))}
							{if isset($categoryProduct.online_only) && $categoryProduct.online_only}
								<span class="online_only">{l s='Online only'}</span>
							{/if}
						{/if}
						{if isset($categoryProduct.on_sale) && $categoryProduct.on_sale && isset($categoryProduct.show_price) && $categoryProduct.show_price && !$PS_CATALOG_MODE}
							{elseif isset($categoryProduct.reduction) && $categoryProduct.reduction && isset($categoryProduct.show_price) && $categoryProduct.show_price && !$PS_CATALOG_MODE}
								<span class="discount">{l s='Reduced price!'}</span>
							{/if}
					</div>
					{if (!$PS_CATALOG_MODE && $PS_STOCK_MANAGEMENT && ((isset($categoryProduct.show_price) && $categoryProduct.show_price) || (isset($categoryProduct.available_for_order) && $categoryProduct.available_for_order)))}
						{if isset($categoryProduct.available_for_order) && $categoryProduct.available_for_order && !isset($restricted_country_mode)}
							<span class="availability">
								{if ($categoryProduct.allow_oosp || $categoryProduct.quantity > 0)}
									<span class="{if $categoryProduct.quantity <= 0 && isset($categoryProduct.allow_oosp) && !$categoryProduct.allow_oosp} label-danger{elseif $categoryProduct.quantity <= 0} label-warning{else} label-success{/if}">
										{if $categoryProduct.quantity <= 0}{if $categoryProduct.allow_oosp}{if isset($categoryProduct.available_later) && $categoryProduct.available_later}{$categoryProduct.available_later}{else}{l s='In Stock'}{/if}{else}{l s='Out of stock'}{/if}{else}{if isset($categoryProduct.available_now) && $categoryProduct.available_now}{$categoryProduct.available_now}{else}{l s='In Stock'}{/if}{/if}
									</span>
								{elseif (isset($categoryProduct.quantity_all_versions) && $categoryProduct.quantity_all_versions > 0)}
									<span class="label-warning">
										{l s='Product available with different options'}
									</span>
								{else}
									<span class="label-danger">
										{l s='Out of stock'}
									</span>
								{/if}
							</span>
						{/if}
					{/if}
				</div>
				{if $page_name != 'index'}
	 				<div class="functional-buttons clearfix">
						{hook h='displayProductListFunctionalButtons' product=$categoryProduct}
						{if isset($comparator_max_item) && $comparator_max_item}
							<div class="compare">
								<a class="add_to_compare" href="{$categoryProduct.link|escape:'html':'UTF-8'}" data-id-product="{$categoryProduct.id_product}">{l s='Add to Compare'}</a>
							</div>
						{/if}
					</div>
				{/if}
			</div><!-- .product-container> -->


<!--
				<a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)}" class="lnk_img product-image" title="{$categoryProduct.name|htmlspecialchars}"><img src="{$link->getImageLink($categoryProduct.link_rewrite, $categoryProduct.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{$categoryProduct.name|htmlspecialchars}" /></a>















				<h5 itemprop="name" class="product-name">
					<a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)|escape:'html':'UTF-8'}" title="{$categoryProduct.name|htmlspecialchars}">{$categoryProduct.name|truncate:42:'...'|escape:'html':'UTF-8'}</a>
				</h5>
				{if $ProdDisplayPrice && $categoryProduct.show_price == 1 && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
					<p class="price_display">
					{if isset($categoryProduct.specific_prices) && $categoryProduct.specific_prices
					&& ($categoryProduct.displayed_price|number_format:2 !== $categoryProduct.price_without_reduction|number_format:2)}

						<span class="price special-price">{convertPrice price=$categoryProduct.displayed_price}</span>
						{if $categoryProduct.specific_prices.reduction && $categoryProduct.specific_prices.reduction_type == 'percentage'}
							<span class="price-percent-reduction small">-{$categoryProduct.specific_prices.reduction * 100}%</span>
						{/if}
						<span class="old-price">{displayWtPrice p=$categoryProduct.price_without_reduction}</span>

					{else}
						<span class="price">{convertPrice price=$categoryProduct.displayed_price}</span>
					{/if}
					</p>
				{else}
				<br />
				{/if}
				{*
				<div class="clearfix" style="margin-top:5px">
					{if !$PS_CATALOG_MODE && ($categoryProduct.allow_oosp || $categoryProduct.quantity > 0)}
						<div class="no-print">
							<a class="exclusive button ajax_add_to_cart_button" href="{$link->getPageLink('cart', true, NULL, "qty=1&amp;id_product={$categoryProduct.id_product|intval}&amp;token={$static_token}&amp;add")|escape:'html':'UTF-8'}" data-id-product="{$categoryProduct.id_product|intval}" title="{l s='Add to cart' mod='productscategory'}">
								<span>{l s='Add to cart' mod='productscategory'}</span>
							</a>
						</div>
					{/if}
				</div>
				*} -->
			</li>
		{/foreach}
		</ul>
	</div>
</section>
{/if}
