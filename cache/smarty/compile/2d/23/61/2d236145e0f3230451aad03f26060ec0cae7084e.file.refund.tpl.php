<?php /* Smarty version Smarty-3.1.19, created on 2020-08-12 15:30:31
         compiled from "/var/www/html/modules/stripe_official/views/templates/admin/_partials/refund.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1229426495f33eef775f747-61279527%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2d236145e0f3230451aad03f26060ec0cae7084e' => 
    array (
      0 => '/var/www/html/modules/stripe_official/views/templates/admin/_partials/refund.tpl',
      1 => 1596578470,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1229426495f33eef775f747-61279527',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f33eef7777608_59161763',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f33eef7777608_59161763')) {function content_5f33eef7777608_59161763($_smarty_tpl) {?>

<form id="configuration_form" class="defaultForm form-horizontal stripe_official" action="#stripe_step_2" method="post" enctype="multipart/form-data" novalidate="">
    <input type="hidden" name="submit_refund_id" value="1">
    <div class="panel" id="fieldset_1">
        <div class="panel-heading">
            <?php echo smartyTranslate(array('s'=>'Choose an Order you want to Refund','mod'=>'stripe_official'),$_smarty_tpl);?>

        </div>
        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label col-lg-3 required">
                    <?php echo smartyTranslate(array('s'=>'Stripe Payment ID','mod'=>'stripe_official'),$_smarty_tpl);?>

                </label>
                <div class="col-lg-9">
                    <input type="text" name="STRIPE_REFUND_ID" id="STRIPE_REFUND_ID" value="" class="fixed-width-xxl" required="required">
                    <p class="help-block">
                        <i><?php echo smartyTranslate(array('s'=>'To process a refund, please input Stripe\'s payment ID below, which can be found in the « Payments » tab of this plugin','mod'=>'stripe_official'),$_smarty_tpl);?>
</i>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-9 col-lg-offset-3">
                    <div class="radio">
                        <label>
                            <input type="radio" name="STRIPE_REFUND_MODE" id="active_on_refund" value="1" checked="checked"><?php echo smartyTranslate(array('s'=>'Full refund','mod'=>'stripe_official'),$_smarty_tpl);?>

                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="STRIPE_REFUND_MODE" id="active_off_refund" value="0"><?php echo smartyTranslate(array('s'=>'Partial Refund','mod'=>'stripe_official'),$_smarty_tpl);?>

                        </label>
                    </div>
                    <p class="help-block">
                        <i>
                            <?php echo smartyTranslate(array('s'=>'We\'ll submit any refund you make to your customer\'s bank immediately.','mod'=>'stripe_official'),$_smarty_tpl);?>
<br>
                            <?php echo smartyTranslate(array('s'=>'Your customer will then receive the funds from a refund approximately 2-3 business days after the date on which the refund was initiated.','mod'=>'stripe_official'),$_smarty_tpl);?>
<br>
                            <?php echo smartyTranslate(array('s'=>'Refunds take 5 to 10 days to appear on your cutomer’s statement.','mod'=>'stripe_official'),$_smarty_tpl);?>

                        </i>
                    </p>
                </div>
            </div>
        </div>
        <div class="form-group partial-amount" style="display: none;">
            <label class="control-label col-lg-3 required"><?php echo smartyTranslate(array('s'=>'Amount','mod'=>'stripe_official'),$_smarty_tpl);?>
</label>
            <div class="col-lg-9">
                <input type="text" name="STRIPE_REFUND_AMOUNT" id="refund_amount" value="" class="fixed-width-sm" size="20" required="required">
                <p class="help-block">
                    <?php echo smartyTranslate(array('s'=>'Please, enter an amount your want to refund','mod'=>'stripe_official'),$_smarty_tpl);?>

                </p>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" value="1" id="configuration_form_submit_btn" name="submit_refund_id" class="btn btn-default pull-right button">
                <i class="process-icon-save"></i> <?php echo smartyTranslate(array('s'=>'Request Refund','mod'=>'stripe_official'),$_smarty_tpl);?>

            </button>
        </div>
    </div>
</form><?php }} ?>
