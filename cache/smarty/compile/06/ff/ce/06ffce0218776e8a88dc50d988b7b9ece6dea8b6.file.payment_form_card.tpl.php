<?php /* Smarty version Smarty-3.1.19, created on 2020-08-17 05:18:20
         compiled from "/var/www/html/modules/stripe_official/views/templates/front/payment_form_card.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15034597625f39f6fc8ccac4-45603510%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '06ffce0218776e8a88dc50d988b7b9ece6dea8b6' => 
    array (
      0 => '/var/www/html/modules/stripe_official/views/templates/front/payment_form_card.tpl',
      1 => 1596578470,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15034597625f39f6fc8ccac4-45603510',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'applepay_googlepay' => 0,
    'prestashop_version' => 0,
    'stripeError' => 0,
    'stripe_reinsurance_enabled' => 0,
    'stripe_payment_methods' => 0,
    'module_dir' => 0,
    'stripe_payment_method' => 0,
    'stripe_cardholdername_enabled' => 0,
    'customer_name' => 0,
    'stripe_postcode_enabled' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f39f6fc9134c0_57503898',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f39f6fc9134c0_57503898')) {function content_5f39f6fc9134c0_57503898($_smarty_tpl) {?>
<form class="stripe-payment-form" id="stripe-card-payment">
    <?php if ($_smarty_tpl->tpl_vars['applepay_googlepay']->value=='on') {?>
        <div id="stripe-payment-request-button"></div>

        <?php if (isset($_smarty_tpl->tpl_vars['prestashop_version']->value)&&$_smarty_tpl->tpl_vars['prestashop_version']->value=='1.7') {?>
            <div class="stripe-payment-request-button-warning modal fade">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <button type="button" class="closer" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="modal-body"><?php echo smartyTranslate(array('s'=>'Please make sure you agreed to our Terms of Service before going any further','mod'=>'stripe_official'),$_smarty_tpl);?>
</div>
                    </div>
                </div>
            </div>
        <?php }?>

        <p class="card-payment-informations"><?php echo smartyTranslate(array('s'=>'Pay now with the card saved in your device by clicking on the button above or fill in your card details below and submit at the end of the page','mod'=>'stripe_official'),$_smarty_tpl);?>
</p>
    <?php }?>

    <input type="hidden" name="stripe-payment-method" value="card">
    <div class="stripe-error-message alert alert-danger">
      <?php if (isset($_smarty_tpl->tpl_vars['stripeError']->value)) {?><p><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['stripeError']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</p><?php }?>
    </div>

    <?php if ($_smarty_tpl->tpl_vars['stripe_reinsurance_enabled']->value=='on') {?>
        <div class="form-row">
            <div id="cards-logos">
                <?php if (isset($_smarty_tpl->tpl_vars['stripe_payment_methods']->value)) {?>
                    <?php  $_smarty_tpl->tpl_vars['stripe_payment_method'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['stripe_payment_method']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['stripe_payment_methods']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['stripe_payment_method']->key => $_smarty_tpl->tpl_vars['stripe_payment_method']->value) {
$_smarty_tpl->tpl_vars['stripe_payment_method']->_loop = true;
?>
                        <img class="card_logo" src="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['module_dir']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
/views/img/logo_<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['stripe_payment_method']->value['name'], ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
.png" />
                    <?php } ?>
                <?php }?>
            </div>
            <?php if (isset($_smarty_tpl->tpl_vars['stripe_cardholdername_enabled']->value)&&$_smarty_tpl->tpl_vars['stripe_cardholdername_enabled']->value=='on') {?>
                <div class="stripe-card-cardholdername">
                    <label for="card-element">
                        <?php echo smartyTranslate(array('s'=>'Cardholder\'s Name','mod'=>'stripe_official'),$_smarty_tpl);?>

                    </label><label class="required"> </label>
                    <input name="cardholder-name" type="text"  autocomplete="off" class="stripe-name" data-stripe="name" value="<?php if (isset($_smarty_tpl->tpl_vars['customer_name']->value)) {?><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['customer_name']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
<?php }?>"/>
                </div>
            <?php }?>
            <label for="card-element">
                <?php echo smartyTranslate(array('s'=>'Card Number','mod'=>'stripe_official'),$_smarty_tpl);?>

            </label><label class="required"> </label>
            <div id="stripe-card-number" class="field"></div>
            <div class="block-left stripe-card-expiry">
                <label for="card-element">
                    <?php echo smartyTranslate(array('s'=>'Expiry date','mod'=>'stripe_official'),$_smarty_tpl);?>

                </label><label class="required"> </label>
                <div id="stripe-card-expiry" class="field"></div>
            </div>
            <div class="stripe-card-cvc">
                <label for="card-element">
                    <?php echo smartyTranslate(array('s'=>'CVC/CVV','mod'=>'stripe_official'),$_smarty_tpl);?>

                </label><label class="required"> </label>
                <div id="stripe-card-cvc" class="field"></div>
            </div>
            <?php if (isset($_smarty_tpl->tpl_vars['stripe_postcode_enabled']->value)&&$_smarty_tpl->tpl_vars['stripe_postcode_enabled']->value!='on') {?>
                <div class="stripe-card-postalcode">
                    <label for="card-element">
                        <?php echo smartyTranslate(array('s'=>'Postal code','mod'=>'stripe_official'),$_smarty_tpl);?>

                    </label><label class="required"> </label>
                    <div id="stripe-card-postalcode" class="field"></div>
                </div>
            <?php }?>
        </div>
    <?php } else { ?>
        <div id="stripe-card-element" class="field"></div>
        <?php if (isset($_smarty_tpl->tpl_vars['stripe_cardholdername_enabled']->value)&&$_smarty_tpl->tpl_vars['stripe_cardholdername_enabled']->value=='on') {?>
            <input name="cardholder-name" type="text"  autocomplete="off" id="stripe-card-cardholdername" class="stripe-name" data-stripe="name" value="<?php if (isset($_smarty_tpl->tpl_vars['customer_name']->value)) {?><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['customer_name']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
<?php }?>"/>
        <?php }?>
    <?php }?>

    <?php if (isset($_smarty_tpl->tpl_vars['prestashop_version']->value)&&$_smarty_tpl->tpl_vars['prestashop_version']->value=='1.6') {?>
        <button class="stripe-submit-button" data-method="card"><?php echo smartyTranslate(array('s'=>'Buy now','mod'=>'stripe_official'),$_smarty_tpl);?>
</button>
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['stripe_reinsurance_enabled']->value=='on') {?>
        <div id="powered_by_stripe">
            <img src="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['module_dir']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
/views/img/powered_by_stripe.png" />
        </div>
    <?php }?>
</form>
<?php }} ?>
