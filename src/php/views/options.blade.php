<form method="post" action="options.php">
    {{ settings_fields( 'azure-uploads-options' ) }}
    {{ do_settings_sections( 'azure-uploads-options' ) }}
    {{ submit_button() }}
</form>
