<script type="text/javascript">
  
  if (!jQuery) window.onload = conpay_add_button;
  else jQuery(document).ready(conpay_add_button);
  
  function conpay_add_button()
  {
    try
    {
      window.conpay.init
      (
        '<?php echo $callback_url; ?>',
        {
          'className': '<?php echo $settings['button_class_name']; ?>',
          'tagName': '<?php echo $settings['button_tag_name']; ?>',
          'text': '<?php echo html_entity_decode($settings['button_text']); ?>',
        }, {}
        <?php // echo json_encode($products); ?>
      );
      <?php foreach ($products as $item) { ?>
      window.conpay.addButton(<?php echo json_encode($item); ?>, '<?php echo $settings['button_container_id']; ?>-<?php echo $item['id']; ?>');
      <?php } ?>
    } catch(e) {}
  }
  
</script>