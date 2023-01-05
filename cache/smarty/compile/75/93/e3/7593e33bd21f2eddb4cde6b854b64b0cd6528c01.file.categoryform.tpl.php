<?php /* Smarty version Smarty-3.1.19, created on 2020-08-13 11:25:56
         compiled from "/var/www/html/modules/sssdiscount/views/templates/categoryform.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5328829785f350724d2be06-42606397%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7593e33bd21f2eddb4cde6b854b64b0cd6528c01' => 
    array (
      0 => '/var/www/html/modules/sssdiscount/views/templates/categoryform.tpl',
      1 => 1597235117,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5328829785f350724d2be06-42606397',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'categories' => 0,
    'category' => 0,
    'subcategory' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f350724d3de16_02763182',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f350724d3de16_02763182')) {function content_5f350724d3de16_02763182($_smarty_tpl) {?><div class="panel">
    <h3><i class="icon-list-ul"></i> <?php echo smartyTranslate(array('s'=>'Discount','mod'=>'sssdiscount'),$_smarty_tpl);?>

	<span class="panel-heading-action">
		
	</span>
    </h3>



  <div class="table-responsive">

  	<form id="discountsfrm" method="post">

  	 <?php  $_smarty_tpl->tpl_vars['category'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['category']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['categories']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['category']->key => $_smarty_tpl->tpl_vars['category']->value) {
$_smarty_tpl->tpl_vars['category']->_loop = true;
?> 

	  	 <div class="card">

	  	 <div class="card-header"><?php echo $_smarty_tpl->tpl_vars['category']->value['name'];?>
</div>
		  <div class="card-body">


		  		 <?php  $_smarty_tpl->tpl_vars['subcategory'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['subcategory']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['category']->value['subcategory']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['subcategory']->key => $_smarty_tpl->tpl_vars['subcategory']->value) {
$_smarty_tpl->tpl_vars['subcategory']->_loop = true;
?> 


		  		 	<table style="    width: 100%;">
		  		 		<tr>
		  		 			<td width="40%"><?php echo $_smarty_tpl->tpl_vars['subcategory']->value['name'];?>
</td>
		  		 			<td><input type="text" name="discount[<?php echo $_smarty_tpl->tpl_vars['subcategory']->value['id_category'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['subcategory']->value['value'];?>
"></td>
		  		 		</tr>
		  		 	</table>


		  		  <?php } ?>
		   
		  </div>
		</div>


  	  <?php } ?>

  	   <input type="submit" value="Submit">

                
  	</form>

  </div>




</div>
<style type="text/css">
	.card {
    position: relative;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid rgba(0,0,0,.125);
    border-radius: .25rem;
    margin-bottom: 10px;
}

.card-body {
    -webkit-box-flex: 1;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    padding: 1.25rem;
}
.card-header {
    padding: .75rem 1.25rem;
    margin-bottom: 0;
    background-color: rgba(0,0,0,.03);
    border-bottom: 1px solid rgba(0,0,0,.125);
}
</style><?php }} ?>
