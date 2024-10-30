<?php
namespace BudgetMailer\Wordpress;
?>
<div class="budgetmailer-subscribe">
    <?php
        $i = rand(0,99);

        if ( $settings['title'] ) {
            print $args['before_title'] . apply_filters( 'widget_title', $settings['title'] ). $args['after_title'];
        }
        
        $fields = array();
    ?>
    
    <noscript><?php L10n::__( 'BudgetMailer Newsletter Subscription requires JavaScript.'); ?></noscript>

    <form action="<?php print admin_url( 'admin-ajax.php' ) ?>" class="comment-form budgetmailer-subscribe-form" id="budgetmailer-subscribe-form-<?php print $i ?>" method="post" novalidate="novalidate">
        <input class="budgetmailer-input" name="budgetmailer[post_id]" type="hidden" value="<?php echo $post_id ?>" data-name="post_id" />
        <input class="budgetmailer-input" name="budgetmailer[type]" type="hidden" value="<?php echo $type ?>" data-name="type" />
        <input class="budgetmailer-input" name="budgetmailer[widget_id]" type="hidden" value="<?php echo $widget_id ?>" data-name="widget_id" />
        
        <?php if ( $settings['html_before'] ): ?>
            <div class="budgetmailer-before"><?php print $settings['html_before'] ?></div>
        <?php endif; ?>
    
        <?php ob_start(); ?>
        <p class="budgetmailer-form-element budgetmailer-email">
            <label for="budgetmailer-email-<?php print $i ?>"><?php print $settings['email'] ?> <span class="required">*</span></label>
            <input class="budgetmailer-input budgetmailer-email-input validate-email" id="budgetmailer-email-<?php print $i ?>" name="budgetmailer[email]" required="required" data-name="email" type="email" />
        </p>
        <?php $fields['email'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['company_displayed'] ): ?>
        <p class="budgetmailer-form-element budgetmailer-company">
            <label for="budgetmailer-company-<?php print $i ?>"><?php print $settings['company_label'] ?><?php if ( $settings['company_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <input class="budgetmailer-input budgetmailer-company-input" id="budgetmailer-company-<?php print $i ?>" type="text" name="budgetmailer[company]"<?php if ( $settings['company_required'] ): ?> required="required"<?php endif ?> data-name="company" />
        </p>
        <?php endif; ?>
        <?php $fields['company_label'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['first_name_displayed'] ): ?>
        <p class="budgetmailer-form-element budgetmailer-first-name">
            <label for="budgetmailer-first-name-<?php print $i ?>"><?php print $settings['first_name_label'] ?><?php if ( $settings['first_name_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <input class="budgetmailer-input budgetmailer-first-name-input" id="budgetmailer-first-name-<?php print $i ?>" type="text" name="budgetmailer[first_name]"<?php if ( $settings['first_name_required'] ): ?> required="required"<?php endif ?> data-name="first_name" />
        </p>
        <?php endif; ?>
        <?php $fields['first_name_label'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['middle_name_displayed'] ): ?>
        <p class="budgetmailer-form-element budgetmailer-middle-name">
            <label for="budgetmailer-middle-name-<?php print $i ?>"><?php print $settings['middle_name_label'] ?><?php if ( $settings['middle_name_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <input class="budgetmailer-input budgetmailer-middle-name-input" id="budgetmailer-middle-name-<?php print $i ?>" type="text" name="budgetmailer[middle_name]"<?php if ( $settings['middle_name_required'] ): ?> required="required"<?php endif ?> data-name="middle_name" />
        </p>
        <?php endif; ?>
        <?php $fields['middle_name_label'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['last_name_displayed'] ): ?>
        <p class="budgetmailer-form-element budgetmailer-last-name">
            <label for="budgetmailer-last-name-<?php print $i ?>"><?php print $settings['last_name_label'] ?><?php if ( $settings['last_name_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <input class="budgetmailer-input budgetmailer-last-name-input" id="budgetmailer-last-name-<?php print $i ?>" type="text" name="budgetmailer[last_name]"<?php if ( $settings['last_name_required'] ): ?> required="required"<?php endif ?> data-name="last_name" />
        </p>
        <?php endif; ?>
        <?php $fields['last_name_label'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['sex_displayed'] ): ?>
        <p class="budgetmailer-form-element budgetmailer-sex">
            <label for="budgetmailer-last-name-<?php print $i ?>"><?php print $settings['sex_label'] ?><?php if ( $settings['sex_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <select class="budgetmailer-input budgetmailer-sex-input" data-name="sex" id="budgetmailer-sex"<?php if ( $settings['sex_required'] ): ?> required="required"<?php endif ?>>
                <option value=""><?php L10n::_e( 'Select Sex') ?></option>
                <option value="1"><?php L10n::_e( 'Male') ?></option>
                <option value="2"><?php L10n::_e( 'Female') ?></option>
            </select>
        </p>
        <?php endif; ?>
        <?php $fields['sex_label'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['telephone_displayed'] ): ?>
        <p class="budgetmailer-form-element budgetmailer-telephone">
            <label for="budgetmailer-telephone-<?php print $i ?>"><?php print $settings['telephone_label'] ?><?php if ( $settings['telephone_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <input class="budgetmailer-input budgetmailer-telephone-input" id="budgetmailer-telephone-<?php print $i ?>" type="text" name="budgetmailer[telephone]"<?php if ( $settings['telephone_required'] ): ?> required="required"<?php endif ?> data-name="telephone" />
        </p>
        <?php endif; ?>
        <?php $fields['telephone_label'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['mobile_displayed'] ): ?>
        <p class="budgetmailer-form-element budgetmailer-mobile">
            <label for="budgetmailer-mobile-<?php print $i ?>"><?php print $settings['mobile_label'] ?><?php if ( $settings['mobile_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <input class="budgetmailer-input budgetmailer-mobile-input" id="budgetmailer-mobile-<?php print $i ?>" type="text" name="budgetmailer[mobile]"<?php if ( $settings['mobile_required'] ): ?> required="required"<?php endif ?> data-name="mobile" />
        </p>
        <?php endif; ?>
        <?php $fields['mobile_label'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['address_displayed'] ): ?>
        <p class="budgetmailer-form-element budgetmailer-address">
            <label for="budgetmailer-address-<?php print $i ?>"><?php print $settings['address_label'] ?><?php if ( $settings['address_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <input class="budgetmailer-input budgetmailer-address-input" id="budgetmailer-address-<?php print $i ?>" type="text" name="budgetmailer[address]"<?php if ( $settings['address_required'] ): ?> required="required"<?php endif ?> data-name="address" />
        </p>
        <?php endif; ?>
        <?php $fields['address_label'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['zip_displayed'] ): ?>
        <p class="budgetmailer-form-element budgetmailer-zip">
            <label for="budgetmailer-zip-<?php print $i ?>"><?php print $settings['zip_label'] ?><?php if ( $settings['zip_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <input class="budgetmailer-input budgetmailer-zip-input" id="budgetmailer-zip-<?php print $i ?>" type="text" name="budgetmailer[zip]"<?php if ( $settings['zip_required'] ): ?> required="required"<?php endif ?> data-name="postalCode" />
        </p>
        <?php endif; ?>
        <?php $fields['zip_label'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['city_displayed'] ): ?>
        <p class="budgetmailer-form-element budgetmailer-city">
            <label for="budgetmailer-city-<?php print $i ?>"><?php print $settings['city_label'] ?><?php if ( $settings['city_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <input class="budgetmailer-input budgetmailer-city-input" id="budgetmailer-city-<?php print $i ?>" type="text" name="budgetmailer[city]"<?php if ( $settings['city_required'] ): ?> required="required"<?php endif ?> data-name="city" />
        </p>
        <?php endif; ?>
        <?php $fields['city_label'] = ob_get_clean(); ?>
        
        <?php ob_start(); ?>
        <?php if ( $settings['country_displayed'] ): ?>
        <p class="budgetmailer-form-element budgetmailer-country">
            <label for="budgetmailer-country-<?php print $i ?>"><?php print $settings['country_label'] ?><?php if ( $settings['country_required'] ): echo ' <span class="required">*</span>'; endif; ?></label>
            <?php Countries::select('budgetmailer-input budgetmailer-country-input', 'countryCode', 'budgetmailer-country', 'budgetmailer[country]', $settings['country_required']) ?>
        </p>
        <?php endif; ?>
        <?php $fields['country_label'] = ob_get_clean(); ?>

        <?php
            if ($sort) {
                uksort($fields, function($a, $b) use ($sort) {
                    $ak = array_search($a, $sort);
                    $bk = array_search($b, $sort);

                    return $ak - $bk;
                });
            }
            
            foreach($fields as $field) {
                print $field;
            }
        ?>
        
        <?php if (isset($captcha)): print '<p class="budgetmailer-form-element budgetmailer-captcha">' . $captcha . '</p>'; endif; ?>
        
        <?php if ( $settings['html_after'] ): ?>
            <div class="budgetmailer-after"><?php print $settings['html_after'] ?></div>
        <?php endif; ?>
        
        <p class="form-submit">
            <input class="budgetmailer-submit" type="submit" value="<?php print $settings['submit_label'] ?>" />
        </p>
        
    </form>
    
    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery('#budgetmailer-subscribe-form-<?php print $i ?>').budgetMailerSubscribe({
                message_invalid: "<?php echo str_replace('"', '\"', $settings['message_invalid']) ?>",
                message_invalid_form: "<?php echo str_replace('"', '\"', $settings['message_invalid_form']) ?>",
                message_error: "<?php echo str_replace('"', '\"', $settings['message_error']) ?>",
                message_success: "<?php echo str_replace('"', '\"', $settings['message_success']) ?>"
            });
        });
    </script>
    
</div>
