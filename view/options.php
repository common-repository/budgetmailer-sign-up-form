<?php
namespace BudgetMailer\Wordpress;
?>
<div class="wrap">
    <h2><?php L10n::__( 'BudgetMailer API Settings' ) ?></h2>
    <form method="post" action="options.php">
        <?php 
        settings_fields( Settings::GROUP );
        do_settings_sections( Settings::GROUP );
        submit_button();
        settings_errors( Settings::GROUP ); // make sure the errors are displayed
        ?>
    </form>
</div>
