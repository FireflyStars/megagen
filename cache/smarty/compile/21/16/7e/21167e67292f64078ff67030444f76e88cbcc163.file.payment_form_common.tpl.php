<?php /* Smarty version Smarty-3.1.19, created on 2020-08-17 05:18:20
         compiled from "/var/www/html/modules/stripe_official/views/templates/front/payment_form_common.tpl" */ ?>
<?php /*%%SmartyHeaderCode:16861199065f39f6fc917129-35658221%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '21167e67292f64078ff67030444f76e88cbcc163' => 
    array (
      0 => '/var/www/html/modules/stripe_official/views/templates/front/payment_form_common.tpl',
      1 => 1596578470,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16861199065f39f6fc917129-35658221',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'stripe_amount' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f39f6fc91c0f6_92670357',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f39f6fc91c0f6_92670357')) {function content_5f39f6fc91c0f6_92670357($_smarty_tpl) {?>

<input type="hidden" id="stripe-amount" value="<?php ob_start();?><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['stripe_amount']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
<?php $_tmp7=ob_get_clean();?><?php echo $_tmp7;?>
"><?php }} ?>
