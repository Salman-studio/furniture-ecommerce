/* furniture/assets/js/payment.js
   Simple client-side validations for payment page and communication helpers.
   IMPORTANT: Real payment processing (Stripe/PayPal) must be performed using secure server-side endpoints.
*/

(function($){
  'use strict';

  // Basic card number Luhn check (client-side only)
  function luhnCheck(cardNumber){
    var s = 0, doubleDigit = false;
    var digits = cardNumber.replace(/\D/g,'').split('').reverse();
    for(var i=0;i<digits.length;i++){
      var d = parseInt(digits[i],10);
      if(doubleDigit){
        d = d*2;
        if(d>9) d -= 9;
      }
      s += d;
      doubleDigit = !doubleDigit;
    }
    return (s % 10) === 0;
  }

  $(function(){
    $('#paymentForm').on('submit', function(e){
      // Prevent default post — examples only. In real setup, you will POST to payment.php
      var card = $('#cardNumber').val() || '';
      if(!luhnCheck(card)){
        e.preventDefault();
        alert('Please enter a valid card number.');
        $('#cardNumber').focus();
        return false;
      }
      // You may disable button to prevent duplicate submissions
      $('#payBtn').prop('disabled', true).text('Processing...');
      // Let form submit normally to server-side handler (payment.php)
    });
  });

  window.paymentHelpers = { luhnCheck };
})(jQuery);
