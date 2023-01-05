<?php /* Smarty version Smarty-3.1.19, created on 2020-08-12 13:30:59
         compiled from "/var/www/html/admin123/themes/default/template/helpers/tree/tree_header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19764178425f33d2f3117ae1-95298783%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4b98cbf6e9f0268d3c34caa9ace3971b99ed67b6' => 
    array (
      0 => '/var/www/html/admin123/themes/default/template/helpers/tree/tree_header.tpl',
      1 => 1596577807,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19764178425f33d2f3117ae1-95298783',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'title' => 0,
    'toolbar' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f33d2f3122148_03356919',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f33d2f3122148_03356919')) {function content_5f33d2f3122148_03356919($_smarty_tpl) {?>
<div class="tree-panel-heading-controls clearfix">
	<?php if (isset($_smarty_tpl->tpl_vars['title']->value)) {?><i class="icon-tag"></i>&nbsp;<?php echo smartyTranslate(array('s'=>$_smarty_tpl->tpl_vars['title']->value),$_smarty_tpl);?>
<?php }?>
	<?php if (isset($_smarty_tpl->tpl_vars['toolbar']->value)) {?><?php echo $_smarty_tpl->tpl_vars['toolbar']->value;?>
<?php }?>
</div><?php }} ?>
