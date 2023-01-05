<?php /* Smarty version Smarty-3.1.19, created on 2020-08-17 05:15:58
         compiled from "/var/www/html/admin123/themes/default/template/helpers/list/list_action_edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15534634585f39f66e7157e3-79045379%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ad1a198d3b3625cec0f5a0c4fc27074b7089423a' => 
    array (
      0 => '/var/www/html/admin123/themes/default/template/helpers/list/list_action_edit.tpl',
      1 => 1596577805,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15534634585f39f66e7157e3-79045379',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'href' => 0,
    'action' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f39f66e71f544_07645927',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f39f66e71f544_07645927')) {function content_5f39f66e71f544_07645927($_smarty_tpl) {?>
<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['href']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
" class="edit">
	<i class="icon-pencil"></i> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>

</a><?php }} ?>
