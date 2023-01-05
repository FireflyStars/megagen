<?php /* Smarty version Smarty-3.1.19, created on 2020-08-19 15:08:04
         compiled from "/var/www/html/themes/megagen/modules/blocknewsletter/blocknewsletter.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8085831425f3d2434186711-37400159%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e6640f1e029470d05aa4207b4f3c368938afc37a' => 
    array (
      0 => '/var/www/html/themes/megagen/modules/blocknewsletter/blocknewsletter.tpl',
      1 => 1596578523,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8085831425f3d2434186711-37400159',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'lang_iso' => 0,
    'msg' => 0,
    'nw_error' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f3d24341b0817_65142606',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f3d24341b0817_65142606')) {function content_5f3d24341b0817_65142606($_smarty_tpl) {?>
<!-- Block Newsletter module-->
<div id="newsletter_block_left" class="block">
	<h4><?php echo smartyTranslate(array('s'=>'Newsletter','mod'=>'blocknewsletter'),$_smarty_tpl);?>
</h4>
	<div class="block_content">

		<!--
			<form action="http://domain.us11.list-manage.com/subscribe/post" method="POST">
			enter mailchimp subscribe here...
		-->
		<form action="/" method="POST">
			<div class="form-group">
				<input type="hidden" name="u" value="0ceef1d2fffb3f47b77f7dde8">
				<input type="hidden" name="id" value="24238b548f">
				<!-- <input class="form-control" type="text" name="MERGE1" id="MERGE1" size="25" value="" placeholder="First Name"> -->
				<!-- <input class="form-control" type="text" name="MERGE2" id="MERGE2" size="25" value="" placeholder="Last Name"> -->
				<?php if ($_smarty_tpl->tpl_vars['lang_iso']->value=='fr') {?>
					<input class="form-control" type="email" autocapitalize="off" autocorrect="off" name="MERGE0" id="MERGE0" size="18" value="" placeholder="Saisissez votre adresse e-mail">
				<?php } else { ?>
					<input class="form-control" type="email" autocapitalize="off" autocorrect="off" name="MERGE0" id="MERGE0" size="18" value="" placeholder="
Geben sie ihre E-Mail Adresse">
				<?php }?>
				<button class="btn btn-default button button-small" type="submit" name="submitNewsletter">
				<span><?php echo smartyTranslate(array('s'=>'Ok','mod'=>'blocknewsletter'),$_smarty_tpl);?>
</span>
				</button>
			</div>
		</form>

	</div>
	<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayBlockNewsletterBottom",'from'=>'blocknewsletter'),$_smarty_tpl);?>

</div>
<!-- /Block Newsletter module-->
<?php if (isset($_smarty_tpl->tpl_vars['msg']->value)&&$_smarty_tpl->tpl_vars['msg']->value) {?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('msg_newsl'=>addcslashes($_smarty_tpl->tpl_vars['msg']->value,'\'')),$_smarty_tpl);?>
<?php }?><?php if (isset($_smarty_tpl->tpl_vars['nw_error']->value)) {?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('nw_error'=>$_smarty_tpl->tpl_vars['nw_error']->value),$_smarty_tpl);?>
<?php }?><?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'placeholder_blocknewsletter')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'placeholder_blocknewsletter'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Enter your e-mail','mod'=>'blocknewsletter','js'=>1),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'placeholder_blocknewsletter'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php if (isset($_smarty_tpl->tpl_vars['msg']->value)&&$_smarty_tpl->tpl_vars['msg']->value) {?><?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'alert_blocknewsletter')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'alert_blocknewsletter'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Newsletter : %1$s','sprintf'=>$_smarty_tpl->tpl_vars['msg']->value,'js'=>1,'mod'=>"blocknewsletter"),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'alert_blocknewsletter'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }?>
<?php }} ?>
