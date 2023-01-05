<?php /* Smarty version Smarty-3.1.19, created on 2020-08-14 07:16:48
         compiled from "/var/www/html/themes/megagen/modules/mailalerts/views/templates/hook/my-account.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2068112605f361e40bbdea6-79563541%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8762c6fb6b3b37cbe4bfbf346ed4a52c030d1647' => 
    array (
      0 => '/var/www/html/themes/megagen/modules/mailalerts/views/templates/hook/my-account.tpl',
      1 => 1596578528,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2068112605f361e40bbdea6-79563541',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f361e40bc8da9_27414257',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f361e40bc8da9_27414257')) {function content_5f361e40bc8da9_27414257($_smarty_tpl) {?>

<li class="mailalerts">
	<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getModuleLink('mailalerts','account',array(),true), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'My alerts','mod'=>'mailalerts'),$_smarty_tpl);?>
" rel="nofollow">
    	<i class="icon-envelope"></i>
		<span><?php echo smartyTranslate(array('s'=>'My alerts','mod'=>'mailalerts'),$_smarty_tpl);?>
</span>
	</a>
</li>
<?php }} ?>
