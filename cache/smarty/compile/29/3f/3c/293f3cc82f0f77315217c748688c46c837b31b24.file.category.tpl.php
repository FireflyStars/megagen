<?php /* Smarty version Smarty-3.1.19, created on 2020-08-17 05:17:00
         compiled from "/var/www/html/themes/megagen/category.tpl" */ ?>
<?php /*%%SmartyHeaderCode:406262235f39f6aca12da8-14095571%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '293f3cc82f0f77315217c748688c46c837b31b24' => 
    array (
      0 => '/var/www/html/themes/megagen/category.tpl',
      1 => 1597324337,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '406262235f39f6aca12da8-14095571',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'category' => 0,
    'tplArr' => 0,
    'mobile_device' => 0,
    'discount' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f39f6aca4dbf1_24138665',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f39f6aca4dbf1_24138665')) {function content_5f39f6aca4dbf1_24138665($_smarty_tpl) {?>

<?php $_smarty_tpl->tpl_vars['tplArr'] = new Smarty_variable(array(3,31,310,311,312,313,314,315,316,318,322,327,328,329,330,331,332,333,334,335,336,337,338,339,340,341,342,344,345,317,347,352,353,354,355,356,357,358,359,360,361,362,363,365,370,371,372,373,374,375,376,377,378,379,380,408,410,411,412,413), null, 0);?>

<?php if (isset($_smarty_tpl->tpl_vars['category']->value)&&isset($_smarty_tpl->tpl_vars['category']->value->id)&&in_array($_smarty_tpl->tpl_vars['category']->value->id,$_smarty_tpl->tpl_vars['tplArr']->value)&&!$_smarty_tpl->tpl_vars['mobile_device']->value) {?>
 <?php if ($_smarty_tpl->tpl_vars['discount']->value!='') {?>
        <div style="    background: #4e4e4e;color: #fff;margin: 5px;padding: 11px;">Discount: <?php echo $_smarty_tpl->tpl_vars['discount']->value;?>
 %</div>
    <?php }?>
    
    <?php echo $_smarty_tpl->getSubTemplate ("./category-".((string)$_smarty_tpl->tpl_vars['category']->value->id).".tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

<?php } else { ?>
 <?php if ($_smarty_tpl->tpl_vars['discount']->value!='') {?>
        <div style="    background: #4e4e4e;color: #fff;margin: 5px;padding: 11px;">Discount: <?php echo $_smarty_tpl->tpl_vars['discount']->value;?>
 %</div>
    <?php }?>

    <?php echo $_smarty_tpl->getSubTemplate ("./category-default.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

<?php }?><?php }} ?>
