<?php /* Smarty version Smarty-3.1.19, created on 2020-08-12 15:30:31
         compiled from "/var/www/html/modules/stripe_official/views/templates/admin/_partials/messages.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15664174535f33eef74f3275-39792049%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9b3d98ae957b729e87c6a107482c1481545ba93d' => 
    array (
      0 => '/var/www/html/modules/stripe_official/views/templates/admin/_partials/messages.tpl',
      1 => 1596578470,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15664174535f33eef74f3275-39792049',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'success' => 0,
    'success_message' => 0,
    'warnings' => 0,
    'warnings_message' => 0,
    'errors' => 0,
    'errors_message' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f33eef7530850_34258409',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f33eef7530850_34258409')) {function content_5f33eef7530850_34258409($_smarty_tpl) {?>

<?php if (isset($_smarty_tpl->tpl_vars['success']->value)) {?>
    <?php  $_smarty_tpl->tpl_vars['success_message'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['success_message']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['success']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['success_message']->key => $_smarty_tpl->tpl_vars['success_message']->value) {
$_smarty_tpl->tpl_vars['success_message']->_loop = true;
?>
    	<div class="alert alert-success clearfix">
            <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['success_message']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>

        </div>
    <?php } ?>
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['warnings']->value)) {?>
    <?php  $_smarty_tpl->tpl_vars['warnings_message'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['warnings_message']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['warnings']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['warnings_message']->key => $_smarty_tpl->tpl_vars['warnings_message']->value) {
$_smarty_tpl->tpl_vars['warnings_message']->_loop = true;
?>
        <div class="alert alert-warning clearfix">
            <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['warnings_message']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>

        </div>
    <?php } ?>
<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['errors']->value)) {?>
    <?php  $_smarty_tpl->tpl_vars['errors_message'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['errors_message']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['errors']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['errors_message']->key => $_smarty_tpl->tpl_vars['errors_message']->value) {
$_smarty_tpl->tpl_vars['errors_message']->_loop = true;
?>
        <div class="alert alert-danger clearfix">
            <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['errors_message']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>

        </div>
    <?php } ?>
<?php }?><?php }} ?>
