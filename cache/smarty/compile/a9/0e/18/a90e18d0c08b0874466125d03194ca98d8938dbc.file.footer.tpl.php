<?php /* Smarty version Smarty-3.1.19, created on 2020-08-18 04:39:55
         compiled from "/var/www/html/admin123/themes/default/template/footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12546781355f3b3f7bf222e7-40125575%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a90e18d0c08b0874466125d03194ca98d8938dbc' => 
    array (
      0 => '/var/www/html/admin123/themes/default/template/footer.tpl',
      1 => 1596577790,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12546781355f3b3f7bf222e7-40125575',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'display_footer' => 0,
    'php_errors' => 0,
    'modals' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f3b3f7bf306c9_42142692',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f3b3f7bf306c9_42142692')) {function content_5f3b3f7bf306c9_42142692($_smarty_tpl) {?>

	</div>
</div>

<?php if ($_smarty_tpl->tpl_vars['display_footer']->value) {?>


<?php }?>
<?php if (isset($_smarty_tpl->tpl_vars['php_errors']->value)) {?>
	<?php echo $_smarty_tpl->getSubTemplate ("error.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

<?php }?>

<?php if (isset($_smarty_tpl->tpl_vars['modals']->value)) {?>
<div class="bootstrap">
	<?php echo $_smarty_tpl->tpl_vars['modals']->value;?>

</div>
<?php }?>

</body>
</html>
<?php }} ?>
