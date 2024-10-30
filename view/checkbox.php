<?php
namespace BudgetMailer\Wordpress;
?>

<h3><?php L10n::_e( $title ) ?></h3>
<p>
    <label>
        <input class="budgetmailer-subscribe" name="budgetmailer_subscribe" type="checkbox" value="1"<?php echo $checked ?>/> 
        <?php print L10n::_e( $label ) ?>
    </label><br/>
</p>
