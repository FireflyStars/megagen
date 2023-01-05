<?php /* Smarty version Smarty-3.1.19, created on 2020-08-17 05:17:10
         compiled from "/var/www/html/themes/megagen/modules/productscategory/productscategory.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2968908325f39f6b6258307-11751521%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f8bf81056420783d44dd290f63314f0749c51388' => 
    array (
      0 => '/var/www/html/themes/megagen/modules/productscategory/productscategory.tpl',
      1 => 1596578529,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2968908325f39f6b6258307-11751521',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'categoryProducts' => 0,
    'categoryProduct' => 0,
    'link' => 0,
    'homeSize' => 0,
    'quick_view' => 0,
    'PS_CATALOG_MODE' => 0,
    'restricted_country_mode' => 0,
    'priceDisplay' => 0,
    'currency' => 0,
    'PS_STOCK_MANAGEMENT' => 0,
    'page_name' => 0,
    'comparator_max_item' => 0,
    'ProdDisplayPrice' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f39f6b644aa91_45838718',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f39f6b644aa91_45838718')) {function content_5f39f6b644aa91_45838718($_smarty_tpl) {?>
<?php if (count($_smarty_tpl->tpl_vars['categoryProducts']->value)>0&&$_smarty_tpl->tpl_vars['categoryProducts']->value!==false) {?>
<section class="page-product-box blockproductscategory">
	<h3 class="productscategory_h3 page-product-heading">
		<?php if (count($_smarty_tpl->tpl_vars['categoryProducts']->value)==1) {?>
			<span><?php echo smartyTranslate(array('s'=>'%s other product in the same category:','sprintf'=>array(count($_smarty_tpl->tpl_vars['categoryProducts']->value)),'mod'=>'productscategory'),$_smarty_tpl);?>
</span>
		<?php } else { ?>
			<span><?php echo smartyTranslate(array('s'=>'%s other products in the same category:','sprintf'=>array(count($_smarty_tpl->tpl_vars['categoryProducts']->value)),'mod'=>'productscategory'),$_smarty_tpl);?>
</span>
		<?php }?>
	</h3>
	<div id="productscategory_list" class="clearfix">
		<ul id="bxslider1" class="bxslider clearfix product_list grid">
		<?php  $_smarty_tpl->tpl_vars['categoryProduct'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['categoryProduct']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['categoryProducts']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['categoryProduct']->key => $_smarty_tpl->tpl_vars['categoryProduct']->value) {
$_smarty_tpl->tpl_vars['categoryProduct']->_loop = true;
?>
			<li class="product-box item">


					<div class="product-container" itemscope itemtype="http://schema.org/Product">
				<div class="left-block">
					<div class="product-image-container">
						<a class="product_img_link" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categoryProduct']->value['link'], ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categoryProduct']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
" itemprop="url">
							<img class="replace-2x img-responsive" src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getImageLink($_smarty_tpl->tpl_vars['categoryProduct']->value['link_rewrite'],$_smarty_tpl->tpl_vars['categoryProduct']->value['id_image'],'home_default'), ENT_QUOTES, 'UTF-8', true);?>
" alt="<?php if (!empty($_smarty_tpl->tpl_vars['categoryProduct']->value['legend'])) {?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categoryProduct']->value['legend'], ENT_QUOTES, 'UTF-8', true);?>
<?php } else { ?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categoryProduct']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
<?php }?>" title="<?php if (!empty($_smarty_tpl->tpl_vars['categoryProduct']->value['legend'])) {?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categoryProduct']->value['legend'], ENT_QUOTES, 'UTF-8', true);?>
<?php } else { ?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categoryProduct']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
<?php }?>" <?php if (isset($_smarty_tpl->tpl_vars['homeSize']->value)) {?> width="<?php echo $_smarty_tpl->tpl_vars['homeSize']->value['width'];?>
" height="<?php echo $_smarty_tpl->tpl_vars['homeSize']->value['height'];?>
"<?php }?> itemprop="image" />
						</a>
						<?php if (isset($_smarty_tpl->tpl_vars['quick_view']->value)&&$_smarty_tpl->tpl_vars['quick_view']->value) {?>
							<div class="quick-view-wrapper-mobile">
							<a class="quick-view-mobile" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categoryProduct']->value['link'], ENT_QUOTES, 'UTF-8', true);?>
" rel="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categoryProduct']->value['link'], ENT_QUOTES, 'UTF-8', true);?>
">
								<i class="icon-eye-open"></i>
							</a>
						</div>
						<a class="quick-view" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categoryProduct']->value['link'], ENT_QUOTES, 'UTF-8', true);?>
" rel="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categoryProduct']->value['link'], ENT_QUOTES, 'UTF-8', true);?>
">
							<span><?php echo smartyTranslate(array('s'=>'Quick view'),$_smarty_tpl);?>
</span>
						</a>
						<?php }?>
						<?php if ((!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value&&((isset($_smarty_tpl->tpl_vars['categoryProduct']->value['show_price'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['show_price'])||(isset($_smarty_tpl->tpl_vars['categoryProduct']->value['available_for_order'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['available_for_order'])))) {?>
							<div class="content_price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
								<?php if (isset($_smarty_tpl->tpl_vars['categoryProduct']->value['show_price'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['show_price']&&!isset($_smarty_tpl->tpl_vars['restricted_country_mode']->value)) {?>
									<span itemprop="price" class="price product-price">
                                        <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayProductPriceBlock",'product'=>$_smarty_tpl->tpl_vars['categoryProduct']->value,'type'=>"before_price"),$_smarty_tpl);?>

										<?php if (!$_smarty_tpl->tpl_vars['priceDisplay']->value) {?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPrice'][0][0]->convertPrice(array('price'=>$_smarty_tpl->tpl_vars['categoryProduct']->value['price']),$_smarty_tpl);?>
<?php } else { ?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPrice'][0][0]->convertPrice(array('price'=>$_smarty_tpl->tpl_vars['categoryProduct']->value['price_tax_exc']),$_smarty_tpl);?>
<?php }?>
									</span>
									<meta itemprop="priceCurrency" content="<?php echo $_smarty_tpl->tpl_vars['currency']->value->iso_code;?>
" />
									<?php if (isset($_smarty_tpl->tpl_vars['categoryProduct']->value['specific_prices'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['specific_prices']&&isset($_smarty_tpl->tpl_vars['categoryProduct']->value['specific_prices']['reduction'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['specific_prices']['reduction']>0) {?>
										<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayProductPriceBlock",'product'=>$_smarty_tpl->tpl_vars['categoryProduct']->value,'type'=>"old_price"),$_smarty_tpl);?>

										<span class="old-price product-price">
											<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPrice'][0][0]->displayWtPrice(array('p'=>$_smarty_tpl->tpl_vars['categoryProduct']->value['price_without_reduction']),$_smarty_tpl);?>

										</span>
										<?php if ($_smarty_tpl->tpl_vars['categoryProduct']->value['specific_prices']['reduction_type']=='percentage') {?>
											<span class="price-percent-reduction">-<?php echo $_smarty_tpl->tpl_vars['categoryProduct']->value['specific_prices']['reduction']*100;?>
%</span>
										<?php }?>
									<?php }?>
									<?php if ($_smarty_tpl->tpl_vars['PS_STOCK_MANAGEMENT']->value&&isset($_smarty_tpl->tpl_vars['categoryProduct']->value['available_for_order'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['available_for_order']&&!isset($_smarty_tpl->tpl_vars['restricted_country_mode']->value)) {?>
										<span class="unvisible">
											<?php if (($_smarty_tpl->tpl_vars['categoryProduct']->value['allow_oosp']||$_smarty_tpl->tpl_vars['categoryProduct']->value['quantity']>0)) {?>
													<link itemprop="availability" href="http://schema.org/InStock" /><?php if ($_smarty_tpl->tpl_vars['categoryProduct']->value['quantity']<=0) {?><?php if ($_smarty_tpl->tpl_vars['categoryProduct']->value['allow_oosp']) {?><?php if (isset($_smarty_tpl->tpl_vars['categoryProduct']->value['available_later'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['available_later']) {?><?php echo $_smarty_tpl->tpl_vars['categoryProduct']->value['available_later'];?>
<?php } else { ?><?php echo smartyTranslate(array('s'=>'In Stock'),$_smarty_tpl);?>
<?php }?><?php } else { ?><?php echo smartyTranslate(array('s'=>'Out of stock'),$_smarty_tpl);?>
<?php }?><?php } else { ?><?php if (isset($_smarty_tpl->tpl_vars['categoryProduct']->value['available_now'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['available_now']) {?><?php echo $_smarty_tpl->tpl_vars['categoryProduct']->value['available_now'];?>
<?php } else { ?><?php echo smartyTranslate(array('s'=>'In Stock'),$_smarty_tpl);?>
<?php }?><?php }?>
											<?php } elseif ((isset($_smarty_tpl->tpl_vars['categoryProduct']->value['quantity_all_versions'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['quantity_all_versions']>0)) {?>
													<link itemprop="availability" href="http://schema.org/LimitedAvailability" /><?php echo smartyTranslate(array('s'=>'Product available with different options'),$_smarty_tpl);?>


											<?php } else { ?>
													<link itemprop="availability" href="http://schema.org/OutOfStock" /><?php echo smartyTranslate(array('s'=>'Out of stock'),$_smarty_tpl);?>

											<?php }?>
										</span>
									<?php }?>
									<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayProductPriceBlock",'product'=>$_smarty_tpl->tpl_vars['categoryProduct']->value,'type'=>"price"),$_smarty_tpl);?>

									<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayProductPriceBlock",'product'=>$_smarty_tpl->tpl_vars['categoryProduct']->value,'type'=>"unit_price"),$_smarty_tpl);?>

								<?php }?>
							</div>
						<?php }?>
						<?php if (isset($_smarty_tpl->tpl_vars['categoryProduct']->value['new'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['new']==1) {?>
							<a class="new-box" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categoryProduct']->value['link'], ENT_QUOTES, 'UTF-8', true);?>
">
								<span class="new-label"><?php echo smartyTranslate(array('s'=>'New'),$_smarty_tpl);?>
</span>
							</a>
						<?php }?>
						<?php if (isset($_smarty_tpl->tpl_vars['categoryProduct']->value['on_sale'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['on_sale']&&isset($_smarty_tpl->tpl_vars['categoryProduct']->value['show_price'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['show_price']&&!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value) {?>
							<a class="sale-box" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categoryProduct']->value['link'], ENT_QUOTES, 'UTF-8', true);?>
">
								<span class="sale-label"><?php echo smartyTranslate(array('s'=>'Sale!'),$_smarty_tpl);?>
</span>
							</a>
						<?php }?>
					</div>
					<?php if (isset($_smarty_tpl->tpl_vars['categoryProduct']->value['is_virtual'])&&!$_smarty_tpl->tpl_vars['categoryProduct']->value['is_virtual']) {?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayProductDeliveryTime",'product'=>$_smarty_tpl->tpl_vars['categoryProduct']->value),$_smarty_tpl);?>
<?php }?>
					<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayProductPriceBlock",'product'=>$_smarty_tpl->tpl_vars['categoryProduct']->value,'type'=>"weight"),$_smarty_tpl);?>

				</div>
				<div class="right-block">
					<h5 itemprop="name">
						<?php if (isset($_smarty_tpl->tpl_vars['categoryProduct']->value['pack_quantity'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['pack_quantity']) {?><?php echo (intval($_smarty_tpl->tpl_vars['categoryProduct']->value['pack_quantity'])).(' x ');?>
<?php }?>
						<a class="product-name" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categoryProduct']->value['link'], ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categoryProduct']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
" itemprop="url" >
							<?php echo htmlspecialchars($_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['truncate'][0][0]->smarty_modifier_truncate($_smarty_tpl->tpl_vars['categoryProduct']->value['name'],100,'...'), ENT_QUOTES, 'UTF-8', true);?>
 
						</a>
					</h5>
					<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>'displayProductListReviews','product'=>$_smarty_tpl->tpl_vars['categoryProduct']->value),$_smarty_tpl);?>

					<p class="product-desc" itemprop="description">
						<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['truncate'][0][0]->smarty_modifier_truncate(strip_tags($_smarty_tpl->tpl_vars['categoryProduct']->value['description_short']),360,'...');?>

					</p>
					<?php if ((!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value&&((isset($_smarty_tpl->tpl_vars['categoryProduct']->value['show_price'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['show_price'])||(isset($_smarty_tpl->tpl_vars['categoryProduct']->value['available_for_order'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['available_for_order'])))) {?>
					
					<?php }?>
					<div class="button-container">
						
						<a class="button lnk_view btn btn-default" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categoryProduct']->value['link'], ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'View'),$_smarty_tpl);?>
">
							<span><?php if ((isset($_smarty_tpl->tpl_vars['categoryProduct']->value['customization_required'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['customization_required'])) {?><?php echo smartyTranslate(array('s'=>'Customize'),$_smarty_tpl);?>
<?php } else { ?><?php echo smartyTranslate(array('s'=>'Produkt anzeigen'),$_smarty_tpl);?>
<?php }?></span>
						</a>
					</div>
					<?php if (isset($_smarty_tpl->tpl_vars['categoryProduct']->value['color_list'])) {?>
						<div class="color-list-container"><?php echo $_smarty_tpl->tpl_vars['categoryProduct']->value['color_list'];?>
</div>
					<?php }?>
					<div class="product-flags">
						<?php if ((!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value&&((isset($_smarty_tpl->tpl_vars['categoryProduct']->value['show_price'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['show_price'])||(isset($_smarty_tpl->tpl_vars['categoryProduct']->value['available_for_order'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['available_for_order'])))) {?>
							<?php if (isset($_smarty_tpl->tpl_vars['categoryProduct']->value['online_only'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['online_only']) {?>
								<span class="online_only"><?php echo smartyTranslate(array('s'=>'Online only'),$_smarty_tpl);?>
</span>
							<?php }?>
						<?php }?>
						<?php if (isset($_smarty_tpl->tpl_vars['categoryProduct']->value['on_sale'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['on_sale']&&isset($_smarty_tpl->tpl_vars['categoryProduct']->value['show_price'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['show_price']&&!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value) {?>
							<?php } elseif (isset($_smarty_tpl->tpl_vars['categoryProduct']->value['reduction'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['reduction']&&isset($_smarty_tpl->tpl_vars['categoryProduct']->value['show_price'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['show_price']&&!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value) {?>
								<span class="discount"><?php echo smartyTranslate(array('s'=>'Reduced price!'),$_smarty_tpl);?>
</span>
							<?php }?>
					</div>
					<?php if ((!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value&&$_smarty_tpl->tpl_vars['PS_STOCK_MANAGEMENT']->value&&((isset($_smarty_tpl->tpl_vars['categoryProduct']->value['show_price'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['show_price'])||(isset($_smarty_tpl->tpl_vars['categoryProduct']->value['available_for_order'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['available_for_order'])))) {?>
						<?php if (isset($_smarty_tpl->tpl_vars['categoryProduct']->value['available_for_order'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['available_for_order']&&!isset($_smarty_tpl->tpl_vars['restricted_country_mode']->value)) {?>
							<span class="availability">
								<?php if (($_smarty_tpl->tpl_vars['categoryProduct']->value['allow_oosp']||$_smarty_tpl->tpl_vars['categoryProduct']->value['quantity']>0)) {?>
									<span class="<?php if ($_smarty_tpl->tpl_vars['categoryProduct']->value['quantity']<=0&&isset($_smarty_tpl->tpl_vars['categoryProduct']->value['allow_oosp'])&&!$_smarty_tpl->tpl_vars['categoryProduct']->value['allow_oosp']) {?> label-danger<?php } elseif ($_smarty_tpl->tpl_vars['categoryProduct']->value['quantity']<=0) {?> label-warning<?php } else { ?> label-success<?php }?>">
										<?php if ($_smarty_tpl->tpl_vars['categoryProduct']->value['quantity']<=0) {?><?php if ($_smarty_tpl->tpl_vars['categoryProduct']->value['allow_oosp']) {?><?php if (isset($_smarty_tpl->tpl_vars['categoryProduct']->value['available_later'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['available_later']) {?><?php echo $_smarty_tpl->tpl_vars['categoryProduct']->value['available_later'];?>
<?php } else { ?><?php echo smartyTranslate(array('s'=>'In Stock'),$_smarty_tpl);?>
<?php }?><?php } else { ?><?php echo smartyTranslate(array('s'=>'Out of stock'),$_smarty_tpl);?>
<?php }?><?php } else { ?><?php if (isset($_smarty_tpl->tpl_vars['categoryProduct']->value['available_now'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['available_now']) {?><?php echo $_smarty_tpl->tpl_vars['categoryProduct']->value['available_now'];?>
<?php } else { ?><?php echo smartyTranslate(array('s'=>'In Stock'),$_smarty_tpl);?>
<?php }?><?php }?>
									</span>
								<?php } elseif ((isset($_smarty_tpl->tpl_vars['categoryProduct']->value['quantity_all_versions'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['quantity_all_versions']>0)) {?>
									<span class="label-warning">
										<?php echo smartyTranslate(array('s'=>'Product available with different options'),$_smarty_tpl);?>

									</span>
								<?php } else { ?>
									<span class="label-danger">
										<?php echo smartyTranslate(array('s'=>'Out of stock'),$_smarty_tpl);?>

									</span>
								<?php }?>
							</span>
						<?php }?>
					<?php }?>
				</div>
				<?php if ($_smarty_tpl->tpl_vars['page_name']->value!='index') {?>
	 				<div class="functional-buttons clearfix">
						<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>'displayProductListFunctionalButtons','product'=>$_smarty_tpl->tpl_vars['categoryProduct']->value),$_smarty_tpl);?>

						<?php if (isset($_smarty_tpl->tpl_vars['comparator_max_item']->value)&&$_smarty_tpl->tpl_vars['comparator_max_item']->value) {?>
							<div class="compare">
								<a class="add_to_compare" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categoryProduct']->value['link'], ENT_QUOTES, 'UTF-8', true);?>
" data-id-product="<?php echo $_smarty_tpl->tpl_vars['categoryProduct']->value['id_product'];?>
"><?php echo smartyTranslate(array('s'=>'Add to Compare'),$_smarty_tpl);?>
</a>
							</div>
						<?php }?>
					</div>
				<?php }?>
			</div><!-- .product-container> -->


<!--
				<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getProductLink($_smarty_tpl->tpl_vars['categoryProduct']->value['id_product'],$_smarty_tpl->tpl_vars['categoryProduct']->value['link_rewrite'],$_smarty_tpl->tpl_vars['categoryProduct']->value['category'],$_smarty_tpl->tpl_vars['categoryProduct']->value['ean13']);?>
" class="lnk_img product-image" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categoryProduct']->value['name']);?>
"><img src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getImageLink($_smarty_tpl->tpl_vars['categoryProduct']->value['link_rewrite'],$_smarty_tpl->tpl_vars['categoryProduct']->value['id_image'],'home_default'), ENT_QUOTES, 'UTF-8', true);?>
" alt="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categoryProduct']->value['name']);?>
" /></a>















				<h5 itemprop="name" class="product-name">
					<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getProductLink($_smarty_tpl->tpl_vars['categoryProduct']->value['id_product'],$_smarty_tpl->tpl_vars['categoryProduct']->value['link_rewrite'],$_smarty_tpl->tpl_vars['categoryProduct']->value['category'],$_smarty_tpl->tpl_vars['categoryProduct']->value['ean13']), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['categoryProduct']->value['name']);?>
"><?php echo htmlspecialchars($_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['truncate'][0][0]->smarty_modifier_truncate($_smarty_tpl->tpl_vars['categoryProduct']->value['name'],42,'...'), ENT_QUOTES, 'UTF-8', true);?>
</a>
				</h5>
				<?php if ($_smarty_tpl->tpl_vars['ProdDisplayPrice']->value&&$_smarty_tpl->tpl_vars['categoryProduct']->value['show_price']==1&&!isset($_smarty_tpl->tpl_vars['restricted_country_mode']->value)&&!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value) {?>
					<p class="price_display">
					<?php if (isset($_smarty_tpl->tpl_vars['categoryProduct']->value['specific_prices'])&&$_smarty_tpl->tpl_vars['categoryProduct']->value['specific_prices']&&(number_format($_smarty_tpl->tpl_vars['categoryProduct']->value['displayed_price'],2)!==number_format($_smarty_tpl->tpl_vars['categoryProduct']->value['price_without_reduction'],2))) {?>

						<span class="price special-price"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPrice'][0][0]->convertPrice(array('price'=>$_smarty_tpl->tpl_vars['categoryProduct']->value['displayed_price']),$_smarty_tpl);?>
</span>
						<?php if ($_smarty_tpl->tpl_vars['categoryProduct']->value['specific_prices']['reduction']&&$_smarty_tpl->tpl_vars['categoryProduct']->value['specific_prices']['reduction_type']=='percentage') {?>
							<span class="price-percent-reduction small">-<?php echo $_smarty_tpl->tpl_vars['categoryProduct']->value['specific_prices']['reduction']*100;?>
%</span>
						<?php }?>
						<span class="old-price"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPrice'][0][0]->displayWtPrice(array('p'=>$_smarty_tpl->tpl_vars['categoryProduct']->value['price_without_reduction']),$_smarty_tpl);?>
</span>

					<?php } else { ?>
						<span class="price"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPrice'][0][0]->convertPrice(array('price'=>$_smarty_tpl->tpl_vars['categoryProduct']->value['displayed_price']),$_smarty_tpl);?>
</span>
					<?php }?>
					</p>
				<?php } else { ?>
				<br />
				<?php }?>
				 -->
			</li>
		<?php } ?>
		</ul>
	</div>
</section>
<?php }?>
<?php }} ?>
