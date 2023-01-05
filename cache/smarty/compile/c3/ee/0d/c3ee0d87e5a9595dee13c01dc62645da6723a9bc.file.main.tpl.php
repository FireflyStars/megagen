<?php /* Smarty version Smarty-3.1.19, created on 2020-08-12 15:30:31
         compiled from "/var/www/html/modules/stripe_official/views/templates/admin/main.tpl" */ ?>
<?php /*%%SmartyHeaderCode:10110566305f33eef74d3f61-13975741%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c3ee0d87e5a9595dee13c01dc62645da6723a9bc' => 
    array (
      0 => '/var/www/html/modules/stripe_official/views/templates/admin/main.tpl',
      1 => 1596578470,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10110566305f33eef74d3f61-13975741',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'logo' => 0,
    'keys_configured' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f33eef74f13b9_30992045',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f33eef74f13b9_30992045')) {function content_5f33eef74f13b9_30992045($_smarty_tpl) {?>

<?php echo $_smarty_tpl->getSubTemplate ("./_partials/messages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

<div class="tabs">
	<div class="sidebar navigation col-md-2">
		<?php if (isset($_smarty_tpl->tpl_vars['logo']->value)) {?>
		  <img class="tabs-logo" src="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['logo']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
"/>
		<?php }?>
		<nav class="list-group categorieList">
			<a class="list-group-item migration-tab" href="#stripe_step_1">
			  	<i class="icon-power-off pstab-icon"></i>
			  	<?php echo smartyTranslate(array('s'=>'Connection','mod'=>'stripe_official'),$_smarty_tpl);?>

			  	<span class="badge-module-tabs pull-right <?php if ($_smarty_tpl->tpl_vars['keys_configured']->value===true) {?>tab-success<?php } else { ?>tab-warning<?php }?>"></span>
			</a>
			<a class="list-group-item migration-tab" href="#stripe_step_2">
			  	<i class="icon-ticket pstab-icon"></i>
			  	<?php echo smartyTranslate(array('s'=>'Refund','mod'=>'stripe_official'),$_smarty_tpl);?>

			</a>
			<a class="list-group-item migration-tab" href="#stripe_step_3">
			  	<i class="icon-question pstab-icon"></i>
			  	<?php echo smartyTranslate(array('s'=>'Contact and FAQ','mod'=>'stripe_official'),$_smarty_tpl);?>

			</a>
		</nav>
	</div>

	<div class="col-md-10">
		<div class="content-wrap panel">
			<section id="section-shape-1">
				<?php echo $_smarty_tpl->getSubTemplate ("./_partials/configuration.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

			</section>
			<section id="section-shape-2">
				<?php echo $_smarty_tpl->getSubTemplate ("./_partials/refund.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

			</section>
			<section id="section-shape-3">
				<?php echo $_smarty_tpl->getSubTemplate ("./_partials/faq.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

			</section>
		</div>
	</div>

</div><?php }} ?>
