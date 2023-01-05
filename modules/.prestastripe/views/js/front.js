/**
* 2007-2015 PrestaShop
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2015 PrestaShop SA
* @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
* International Registered Trademark & Property of PrestaShop SA
*/

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
	if (typeof mode != 'undefined' && mode == 1) {
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
