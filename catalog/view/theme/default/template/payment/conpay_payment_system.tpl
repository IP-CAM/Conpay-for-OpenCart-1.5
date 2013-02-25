<!--<script type="text/javascript" src="http://www.conpay.ru/public/api/btn.1.6.min.js"></script>-->
<p><?php echo $order_confirmed; ?></p>
<div id="conpay-btn-container"></div>
<script type="text/javascript">

  conpay_load_api
  (
    "http://www.conpay.ru/public/api/btn.1.6.min.js",
    function() { return (typeof window.conpay.init == 'function'); },
    conpay_add_button
  );

  function conpay_load_api(src, test, callback) {
    var s = document.createElement('script');
    s.src = src;
    document.body.appendChild(s);
    
    var callbackTimer = setInterval(function() {
      var call = false;
      try {
        call = test.call();
      } catch (e) {}
      
      if (call) {
        clearInterval(callbackTimer);
        callback.call();
      }
    }, 100);
  }
  
  function conpay_add_button()
  {
    if (jQuery) jQuery('#cart').load('index.php?route=module/cart #cart > *');
    try
    {
      window.conpay.init
      (
        '<?php echo $callback_url; ?>',
        {
          'className': '<?php echo $settings['button_class_name']; ?>',
          'tagName': '<?php echo $settings['button_tag_name']; ?>',
          'text': '<?php echo html_entity_decode($button_text); ?>',
        }, {}
      );
      window.conpay.addButton(<?php echo json_encode($products); ?>, 'conpay-btn-container');
    } catch(e) {}
  }
  
</script>
