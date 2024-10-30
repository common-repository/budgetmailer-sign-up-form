jQuery.fn.budgetMailerSubscribe = function(messages) {
    function validate_email_element($e) {
        if (!$e.hasClass('validate-email')) {
            return true;
        }
        
        var email = $e.val();
        
        return email.indexOf('@') > 0 && email.indexOf('.') > 0;
    }
    
    function validate($form) {
        var inputs = $form.find('.budgetmailer-input');
        var valid = true;
        
        jQuery.each(inputs, function(i, e) {
            var $e = jQuery(e);
            
            if ($e.attr('required') && ( !$e.val() || !validate_email_element($e) ) ) {
                valid = false;
                element_add_message($e, messages.message_invalid);
            } else {
                element_remove_message($e);
            }
        });
        
        if (valid) {
            form_remove_message(wpbm_form);
        }
        
        return valid;
    }
    
    function ajax($form) {
        var data = {
            action: "budgetmailer_subscribe"
        };
        var inputs = $form.find(".budgetmailer-input");
        var re = $form.find('.g-recaptcha-response');
        var si = $form.find('input[name*="si_code"]');
        
        jQuery.each(inputs, function(i, e) {
            var $e = jQuery(e);
            
            if ($e.attr('data-name')) {
                data[$e.attr('data-name')] = $e.val();
            }
        });

        // add captcha plug-ins data if any
        if (re.length) { data['captcha'] = re[0].value; }
        if (si.length) { data[si[0].name] = si[0].value; }
        
        wpbm_form.addClass('budgetmailer-progress');
        wpbm_form.find('.budgetmailer-button').attr('disabled', 'disabled');
        
        jQuery.ajax(ajaxurl, {
            data: data,
            method: "POST",
        })
        .done( ajax_done )
        .fail( ajax_fail );
    }
    
    function ajax_done( data, textStatus, jqXHR ) {
        wpbm_form.removeClass('budgetmailer-progress');
        wpbm_form.find('.budgetmailer-button').removeAttr('disabled');
        
        if ( data === "0" ) {
            form_add_message( wpbm_form, messages.message_error);
            return;
        }

        var response = JSON.parse( data );

        if ("object" == typeof(response) && "undefined" != typeof(response.success) && response.success ) {
            form_add_message( wpbm_form, messages.message_success, "budgetmailer-success");
            wpbm_form.slideUp();
        } else if ("undefined" != typeof(response.exception)) {
            form_add_message( wpbm_form, messages.message_error);
        } else if ( "undefined" != typeof(response.invalid)) {
            //console.log('err2');
            
            jQuery.each(response.invalid, function(i, e) {
                if ("captcha" == e) {
                    var $e = jQuery( wpbm_form.find('div.budgetmailer-captcha')[0] );
                } else {
                    var $e = jQuery(e);
                }
                
                element_add_message($e, messages.message_invalid);
            });
            
            form_add_message(wpbm_form, messages.message_invalid_form);
        } else {
            form_add_message( wpbm_form, messages.message_error);
        }
    }
    
    function ajax_fail( jqXHR, textStatus, errorThrown ) {
        wpbm_form.removeClass('budgetmailer-progress');
        wpbm_form.find('.budgetmailer-button').removeAttr('disabled');
        
        //console.log('err5');
        form_add_message( wpbm_form, messages.message_error );
    }
    
    function form_add_message($form, message, type) {
        if ("undefined" == typeof(type)) {
            type = "budgetmailer-invalid";
        }
        
        if (!$form.prev().hasClass("budgetmailer-invalid")) {
            $form.before('<span class="' + type + '">' + message + '</span>');
        }
    }
    
    function form_remove_message($form) {
        var $prev = $form.prev();
        
        if ($prev.hasClass("budgetmailer-invalid")) {
            $prev.remove();
        }
    }
    
    function element_add_message($e, message) {
        $e.addClass("budgetmailer-required");

        if (!$e.next('span').length) {
            $e.after('<span class="budgetmailer-invalid">' + message + '</span>');
        }
    }
    
    function element_remove_message($e) {
        $e.removeClass("budgetmailer-required");
        $e.next('span').remove();
    }
    
    this.on('submit', function(e) {
        //console.log('sub');
        e.preventDefault();
        
        wpbm_form = jQuery(this);
        var valid = validate(wpbm_form);
        
        if (valid) {
            form_remove_message(wpbm_form);
            ajax(wpbm_form);
        } else {
            form_add_message(wpbm_form, messages.message_invalid_form);
        }
    });
    
    //console.log('init', this);
}

/*
jQuery(document).ready(function() {
    //console.log('bm', budgetmailer);
    jQuery('form.budgetmailer-subscribe-form').budgetMailerSubscribe();
});
*/

function wpbm_init_recaptchas() {
    jQuery('.g-recaptcha').each(function(i, e) {
        grecaptcha.render(e.id, {
            sitekey: wpbm_site_key, theme: wpbm_theme
        });
    });
}

var wpbm_form = null;
