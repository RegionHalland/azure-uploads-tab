<?php /**/ ?>
  <div>
  <form method="post" action="options.php">
    <?php settings_fields( 'azure-uploads-options' ); ?>
    <?php do_settings_sections( 'azure-uploads-options' ); ?>
    <?php submit_button(); ?>
  </form>
  </div>

