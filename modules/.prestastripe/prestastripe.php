<?php
/**
 * 2007-2015 PrestaShop
 *
 * DISCLAIMER
 ** Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 * International Registered Trademark & Property of PrestaShop SA
 */

require_once dirname(__FILE__).'/stripe/init.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

class PrestaStripe extends PaymentModule
{
    /* status */
    const _FLAG_NULL_ = 0;

    const _FLAG_ERROR_ = 1;
    const _FLAG_WARNING_ = 2;
    const _FLAG_SUCCESS_ = 4;

    const _FLAG_STDERR_ = 1;
    const _FLAG_STDOUT_ = 2;
    const _FLAG_STDIN_ = 4;
    const _FLAG_MAIL_ = 8;
    const _FLAG_NO_FLUSH__ = 16;
    const _FLAG_FLUSH__ = 32;

    /* Stripe Pre fix */
    const _PS_STRIPE_ = '_PS_STRIPE_';

    /* 0: no VERBOSE, 1: VERBOSE, 2: VERBOSE + ANSI COLOR */
    public static $verbose = 2;
    public static $log_file = '';
    public $mail = '';

    /* init conf var */
    private static $psconf = array(
    );
    /* init hook var */
    private static $pshook = array(
    );

    /* tab section shape */
    private $section_shape = 1;

    public $addons_track;

    public $errors = array();
    public $warnings = array();
    public $infos = array();
    public $success = array();

    /* refund */
    public $refund = 0;

    public function __construct()
    {
        $this->name = 'prestastripe';
        $this->tab = 'payments_gateways';
        $this->version = '1.1.2';
        $this->module_key = '1aaea58011ea45f7fe2fee243a920384';
        $this->author = 'PrestaShop';
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');
        $this->bootstrap = true;
        $this->display = 'view';

        /* curl check */
        if (is_callable('curl_init') === false) {
            $this->warning = $this->l('To be able to use this module, please activate cURL (PHP extension).');
        }

        parent::__construct();

        $this->meta_title = $this->l('Stripe');
        $this->displayName = $this->l('Stripe payment module');
        $this->description = $this->l('Start accepting stripe payments today, directly from your shop!');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?', $this->name);
        if (!Configuration::get('PS_SSL_ENABLED')) {
            $this->warning = $this->l('You must enable SSL on the store if you want to use this module');
        }

        /* Use a specific name to bypass an Order confirmation controller check */
        if (in_array(Tools::getValue('controller'), array('orderconfirmation', 'order-confirmation'))) {
            $this->displayName = $this->l('Payment by Stripe');
        }
    }

    public function install()
    {
        $partial_refund_state = Configuration::get(self::_PS_STRIPE_.'partial_refund_state');

        /* Create Order State for Stripe */
        if ($partial_refund_state === false) {
            $order_state = new OrderState();
            $langs = Language::getLanguages();
            foreach ($langs as $lang) {
                $order_state->name[$lang['id_lang']] = pSQL('Stripe Partial Refund');
            }
            $order_state->invoice = false;
            $order_state->send_email = false;
            $order_state->logable = true;
            $order_state->color = '#FFDD99';
            $order_state->save();

            Configuration::updateValue(self::_PS_STRIPE_.'partial_refund_state', $order_state->id);
        }

        /* Check PHP version for namespace */
        if (version_compare(phpversion(), '5.3.0', '<')) {
            // php version isn't high enough
            $this->_errors[] = $this->l(
                'You\'re php version isn\'t high enough to use '. $this->meta_title.
                ' ,please contact your hosting provider to upgrade it'
            );
            return false;
        }

        return parent::install()
            && $this->registerHook('payment')
            && $this->registerHook('header')
            && $this->registerHook('orderConfirmation')
            && $this->registerHook('adminOrder')
            && Configuration::updateValue(self::_PS_STRIPE_.'mode', 1)
            && Configuration::updateValue(self::_PS_STRIPE_.'refund_mode', 1)
            && $this->createStripePayment();
    }

    public function uninstall()
    {
        /* Delete Database + Table */
        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'stripe_payment`');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'stripe_payment`');

        return parent::uninstall()
            && Configuration::updateValue(self::_PS_STRIPE_.'key', '')
            && Configuration::updateValue(self::_PS_STRIPE_.'test_key', '')
            && Configuration::updateValue(self::_PS_STRIPE_.'publishable', '')
            && Configuration::updateValue(self::_PS_STRIPE_.'test_publishable', '');
    }

    /* Create Database Stripe Payment */
    protected function createStripePayment()
    {
        $db = Db::getInstance();
        $query = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'stripe_payment` (
            `id_payment` int(11) NOT NULL AUTO_INCREMENT,
            `id_stripe` varchar(255) NOT NULL,
            `name` varchar(255) NOT NULL,
            `id_cart` int(11) NOT NULL,
            `last4` varchar(4) NOT NULL,
            `type` varchar(255) NOT NULL,
            `amount` varchar(255) NOT NULL,
            `refund` varchar(255) NOT NULL,
            `currency` varchar(255) NOT NULL,
            `result` tinyint(4) NOT NULL,
            `date_add` datetime NOT NULL,
            PRIMARY KEY (`id_payment`),
           KEY `id_cart` (`id_cart`)
       ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';
        $db->Execute($query);

        return true;
    }

    private function hasErrors()
    {
        return !!$this->errors;
    }

    private function hasWarnings()
    {
        return !!$this->warnings;
    }

    private function hasInfos()
    {
        return !!$this->infos;
    }

    private function hasSuccess()
    {
        return !!$this->success;
    }

    public static function arrayAsHtmlList(array $ar = array())
    {
        if (!empty($ar)) {
            return '<ul><li>'.implode('</li><li>', $ar).'</li></ul>';
        }
        return '';
    }

    public function displayInformation($msg)
    {
        if ($msg) {
            return '<div class="'.(version_compare(_PS_VERSION_, '1.6', '>=') ? 'alert ' : '').'alert-info info">'.$msg.'</div>';
        }
        return $msg;
    }

    public function displayWarning($msg)
    {
        if ($msg) {
            return '<div class="'.(version_compare(_PS_VERSION_, '1.6', '>=') ? 'alert ' : '').'alert-warning warn">'.$msg.'</div>';
        }
        return $msg;
    }

    /*
     ** @method: showHeadMessages
     ** @description: show errors
     **
     ** @arg: $key
     ** @return: key if configuration has key else throw new exception
     */
    public function showHeadMessages(&$terror = '')
    {
        $msgs_list = array_map('array_filter', array(
            'displayInfos' => $this->infos,
            'displayWarning' => $this->warnings,
            'displayError' => $this->errors,
            'displayConfirmation' => $this->success,
        ));

        foreach ($msgs_list as $display => $msgs) {
            if (!empty($msgs)) {
                $terror = call_user_func(array($this, $display), '<p>Stripe</p>'.self::arrayAsHtmlList($msgs)).$terror;
            }
        }

        return (!empty($terror) ? $terror : ($terror = $this->displayError('Unknow error(s)')));
    }

    /*
     ** @method: c
     ** @description: self configuration key check
     **
     ** @arg: $key
     ** @return: key if configuration has key else throw new exception
     */
    private function c($key = false)
    {
        if ($key && array_key_exists($key, self::$psconf)) {
            return self::_CONF_PREFIX_.$key;
        }
        throw new PrestaShopException(sprintf($this->l('undefined key : [%s]'), ($key ? $key : 'none')));
    }

    /*
     ** @Method: updateConfiguration
     ** @description: submitOptionsconfiguration update values
     **
     ** @arg:
     ** @return: (none)
     */
    private function updateOptionsConfiguration()
    {
        if (Tools::isSubmit('submitOptionsconfiguration')) {
            $prefix_len = Tools::strlen(self::_CONF_PREFIX_);
            foreach ($_POST as $key => $value) {
                /* 	$key = sprintf('%.50s', $key); */
                if (Tools::isSubmit($key) && !strncmp($key, self::_CONF_PREFIX_, $prefix_len)) {
                    if (Configuration::hasKey($key)) {
                        Configuration::updateValue($key, Tools::getValue($key));
                    } else {
                        $this->errors[] = sprintf($this->l('the key: [%s] doesn\'t exist..'), $key);
                    }
                }
            }
        }

        return !$this->hasErrors();
    }

    public function getBadgesClass(array $keys = array())
    {
        $class = self::_FLAG_NULL_;

        if (!empty($keys)) {
            foreach ($keys as $key) {
                if (isset($this->errors[$key])) {
                    $class |= self::_FLAG_ERROR_;
                } else if (isset($this->warnings[$key])) {
                    $class |= self::_FLAG_WARNING_;
                } else {
                    $class |= self::_FLAG_SUCCESS_;
                }
            }

            if ($class & self::_FLAG_ERROR_) {
                return 'tab-error';
            } else if ($class & self::_FLAG_WARNING_) {
                return 'tab-warning';
            } else if ($class & self::_FLAG_SUCCESS_) {
                return 'tab-success';
            }
        }

        return false;
    }

    /**
     * Loads asset resources
     */
    public function loadAssetCompatibility()
    {
        $css_compatibility = $js_compatibility = array();

        $css_compatibility = array(
            $this->_path.'/views/css/compatibility/font-awesome.min.css',
            $this->_path.'/views/css/compatibility/bootstrap-select.min.css',
            $this->_path.'/views/css/compatibility/bootstrap-responsive.min.css',
            $this->_path.'/views/css/compatibility/bootstrap.min.css',
            $this->_path.'/views/css/tabs15.css',
            $this->_path.'/views/css/compatibility/bootstrap.extend.css',
        );
        $this->context->controller->addCSS($css_compatibility, 'all');

        // Load JS
        $js_compatibility = array(
            $this->_path.'/views/js/compatibility/bootstrap-select.min.js',
            $this->_path.'/views/js/compatibility/bootstrap.min.js'
        );

        $this->context->controller->addJS($js_compatibility);
    }

    private function loadRessources()
    {
        $content = array(
            $this->displaySomething(),
            $this->displayForm(),
            $this->displayTransaction(),
            $this->displayRefundForm(),
            $this->displayFAQ(),
            $this->displayContact()
        );

        $tab_contents = array(
            'title' => $this->l('UPS tracking'),
            'contents' => array(
                array(
                    'name' => $this->l('Get Started'),
                    'icon' => 'icon-book',
                    'value' => $content[0],
                    'badge' => $this->getBadgesClass(),
                ),
                array(
                    'name' => $this->l('Connection'),
                    'icon' => 'icon-power-off',
                    'value' => $content[1],
                    'badge' => $this->getBadgesClass(array(
                        'log_error_secret',
                        'log_error_publishable',
                        'log_success_secret',
                        'log_success_publishable',
                        'connection',
                        'empty'
                    )),
                ),
                array(
                    'name' => $this->l('Transactions'),
                    'icon' => 'icon-credit-card',
                    'value' => $content[2],
                    'badge' => $this->getBadgesClass(),
                ),
                array(
                    'name' => $this->l('Refund'),
                    'icon' => 'icon-ticket',
                    'value' => $content[3],
                    'badge' => $this->getBadgesClass(),
                ),
                array(
                    'name' => $this->l('FAQ'),
                    'icon' => 'icon-question',
                    'value' => $content[4],
                    'badge' => $this->getBadgesClass(),
                ),
                array(
                    'name' => $this->l('Contact'),
                    'icon' => 'icon-envelope',
                    'value' => $content[5],
                    'badge' => $this->getBadgesClass(),
                ),
            ),
            'logo' => Tools::getShopDomain(true, true).__PS_BASE_URI__.basename(_PS_MODULE_DIR_).'/'.$this->name.'/views/img/logo.png'
        );

        $this->context->smarty->assign('tab_contents', $tab_contents);
        $this->context->smarty->assign('ps_version', _PS_VERSION_);
        $this->context->controller->addJs($this->_path.'/views/js/PSTabs.js');
        $this->context->controller->addJs($this->_path.'/views/js/faq.js');
        $this->context->controller->addCss($this->_path.'/views/css/started.css');
        $this->context->controller->addCss($this->_path.'/views/css/tabs.css');

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->context->controller->addJs($this->_path.'/views/js/back_1.5.js');
            $this->loadAssetCompatibility();
        } else {
            $this->context->controller->addJs($this->_path.'/views/js/back.js');
        }

        return $this->display($this->_path, 'views/templates/admin/main.tpl');
    }

    public function loadAddonTracker()
    {
        $track_query = 'utm_source=back-office&utm_medium=module&utm_campaign=back-office-%s&utm_content=%s';
        $lang = new Language(Configuration::get('PS_LANG_DEFAULT'));

        if ($lang && Validate::isLoadedObject($lang)) {
            $track_query = sprintf($track_query, Tools::strtoupper($lang->iso_code), $this->name);
            $this->context->smarty->assign('url_track', $track_query);
            return true;
        }

        return false;
    }

    public function retrieveAccount($secret_key, $publishable_key = '', $log = 0)
    {
        \Stripe\Stripe::setApiKey($secret_key);
        try {
            \Stripe\Account::retrieve();
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->errors['exception'] = $e->getMessage();
            return false;
        }
        return true;
    }

    /*
     ** @Method: contentLogIn
     ** @description: Submit Log in action
     **
     ** @arg: (none)
     ** @return: (none)
     */
    public function contentLogIn()
    {
        if (Tools::isSubmit('submit_login')) {
            if (Tools::getValue(self::_PS_STRIPE_.'mode') == 1) {
                $secret_key = Tools::getValue(self::_PS_STRIPE_.'test_key');
                $publishable_key = Tools::getValue(self::_PS_STRIPE_.'test_publishable');
                if (!empty($secret_key) && !empty($publishable_key)) {
                    if ($this->retrieveAccount($secret_key, $publishable_key)) {
                        Configuration::updateValue(self::_PS_STRIPE_.'test_key', Tools::getValue(self::_PS_STRIPE_.'test_key'));
                        Configuration::updateValue(self::_PS_STRIPE_.'test_publishable', Tools::getValue(self::_PS_STRIPE_.'test_publishable'));
                    }
                } else {
                    $this->errors['empty'] = 'Client ID and Secret Key fields are mandatory';
                }
                Configuration::updateValue(self::_PS_STRIPE_.'mode', Tools::getValue(self::_PS_STRIPE_.'mode'));
            } else {
                $secret_key = Tools::getValue(self::_PS_STRIPE_.'key');
                $publishable_key = Tools::getValue(self::_PS_STRIPE_.'publishable');
                if (!empty($secret_key) && !empty($publishable_key)) {
                    if ($this->retrieveAccount($secret_key, $publishable_key)) {
                        Configuration::updateValue(self::_PS_STRIPE_.'key', Tools::getValue(self::_PS_STRIPE_.'key'));
                        Configuration::updateValue(self::_PS_STRIPE_.'publishable', Tools::getValue(self::_PS_STRIPE_.'publishable'));
                    }
                } else {
                    $this->errors['empty'] = 'Client ID and Secret Key fields are mandatory';
                }
                Configuration::updateValue(self::_PS_STRIPE_.'mode', Tools::getValue(self::_PS_STRIPE_.'mode'));
            }
        }
    }

    /*
     ** @Method: contentRefund
     ** @description: Make a payment Refund with ID
     **
     ** @arg: (none)
     ** @return: (none)
     */
    public function contentRefund()
    {
        if (Tools::isSubmit('submit_refund_id')) {
            $refund_id = Tools::getValue(self::_PS_STRIPE_.'refund_id');
            if (!empty($refund_id)) {
                $refund = Db::getInstance()->ExecuteS('SELECT *	FROM '._DB_PREFIX_.'stripe_payment WHERE `id_stripe` = "'.pSQL($refund_id).'"');
            } else {
                $this->errors['refund'] = $this->l('Please make sure to put a Stripe Id');
                return false;
            }

            if ($refund) {
                $this->refund = 1;
                Configuration::updateValue(self::_PS_STRIPE_.'refund_id', Tools::getValue(self::_PS_STRIPE_.'refund_id'));
            } else {
                $this->refund = 0;
                $this->errors['refund'] = $this->l('This Stipe ID doesn\'t exist, please check it again');
                Configuration::updateValue(self::_PS_STRIPE_.'refund_id', '');
            }
        }

        if (Tools::isSubmit('submit_refund_action')) {
            $amount = null;
            $refund_id = Configuration::get(self::_PS_STRIPE_.'refund_id');
            if (!empty($refund_id)) {
                $refund = Db::getInstance()->ExecuteS('SELECT *	FROM '._DB_PREFIX_.'stripe_payment WHERE `id_stripe` = "'.pSQL($refund_id).'"');
            }

            $mode = Tools::getValue(self::_PS_STRIPE_.'refund_mode');
            if ($mode == 0) {
                $amount = Tools::getValue(self::_PS_STRIPE_.'refund_amount');
            }

            $this->apiRefund($refund[0]['id_stripe'], $amount, $refund[0]['currency'], $mode, $refund[0]['id_cart']);
        }
    }

    /*
     ** @Method: getContent
     ** @description: render main content
     **
     ** @arg:
     ** @return: (none)
     */
    public function getContent()
    {
        /* Check if SSL is enabled */
        if (!Configuration::get('PS_SSL_ENABLED')) {
            $this->errors[] = $this->l('A SSL certificate is required to process credit card payments using Stripe. Please consult the FAQ.');
        }

        /* Do Log In  */
        $this->contentLogIn();

        if (!Configuration::get(self::_PS_STRIPE_.'key') && !Configuration::get(self::_PS_STRIPE_.'publishable')
            && !Configuration::get(self::_PS_STRIPE_.'test_key') && !Configuration::get(self::_PS_STRIPE_.'test_publishable')) {
            $this->warnings['connection'] = false;
        }

        /* Do Refund */
        $this->contentRefund();

        /* generate url track */
        $this->loadAddonTracker();

        /* Your content */
        $html = $this->loadRessources();

        if (!empty($this->errors) || !empty($this->warnings)) {
            $this->showHeadMessages($html);
        }

        return $html;
    }

    /*
     ** @Method: apiRefund
     ** @description: Make a Refund (charge) with Stripe
     **
     ** @arg: amount, id_stripe
     ** @amount: if null total refund
     ** @currency: "USD", "EUR", etc..
     ** @mode: (boolean) ? total : partial
     ** @return: (none)
     */
    public function apiRefund($refund_id, $amount = null, $currency, $mode, $id_card)
    {
        $secret_key = $this->getSecretKey();
        if ($this->retrieveAccount($secret_key, '', 1) && !empty($refund_id)) {
            $refund = Db::getInstance()->ExecuteS('SELECT *	FROM '._DB_PREFIX_.'stripe_payment WHERE `id_stripe` = "'.pSQL($refund_id).'"');
            if ($mode == 1) { /* Total refund */
                try {
                    $ch = \Stripe\Charge::retrieve($refund_id);
                    $ch->refunds->create();
                } catch (Exception $e) {
                    // Something else happened, completely unrelated to Stripe
                    $this->errors['exception'] = $e->getMessage();
                    return false;
                }

                Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'stripe_payment` SET `result` = 2, `date_add` = NOW(), `refund` = "'
                    .pSQL($refund[0]['amount']).'" WHERE `id_stripe` = "'.pSQL($refund_id).'"');
            } else { /* Partial refund */
                if (!preg_match('/BIF|DJF|JPY|KRW|PYG|VND|XAF|XPF|CLP|GNF|KMF|MGA|RWF|VUV|XOF/i', $currency)) {
                    $ref_amount = $amount * 100;
                }
                try {
                    $ch = \Stripe\Charge::retrieve($refund_id);
                    $ch->refunds->create(array('amount' => $ref_amount));
                } catch (Exception $e) {
                    // Something else happened, completely unrelated to Stripe
                    $this->errors['exception'] = $e->getMessage();
                    return false;
                }

                $amount += ($refund[0]['refund']);
                if ($amount == $refund[0]['amount']) {
                    $result = 2;
                } else {
                    $result = 3;
                }

                if ($amount <= $refund[0]['amount']) {
                    Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'stripe_payment` SET `result` = '.(int)$result.', `date_add` = NOW(), `refund` = "'
                        .pSQL($amount).'" WHERE `id_stripe` = "'.pSQL($refund_id).'"');
                }
            }

            $id_order = Order::getOrderByCartId($id_card);
            $order = new Order($id_order);
            $state = Db::getInstance()->getValue('SELECT `result` FROM '._DB_PREFIX_.'stripe_payment WHERE `id_stripe` = "'.pSQL($refund_id).'"');

            if ($state == 2) {
                /* Refund State */
                $order->setCurrentState(7);
            } else if ($state == 3) {
                /* Partial Refund State */
                $order->setCurrentState(Configuration::get(self::_PS_STRIPE_.'partial_refund_state'));
            }
            $this->success['refund_success'] = $this->l('Refunds processed successfully');
        } else {
            $this->errors['cred'] = $this->l('Invalid Stripe credentials, please check your configuration.');
        }
    }

    public function isZeroDecimalCurrency($currency)
    {
        // @see: https://support.stripe.com/questions/which-zero-decimal-currencies-does-stripe-support
        $zeroDecimalCurrencies = array(
            'BIF',
            'CLP',
            'DJF',
            'GNF',
            'JPY',
            'KMF',
            'KRW',
            'MGA',
            'PYG',
            'RWF',
            'VND',
            'VUV',
            'XAF',
            'XOF',
            'XPF'
        );
        return in_array($currency, $zeroDecimalCurrencies);
    }

    public function chargev2(array $params)
    {
        if (!$this->retrieveAccount($this->getSecretKey(), '', 1)) {
            die(Tools::jsonEncode(array('code' => '0', 'msg' => $this->l('Invalid Stripe credentials, please check your configuration.'))));
        }

        try {
            // Create the charge on Stripe's servers - this will charge the user's card
            \Stripe\Stripe::setApiKey($this->getSecretKey());
            $charge = \Stripe\Charge::create(
                array(
                    "amount" => $params['amount'], // amount in cents, again
                    "currency" => $params['currency'],
                    "source" => $params['token'],
                )
            );
        } catch (\Stripe\Error\Card $e) {
            // The card has been declined

            $refund = $params['amount'];
            $this->addTentative($e->getMessage(), $params['cardHolderName'], (int)$this->context->cart->id, $params['type'], $params['amount'], $params['amount'], $params['currency'], 0);
            die(Tools::jsonEncode(array(
                'code' => '0',
                'msg' => $e->getMessage(),
            )));
        }
        if ($charge->status == 'succeeded' && $charge->object == 'charge' && $charge->id) {
            /* The payment was approved */
            $message = 'Stripe Transaction ID: '.$charge->id;
            try {
                $paid = $this->isZeroDecimalCurrency($params['currency']) ? $params['amount'] : $params['amount'] / 100;
                /* Add transaction on Prestashop back Office (Order) */
                $this->validateOrder(
                    (int)$this->context->cart->id,
                    (int)Configuration::get('PS_OS_PAYMENT'),
                    $paid,
                    $this->l('Payment by Stripe'),
                    $message,
                    array(),
                    null,
                    false,
                    $this->context->customer->secure_key
                );
            } catch (PrestaShopException $e) {
                $this->_error[] = (string)$e->getMessage();
            }

            /* Add transaction on database */
            $this->addTentative(
                $charge->id,
                $charge->source->name,
                (int)$this->context->cart->id,
                $charge->source->brand,
                $charge->amount,
                0,
                $charge->currency,
                1
            );
            $id_order = Order::getOrderByCartId($this->context->cart->id);
            /* Ajax redirection Order Confirmation */
            die(Tools::jsonEncode(array(
                'chargeObject' => $charge,
                'code' => '1',
                'url' => __PS_BASE_URI__.'index.php?controller=order-confirmation&id_cart='.(int)$this->context->cart->id.'&id_module='.(int)$this->id.'&id_order='.(int)$id_order.'&key='.$this->context->customer->secure_key,
            )));
        } else {
            /* The payment was declined */
            /* Add transaction on database */
            $this->addTentative(
                $charge->id,
                $charge->source->name,
                (int)$this->context->cart->id,
                $charge->source->brand,
                $charge->amount,
                0,
                $charge->currency,
                0
            );
            die(Tools::jsonEncode(array(
                'code' => '0', 
                'msg' => $this->l('Payment declined. Unknown error, please use another card or contact us.'),
            )));
        }
    }

    /*
     ** @Method: addTentative
     ** @description: Add Payment on Database
     **
     ** @return: (none)
     */
    private function addTentative($id_stripe, $name, $id_cart = 0, $type, $amount, $refund, $currency, $result)
    {
        if ($id_cart == 0) {
            $id_cart = (int)$this->context->cart->id;
        }

        if ($type == 'American Express') {
            $type = 'amex';
        } else if ($type == 'Diners Club') {
            $type = 'diners';
        }

        // @see: https://support.stripe.com/questions/which-zero-decimal-currencies-does-stripe-support
        $zeroDecimalCurrencies = array(
            'BIF',
            'CLP',
            'DJF',
            'GNF',
            'JPY',
            'KMF',
            'KRW',
            'MGA',
            'PYG',
            'RWF',
            'VND',
            'VUV',
            'XAF',
            'XOF',
            'XPF'
        );

        if (!in_array($currency, $zeroDecimalCurrencies)) {
            $amount /= 100;
            $refund /= 100;
        }

        /* Add request on Database */
        Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_
            .'stripe_payment (id_stripe, name, id_cart, type, amount, refund, currency, result, date_add)
            VALUES ("'.pSQL($id_stripe).'", "'.pSQL($name).'", \''.(int)$id_cart.'\', "'.pSQL(Tools::strtolower($type)).'", "'
            .pSQL($amount).'", "'.pSQL($refund).'", "'.pSQL(Tools::strtolower($currency)).'", '.(int)$result.', NOW())');
    }

    /*
     ** Hook Order Confirmation
     */
    public function hookOrderConfirmation($params)
    {
        $this->context->smarty->assign('stripe_order_reference', pSQL($params['objOrder']->reference));
        if ($params['objOrder']->module == $this->name) {
            return $this->display(__FILE__, 'views/templates/front/order-confirmation.tpl');
        }
    }

    public function hookHeader()
    {
        $opcEnabled = Configuration::get('PS_ORDER_PROCESS_TYPE');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
        $this->context->controller->addJs('https://js.stripe.com/v2/');
        // Prevent jQuery bindings if One Page Checkout is enabled. The DOM isn't fully available yet.
        if (!$opcEnabled) {
            $this->context->controller->addJs($this->_path . 'views/js/front.js');
        }
    }

    /*
     ** Hook Stripe Payment
     */
    public function hookPayment()
    {
        if (Configuration::get('PS_SSL_ENABLED')) {
            $this->context->smarty->assign(
                array(
                    'publishableKey' => $this->getPublishableKey(),
                    'mode' => Configuration::get(self::_PS_STRIPE_.'mode'),
                    'onePageCheckoutEnabled' => Configuration::get('PS_ORDER_PROCESS_TYPE'),
                )
            );

            $html = '';
            $html .= '<script>var mode = "'.Configuration::get(self::_PS_STRIPE_.'mode').'";</script>';
            $html .= $this->display(__FILE__, 'views/templates/hook/payment.tpl');

            return $html;
        }
    }

    /*
     ** Display Form
     */
    public function displayForm()
    {
        $fields_form = array();
        $fields_value = array();
        $type = 'switch';

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $type = 'radio';
        }

        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Stripe log in'),
            ),
            'input' => array(
                array(
                    'type' => $type,
                    'label' => $this->l('Test mode'),
                    'name' => self::_PS_STRIPE_.'mode',
                    'desc' => $this->l('You can manage your API keys from your ')
                    .'<a href="https://dashboard.stripe.com/account" target="_blank">'.$this->l('account').'</a>',
                        'size' => 50,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Stripe Secrey Key'),
                        'name' => self::_PS_STRIPE_.'key',
                        'size' => 20,
                        'id' => 'secret_key',
                        'class' => 'fixed-width-xxl',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Stripe Publishable Key'),
                        'name' => self::_PS_STRIPE_.'publishable',
                        'id' => 'public_key',
                        'class' => 'fixed-width-xxl',
                        'size' => 20,
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Stripe Test Secrey Key'),
                        'name' => self::_PS_STRIPE_.'test_key',
                        'id' => 'test_secret_key',
                        'size' => 20,
                        'class' => 'fixed-width-xxl',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Stripe Test Publishable Key'),
                        'name' => self::_PS_STRIPE_.'test_publishable',
                        'desc' => $this->l('Please note that if the module is in test mode, you are able to click any of the credit card buttons (VISA, MasterCard, etc.) ').'<br/>'
                        .$this->l('in the payment page to generate a sample credit card number for testing purposes.'),
                            'id' => 'test_public_key',
                            'class' => 'fixed-width-xxl',
                            'size' => 20,
                            'required' => true
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Save'),
                        'class' => 'btn btn-default pull-right button',
                    ),
                );

        $submit_action = 'submit_login';
        $fields_value = array_merge($fields_value, array(
            self::_PS_STRIPE_.'mode' => Configuration::get(self::_PS_STRIPE_.'mode'),
            self::_PS_STRIPE_.'key' => Configuration::get(self::_PS_STRIPE_.'key'),
            self::_PS_STRIPE_.'publishable' => Configuration::get(self::_PS_STRIPE_.'publishable'),
            self::_PS_STRIPE_.'test_key' => Configuration::get(self::_PS_STRIPE_.'test_key'),
            self::_PS_STRIPE_.'test_publishable' => Configuration::get(self::_PS_STRIPE_.'test_publishable'),
        ));

        return $this->renderGenericForm($fields_form, $fields_value, $this->getSectionShape(), $submit_action);
    }

    /*
     ** Display All Stripe transactions
     */
    public function displayTransaction($refresh = 0)
    {
        $this->getSectionShape();
        $orders = Db::getInstance()->ExecuteS('SELECT *	FROM '._DB_PREFIX_.'stripe_payment ORDER BY date_add DESC');
        $tenta = array();
        $html = '';

        foreach ($orders as $order) {
            if ($order['result'] == 0) {
                $result = 'n';
            } else if ($order['result'] == 1) {
                $result = '';
            } else if ($order['result'] == 2) {
                $result = 2;
            } else {
                $result = 3;
            }

            $refund = Tools::safeOutput($order['amount']) - Tools::safeOutput($order['refund']);
            array_push($tenta, array(
                'date' => Tools::safeOutput($order['date_add']),
                'last_digits' => Tools::safeOutput($order['last4']),
                'type' => Tools::strtolower($order['type']),
                'amount' => Tools::safeOutput($order['amount']),
                'currency' => Tools::safeOutput(Tools::strtoupper($order['currency'])),
                'refund' => $refund,
                'id_stripe' => Tools::safeOutput($order['id_stripe']),
                'name' => Tools::safeOutput($order['name']),
                'result' => $result
            ));
        }

        $this->context->smarty->assign('tenta', $tenta);
        if ($refresh == 0) {
            $html = '<div class="col-lg-2" style="float:right"><a class="close refresh"><i class="process-icon-refresh" style="font-size:1em"></i></a></div>';
            $html .= '<script>var validate = "'.$this->_path.'";</script>';
        }
        $html .= $this->display($this->_path, 'views/templates/admin/transaction.tpl');

        return $html;
    }

    /*
     ** Display Submit form for Refund
     */
    public function displayRefundForm()
    {
        $output = '';

        $fields_form = array();
        $fields_value = array();

        $fields_form1 = array();
        $fields_value1 = array();

        $fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('Choose an Order you want to Refund'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Stripe Payment ID'),
                    'desc' => '<strong>'.$this->l('To process a refund, input the order ID below. The order ID can be located in the Transaction page.').'</strong>',
                    'name' => self::_PS_STRIPE_.'refund_id',
                    'class' => 'fixed-width-xxl',
                    'required' => true
                ),
            ),
            'submit' => array(
                'title' => $this->l('Request Refund'),
                'class' => 'btn btn-default pull-right button',
            ),
        );
        $this->refund = 1;
        if ($this->refund)
        {
            $fields_form1[1]['form'] = array(
                'legend' => array(
                    'title' => $this->l('Choose an Order you want to Refund'),
                ),
                'input' => array(
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Choose a refund'),
                        'name' => self::_PS_STRIPE_.'refund_mode',
                        'size' => 50,
                        'values' => array(
                            array(
                                'id' => 'active_on_refund',
                                'value' => 1,
                                'label' => $this->l('Total Refund')
                            ),
                            array(
                                'id' => 'active_off_refund',
                                'value' => 0,
                                'label' => $this->l('Partial Refund')
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Amount'),
                        'desc' => $this->l('Please, enter an amount your want to refund'),
                        'name' => self::_PS_STRIPE_.'refund_amount',
                        'size' => 20,
                        'id' => 'refund_amount',
                        'class' => 'fixed-width-sm',
                        'required' => true
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Submit'),
                    'name' => 'submit_refund_action',
                    'class' => 'btn btn-default pull-right button',
                ),
            );
        }

        $submit_action = 'submit_refund_id';
        $fields_value = array_merge($fields_value1, array(
            self::_PS_STRIPE_.'refund_id' => Configuration::get(self::_PS_STRIPE_.'refund_id'),
        ));

        $submit_action1 = 'submit_refund_action';
        $fields_value1 = array_merge($fields_value1, array(
            self::_PS_STRIPE_.'refund_mode' => Configuration::get(self::_PS_STRIPE_.'refund_mode'),
            self::_PS_STRIPE_.'refund_amount' => Configuration::get(self::_PS_STRIPE_.'refund_amount'),
        ));

        $output .= $this->renderGenericForm($fields_form, $fields_value, $this->getSectionShape(), $submit_action);

        if ($this->refund) {
            $refund_id = Tools::getValue(self::_PS_STRIPE_.'refund_id');
            $orders = Db::getInstance()->ExecuteS('SELECT *	FROM '._DB_PREFIX_.'stripe_payment WHERE `id_stripe` = "'.pSQL($refund_id).'"');

            $tenta = array();

            foreach ($orders as $order) {
                if ($order['result'] == 0) {
                    $result = 'n';
                } else if ($order['result'] == 1) {
                    $result = '';
                } else if ($order['result'] == 2) {
                    $result = 2;
                } else {
                    $result = 3;
                }

                $refund = Tools::safeOutput($order['amount']) - Tools::safeOutput($order['refund']);
                array_push($tenta, array(
                    'date' => Tools::safeOutput($order['date_add']),
                    'last_digits' => Tools::safeOutput($order['last4']),
                    'type' => Tools::strtolower($order['type']),
                    'amount' => Tools::safeOutput($order['amount']),
                    'currency' => Tools::safeOutput(Tools::strtoupper($order['currency'])),
                    'refund' => $refund,
                    'id_stripe' => Tools::safeOutput($order['id_stripe']),
                    'name' => Tools::safeOutput($order['name']),
                    'result' => $result
                ));
            }

            $this->context->smarty->assign('tenta', $tenta);
            $output .= $this->display($this->_path, 'views/templates/admin/transaction.tpl');
            $output .= $this->renderGenericForm($fields_form1, $fields_value1, "section-shape-3", $submit_action1);
        }

        return $output;
    }

    /*
     ** Generate Contact
     */
    public function displayFaq()
    {
        return $this->display($this->_path, 'views/templates/admin/faq.tpl');
    }

    /*
     ** Generate Contact
     */
    public function displayContact()
    {
        $this->getSectionShape();
        return $this->display($this->_path, 'views/templates/admin/contact.tpl');
    }

    /*
     ** @Method: displaySomething
     ** @description: just display something (it's something)
     **
     ** @arg: (none)
     ** @return: (none)
     */
    public function displaySomething()
    {
        $this->getSectionShape();
        return $this->display($this->_path, 'views/templates/admin/started.tpl');
    }

    /*
     ** @Method: generateList
     ** @description: generate select list with the given array
     **
     ** @arg: array $values, $key_id
     ** @return: (none)
     */
    public static function generateList(array $values, $identifier = 'mode')
    {
        $arr = array();

        foreach (array_values($values) as $key => $value) {
            array_push($arr, array($identifier => $key, 'name' => $value));
        }

        return $arr;
    }

    /*
     ** @Method: checkList
     ** @description: check if confkey exist in list
     **
     ** @arg: array $values, $key_id
     ** @return: (none)
     */
    public function checkList($key, array $arr)
    {
        if (($id = (int)Configuration::get($this->c($key))) !== false && isset($arr[(int)$id])) {
            return $arr[(int)$id];
        }
        return false;
    }

    /*
     ** @Method: getSectionShape
     ** @description: generate a new description
     **
     ** @arg:
     ** @return: (none)
     */
    public function generateDescription(array $ar = array())
    {
        if (!empty($ar)) {
            return '<p>'.implode('</p><p>', $ar).'</p>';
        }
        return '';
    }

    /*
     ** @Method: getSectionShape
     ** @description: get section shape fragment
     **
     ** @arg:
     ** @return: (none)
     */
    private function getSectionShape()
    {
        return 'section-shape-'.(int)$this->section_shape++;
    }

    /*
     ** @Method: renderGenericForm
     ** @description: render generic form for prestashop
     **
     ** @arg: $fields_form, $fields_value, $submit = false, array $tpls_vars = array()
     ** @return: (none)
     */
    public function renderGenericForm($fields_form, $fields_value = array(), $fragment = false, $submit = false, array $tpl_vars = array())
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        if ($fragment !== false) {
            $helper->token .= '#'.$fragment;
        }

        if ($submit) {
            $helper->submit_action = $submit;
        }

        $helper->tpl_vars = array_merge(array(
            'fields_value' => $fields_value,
            'id_language' => $this->context->language->id,
            'back_url' => $this->context->link->getAdminLink('AdminModules')
            .'&configure='.$this->name
            .'&tab_module='.$this->tab
            .'&module_name='.$this->name.($fragment !== false ? '#'.$fragment : '')
        ), $tpl_vars);

        return $helper->generateForm($fields_form);
    }

    /*
     ** Render helper
     */
    public function renderGenericOptions($fields_form, $fragment = false, array $tpl_vars = array())
    {
        $helper = new HelperOptions($this);
        $helper->toolbar_scroll = true;
        $helper->toolbar_btn = array('save' => array(
            'href' => '',
            'desc' => $this->l('Save')
        ));

        $helper->id = $this->id;
        $helper->module = $this;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        if ($fragment !== false) {
            $helper->token .= '#'.$fragment;
        }

        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->title = $this->displayName;
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = $default_lang;
        $helper->tpl_vars = array_merge(array(
            'submit_action' => 'index.php',
            'id_language' => $this->context->language->id,
            'back_url' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name
        ), $tpl_vars);

        return $helper->generateOptions($fields_form);
    }

    public function getSecretKey()
    {
        if (Configuration::get(self::_PS_STRIPE_.'mode')) {
            return Configuration::get(self::_PS_STRIPE_.'test_key');
        } else {
            return Configuration::get(self::_PS_STRIPE_.'key');
        }
    }

    public function getPublishableKey()
    {
        if (Configuration::get(self::_PS_STRIPE_.'mode')) {
            return Configuration::get(self::_PS_STRIPE_.'test_publishable');
        } else {
            return Configuration::get(self::_PS_STRIPE_.'publishable');
        }
    }
}
