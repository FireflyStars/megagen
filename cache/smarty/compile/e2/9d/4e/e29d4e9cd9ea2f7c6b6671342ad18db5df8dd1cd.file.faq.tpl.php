<?php /* Smarty version Smarty-3.1.19, created on 2020-08-12 15:30:31
         compiled from "/var/www/html/modules/stripe_official/views/templates/admin/_partials/faq.tpl" */ ?>
<?php /*%%SmartyHeaderCode:7497973135f33eef777a1f1-69728382%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e29d4e9cd9ea2f7c6b6671342ad18db5df8dd1cd' => 
    array (
      0 => '/var/www/html/modules/stripe_official/views/templates/admin/_partials/faq.tpl',
      1 => 1596578470,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7497973135f33eef777a1f1-69728382',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'module_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5f33eef7a82977_51363254',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5f33eef7a82977_51363254')) {function content_5f33eef7a82977_51363254($_smarty_tpl) {?>

<div class="clearfix"></div>
<h3><i class="icon-info-sign"></i> <?php echo smartyTranslate(array('s'=>'THANKS FOR CHOOSING STRIPE','mod'=>'stripe_official'),$_smarty_tpl);?>
</h3>
<div class="form-group">
    <br>
    <?php echo smartyTranslate(array('s'=>'If you run into any issue after having installed this plugin, please first read our below FAQ and make sure :','mod'=>'stripe_official'),$_smarty_tpl);?>

    <br>
    <ol type="1">
        <li><?php echo smartyTranslate(array('s'=>'You have entered your API keys in the “Connection” tab of the Stripe module (we recommend checking that there is no space in the field).','mod'=>'stripe_official'),$_smarty_tpl);?>
</li>
        <li><?php echo smartyTranslate(array('s'=>'You are using test cards in Test mode and live cards in Live mode.','mod'=>'stripe_official'),$_smarty_tpl);?>
</li>
        <li><?php echo smartyTranslate(array('s'=>'If you’ve recently updated the module, you have refreshed your cache.','mod'=>'stripe_official'),$_smarty_tpl);?>
</li>
        <li><?php echo smartyTranslate(array('s'=>'You’re not using any other plugin that could impact payments.','mod'=>'stripe_official'),$_smarty_tpl);?>
</li>
    </ol>
    <br>
    <?php echo smartyTranslate(array('s'=>'You can also check out our support website:','mod'=>'stripe_official'),$_smarty_tpl);?>

    <a href="https://support.stripe.com" target="_blank">https://support.stripe.com</a>
    <br><br>
    <?php echo smartyTranslate(array('s'=>'If you have any additional question or remaining issue related to Stripe and this plugin, please contact our support team:','mod'=>'stripe_official'),$_smarty_tpl);?>

    <a href="https://support.stripe.com/email" target="_blank">https://support.stripe.com/email</a>
</div>

<div class="clearfix"></div>
<h3><i class="icon-info-sign"></i> <?php echo smartyTranslate(array('s'=>'Improve your conversion rate and securely charge your customers with Stripe, the easiest payment platform','mod'=>'stripe_official'),$_smarty_tpl);?>
</h3>
<div class="form-group stripe-module-col1inner">
    - <?php ob_start();?><?php echo smartyTranslate(array('s'=>'[b]Improve your conversion rate[/b] by offering a seamless payment experience to your customers: Stripe lets you host the payment form on your own pages, without redirection to a bank third-part page.','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp22=ob_get_clean();?><?php ob_start();?><?php echo 'https://stripe.com/radar';?>
<?php $_tmp23=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp24=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp22,array('@href1@'=>$_tmp23,'@target@'=>$_tmp24));?>
<br>
    - <?php ob_start();?><?php echo smartyTranslate(array('s'=>'[b]Keep your fraud under control[/b] thanks to customizable 3D-Secure and [a @href1@]Stripe Radar[/a], our suite of anti-fraud tools.','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp25=ob_get_clean();?><?php ob_start();?><?php echo 'https://stripe.com/radar';?>
<?php $_tmp26=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp27=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp25,array('@href1@'=>$_tmp26,'@target@'=>$_tmp27));?>
<br>
    - <?php ob_start();?><?php echo smartyTranslate(array('s'=>'[b]Easily refund[/b] your orders through your PrestaShop’s back-office (and automatically update your PrestaShop order status).','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp28=ob_get_clean();?><?php ob_start();?><?php echo 'https://stripe.com/radar';?>
<?php $_tmp29=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp30=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp28,array('@href1@'=>$_tmp29,'@target@'=>$_tmp30));?>
<br>
    - <?php ob_start();?><?php echo smartyTranslate(array('s'=>'Start selling abroad by offering payments in [b]135+ currencies[/b] and 4 local payment methods (iDEAL, Bancontact, SOFORT, Giropay).','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp31=ob_get_clean();?><?php ob_start();?><?php echo 'https://stripe.com/radar';?>
<?php $_tmp32=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp33=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp31,array('@href1@'=>$_tmp32,'@target@'=>$_tmp33));?>
<br><br>
    <img src="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['module_dir']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
/views/img/started.png" style="width:100%;">
    <br><br>
    <p>
        <?php ob_start();?><?php echo smartyTranslate(array('s'=>'Find out more about Stripe on our website: [a @href1@]www.stripe.com[/a]','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp34=ob_get_clean();?><?php ob_start();?><?php echo 'https://stripe.com/fr';?>
<?php $_tmp35=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp36=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp34,array('@href1@'=>$_tmp35,'@target@'=>$_tmp36));?>

    </p>
</div>

<div class="clearfix"></div>
<h3><i class="icon-info-sign"></i> <?php echo smartyTranslate(array('s'=>'Frequently Asked Questions','mod'=>'stripe_official'),$_smarty_tpl);?>
</h3>
<div class="faq items">
    <span class="faq-title"><?php echo smartyTranslate(array('s'=>'General','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
    <ul id="basics" class="faq-items">
        <li class="faq-item">
            <span class="faq-trigger"><?php echo smartyTranslate(array('s'=>'Do I need a Stripe account to use this module?','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    <?php ob_start();?><?php echo smartyTranslate(array('s'=>'Yes. It takes only a few minutes to sign up and it\'s free: [a @href1@]https://dashboard.stripe.com/register[/a].','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp37=ob_get_clean();?><?php ob_start();?><?php echo 'https://dashboard.stripe.com/register';?>
<?php $_tmp38=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp39=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp37,array('@href1@'=>$_tmp38,'@target@'=>$_tmp39));?>

                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger"><?php echo smartyTranslate(array('s'=>'Can I test before creating an account?','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    <?php echo smartyTranslate(array('s'=>'Unfortunately, you have to use your own account. Again, this is quick & free and doesn\'t engage you to anything. You can use the test mode and never go live if you are not satisfied with our solutions!','mod'=>'stripe_official'),$_smarty_tpl);?>

                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger"><?php echo smartyTranslate(array('s'=>'How much will it cost me?','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    <?php ob_start();?><?php echo smartyTranslate(array('s'=>'Downloading, installing and testing this module is entirely free. You will only get charged once you start processing live payments. You can learn more about our pricing model online: [a @href1@]https://stripe.com/pricing[/a].','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp40=ob_get_clean();?><?php ob_start();?><?php echo 'https://stripe.com/pricing';?>
<?php $_tmp41=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp42=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp40,array('@href1@'=>$_tmp41,'@target@'=>$_tmp42));?>

                </p>
            </div>
        </li>
    </ul>

    <span class="faq-title"><?php echo smartyTranslate(array('s'=>'Features','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
    <ul id="basics" class="faq-items">
        <li class="faq-item">
            <span class="faq-trigger"><?php echo smartyTranslate(array('s'=>'What payment methods are supported?','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    <?php echo smartyTranslate(array('s'=>'This module supports card payments, Apple Pay, Google Pay, Microsoft Pay, Bancontact, iDeal, Giropay and Sofort.','mod'=>'stripe_official'),$_smarty_tpl);?>
<br>
                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger"><?php echo smartyTranslate(array('s'=>'Why are some Stripe features not supported by this module?','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <?php echo smartyTranslate(array('s'=>'Implementing features in this module requires time for development, testing and releasing. We started with what we felt was more likely to cover most of the merchants and customers needs. We will be adding the missing features one by one in the future.','mod'=>'stripe_official'),$_smarty_tpl);?>

            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger"><?php echo smartyTranslate(array('s'=>'Can I ask you to add a new feature?','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    <?php ob_start();?><?php echo smartyTranslate(array('s'=>'We don\'t take feature requests at the moment. If you still feel like your suggestion could benefit all our PrestaShop users, feel free to reach out to us: [a @href1@]https://support.stripe.com/contact[/a].','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp43=ob_get_clean();?><?php ob_start();?><?php echo 'https://support.stripe.com/contact';?>
<?php $_tmp44=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp45=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp43,array('@href1@'=>$_tmp44,'@target@'=>$_tmp45));?>

                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger"><?php echo smartyTranslate(array('s'=>'Is Stripe Radar supported?','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    <?php echo smartyTranslate(array('s'=>'Yes, if available for your Stripe account, you can use Stripe Radar with this module.','mod'=>'stripe_official'),$_smarty_tpl);?>

                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger"><?php echo smartyTranslate(array('s'=>'Are card payments compatible with the new Strong Customer Authentication requirement and 3DS v2?','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    <?php echo smartyTranslate(array('s'=>'Yes, starting from the version 2.0 of this module, all card payments are compatible with 3DS v2 and SCA ready.','mod'=>'stripe_official'),$_smarty_tpl);?>

                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger"><?php echo smartyTranslate(array('s'=>'Is Stripe Billing supported?','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    <?php echo smartyTranslate(array('s'=>'No, Stripe Billing is not currently supported.','mod'=>'stripe_official'),$_smarty_tpl);?>

                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger"><?php echo smartyTranslate(array('s'=>'Is Stripe Connect supported?','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    <?php echo smartyTranslate(array('s'=>'No, Stripe Connect is not currently supported.','mod'=>'stripe_official'),$_smarty_tpl);?>

                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger"><?php echo smartyTranslate(array('s'=>'How can I implement a new feature myself?','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    <?php ob_start();?><?php echo smartyTranslate(array('s'=>'If you\'re a developer, you can follow our online guides and documentation: [a @href1@]https://stripe.com/docs[/a].','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp46=ob_get_clean();?><?php ob_start();?><?php echo 'https://stripe.com/docs';?>
<?php $_tmp47=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp48=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp46,array('@href1@'=>$_tmp47,'@target@'=>$_tmp48));?>
<br/>
                    <?php ob_start();?><?php echo smartyTranslate(array('s'=>'Otherwise, you can look for a developer or an agency specialised in PrestaShop: [a @href1@]https://www.prestashop.com/experts[/a].','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp49=ob_get_clean();?><?php ob_start();?><?php echo 'https://www.prestashop.com/experts';?>
<?php $_tmp50=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp51=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp49,array('@href1@'=>$_tmp50,'@target@'=>$_tmp51));?>

                </p>
            </div>
        </li>
    </ul>

    <span class="faq-title"><?php echo smartyTranslate(array('s'=>'Installation','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
    <ul id="basics" class="faq-items">
        <li class="faq-item">
            <span class="faq-trigger"><?php echo smartyTranslate(array('s'=>'What are the requirements?','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    - <?php echo smartyTranslate(array('s'=>'PrestaShop 1.6 or higher','mod'=>'stripe_official'),$_smarty_tpl);?>
<br/>
                    - <?php echo smartyTranslate(array('s'=>'PHP 5.6 or higher','mod'=>'stripe_official'),$_smarty_tpl);?>
<br/>
                    - <?php echo smartyTranslate(array('s'=>'TLS 1.2 (live mode)','mod'=>'stripe_official'),$_smarty_tpl);?>
<br/>
                    - <?php echo smartyTranslate(array('s'=>'A Stripe account','mod'=>'stripe_official'),$_smarty_tpl);?>

                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger"><?php echo smartyTranslate(array('s'=>'Are there any known incompatibilities with other modules?','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    - <?php echo smartyTranslate(array('s'=>'PrestaShop is a highly customisable online shop solution with many ready to use extensions in order to fit your specific needs.','mod'=>'stripe_official'),$_smarty_tpl);?>
<br>
                    - <?php echo smartyTranslate(array('s'=>'Knowing this, it is impossible to guarantee that our module will work with all customised shops.','mod'=>'stripe_official'),$_smarty_tpl);?>
<br>
                    - <?php echo smartyTranslate(array('s'=>'This module is compatible with most of the existing modules. The only exception concerns modules altering the standard behavior of the checkout flow. If you have such modules installed in your shop, we recommend you try our module in your test environment first.','mod'=>'stripe_official'),$_smarty_tpl);?>
<br>
                    - <?php echo smartyTranslate(array('s'=>'In case there is an incompatibility, you should reach out to your developer to make our module compatible with your shop.','mod'=>'stripe_official'),$_smarty_tpl);?>
<br>
                    - <?php echo smartyTranslate(array('s'=>'There is a code compatibility problem with some modules that modify the checkout funnel. Stripe provides the necessary hooks for the other modules, but some of them are not able to read the hooks correctly.','mod'=>'stripe_official'),$_smarty_tpl);?>
<br>
                    - <?php echo smartyTranslate(array('s'=>'We encountered this problem with the following modules (non-exhaustive list):','mod'=>'stripe_official'),$_smarty_tpl);?>
<br>
                    <ul>
                        <li><?php echo smartyTranslate(array('s'=>'The checkout','mod'=>'stripe_official'),$_smarty_tpl);?>
</li>
                        <li><?php echo smartyTranslate(array('s'=>'One Page Checkout, Social Login & Mailchimp','mod'=>'stripe_official'),$_smarty_tpl);?>
</li>
                    </ul>
                    <?php echo smartyTranslate(array('s'=>'These modules have encountered problems with the 2.0.7 version of Stripe in October 2019, it’s possible that evolutions have been made to these modules since that date making them compatible with the Stripe module.','mod'=>'stripe_official'),$_smarty_tpl);?>

                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger"><?php echo smartyTranslate(array('s'=>'What are test and live modes?','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    - <?php echo smartyTranslate(array('s'=>'Test mode allows you to validate that the module works well in your shop and to see what the user experience feels like without actually debiting any money nor triggering any cost to you.','mod'=>'stripe_official'),$_smarty_tpl);?>
<br>
                    - <?php echo smartyTranslate(array('s'=>'Once ready to charge your customers with our module, you can switch to live mode.','mod'=>'stripe_official'),$_smarty_tpl);?>
<br>
                    - <?php echo smartyTranslate(array('s'=>'Test and live modes are distinguished by different sets of API keys.','mod'=>'stripe_official'),$_smarty_tpl);?>
<br>
                    - <?php ob_start();?><?php echo smartyTranslate(array('s'=>'For more information: [a @href1@]https://stripe.com/docs/keys[/a] and [a @href2@]https://stripe.com/docs/testing[/a]\'s terms of service.','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp52=ob_get_clean();?><?php ob_start();?><?php echo 'https://stripe.com/docs/keys';?>
<?php $_tmp53=ob_get_clean();?><?php ob_start();?><?php echo 'https://stripe.com/docs/testing';?>
<?php $_tmp54=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp55=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp52,array('@href1@'=>$_tmp53,'@href2@'=>$_tmp54,'@target@'=>$_tmp55));?>

                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger"><?php echo smartyTranslate(array('s'=>'How can I check if my installation is successful?','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    <?php ob_start();?><?php echo smartyTranslate(array('s'=>'Configure your test mode API keys (see [a @href1@]https://stripe.com/docs/keys[/a]), activate your preferred payment methods and go through the consumer checkout flow.','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp56=ob_get_clean();?><?php ob_start();?><?php echo 'https://stripe.com/docs/keys';?>
<?php $_tmp57=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp58=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp56,array('@href1@'=>$_tmp57,'@target@'=>$_tmp58));?>

                    <?php ob_start();?><?php echo smartyTranslate(array('s'=>'You can use our testing card numbers: [a @href1@]https://stripe.com/docs/testing[/a].','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp59=ob_get_clean();?><?php ob_start();?><?php echo 'https://stripe.com/docs/testing';?>
<?php $_tmp60=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp61=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp59,array('@href1@'=>$_tmp60,'@target@'=>$_tmp61));?>
<br/>
                    <?php echo smartyTranslate(array('s'=>'If the module works well in test mode, it should work just as well in live mode.','mod'=>'stripe_official'),$_smarty_tpl);?>

                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger"><?php echo smartyTranslate(array('s'=>'How can I configure my 3DS preferences?','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    <?php ob_start();?><?php echo smartyTranslate(array('s'=>'You can use Stripe Radar, our anti-fraud module to fine-tune your protection needs directly from your Stripe Dashboard: [a @href1@]https://stripe.com/radar[/a].','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp62=ob_get_clean();?><?php ob_start();?><?php echo 'https://stripe.com/radar';?>
<?php $_tmp63=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp64=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp62,array('@href1@'=>$_tmp63,'@target@'=>$_tmp64));?>

                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger"><?php echo smartyTranslate(array('s'=>'Add my domain ApplePay manually from my dashboard in order to use ApplePay','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    <?php echo smartyTranslate(array('s'=>'You can manually add your domain(s) throught your Dashboard. You can easily do this by following those five steps once you are logged into your Dashboard : ','mod'=>'stripe_official'),$_smarty_tpl);?>
<br/>
                    A : <?php echo smartyTranslate(array('s'=>'Go to the "Payments" menu.','mod'=>'stripe_official'),$_smarty_tpl);?>
<br/>
                    B : <?php echo smartyTranslate(array('s'=>'Click on Apple Pay.','mod'=>'stripe_official'),$_smarty_tpl);?>
<br/>
                    C : <?php echo smartyTranslate(array('s'=>'Click on the button "Add new domain".','mod'=>'stripe_official'),$_smarty_tpl);?>
<br/>
                    D : <?php echo smartyTranslate(array('s'=>'Provide the domain by filling the input.','mod'=>'stripe_official'),$_smarty_tpl);?>
<br/>
                    (<?php echo smartyTranslate(array('s'=>'Additional step : make sure you have the file "apple-developer-merchantid-domain-association" on the root of your website in the folder ".well-known/". If no, please follow the steps 2 and 3 in the popup "Add new domain."','mod'=>'stripe_official'),$_smarty_tpl);?>
)<br/>
                    E : <?php echo smartyTranslate(array('s'=>'Add your domain by clicking thebutton "Add".','mod'=>'stripe_official'),$_smarty_tpl);?>
<br/><br/>
                    <img class="add_domain" src="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['module_dir']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
views/img/stripe_add_domain.png" />
                </p>
            </div>
        </li>
    </ul>

    <span class="faq-title"><?php echo smartyTranslate(array('s'=>'Troubleshooting problems','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
    <ul id="basics" class="faq-items">
        <li class="faq-item">
            <span class="faq-trigger"><?php echo smartyTranslate(array('s'=>'I activated Apple Pay / Google Pay, why can\'t I see the Pay button?','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    - <?php echo smartyTranslate(array('s'=>'Make sure that your host supports TLS 1.2.','mod'=>'stripe_official'),$_smarty_tpl);?>
<br/>
                    - <?php ob_start();?><?php echo smartyTranslate(array('s'=>'For Apple Pay, you also need to get your domain verified by Apple (see [a @href1@]https://stripe.com/docs/apple-pay/web/v2#going-live[/a]).','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp65=ob_get_clean();?><?php ob_start();?><?php echo 'https://stripe.com/docs/apple-pay/web/v2#going-live';?>
<?php $_tmp66=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp67=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp65,array('@href1@'=>$_tmp66,'@target@'=>$_tmp67));?>
<br/>
                    - <?php echo smartyTranslate(array('s'=>'Check that you have a payment card saved in your device/browser.','mod'=>'stripe_official'),$_smarty_tpl);?>
<br/>
                    <?php ob_start();?><?php echo smartyTranslate(array('s'=>'If the button still doesn\'t show, please contact our developers: [a @href1@]https://addons.prestashop.com/en/contact-us?id_product=24922[/a].','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp68=ob_get_clean();?><?php ob_start();?><?php echo 'https://addons.prestashop.com/en/contact-us?id_product=24922';?>
<?php $_tmp69=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp70=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp68,array('@href1@'=>$_tmp69,'@target@'=>$_tmp70));?>

                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger"><?php echo smartyTranslate(array('s'=>'My customer can\'t/couldn\'t pay, how can I help him?','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    <?php ob_start();?><?php echo smartyTranslate(array('s'=>'You can check your Stripe Dashboard to see if his payment has been declined: [a @href1@]https://dashboard.stripe.com/payments[/a].','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp71=ob_get_clean();?><?php ob_start();?><?php echo 'https://dashboard.stripe.com/payments';?>
<?php $_tmp72=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp73=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp71,array('@href1@'=>$_tmp72,'@target@'=>$_tmp73));?>
<br>
                    <?php ob_start();?><?php echo smartyTranslate(array('s'=>'If you don\'t find a trace of any payment attempt for that customer, there might have been a technical issue. Please reach out to our developers: [a @href1@]https://addons.prestashop.com/en/contact-us?id_product=24922[/a].','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp74=ob_get_clean();?><?php ob_start();?><?php echo 'https://addons.prestashop.com/en/contact-us?id_product=24922';?>
<?php $_tmp75=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp76=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp74,array('@href1@'=>$_tmp75,'@target@'=>$_tmp76));?>

                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger"><?php echo smartyTranslate(array('s'=>'My customer already provided his payment details, can I debit him myself for future orders?','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    <?php ob_start();?><?php echo smartyTranslate(array('s'=>'Stripe allows payment methods to be securely saved against your customers for future use: [a @href1@]https://stripe.com/docs/payments/payment-methods/saving[/a].','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp77=ob_get_clean();?><?php ob_start();?><?php echo 'https://stripe.com/docs/payments/payment-methods/saving';?>
<?php $_tmp78=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp79=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp77,array('@href1@'=>$_tmp78,'@target@'=>$_tmp79));?>
<br/>
                    <?php echo smartyTranslate(array('s'=>'Unfortunately, this module doesn\'t support this feature yet. Thus, your customer has to enter his payment details for any new payment via the usual checkout flow.','mod'=>'stripe_official'),$_smarty_tpl);?>

                </p>
            </div>
        </li>

        <li class="faq-item">
            <span class="faq-trigger"><?php echo smartyTranslate(array('s'=>'My customer paid but the order status has not been updated, what should I do?','mod'=>'stripe_official'),$_smarty_tpl);?>
</span>
            <span class="expand pull-right">+</span>
            <div class="faq-content">
                <p>
                    <?php echo smartyTranslate(array('s'=>'A technical issue might have occured between the payment and the order validation.','mod'=>'stripe_official'),$_smarty_tpl);?>
<br/>
                    <?php ob_start();?><?php echo smartyTranslate(array('s'=>'You can check your Stripe Dashboard to confirm that the customer has indeed been debited ([a @href1@]https://dashboard.stripe.com/payments[/a]) and update the order manually in your shop\'s back office.','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp80=ob_get_clean();?><?php ob_start();?><?php echo 'https://dashboard.stripe.com/payments';?>
<?php $_tmp81=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp82=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp80,array('@href1@'=>$_tmp81,'@target@'=>$_tmp82));?>
<br/>
                    <?php ob_start();?><?php echo smartyTranslate(array('s'=>'If this occurs more than once, you may want to ask your developer to investigate for any Javascript or PHP errors occuring during the checkout flow and eventually reach out to our developers for help: [a @href1@]https://addons.prestashop.com/en/contact-us?id_product=24922[/a].','mod'=>'stripe_official'),$_smarty_tpl);?>
<?php $_tmp83=ob_get_clean();?><?php ob_start();?><?php echo 'https://addons.prestashop.com/en/contact-us?id_product=24922';?>
<?php $_tmp84=ob_get_clean();?><?php ob_start();?><?php echo 'target="blank"';?>
<?php $_tmp85=ob_get_clean();?><?php echo smarty_modifier_stripelreplace($_tmp83,array('@href1@'=>$_tmp84,'@target@'=>$_tmp85));?>

                </p>
            </div>
        </li>
    </ul>
</div><?php }} ?>
