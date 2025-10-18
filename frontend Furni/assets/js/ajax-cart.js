/* furniture/assets/js/ajax-cart.js
   AJAX cart operations: add to cart, update quantity, remove item.
   Requires: jQuery and server-side endpoints (e.g., /cart.php handling POST actions).
   Comments explain how to connect with backend.
*/

(function($){
  'use strict';

  // CSRF token retrieval helper: expecting a meta tag <meta name="csrf-token" content="...">
  function csrfToken(){
    return $('meta[name="csrf-token"]').attr('content') || '';
  }

  // Add product to cart via AJAX
  // data: {product_id, qty}
  function addToCart(data, onSuccess, onError){
    $.ajax({
      url: 'cart.php',
      method: 'POST',
      dataType: 'json',
      data: $.extend({action: 'add'}, data, {csrf_token: csrfToken()}),
      success: function(res){
        if(res && res.success){
          if(typeof onSuccess === 'function') onSuccess(res);
        } else {
          if(typeof onError === 'function') onError(res);
          else alert(res.message || 'Failed to add to cart');
        }
      },
      error: function(xhr){
        if(typeof onError === 'function') onError(xhr);
        else alert('Network error while adding to cart');
      }
    });
  }

  // Update item quantity
  function updateCartItem(itemId, qty, onDone){
    $.ajax({
      url: 'cart.php',
      method: 'POST',
      dataType: 'json',
      data: {action:'update', item_id:itemId, qty:qty, csrf_token: csrfToken()},
      success: function(res){ if(typeof onDone === 'function') onDone(res); },
      error: function(){ if(typeof onDone === 'function') onDone({success:false}); }
    });
  }

  // Remove item from cart
  function removeCartItem(itemId, onDone){
    $.ajax({
      url: 'cart.php',
      method: 'POST',
      dataType: 'json',
      data: {action:'remove', item_id:itemId, csrf_token: csrfToken()},
      success: function(res){ if(typeof onDone === 'function') onDone(res); },
      error: function(){ if(typeof onDone === 'function') onDone({success:false}); }
    });
  }

  // Expose small API
  window.ajaxCart = { addToCart, updateCartItem, removeCartItem };

})(jQuery);
