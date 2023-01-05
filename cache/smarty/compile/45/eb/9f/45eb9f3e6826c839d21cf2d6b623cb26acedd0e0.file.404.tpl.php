<?php /* Smarty version Smarty-3.1.19, created on 2020-08-13 15:03:03
         compiled from "/var/www/html/themes/megagen/404.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14654868095f353a073ecbd1-79928735%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '45eb9f3e6826c839d21cf2d6b623cb26acedd0e0' => 
    array (
      0 => '/var/www/html/themes/megagen/404.tpl',
      1 => 1596578506,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14654868095f353a073ecbd1-79928735',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'base_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f353a0742ed26_48341676',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f353a0742ed26_48341676')) {function content_5f353a0742ed26_48341676($_smarty_tpl) {?>
<div class="pagenotfound">
	<h1><?php echo smartyTranslate(array('s'=>'This page is not available'),$_smarty_tpl);?>
</h1>

	<p>
		<?php echo smartyTranslate(array('s'=>'We\'re sorry, but the Web address you\'ve entered is no longer available.'),$_smarty_tpl);?>

	</p>

	<h3><?php echo smartyTranslate(array('s'=>'To find a product, please type its name in the field below.'),$_smarty_tpl);?>
</h3>
	<form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('search'), ENT_QUOTES, 'UTF-8', true);?>
" method="post" class="std">
		<fieldset>
			<div>
				<label for="search_query"><?php echo smartyTranslate(array('s'=>'Search our product catalog:'),$_smarty_tpl);?>
</label>
				<input id="search_query" name="search_query" type="text" class="form-control grey" />
                <button type="submit" name="Submit" value="OK" class="btn btn-default button button-small"><span><?php echo smartyTranslate(array('s'=>'Ok'),$_smarty_tpl);?>
</span></button>
			</div>
		</fieldset>
	</form>

	<div class="buttons"><a class="btn btn-default button button-medium" href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
" title="<?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>
"><span><i class="icon-chevron-left left"></i><?php echo smartyTranslate(array('s'=>'Home page'),$_smarty_tpl);?>
</span></a></div>
</div>
<?php }} ?>
