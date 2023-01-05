<?php /* Smarty version Smarty-3.1.19, created on 2020-08-17 05:18:20
         compiled from "/var/www/html/themes/megagen/modules/bankwire/views/templates/hook/payment.tpl" */ ?>
<?php /*%%SmartyHeaderCode:10606586805f39f6fc03fc01-75330164%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '38abe829f32e91f769cb4de4d973b00f07447697' => 
    array (
      0 => '/var/www/html/themes/megagen/modules/bankwire/views/templates/hook/payment.tpl',
      1 => 1596578521,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10606586805f39f6fc03fc01-75330164',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f39f6fc04ad72_31695127',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f39f6fc04ad72_31695127')) {function content_5f39f6fc04ad72_31695127($_smarty_tpl) {?>
<div class="row">
	<div class="col-xs-12">
		<p class="payment_module">
			<a class="bankwire" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getModuleLink('bankwire','payment'), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Pay by bank wire','mod'=>'bankwire'),$_smarty_tpl);?>
">
				<?php echo smartyTranslate(array('s'=>'Pay by bank wire','mod'=>'bankwire'),$_smarty_tpl);?>

			</a>
		</p>
	</div>
</div>
<?php }} ?>
