<?php /* deny direct access: */ if (!defined('ABSPATH')) exit; ?>

<style>
#phpinfo pre {  display:block; width: 740; height: 100%; overflow: hidden; }
</style>
<div class="wrap" id="phpinfo">
  <?php screen_icon() ?>
  <h2>PHP Info</h2>
  <pre><?php echo strip_tags(str_replace('</td>', '&nbsp;', $phpinfo)) ?></pre>
</div>