<?php /* Smarty version Smarty-3.1.19, created on 2020-08-19 15:08:04
         compiled from "/var/www/html/themes/megagen/modules/blockcontact/nav.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20226337395f3d24345401c9-41911745%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1c0be14df7bd5597ea834443006dd948edeaeef8' => 
    array (
      0 => '/var/www/html/themes/megagen/modules/blockcontact/nav.tpl',
      1 => 1596578522,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20226337395f3d24345401c9-41911745',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'is_logged' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f3d243454de89_00222843',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f3d243454de89_00222843')) {function content_5f3d243454de89_00222843($_smarty_tpl) {?>
<div id="contact-link" <?php if (isset($_smarty_tpl->tpl_vars['is_logged']->value)&&$_smarty_tpl->tpl_vars['is_logged']->value) {?> class="is_logged"<?php }?>>
	<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('contact',true), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Contact us','mod'=>'blockcontact'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Contact us','mod'=>'blockcontact'),$_smarty_tpl);?>
</a>
</div>

<?php }} ?>
