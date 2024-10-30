<?php

/**
 * @package BudgetMailer\Wordpress
 */
namespace BudgetMailer\Wordpress;

/**
 * Implementation of the 3 most used Captcha Plug-ins for WP
 * @package BudgetMailer\Wordpress
 */
class Captcha
{
    /**
     * @var string really simple captcha id
     */
    const CAPTCHA_RSC = 'really-simple-captcha';
    /**
     * @var string si captcha for wordpress id
     */
    const CAPTCHA_SI = 'si-captcha-for-wordpress';
    /**
     * @var string recaptcha for wp id
     */
    const CAPTCHA_RE = 'wp-recaptcha';
    /**
     * @var string catpcha input id
     */
    const ID = 'wpbm-captcha';
    /**
     * @var string catpcha input name
     */
    const NAME = 'wpbm-captcha';
    
    /**
     * Singleton instance
     * @var Captcha self
     */
    protected static $instance;
    
    /**
     * @var array auto-detected captchas
     */
    protected $active = array();
    /**
     * @var array assoc. array of captcha definitions
     */
    protected $captchas = array(
        self::CAPTCHA_RSC => array(
            'enabled' => false,
            'name' => 'Really Simple Captcha',
            'plugin' => 'really-simple-captcha/really-simple-captcha.php'
        ),
        self::CAPTCHA_SI => array(
            'enabled' => false,
            'name' => 'SI Captcha for Wordpress',
            'plugin' => 'si-captcha-for-wordpress/si-captcha.php'
        ),
        self::CAPTCHA_RE => array(
            'enabled' => false,
            'name' => 'WP ReCaptcha Integration',
            'plugin' => 'wp-recaptcha-integration/wp-recaptcha-integration.php'
        )
    );
    /**
     * @var string selected captcha plug-in
     */
    protected $plugin;
    /**
     * @var string really simple captcha prefix
     */
    protected $rscPrefix;
    
    /**
     * Create singleton instance of this class
     * @return Captcha
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        
        return self::$instance;
    }
    
    /**
     * Set current Captcha plug-in
     * @param string selected captcha plug-in
     */
    public function setPlugin($plugin)
    {
        $this->checkPlugin($plugin);
        $this->plugin = $plugin;
    }
    
    /**
     * Check if plug-in was set
     * @return boolean
     */
    public function hasPlugin()
    {
        return !empty($this->plugin);
    }
    
    /**
     * Will return assoc. array of active Captcha plug-ins
     * @param boolean $noCaptcha if true first item will be no captcha
     * @return array assoc. array of active Captcha plug-ins
     */
    public function getActiveCaptchas($noCaptcha = true)
    {
        if (!count($this->active)) {
            if ($noCaptcha) {
                $this->active[0] = L10n::__( 'No CAPTCHA' );
            }

            foreach( $this->captchas as $captcha => $def ) {
                if ( is_plugin_active( $def['plugin'] ) ) {
                    $this->active[$captcha] = $def['name'];
                }
            }
        }
        
        return $this->active;
    }
    
    /**
     * Render selected Captcha plug-in
     * @param string $id captcha input id
     * @param string $name captcha input name
     * @param boolean $print if true, captcha will print, otherwise return
     * @return boolean|string
     */
    public function render($id = self::ID, $name = self::NAME, $print = false)
    {
        if (!$this->hasPlugin()) {
            return null;
        }
        
        $this->checkHasPlugin();
        
        $t = new Template(
            'captcha', array(
                'captcha' => $this->renderPlugin($id, $name),
                'id' => $id,
                'label' => L10n::__('What code is in the image?'),
                'name' => $name
            )
        );
        
        return $print ? print $t : (string) $t;
    }
    
    /**
     * Render captcha plug-in
     * @param string $id captcha input id
     * @param string $name captcha input name
     * @return string rendered captcha
     */
    public function renderPlugin($id, $name)
    {
        $method = $this->renderPluginMethod();
        return $this->$method($id, $name);
    }
    
    /**
     * Generate plug-in render method
     * @param null|string $plugin null or plugin
     * @return string method name
     */
    public function renderPluginMethod($plugin = null)
    {
        if (is_null($plugin)) {
            $plugin = $this->plugin;
        }
        
        return 'renderPlugin' . str_replace(' ', '', ucwords( str_replace('-', ' ', $plugin) ) );
    }
    
    /**
     * Render Really Simple Captcha plug-in
     * @param string $id captcha input id
     * @param string $name captcha input name
     * @return string rendered captcha
     */
    public function renderPluginReallySimpleCaptcha($id, $name)
    {
        $c = new \ReallySimpleCaptcha();
        $html = null;

        $prefix = mt_rand();
        $image = $c->generate_image( $prefix, $c->generate_random_word() );

        if ( $image ) {
            $html = '<img src="' . get_site_url( null, 'wp-content/plugins/really-simple-captcha/tmp/' . $image ) . '" /><br/>';
            $html .= '<input class="budgetmailer-input" id="' . $id . '-prefix" name="budgetmailer[' . $name . ']" type="hidden" value="' . $prefix . '" data-name="captcha_prefix" />';
            $html .= '<input class="budgetmailer-input" id="' . $id . '" name="budgetmailer[' . $name . ']" type="text" required="required" data-name="captcha" /><br/>';
            $html .= '<small>' . L10n::__('This question is for testing whether or not you are a human visitor and to prevent automated spam submissions.') . '</small>';
        } else {
            $html = L10n::__( 'Generating Really Simple CAPTCHA image failed.' );
        }

        return $html;
    }
    
    /**
     * Render SI Captcha for WP plug-in
     * @param string $id captcha input id
     * @param string $name captcha input name
     * @return string rendered captcha
     */
    public function renderPluginSiCaptchaForWordpress($id, $name)
    {
        global $si_image_captcha;

        $prefix = substr(md5(time()), 0, 16);
        
        if ( isset( $si_image_captcha ) && $si_image_captcha instanceof \siCaptcha ) {
            $html = $si_image_captcha->si_captcha_captcha_html('si_image', $prefix, true);
            $html .= '<input class="budgetmailer-input" id="' . $id . '-prefix" name="budgetmailer[' . $name . ']" type="hidden" value="' . $prefix . '" data-name="captcha_prefix" />';
            $html .= '<input class="budgetmailer-input" id="' . $id . '" name="budgetmailer[' . $name . ']" type="text" required="required" data-name="captcha" /><br/>';
            $html .= '<small>' . L10n::__('This question is for testing whether or not you are a human visitor and to prevent automated spam submissions.') . '</small>';
            
            return $html;
        }

        return null;
    }
    
    /**
     * Render WP Re-Captcha plug-in
     * @param string $id captcha input id
     * @param string $name captcha input name
     * @return string rendered captcha
     */
    public function renderPluginWpRecaptcha($id, $name)
    {
        if ( class_exists('\WP_reCaptcha') ) {
            
            ob_start();
            do_action('recaptcha_print' , array());
            
            return ob_get_clean();
        }

        return null;
    }

    /**
     * Validate captcha code
     * @param string $str captcha code
     * @param string $prefix captcha prefix
     * @return null|boolean
     */
    public function validate($str, $prefix = null)
    {
        if (!$this->hasPlugin()) {
            return null;
        }
        
        $this->checkHasPlugin();
        
        return $this->validatePlugin($str, $prefix);
    }
    
    /**
     * Validate captcha code using selected plug-in
     * @param string $str captcha code
     * @param string $prefix captcha prefix
     * @return null|boolean
     */
    public function validatePlugin($str, $prefix = null)
    {
        $method = $this->validatePluginMethod();
        return $this->$method($str, $prefix);
    }
    
    /**
     * Generate plug-in render method
     * @param null|string $plugin null or plugin
     * @return string method name
     */
    public function validatePluginMethod($plugin = null)
    {
        if (is_null($plugin)) {
            $plugin = $this->plugin;
        }
        
        return 'validatePlugin' . str_replace(' ', '', ucwords( str_replace('-', ' ', $plugin) ) );
    }
    
    /**
     * Validate captcha code using Really Simple Captcha
     * @param string $str captcha code
     * @param string $prefix captcha prefix
     * @return null|boolean
     */
    public function validatePluginReallySimpleCaptcha($str, $prefix)
    {
        $c = new \ReallySimpleCaptcha();
        return $c->check( $prefix, $str );
    }
    
    /**
     * Validate captcha code using SI Captcha for WP
     * @param string $str captcha code
     * @param string $prefix captcha prefix
     * @return null|boolean
     */
    public function validatePluginSiCaptchaForWordpress($str, $prefix)
    {
        global $si_image_captcha;
        $_POST['captcha_code'] = $str;

        if ( isset( $si_image_captcha ) && $si_image_captcha instanceof \siCaptcha ) {
            return 'valid' == $si_image_captcha->si_captcha_validate_code($prefix);
        }
        
        return false;
    }
    
    /**
     * Validate captcha code using WP Re-Captcha
     * @param string $str captcha code
     * @param string $prefix captcha prefix
     * @return null|boolean
     */
    public function validatePluginWpRecaptcha($str)
    {
        if ( class_exists('\WP_reCaptcha') ) {
            $private_key = \WP_reCaptcha::instance()->get_option( 'recaptcha_privatekey' );
            $user_response = isset( $_REQUEST['captcha'] ) ? $_REQUEST['captcha'] : false;
            
            if ( $user_response !== false ) {
                $remote_ip = $_SERVER['REMOTE_ADDR'];
                $url = "https://www.google.com/recaptcha/api/siteverify?secret=$private_key&response=$user_response&remoteip=$remote_ip";
                $response = wp_remote_get( $url );

                if ( ! is_wp_error($response) ) {
                    $response_data = wp_remote_retrieve_body( $response );
                    $this->_last_result = json_decode($response_data);
                } else {
                    $this->_last_result = (object) array( 'success' => false , 'wp_error' => $response );
                }

                return $this->_last_result->success;
            }
        }
        
        return null;
    }

    /**
     * Check if plug-in was set
     * @return boolean
     * @throws \RuntimeException in case no plug-in was set
     */
    protected function checkHasPlugin()
    {
        if (!$this->hasPlugin()) {
            throw new \RuntimeException(L10n::__('You must set CAPTCHA plugin before rendering or validating.'));
        }
    }
    
    /**
     * Check if plug-in is valid catpcha plug-in
     * @return null
     */
    public function checkPlugin($plugin)
    {
        // INFO this can throw exception in inconvenient places 
        // (e.g. wrong captcha set -> won't render settings anymore)
        if (!isset($this->captchas[$plugin])) {
            return false;
            //throw new \RuntimeException(L10n::__('Invalid CAPTCHA plugin.'));
        }
        
        return true;
    }
}
