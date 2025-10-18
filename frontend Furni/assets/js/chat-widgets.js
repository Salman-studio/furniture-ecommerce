/* furniture/assets/js/chat-widgets.js
   Integrations for chat/contact quick-links (WhatsApp, Messenger, etc.)
   This file only handles client-side opening of channels. Actual chat widgets (FB, Intercom) should be
   loaded according to each provider's integration docs.
*/

(function($){
  'use strict';

  // Example: open WhatsApp chat
  function openWhatsApp(phone, text){
    var url = 'https://wa.me/' + encodeURIComponent(phone) + '?text=' + encodeURIComponent(text || '');
    window.open(url, '_blank');
  }

  // Example: open Telegram
  function openTelegram(username){
    var url = 'https://t.me/' + encodeURIComponent(username);
    window.open(url, '_blank');
  }

  // Attach click handlers to our icon buttons
  $(function(){
    $(document).on('click', '.open-whatsapp', function(e){
      e.preventDefault();
      var phone = $(this).data('phone') || '';
      openWhatsApp(phone, 'Hi, I have a question about a product.');
    });

    $(document).on('click', '.open-telegram', function(e){
      e.preventDefault();
      var user = $(this).data('user') || '';
      openTelegram(user);
    });
  });

  // Public API
  window.chatWidgets = { openWhatsApp, openTelegram };

})(jQuery);
