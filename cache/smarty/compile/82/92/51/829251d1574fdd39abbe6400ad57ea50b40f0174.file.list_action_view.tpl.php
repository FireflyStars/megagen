<?php /* Smarty version Smarty-3.1.19, created on 2020-08-17 05:15:58
         compiled from "/var/www/html/admin123/themes/default/template/helpers/list/list_action_view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6472276765f39f66e7220a5-46911983%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '829251d1574fdd39abbe6400ad57ea50b40f0174' => 
    array (
      0 => '/var/www/html/admin123/themes/default/template/helpers/list/list_action_view.tpl',
      1 => 1596577806,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6472276765f39f66e7220a5-46911983',
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
  'unifunc' => 'content_5f39f66e72b2d8_51758152',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f39f66e72b2d8_51758152')) {function content_5f39f66e72b2d8_51758152($_smarty_tpl) {?>
<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['href']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
" >
	<i class="icon-search-plus"></i> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>

</a><?php }} ?>
