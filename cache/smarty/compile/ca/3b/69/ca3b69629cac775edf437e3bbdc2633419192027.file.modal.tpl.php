<?php /* Smarty version Smarty-3.1.19, created on 2020-08-18 04:39:55
         compiled from "/var/www/html/admin123/themes/default/template/helpers/modules_list/modal.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4082479715f3b3f7be884c0-73626298%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ca3b69629cac775edf437e3bbdc2633419192027' => 
    array (
      0 => '/var/www/html/admin123/themes/default/template/helpers/modules_list/modal.tpl',
      1 => 1596577806,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4082479715f3b3f7be884c0-73626298',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f3b3f7be8ba25_00545523',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f3b3f7be8ba25_00545523')) {function content_5f3b3f7be8ba25_00545523($_smarty_tpl) {?><div class="modal fade" id="modules_list_container">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 class="modal-title"><?php echo smartyTranslate(array('s'=>'Recommended Modules and Services'),$_smarty_tpl);?>
</h3>
			</div>
			<div class="modal-body">
				<div id="modules_list_container_tab_modal" style="display:none;"></div>
				<div id="modules_list_loader"><i class="icon-refresh icon-spin"></i></div>
			</div>
		</div>
	</div>
</div>
<?php }} ?>
