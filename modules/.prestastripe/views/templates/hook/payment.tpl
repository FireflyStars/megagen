{*
* 2007-2015 PrestaShop
*

* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*	@author PrestaShop SA <contact@prestashop.com>
*	@copyright	2007-2015 PrestaShop SA
*	@license		http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
*	International Registered Trademark & Property of PrestaShop SA
*}
<div class="row">
	<div class="col-xs-12 col-md-6">
		<div class="payment_module" style="border: 1px solid #d6d4d4; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; padding-left: 15px; padding-right: 15px; background: #fbfbfb;">
			<h3 class="stripe_title"><img alt="" src="{$module_dir|escape:html:'UTF-8'}views/img/secure-icon.png" />{l s='Pay by credit card with ' mod='prestastripe'}<img alt="" src="{$module_dir|escape:html:'UTF-8'}views/img/logo.png" width="110px"/></h3>
			{* Classic Credit card form *}
			<div id="stripe-ajax-loader"><img src="{$module_dir|escape:html:'UTF-8'}views/img/ajax-loader.gif" alt="" />&nbsp; {l s='Transaction in progress, please wait.' mod='prestastripe'}</div>
			<form action="#" id="stripe-payment-form"{if isset($stripe_save_tokens_ask) && $stripe_save_tokens_ask && isset($stripe_credit_card)} style="display: none;"{/if}>
				<div class="stripe-payment-errors">{if isset($smarty.get.stripe_error)}{$smarty.get.stripe_error|escape:'html'}{/if}</div><a name="stripe_error" style="display:none"></a>
        <input type="hidden" id="stripe-publishable-key" value="{$publishableKey}"/>
				<label>{l s='Card Number' mod='prestastripe'}</label><br />
				<input type="text" size="20" autocomplete="off" class="stripe-card-number" data-stripe="number" />
				<br />
				<label>{l s='Cardholder Name' mod='prestastripe'}</label><br />
        <input type="text" style="width: 200px;display:block;" autocomplete="off" class="stripe-name" data-stripe="name"/>
				<div class="block-left">
					<label>{l s='Card Type' mod='prestastripe'}</label><br />
					{if $mode == 1}
						<p>{l s='Click on any of the credit card buttons below in order to fill automatically the required fields to submit a test payment.' mod='prestastripe'}</p>
					{/if}
					<img class="cc-icon disable"  id="visa"       rel="Visa"       alt="" src="{$module_dir|escape:html:'UTF-8'}views/img/cc-visa.png" />
					<img class="cc-icon disable"  id="mastercard" rel="MasterCard" alt="" src="{$module_dir|escape:html:'UTF-8'}views/img/cc-mastercard.png" />
					<img class="cc-icon disable"  id="discover"   rel="Discover"   alt="" src="{$module_dir|escape:html:'UTF-8'}views/img/cc-discover.png" />
					<img class="cc-icon disable"  id="amex"       rel="Amex"       alt="" src="{$module_dir|escape:html:'UTF-8'}views/img/cc-amex.png" />
					<img class="cc-icon disable"  id="jcb"        rel="Jcb"        alt="" src="{$module_dir|escape:html:'UTF-8'}views/img/cc-jcb.png" />
					<img class="cc-icon disable"  id="diners"     rel="Diners"     alt="" src="{$module_dir|escape:html:'UTF-8'}views/img/cc-diners.png" />
				</div>
				<div class="block-left">
					<label>{l s='CVC' mod='prestastripe'}</label><br />
					<input type="text" size="4" autocomplete="off" data-stripe="cvc" class="stripe-card-cvc" />
					<a href="javascript:void(0)" class="stripe-card-cvc-info" style="border: none;">
						{l s='What\'s this?' mod='prestastripe'}
						<div class="cvc-info">
						{l s='The CVC (Card Validation Code) is a 3 or 4 digit code on the reverse side of Visa, MasterCard and Discover cards and on the front of American Express cards.' mod='prestastripe'}
						</div>
					</a>
				</div>
				<div class="clear"></div>
				<label>{l s='Expiration (MM/YYYY)' mod='prestastripe'}</label><br />
				<select id="month" name="month" data-stripe="exp-month" class="stripe-card-expiry-month">
					<option value="01">{l s='January' mod='prestastripe'}</option>
					<option value="02">{l s='February' mod='prestastripe'}</option>
					<option value="03">{l s='March' mod='prestastripe'}</option>
					<option value="04">{l s='April' mod='prestastripe'}</option>
					<option value="05">{l s='May' mod='prestastripe'}</option>
					<option value="06">{l s='June' mod='prestastripe'}</option>
					<option value="07">{l s='July' mod='prestastripe'}</option>
					<option value="08">{l s='August' mod='prestastripe'}</option>
					<option value="09">{l s='September' mod='prestastripe'}</option>
					<option value="10">{l s='October' mod='prestastripe'}</option>
					<option value="11">{l s='November' mod='prestastripe'}</option>
					<option value="12">{l s='December' mod='prestastripe'}</option>
				</select>
				<span> / </span>
				<select id="year" name="year" data-stripe="exp-year" class="stripe-card-expiry-year">
				{for $n_pp_year={'Y'|date} to {'Y'|date}+9}
					<option value="{$n_pp_year|escape:html:'UTF-8'}">{$n_pp_year|escape:html:'UTF-8'}</option>
				{/for}
				</select>
				<br />
				<button type="submit" class="stripe-submit-button">{l s='Submit Payment' mod='prestastripe'}</button>
			</form>
			<div id="stripe-translations">
				<span id="stripe-wrong-cvc">{l s='Wrong CVC.' mod='prestastripe'}</span>
				<span id="stripe-wrong-expiry">{l s='Wrong Credit Card Expiry date.' mod='prestastripe'}</span>
				<span id="stripe-wrong-card">{l s='Wrong Credit Card number.' mod='prestastripe'}</span>
				<span id="stripe-please-fix">{l s='Please fix it and submit your payment again.' mod='prestastripe'}</span>
				<span id="stripe-card-del">{l s='Your Credit Card has been successfully deleted, please enter a new Credit Card:' mod='prestastripe'}</span>
				<span id="stripe-card-del-error">{l s='An error occured while trying to delete this Credit card. Please contact us.' mod='prestastripe'}</span>
			</div>
		</div>
	</div>
</div>

{if $onePageCheckoutEnabled == 1}
{literal}
<script type="text/javascript">
function lookupCardType(number)
{
  if (number.match(new RegExp('^4')) !== null) {
    return 'Visa';
  }
  if (number.match(new RegExp('^(34|37)')) !== null) {
    return 'Amex';
  }
  if (number.match(new RegExp('^5[1-5]')) !== null) {
    return 'MasterCard';
  }
  if (number.match(new RegExp('^6011')) !== null) {
    return 'Discover';
  }
  if (number.match(new RegExp('^(?:2131|1800|35[0-9]{3})[0-9]{3,}')) !== null) {
    return 'Jcb';
  }
  if (number.match(new RegExp('^3(?:0[0-5]|[68][0-9])[0-9]{4,}')) !== null) {
    return 'Diners';
  }
}

$(document).ready(function() {
  // Get Stripe public key
  var StripePubKey = $('#stripe-publishable-key').val();
  Stripe.setPublishableKey(StripePubKey);

  $('#stripe-payment-form').submit(function (event) {
    /* Disable the submit button to prevent repeated clicks */
    $('.stripe-submit-button').attr('disabled', 'disabled'); 
    $('.stripe-payment-errors').hide();
    $('#stripe-payment-form').hide();
    $('#stripe-ajax-loader').show();
    var $form = $(this);
    Stripe.card.createToken($form, function (status, response) {
      var $form = $('#stripe-payment-form');
      if (response.error) {
        // Show error on the form
        $('#stripe-ajax-loader').hide();
        $('#stripe-payment-form').show();
        $('.stripe-submit-button').removeAttr('disabled');
        $form.find('.stripe-payment-errors').text(response.error.message).fadeIn(1000);
      } else {
        // Send the token back to the server so that it can charge the card
        $.ajax({
          type: 'POST',
          dataType: 'json',
          url: baseDir + 'modules/prestastripe/ajax.php',
          data: {
            stripeToken: response.id,
            cardType: lookupCardType($('.stripe-card-number').val()),
            cardHolderName: $('.stripe-name').val(),
          },
          success: function(data) {
            if (data.code == '1') {
              // Charge ok : redirect the customer to order confirmation page
              location.replace(data.url);
            } else {
              //  Charge ko
              $('#stripe-ajax-loader').hide();
              $('#stripe-payment-form').show();
              $('.stripe-payment-errors').show();
              $('.stripe-payment-errors').text(data.msg).fadeIn(1000);
              $('.stripe-submit-button').removeAttr('disabled');
            }
          },
          error: function(err) {
            // AJAX ko
            $('#stripe-ajax-loader').hide();
            $('#stripe-payment-form').show();
            $('.stripe-payment-errors').show();
            $('.stripe-payment-errors').text('An error occured during the request. Please contact us').fadeIn(1000);
            $('.stripe-submit-button').removeAttr('disabled');
            console.log(err);
          }
        });
      }
    });
    return false;
  });

  /* Cards mode */
  var cards_numbers = {
    "visa" : "4242424242424242",
    "mastercard" : "5555555555554444",
    "discover" : "378282246310005",
    "amex" : "6011111111111117",
    "jcb" : "30569309025904" ,
    "diners" : "3530111333300000"
  };

  /* Test Mode All Card enable */
  var cards = ["visa", "mastercard", "discover", "amex", "jcb", "diners"];
  if (mode == 1) {
    $.each(cards, function(data) {
      $('#' + cards[data]).addClass('enable');
    });

    /* Auto Fill in Test Mode */
    $.each(cards_numbers, function(key, value) {
      $('#' + key).click(function()  {
        $('.stripe-card-number').val(value);
        $('.stripe-name').val('Joe Smith');
        $('.stripe-card-cvc').val(131);
        $('.stripe-card-expiry-year').val('2023');
      });
    });

  }

  /* Determine the Credit Card Type */
  $('.stripe-card-number').keyup(function () {
    if ($(this).val().length >= 2) {
      stripe_card_type = lookupCardType($('.stripe-card-number').val());
      $('.cc-icon').removeClass('enable');
      $('.cc-icon').removeClass('disable');
      $('.cc-icon').each(function() {
        if ($(this).attr('rel') == stripe_card_type) {
          $(this).addClass('enable');
        } else {
          $(this).addClass('disable');
        }
      });
    } else {
      $('.cc-icon').removeClass('enable');
      $('.cc-icon:not(.disable)').addClass('disable');
    }
  });

  // TODO : Seems useless ...
  $('#stripe-payment-form-cc').submit(function (event) {
    $('.stripe-payment-errors').hide();
    $('#stripe-payment-form-cc').hide();
    $('#stripe-ajax-loader').show();
    $('.stripe-submit-button-cc').attr('disabled', 'disabled'); /* Disable the submit button to prevent repeated clicks */
  });

  /* Catch callback errors */
  if ($('.stripe-payment-errors').text()) {
    $('.stripe-payment-errors').fadeIn(1000);
  }

  $('#stripe-payment-form input').keypress(function () {
    $('.stripe-payment-errors').fadeOut(500); 
  });
});
</script>
{/literal}
{/if}
