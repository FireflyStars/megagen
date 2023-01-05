<?php /* Smarty version Smarty-3.1.19, created on 2020-08-17 05:17:10
         compiled from "/var/www/html/themes/megagen/product.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19443620035f39f6b67554a6-82767009%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '58782ecc2c84610335477b55bd13922fedc89c24' => 
    array (
      0 => '/var/www/html/themes/megagen/product.tpl',
      1 => 1596578506,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19443620035f39f6b67554a6-82767009',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product' => 0,
    'tpl4Arr' => 0,
    'tpl2Arr' => 0,
    'tpl3Arr' => 0,
    'combinations' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f39f6b6789e23_60219852',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f39f6b6789e23_60219852')) {function content_5f39f6b6789e23_60219852($_smarty_tpl) {?>

<?php $_smarty_tpl->tpl_vars['tpl2Arr'] = new Smarty_variable(array(2,2077,2277,2175), null, 0);?>
<?php $_smarty_tpl->tpl_vars['tpl3Arr'] = new Smarty_variable(array(2272), null, 0);?>
<?php $_smarty_tpl->tpl_vars['tpl4Arr'] = new Smarty_variable(array(2153), null, 0);?>


<?php if (isset($_smarty_tpl->tpl_vars['product']->value)&&isset($_smarty_tpl->tpl_vars['product']->value->id)&&in_array($_smarty_tpl->tpl_vars['product']->value->id,$_smarty_tpl->tpl_vars['tpl4Arr']->value)) {?>
    <?php echo $_smarty_tpl->getSubTemplate ("./product-2153.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

<?php } elseif (isset($_smarty_tpl->tpl_vars['product']->value)&&isset($_smarty_tpl->tpl_vars['product']->value->id)&&in_array($_smarty_tpl->tpl_vars['product']->value->id,$_smarty_tpl->tpl_vars['tpl2Arr']->value)) {?>
    <?php echo $_smarty_tpl->getSubTemplate ("./product-2.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

<?php } elseif (isset($_smarty_tpl->tpl_vars['product']->value)&&isset($_smarty_tpl->tpl_vars['product']->value->id)&&in_array($_smarty_tpl->tpl_vars['product']->value->id,$_smarty_tpl->tpl_vars['tpl3Arr']->value)) {?>
    <?php echo $_smarty_tpl->getSubTemplate ("./product-type.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

<?php } else { ?>




 <?php if (count($_smarty_tpl->tpl_vars['combinations']->value)==0) {?>
  <?php echo $_smarty_tpl->getSubTemplate ("./product-default2.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

 <?php } else { ?>
  <?php echo $_smarty_tpl->getSubTemplate ("./product-3.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

 <?php }?>


   
<?php }?><?php }} ?>
