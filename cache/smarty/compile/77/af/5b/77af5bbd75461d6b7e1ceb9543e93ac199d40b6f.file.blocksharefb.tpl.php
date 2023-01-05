<?php /* Smarty version Smarty-3.1.19, created on 2020-08-17 05:17:10
         compiled from "/var/www/html/modules/blocksharefb/blocksharefb.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5373555135f39f6b653cdf2-42652167%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '77af5bbd75461d6b7e1ceb9543e93ac199d40b6f' => 
    array (
      0 => '/var/www/html/modules/blocksharefb/blocksharefb.tpl',
      1 => 1596578326,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5373555135f39f6b653cdf2-42652167',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product_link' => 0,
    'product_title' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f39f6b65422f4_80498393',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f39f6b65422f4_80498393')) {function content_5f39f6b65422f4_80498393($_smarty_tpl) {?>

<li id="left_share_fb">
	<a href="http://www.facebook.com/sharer.php?u=<?php echo $_smarty_tpl->tpl_vars['product_link']->value;?>
&amp;t=<?php echo $_smarty_tpl->tpl_vars['product_title']->value;?>
" class="_blank"><?php echo smartyTranslate(array('s'=>'Share on Facebook!','mod'=>'blocksharefb'),$_smarty_tpl);?>
</a>
</li><?php }} ?>
