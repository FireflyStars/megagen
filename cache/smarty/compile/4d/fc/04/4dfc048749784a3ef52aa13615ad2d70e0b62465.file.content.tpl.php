<?php /* Smarty version Smarty-3.1.19, created on 2020-08-17 05:15:58
         compiled from "/var/www/html/admin123/themes/default/template/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:336495555f39f66eae4085-60161291%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4dfc048749784a3ef52aa13615ad2d70e0b62465' => 
    array (
      0 => '/var/www/html/admin123/themes/default/template/content.tpl',
      1 => 1596577790,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '336495555f39f66eae4085-60161291',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f39f66eae9a36_74996494',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f39f66eae9a36_74996494')) {function content_5f39f66eae9a36_74996494($_smarty_tpl) {?>
<div id="ajax_confirmation" class="alert alert-success hide"></div>

<div id="ajaxBox" style="display:none"></div>


<div class="row">
	<div class="col-lg-12">
		<?php if (isset($_smarty_tpl->tpl_vars['content']->value)) {?>
			<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

		<?php }?>
	</div>
</div><?php }} ?>
