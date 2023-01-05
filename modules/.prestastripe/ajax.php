<?php
if (!file_exists(dirname(__FILE__).'/../../config/config.inc.php')) {
    die('ko');
}

include dirname(__FILE__).'/../../config/config.inc.php';
include dirname(__FILE__).'/../../init.php';
include dirname(__FILE__).'/prestastripe.php';

$stripe = Module::getInstanceByName('prestastripe');

if ($stripe && $stripe->active) {
    $context = Context::getContext();

    /* Loading current billing address from PrestaShop */
    if (!isset($context->cart->id)
        || empty($context->cart->id)
        || !isset($context->cart->id_address_invoice)
        || empty($context->cart->id_address_invoice)
    ) {
        die('No active shopping cart');
    }

    $amount = $context->cart->getOrderTotal();

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

    if (!in_array($context->currency->iso_code, $zeroDecimalCurrencies)) {
        $amount *= 100;
    }

    $params = array(
        'token' => Tools::getValue('stripeToken'),
        'amount' => $amount,
        'currency' => $context->currency->iso_code,
        'cardHolderName' => Tools::getValue('cardHolderName'),
        'type' => Tools::getValue('cardType'),
    );

    if (isset($params['token']) && !empty($params['token'])) {
        $stripe->chargev2($params);
    } else {
        die('ko');
    }
}
