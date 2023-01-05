<?php /* Smarty version Smarty-3.1.19, created on 2020-08-12 15:30:31
         compiled from "/var/www/html/modules/stripe_official/views/templates/admin/_partials/configuration.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5111088605f33eef7532df6-55358848%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '12b73394bf7701bcb925f351b98539fe4ad27db2' => 
    array (
      0 => '/var/www/html/modules/stripe_official/views/templates/admin/_partials/configuration.tpl',
      1 => 1596578470,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5111088605f33eef7532df6-55358848',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'stripe_url' => 0,
    'return_url' => 0,
    'stripe_mode' => 0,
    'stripe_publishable' => 0,
    'stripe_key' => 0,
    'stripe_test_publishable' => 0,
    'stripe_test_key' => 0,
    'reinsurance' => 0,
    'visa' => 0,
    'mastercard' => 0,
    'american_express' => 0,
    'cb' => 0,
    'diners_club' => 0,
    'union_pay' => 0,
    'jcb' => 0,
    'discovers' => 0,
    'postcode' => 0,
    'cardholdername' => 0,
    'ideal' => 0,
    'bancontact' => 0,
    'sofort' => 0,
    'giropay' => 0,
    'applepay_googlepay' => 0,
    'url_webhhoks' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f33eef7755476_05407395',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f33eef7755476_05407395')) {function content_5f33eef7755476_05407395($_smarty_tpl) {?>

<form id="configuration_form" class="defaultForm form-horizontal stripe_official" action="#stripe_step_1" method="post" enctype="multipart/form-data" novalidate="">
	<input type="hidden" name="submit_login" value="1">
	<div class="panel" id="fieldset_0">
		<div class="form-wrapper">
			<div class="form-group stripe-connection">
				<?php $_smarty_tpl->tpl_vars['stripe_url'] = new Smarty_variable('https://partners-subscribe.prestashop.com/stripe/connect.php?params[return_url]=', null, 0);?>
				<?php ob_start();?><?php echo smartyTranslate(array('s'=>'[a @href1@]Create your Stripe account in 10 minutes[/a] and immediately start accepting payments via Visa, MasterCard and American Express (no additional contract/merchant ID needed from your bank).','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp1=ob_get_clean();?><?php ob_start();?><?php echo mb_convert_encoding(htmlspecialchars(($_smarty_tpl->tpl_vars['stripe_url']->value).($_smarty_tpl->tpl_vars['return_url']->value), ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
<?php $_tmp2=ob_get_clean();?><?php ob_start();?><?php echo $_tmp2;?>
<?php $_tmp3=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp4=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp1,array('@href1@'=>$_tmp3,'@target@'=>$_tmp4));?>
<br>

				<div class="connect_btn">
					<a href="https://partners-subscribe.prestashop.com/stripe/connect.php?params[return_url]=<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['return_url']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" class="stripe-connect">
						<span><?php echo smartyTranslate(array('s'=>'Connect with Stripe','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
					</a>
				</div>
			</div>
			<hr/>
			<div class="form-group">
				<label class="control-label col-lg-3"><?php echo smartyTranslate(array('s'=>'Mode','mod'=>'stripe_official'),$_smarty_tpl);?>
</label>
				<div class="col-lg-9">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="STRIPE_MODE" id="STRIPE_MODE_ON" value="1" <?php if ($_smarty_tpl->tpl_vars['stripe_mode']->value==1) {?>checked="checked"<?php }?>>
						<label for="STRIPE_MODE_ON"><?php echo smartyTranslate(array('s'=>'test','mod'=>'stripe_official'),$_smarty_tpl);?>
</label>
						<input type="radio" name="STRIPE_MODE" id="STRIPE_MODE_OFF" value="0" <?php if ($_smarty_tpl->tpl_vars['stripe_mode']->value==0) {?>checked="checked"<?php }?>>
						<label for="STRIPE_MODE_OFF"><?php echo smartyTranslate(array('s'=>'live','mod'=>'stripe_official'),$_smarty_tpl);?>
</label>
						<a class="slide-button btn"></a>
					</span>
					<p class="help-block"></p>
				</div>
				<span><?php echo smartyTranslate(array('s'=>'Now that you have created your Stripe account, you have to enter below your API keys in both test and live mode.','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
				<br/>
				<span>
					<?php ob_start();?><?php echo smartyTranslate(array('s'=>'These API keys can be found and managed from your Stripe [a @href1@]dashboard[/a].','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp5=ob_get_clean();?><?php ob_start();?><?php echo 'https://dashboard.stripe.com/account/apikeys';?>
<?php $_tmp6=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp7=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp5,array('@href1@'=>$_tmp6,'@target@'=>$_tmp7));?>

				</span>
			</div>

			<div class="form-group" <?php if ($_smarty_tpl->tpl_vars['stripe_mode']->value==1) {?>style="display: none;"<?php }?>>
				<label class="control-label col-lg-3 required"><?php echo smartyTranslate(array('s'=>'Stripe Publishable Key','mod'=>'stripe_official'),$_smarty_tpl);?>
</label>
				<div class="col-lg-9">
					<input type="text" name="STRIPE_PUBLISHABLE" id="public_key" value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['stripe_publishable']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" class="fixed-width-xxl" size="20" required="required">
				</div>
			</div>
			<div class="form-group" <?php if ($_smarty_tpl->tpl_vars['stripe_mode']->value==1) {?>style="display: none;"<?php }?>>
				<label class="control-label col-lg-3 required"><?php echo smartyTranslate(array('s'=>'Stripe Secrey Key','mod'=>'stripe_official'),$_smarty_tpl);?>
</label>
				<div class="col-lg-9">
					<input type="password" name="STRIPE_KEY" id="secret_key" value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['stripe_key']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" class="fixed-width-xxl" size="20" required="required">
				</div>
			</div>
			<div class="form-group"<?php if ($_smarty_tpl->tpl_vars['stripe_mode']->value==0) {?>style="display: none;"<?php }?>>
				<label class="control-label col-lg-3 required"><?php echo smartyTranslate(array('s'=>'Stripe Test Publishable Key','mod'=>'stripe_official'),$_smarty_tpl);?>
</label>
				<div class="col-lg-9">
					<input type="text" name="STRIPE_TEST_PUBLISHABLE" id="test_public_key" value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['stripe_test_publishable']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" class="fixed-width-xxl" size="20" required="required">
				</div>
			</div>
			<div class="form-group"<?php if ($_smarty_tpl->tpl_vars['stripe_mode']->value==0) {?>style="display: none;"<?php }?>>
				<label class="control-label col-lg-3 required"><?php echo smartyTranslate(array('s'=>'Stripe Test Secrey Key','mod'=>'stripe_official'),$_smarty_tpl);?>
</label>
				<div class="col-lg-9">
					<input type="password" name="STRIPE_TEST_KEY" id="test_secret_key" value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['stripe_test_key']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" class="fixed-width-xxl" size="20" required="required">
				</div>
			</div>

			<div id="conf-payment-methods">
				<p><b><?php echo smartyTranslate(array('s'=>'Testing Stripe','mod'=>'stripe_official'),$_smarty_tpl);?>
</b></p>
				<ul>
					<li><?php echo smartyTranslate(array('s'=>'Toggle the button above to Test Mode.','mod'=>'stripe_official'),$_smarty_tpl);?>
</li>
					<li>
						<?php ob_start();?><?php echo smartyTranslate(array('s'=>'To perform test payments, you can use test card numbers available in our [a @href1@]documentation[/a].','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp8=ob_get_clean();?><?php ob_start();?><?php echo 'http://www.stripe.com/docs/testing';?>
<?php $_tmp9=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp10=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp8,array('@href1@'=>$_tmp9,'@target@'=>$_tmp10));?>

					</li>
					<li><?php echo smartyTranslate(array('s'=>'In Test Mode, you can not run live charges.','mod'=>'stripe_official'),$_smarty_tpl);?>
</li>
				</ul>
				<p><b><?php echo smartyTranslate(array('s'=>'Using Stripe Live','mod'=>'stripe_official'),$_smarty_tpl);?>
</b></p>
				<ul>
					<li><?php echo smartyTranslate(array('s'=>'Toggle the button above to Live Mode.','mod'=>'stripe_official'),$_smarty_tpl);?>
</li>
					<li><?php echo smartyTranslate(array('s'=>'In Live Mode, you can not run test charges.','mod'=>'stripe_official'),$_smarty_tpl);?>
</li>
				</ul>

				<p><b><?php echo smartyTranslate(array('s'=>'Set up the form','mod'=>'stripe_official'),$_smarty_tpl);?>
</b></p>
				<ol item="1">
					<li>
						<p><?php echo smartyTranslate(array('s'=>'Options for the card payment form','mod'=>'stripe_official'),$_smarty_tpl);?>
</p>

						<div class="form-group">
							<input type="checkbox" id="reinsurance" name="reinsurance" <?php if ($_smarty_tpl->tpl_vars['reinsurance']->value) {?>checked="checked"<?php }?>/>
							<label for="reinsurance"><?php echo smartyTranslate(array('s'=>'Activate extended display containing reinsurance elements (logo of cards. You must choose to display the cards you configured on Stripe\'s dashboard)','mod'=>'stripe_official'),$_smarty_tpl);?>
</label><br/>

							<input type="checkbox" id="visa" name="visa" <?php if ($_smarty_tpl->tpl_vars['visa']->value) {?>checked="checked"<?php }?>/>
							<label for="visa"><?php echo smartyTranslate(array('s'=>'Visa','mod'=>'stripe_official'),$_smarty_tpl);?>
</label><br/>
							<input type="checkbox" id="mastercard" name="mastercard" <?php if ($_smarty_tpl->tpl_vars['mastercard']->value) {?>checked="checked"<?php }?>/>
							<label for="mastercard"><?php echo smartyTranslate(array('s'=>'Mastercard','mod'=>'stripe_official'),$_smarty_tpl);?>
</label><br/>
							<input type="checkbox" id="american_express" name="american_express" <?php if ($_smarty_tpl->tpl_vars['american_express']->value) {?>checked="checked"<?php }?>/>
							<label for="american_express"><?php echo smartyTranslate(array('s'=>'American Express','mod'=>'stripe_official'),$_smarty_tpl);?>
</label><br/>
							<input type="checkbox" id="cb" name="cb" <?php if ($_smarty_tpl->tpl_vars['cb']->value) {?>checked="checked"<?php }?>/>
							<label for="cb"><?php echo smartyTranslate(array('s'=>'CB (Cartes Bancaires)','mod'=>'stripe_official'),$_smarty_tpl);?>
</label><br/>
							<input type="checkbox" id="diners_club" name="diners_club" <?php if ($_smarty_tpl->tpl_vars['diners_club']->value) {?>checked="checked"<?php }?>/>
							<label for="diners_club"><?php echo smartyTranslate(array('s'=>'Diners Club / Discover','mod'=>'stripe_official'),$_smarty_tpl);?>
</label><br/>
							<input type="checkbox" id="union_pay" name="union_pay" <?php if ($_smarty_tpl->tpl_vars['union_pay']->value) {?>checked="checked"<?php }?>/>
							<label for="union_pay"><?php echo smartyTranslate(array('s'=>'China UnionPay','mod'=>'stripe_official'),$_smarty_tpl);?>
</label><br/>
							<input type="checkbox" id="jcb" name="jcb" <?php if ($_smarty_tpl->tpl_vars['jcb']->value) {?>checked="checked"<?php }?>/>
							<label for="jcb"><?php echo smartyTranslate(array('s'=>'JCB','mod'=>'stripe_official'),$_smarty_tpl);?>
</label><br/>
							<input type="checkbox" id="discovers" name="discovers" <?php if ($_smarty_tpl->tpl_vars['discovers']->value) {?>checked="checked"<?php }?>/>
							<label for="discovers"><?php echo smartyTranslate(array('s'=>'Discovers','mod'=>'stripe_official'),$_smarty_tpl);?>
</label><br/>
						</div>

						<div class="form-group">
							<input type="checkbox" id="postcode" name="postcode" <?php if ($_smarty_tpl->tpl_vars['postcode']->value) {?>checked="checked"<?php }?>/>
							<label for="postcode"><?php echo smartyTranslate(array('s'=>'Disable the Postal Code field for cards from the United States, United Kingdom and Canada (not recommended *).','mod'=>'stripe_official'),$_smarty_tpl);?>
</label><br/>
							<span>*<?php echo smartyTranslate(array('s'=>'Collecting postal code optimizes the chances of successful payment for these countries.','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
						</div>

						<div class="form-group">
							<input type="checkbox" id="cardholdername" name="cardholdername" <?php if ($_smarty_tpl->tpl_vars['cardholdername']->value) {?>checked="checked"<?php }?>/>
							<label for="cardholdername"><?php echo smartyTranslate(array('s'=>'Activate display of card holder name','mod'=>'stripe_official'),$_smarty_tpl);?>
</label>
						</div>
					</li>
					<li>
						<p><?php echo smartyTranslate(array('s'=>'Additional payment methods (For users in Europe only): iDEAL, Bancontact, SOFORT and Giropay.','mod'=>'stripe_official'),$_smarty_tpl);?>
</p>
						<p>
							<?php ob_start();?><?php echo smartyTranslate(array('s'=>'These payment methods are available within this plugin for our European users only. To activate them, follow these [b]three steps:[/b]','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp11=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp11);?>

						</p>

						<ol type="A">
							<li>
								<?php echo smartyTranslate(array('s'=>'Select below each payment method you wish to offer on your website :','mod'=>'stripe_official'),$_smarty_tpl);?>

								<br><br>
								<div class="form-group">
									<input type="checkbox" id="ideal" name="ideal" <?php if ($_smarty_tpl->tpl_vars['ideal']->value) {?>checked="checked"<?php }?>/>
									<label for="ideal"><?php echo smartyTranslate(array('s'=>'Activate iDEAL (if you have Dutch customers)','mod'=>'stripe_official'),$_smarty_tpl);?>
</label><br>
									<input type="checkbox" id="bancontact" name="bancontact" <?php if ($_smarty_tpl->tpl_vars['bancontact']->value) {?>checked="checked"<?php }?>/>
									<label for="bancontact"><?php echo smartyTranslate(array('s'=>'Activate Bancontact (if you have Belgian customers)','mod'=>'stripe_official'),$_smarty_tpl);?>
</label><br>
									<input type="checkbox" id="sofort" name="sofort" <?php if ($_smarty_tpl->tpl_vars['sofort']->value) {?>checked="checked"<?php }?>/>
									<label for="sofort"><?php echo smartyTranslate(array('s'=>'Activate SOFORT (if you have German, Austrian customers)','mod'=>'stripe_official'),$_smarty_tpl);?>
</label><br>
									<input type="checkbox" id="giropay" name="giropay" <?php if ($_smarty_tpl->tpl_vars['giropay']->value) {?>checked="checked"<?php }?>/>
									<label for="giropay"><?php echo smartyTranslate(array('s'=>'Activate Giropay (if you have German customers)','mod'=>'stripe_official'),$_smarty_tpl);?>
</label><br>
									<input type="checkbox" id="applepay_googlepay" name="applepay_googlepay" <?php if ($_smarty_tpl->tpl_vars['applepay_googlepay']->value) {?>checked="checked"<?php }?>/>
									<label for="applepay_googlepay">
										<?php ob_start();?><?php echo smartyTranslate(array('s'=>'Enable Payment Request Buttons. (Apple Pay/Google Pay)[br]By using Apple Pay, you agree to [a @href1@]Stripe[/a] and [a @href2@]Apple[/a]\'s terms of service.','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp12=ob_get_clean();?><?php ob_start();?><?php echo 'https://stripe.com/us/legal';?>
<?php $_tmp13=ob_get_clean();?><?php ob_start();?><?php echo 'https://www.apple.com/legal/internet-services/terms/site.html';?>
<?php $_tmp14=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp15=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp12,array('@href1@'=>$_tmp13,'@href2@'=>$_tmp14,'@target@'=>$_tmp15));?>

									</label>
								</div>

							</li>
							<li>
								<?php echo smartyTranslate(array('s'=>'To track correctly charges performed with these payment methods, you’ll need to add a “webhook”. A webhook is a way to be notified when an event (such as a successful payment) happens on your website.','mod'=>'stripe_official'),$_smarty_tpl);?>

								<br><br>
								<ul>
									<li>
										<?php ob_start();?><?php echo smartyTranslate(array('s'=>'Go on the webhook page of your Stripe dashboard: [a @href1@]https://dashboard.stripe.com/account/webhooks[/a]','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp16=ob_get_clean();?><?php ob_start();?><?php echo 'https://dashboard.stripe.com/account/webhooks';?>
<?php $_tmp17=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp18=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp16,array('@href1@'=>$_tmp17,'@target@'=>$_tmp18));?>

									</li>
									<li><?php echo smartyTranslate(array('s'=>'Click on "Add Endpoint" and copy/paste this URL in the "URL to be called" field:','mod'=>'stripe_official'),$_smarty_tpl);?>
 <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['url_webhhoks']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</li>
									<li><?php echo smartyTranslate(array('s'=>'Set the "Events to send" radion button to "Live events"','mod'=>'stripe_official'),$_smarty_tpl);?>
</li>
									<li><?php echo smartyTranslate(array('s'=>'Set the "Filter event" radio button to "Send all event types"','mod'=>'stripe_official'),$_smarty_tpl);?>
</li>
									<li><?php echo smartyTranslate(array('s'=>'Click on "Add endpoint"','mod'=>'stripe_official'),$_smarty_tpl);?>
<br>
										<img class="img-example1" src="/modules/stripe_official//views/img/example1.png">
									</li>
									<li><?php echo smartyTranslate(array('s'=>'Ultimately, your webhook dashboard page should look like this:','mod'=>'stripe_official'),$_smarty_tpl);?>
<br>
										<img class="img-example2" src="/modules/stripe_official//views/img/example2.png">
									</li>
								</ul>
							</li>
							<br>
							<li>
								<?php ob_start();?><?php echo smartyTranslate(array('s'=>'Activate these payment methods on your [a @href1@]Stripe dashboard[/a].','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp19=ob_get_clean();?><?php ob_start();?><?php echo 'https://dashboard.stripe.com/account/payments/settings';?>
<?php $_tmp20=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp21=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp19,array('@href1@'=>$_tmp20,'@target@'=>$_tmp21));?>

							</li>
							<p><?php echo smartyTranslate(array('s'=>'After clicking "Activate", the payment method is shown as pending with an indication of how long it might take to activate.','mod'=>'stripe_official'),$_smarty_tpl);?>

								<?php echo smartyTranslate(array('s'=>'Once you\'ve submitted this form, the payment method will move from pending to live within 10 minutes.','mod'=>'stripe_official'),$_smarty_tpl);?>
</p>
						</ol>
					</li>
				</ol>


			</div>
		</div>
		<div class="panel-footer">
			<button type="submit" value="1" id="configuration_form_submit_btn" name="submit_login" class="btn btn-default pull-right button">
				<i class="process-icon-save"></i>
				<?php echo smartyTranslate(array('s'=>'Save','mod'=>'stripe_official'),$_smarty_tpl);?>

			</button>
		</div>
	</div>
</form><?php }} ?>
