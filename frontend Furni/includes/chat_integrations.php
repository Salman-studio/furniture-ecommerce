<?php
// furniture/includes/chat_integrations.php
// Include snippets to integrate chat providers (WhatsApp, Messenger) if required.
// Keep minimal: just show icons linking to contact channels.

?>
<div class="chat-widgets position-fixed" style="right:12px;bottom:18px;z-index:1100">
  <!-- Example quick links -->
  <a href="#" class="open-whatsapp d-block mb-2" data-phone="1234567890" title="WhatsApp">
    <img src="assets/images/icons/whatsapp.png" alt="WhatsApp" style="width:48px;height:48px">
  </a>
  <a href="#" class="open-telegram d-block mb-2" data-user="YourChannel" title="Telegram">
    <img src="assets/images/icons/telegram.png" alt="Telegram" style="width:48px;height:48px">
  </a>
</div>
