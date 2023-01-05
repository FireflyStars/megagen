<?php /* Smarty version Smarty-3.1.19, created on 2020-08-19 15:08:04
         compiled from "/var/www/html/themes/megagen/modules/blockcms/blockcms.tpl" */ ?>
<?php /*%%SmartyHeaderCode:13452547645f3d243426a734-59066175%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4560dbd110dfe7ae629e2bf6a7269cbe18d876e3' => 
    array (
      0 => '/var/www/html/themes/megagen/modules/blockcms/blockcms.tpl',
      1 => 1596578522,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13452547645f3d243426a734-59066175',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'block' => 0,
    'cms_titles' => 0,
    'cms_key' => 0,
    'cms_title' => 0,
    'cms_page' => 0,
    'link' => 0,
    'show_price_drop' => 0,
    'PS_CATALOG_MODE' => 0,
    'show_new_products' => 0,
    'show_best_sales' => 0,
    'display_stores_footer' => 0,
    'show_contact' => 0,
    'contact_url' => 0,
    'cmslinks' => 0,
    'cmslink' => 0,
    'show_sitemap' => 0,
    'footer_text' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f3d2434319a56_74713142',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f3d2434319a56_74713142')) {function content_5f3d2434319a56_74713142($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/var/www/html/tools/smarty/plugins/modifier.date_format.php';
?>

<?php if ($_smarty_tpl->tpl_vars['block']->value==1) {?>
	<!-- Block CMS module -->
	<?php  $_smarty_tpl->tpl_vars['cms_title'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cms_title']->_loop = false;
 $_smarty_tpl->tpl_vars['cms_key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['cms_titles']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cms_title']->key => $_smarty_tpl->tpl_vars['cms_title']->value) {
$_smarty_tpl->tpl_vars['cms_title']->_loop = true;
 $_smarty_tpl->tpl_vars['cms_key']->value = $_smarty_tpl->tpl_vars['cms_title']->key;
?>
		<section id="informations_block_left_<?php echo $_smarty_tpl->tpl_vars['cms_key']->value;?>
" class="block informations_block_left">
			<p class="title_block">
				<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cms_title']->value['category_link'], ENT_QUOTES, 'UTF-8', true);?>
">
					<?php if (!empty($_smarty_tpl->tpl_vars['cms_title']->value['name'])) {?><?php echo $_smarty_tpl->tpl_vars['cms_title']->value['name'];?>
<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['cms_title']->value['category_name'];?>
<?php }?>
				</a>
			</p>
			<div class="block_content list-block">
				<ul>
					<?php  $_smarty_tpl->tpl_vars['cms_page'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cms_page']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cms_title']->value['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cms_page']->key => $_smarty_tpl->tpl_vars['cms_page']->value) {
$_smarty_tpl->tpl_vars['cms_page']->_loop = true;
?>
						<?php if (isset($_smarty_tpl->tpl_vars['cms_page']->value['link'])) {?>
							<li class="bullet">
								<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cms_page']->value['link'], ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cms_page']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
">
									<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cms_page']->value['name'], ENT_QUOTES, 'UTF-8', true);?>

								</a>
							</li>
						<?php }?>
					<?php } ?>
					<?php  $_smarty_tpl->tpl_vars['cms_page'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cms_page']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cms_title']->value['cms']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cms_page']->key => $_smarty_tpl->tpl_vars['cms_page']->value) {
$_smarty_tpl->tpl_vars['cms_page']->_loop = true;
?>
						<?php if (isset($_smarty_tpl->tpl_vars['cms_page']->value['link'])) {?>
							<li>
								<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cms_page']->value['link'], ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cms_page']->value['meta_title'], ENT_QUOTES, 'UTF-8', true);?>
">
									<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cms_page']->value['meta_title'], ENT_QUOTES, 'UTF-8', true);?>

								</a>
							</li>
						<?php }?>
					<?php } ?>
					<?php if ($_smarty_tpl->tpl_vars['cms_title']->value['display_store']) {?>
						<li>
							<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('stores'), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Our stores','mod'=>'blockcms'),$_smarty_tpl);?>
">
								<?php echo smartyTranslate(array('s'=>'Our stores','mod'=>'blockcms'),$_smarty_tpl);?>

							</a>
						</li>
					<?php }?>
				</ul>
			</div>
		</section>
	<?php } ?>
	<!-- /Block CMS module -->
<?php } else { ?>
	<!-- MODULE Block footer -->
	<section class="footer-block col-xs-12" id="block_various_links_footer">
		<!-- <h4><?php echo smartyTranslate(array('s'=>'Information','mod'=>'blockcms'),$_smarty_tpl);?>
</h4> -->
		<ul class="toggle-footer">
			<?php if (isset($_smarty_tpl->tpl_vars['show_price_drop']->value)&&$_smarty_tpl->tpl_vars['show_price_drop']->value&&!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value) {?>
				<li class="item">
					<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('prices-drop'), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Specials','mod'=>'blockcms'),$_smarty_tpl);?>
">
						<?php echo smartyTranslate(array('s'=>'Specials','mod'=>'blockcms'),$_smarty_tpl);?>

					</a>
				</li>
			<?php }?>
			<?php if (isset($_smarty_tpl->tpl_vars['show_new_products']->value)&&$_smarty_tpl->tpl_vars['show_new_products']->value) {?>
			<li class="item">
				<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('new-products'), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'New products','mod'=>'blockcms'),$_smarty_tpl);?>
">
					<?php echo smartyTranslate(array('s'=>'New products','mod'=>'blockcms'),$_smarty_tpl);?>

				</a>
			</li>
			<?php }?>
			<?php if (isset($_smarty_tpl->tpl_vars['show_best_sales']->value)&&$_smarty_tpl->tpl_vars['show_best_sales']->value&&!$_smarty_tpl->tpl_vars['PS_CATALOG_MODE']->value) {?>
				<li class="item">
					<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('best-sales'), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Top sellers','mod'=>'blockcms'),$_smarty_tpl);?>
">
						<?php echo smartyTranslate(array('s'=>'Top sellers','mod'=>'blockcms'),$_smarty_tpl);?>

					</a>
				</li>
			<?php }?>
			<?php if (isset($_smarty_tpl->tpl_vars['display_stores_footer']->value)&&$_smarty_tpl->tpl_vars['display_stores_footer']->value) {?>
				<li class="item">
					<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('stores'), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Our stores','mod'=>'blockcms'),$_smarty_tpl);?>
">
						<?php echo smartyTranslate(array('s'=>'Our stores','mod'=>'blockcms'),$_smarty_tpl);?>

					</a>
				</li>
			<?php }?>
			<?php if (isset($_smarty_tpl->tpl_vars['show_contact']->value)&&$_smarty_tpl->tpl_vars['show_contact']->value) {?>
			<li class="item">
				<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink($_smarty_tpl->tpl_vars['contact_url']->value,true), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Contact us','mod'=>'blockcms'),$_smarty_tpl);?>
">
					<?php echo smartyTranslate(array('s'=>'Contact us','mod'=>'blockcms'),$_smarty_tpl);?>

				</a>
			</li>
			<?php }?>
			<?php  $_smarty_tpl->tpl_vars['cmslink'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cmslink']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cmslinks']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cmslink']->key => $_smarty_tpl->tpl_vars['cmslink']->value) {
$_smarty_tpl->tpl_vars['cmslink']->_loop = true;
?>
				<?php if ($_smarty_tpl->tpl_vars['cmslink']->value['meta_title']!='') {?>
					<li class="item">
						<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cmslink']->value['link'], ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cmslink']->value['meta_title'], ENT_QUOTES, 'UTF-8', true);?>
">
							<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cmslink']->value['meta_title'], ENT_QUOTES, 'UTF-8', true);?>

						</a>
					</li>
				<?php }?>
			<?php } ?>
			<?php if (isset($_smarty_tpl->tpl_vars['show_sitemap']->value)&&$_smarty_tpl->tpl_vars['show_sitemap']->value) {?>
			<li>
				<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('sitemap'), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Sitemap','mod'=>'blockcms'),$_smarty_tpl);?>
">
					<?php echo smartyTranslate(array('s'=>'Sitemap','mod'=>'blockcms'),$_smarty_tpl);?>

				</a>
			</li>
			<?php }?>
		</ul>
		<?php echo $_smarty_tpl->tpl_vars['footer_text']->value;?>

		<div id="footer_copyright">&copy; <?php echo smarty_modifier_date_format(time(),"%Y");?>
 <a class="_blank" href="http://www.megagen.fr" target="_blank">MegaGen F.D. SA</a>. All Rights Reserved. <a href="http://www.inceptionweb.com.au/">Website Design</a></div>
		<a href="https://www.trustlogo.com/ttb_searcher/trustlogo?v_querytype=W&v_shortname=CL1&v_search=http://shop.megagen.fr&x=6&y=5" target="_blank"><img id="comodo_secure" src="/img/comodo_secure.png" alt="Comodo SSL Secure Website" /></a>
	</section>

	<!-- /MODULE Block footer -->
<?php }?>
<?php }} ?>
