<?php

/**
 * @package BudgetMailer\Wordpress
 */
namespace BudgetMailer\Wordpress;

/**
 * Wordpress Plug-in API Implementation
 * @package BudgetMailer\Wordpress
 */
class Plugin
{
    /**
     * @var integer Minimal allowed name length
     */
    const MIN_NAME_LEN = 2;
    /**
     * @var integer default action priority
     */
    const PRIORITY = 10;
    
    /**
     * Self instance for singleton pattern implementation
     * @var Plugin self 
     */
    protected static $instance;
    
    /**
     * @var array associative array of all actions of this plug-in
     */
    protected $actions = array(
        'actions' => array(), 'admin' => array(), 'front' => array()
    );
    
    /**
     * @var AdminMenu Instance of WP API Admin Menu Implementation 
     */
    protected $adminMenu;
    
    /**
     * @var Assets Instance of WP API Assets Implementation 
     */
    protected $assets;
    
    /**
     * @var Captcha 3 most used Captcha plug-ins integration
     */
    protected $captcha;
    
    /**
     * @var Client BudgetMailer PHP API Client Wrapper
     */
    protected $client;
    
    /**
     * @var array associative array of all filters of this plug-in
     */
    protected $filters = array(
        'admin' => array(), 'filters' => array(), 'front' => array()
    );
    
    /**
     * @var Settings Instance of WP API Settings Implementation 
     */
    protected $settings;
    
    /**
     * @var array associative array of post params for subscription
     */
    protected $subscribeData = array(
        'captcha' => FILTER_SANITIZE_STRING,
        'captcha_prefix' => FILTER_SANITIZE_STRING,
        'email' => FILTER_SANITIZE_EMAIL,
        'company' => FILTER_SANITIZE_STRING,
        'first_name' => FILTER_SANITIZE_STRING,
        'middle_name' => FILTER_SANITIZE_STRING,
        'last_name' => FILTER_SANITIZE_STRING,
        'post_id' => FILTER_SANITIZE_NUMBER_INT,
        'type' => FILTER_SANITIZE_STRING,
        'widget_id' => FILTER_SANITIZE_NUMBER_INT,
        'sex' => FILTER_SANITIZE_NUMBER_INT,
        'telephone' => FILTER_SANITIZE_STRING,
        'mobile' => FILTER_SANITIZE_STRING,
        'address' => FILTER_SANITIZE_STRING,
        'postalCode' => FILTER_SANITIZE_STRING,
        'city' => FILTER_SANITIZE_STRING,
        'countryCode' => FILTER_SANITIZE_STRING
    );
    
    /**
     * Create singlton instance
     * @return Plugin self
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        
        return self::$instance;
    }
    
    /**
     * Protected singleton constructor.
     * 
     * Method initiate the plug-in, by creating WP API Implementation Instances,
     * and hooking up the actions, filters and other WP API calls.
     */
    protected function __construct()
    {
        $this->init();
        $this->actions();
        $this->hook();
    }
    
    /**
     * Create instances of WP API Implementation Classes. Only selected optional 
     * features will be initiated.
     */
    protected function init()
    {
        $this->captcha = Captcha::getInstance();
        $this->settings = new Settings($this->captcha);
        
        // if is captcha plugin set
        if ($this->settings->getOption(Settings::CAPTCHA)) {
            $this->captcha->setPlugin($this->settings->getOption(Settings::CAPTCHA));
        }
        
        $this->assets = new Assets();
        $this->adminMenu = new AdminMenu();
        $this->client = Client::getInstance($this->settings->getClientConfigData());
        
        // optional feature: shortcode
        if ($this->settings->hasFeature(Settings::SHORTCODE)) {
            $this->shortcode = new Shortcode();
        }
    }
    
    /**
     * Initiate actions implemented by this plug-in. Only selected optional features 
     * will be implemented.
     */
    protected function actions()
    {
        $this->actions['actions'] = array(
            'plugins_loaded' => array($this, 'onPluginsLoaded'),
            'wp_ajax_budgetmailer_subscribe' => array($this, 'onAjaxSubscribe'),
            'wp_ajax_nopriv_budgetmailer_subscribe' => array($this, 'onAjaxSubscribe'),
        );
        
        $this->actions['admin'] = array(
            'admin_enqueue_scripts' => array($this->assets, 'onAdminEnqueueScripts'),
            'admin_init' => array($this->settings, 'onAdminInit'),
            'admin_menu' => array($this->adminMenu, 'onAdminMenu'),
            'admin_notices' => array($this->settings, 'onAdminNotices')
        );
        
        $this->actions['front'] = array(
            'wp_enqueue_scripts' => array($this->assets, 'onEnqueueScripts'),
            'wp_head' => array($this, 'onWpHead')
        );
        
        // conditional actions
        
        // optional feature: comment form
        if ($this->settings->hasFeature(Settings::COMMENTS)) {
            $this->actions['actions']['wp_insert_comment'] = array('argc' => 2, 'callback' => array($this, 'onWpInsertComment'));
            
            $this->filters['front'] = array(
                'comment_form_submit_button' => array($this, 'filterCommentFormSubmitButton')
            );
        }
        
        // optional feature: registration
        if ($this->settings->hasFeature(Settings::REGISTRATION)) {
            $this->actions['actions']['register_form'] = array($this, 'onRegisterForm');
            $this->actions['actions']['user_register'] = array($this, 'onUserRegister');
        }
        
        // optional feature: user profile (both self / others) forms
        if ($this->settings->hasFeature(Settings::PROFILE)) {
            $this->actions['actions']['edit_user_profile'] = array($this, 'onEditUserProfile');
            $this->actions['actions']['profile_update'] = array('argc' => 2, 'callback' => array($this, 'onUserUpdate'));
            $this->actions['actions']['show_user_profile'] = array($this, 'onEditUserProfile');
        }
        
        // optional feature: widget
        if ($this->settings->hasFeature(Settings::WIDGET)) {
            $this->actions['actions']['widgets_init'] = array($this, 'onWidgetsInit');
        }
    }
    
    /**
     * Add tags to contact
     * @param \stdClass $contact contact object
     * @param string $tags comma separated tags list (or empty)
     */
    protected function addTags(\stdClass $contact, $tags)
    {
        $tags = trim($tags);
        
        if (empty($tags)) {
            return null;
        }
        
        if (!isset($contact->tags) || !is_array($contact->tags)) {
            $contact->tags = array();
        }
        
        $tags = substr_count($tags, ',') ? explode(',', $tags) : array($tags);
        $tags = array_map('trim', $tags);
        
        $contact->tags = array_merge($contact->tags, $tags);
    }
    
    /**
     * "Hook" actions and filters 
     */
    protected function hook()
    {
        $key = is_admin() ? 'admin' : 'front';
        $actions = array_merge($this->actions['actions'], $this->actions[$key]);
        
        foreach($actions as $action => $callback) {
            if (isset($callback['callback'])) {
                $tmp = $callback;
                $callback = $callback['callback'];
                $priority = isset($tmp['priority']) ? $tmp['priority'] : self::PRIORITY;
                $argc = isset($tmp['argc']) ? $tmp['argc'] : 1;
            } else {
                $priority = self::PRIORITY;
                $argc = 1;
            }
            
            add_action($action, $callback, $priority, $argc);
        }
        
        $filters = array_merge($this->filters['filters'], $this->filters[$key]);
        
        foreach($filters as $filter => $callback) {
            add_filter($filter, $callback);
        }
    }
    
    /**
     * Get post parameter containing value of subscribe checkbox.
     * @return integer 
     */
    protected function getSubscribe()
    {
        return filter_input(INPUT_POST, 'budgetmailer_subscribe', FILTER_SANITIZE_NUMBER_INT);
    }
    
    /**
     * Handle subscription of an email to BudgetMailer API
     * @param string $email email to subscribe
     * @param boolean $subscribe yes / no
     * @param boolean $tags true = set default tags
     * @param null|string $email_old email before changed
     * @return boolean|\stdClass new contact or update result
     */
    protected function handleSubscribe($email, $subscribe = true, $tags = true, $email_old = null)
    {
        $contact = $this->client->getContact($email_old ? $email_old : $email);
        $defaultTags = $this->settings->getOption(Settings::TAGS);
        
        // create contact
        if ( is_null( $contact ) ) {
            $newContact = new \stdClass();
            $newContact->email = $email;
            $newContact->unsubscribed = !$subscribe;
            
            if ( $tags ) {
                $this->addTags( $newContact, $defaultTags );
            }

            $rs = $this->client->postContact( $newContact );
        // update contact
        } else {
            $newContact = clone $contact;
            
            if ($email_old) {
                $newContact->email = $email;
            }
            
            $newContact->unsubscribed = !$subscribe;
            
            if ( $tags ) {
                $this->addTags( $newContact, $defaultTags );
            }

            $rs = $this->client->putContact(
                $email_old ? $email_old : $email,
                $newContact
            );
        }
        
        return $rs;
    }

    /**
     * Render subscribe checkbox
     * @param \stdClass $user wordpress user instance, or null for current user
     * @param boolean $subscribed current subscription status
     * @param boolean $print true = print, false = return
     * @return integer|string 
     */
    protected function subscribeCheckbox($user, $subscribed = false, $print = true)
    {
        if ( ! $user ) {
            $user = wp_get_current_user();
        }
        
        $t = new Template(
            'checkbox', array('checked' => $subscribed ? ' checked="checked"' : null, 'label' => $this->settings->getOption(Settings::LABEL_CHECKBOX_LABEL), 'title' => $this->settings->getOption(Settings::LABEL_CHECKBOX_TITLE))
        );
        
        return $print ? print $t : (string)$t;
    }
    
    /**
     * Get AJAX Settings from either post or widget instance.
     * @param null|integer $widget_id null or widget instance id
     * @return array associative array of settings
     */
    protected function getAjaxSubscribeSettings($widget_id)
    {
        if ($widget_id) {
            $widget = new \BudgetMailer\Wordpress\Widget\Subscribe;
            $widget->id = $widget->id_base . '-' . $widget_id;
            $settings = $widget->getSettings();
        }
        
        $settings['captcha'] = $this->settings->getOption(Settings::CAPTCHA);
        
        return $settings;
    }

    /**
     * Get subscribe data
     * @return array associative array of submitted data
     */
    protected function getAjaxSubscribeData()
    {
        return filter_input_array(INPUT_POST, $this->subscribeData);
    }
    
    /**
     * Run submitted data validation
     * @param array $data submitted data
     * @param array $settings ajax settings
     * @return array associative array of invalid elements (or empty array)
     */
    protected function validateAjaxData(array $data, array $settings)
    {
        $invalid = array();
        $captcha = $settings['captcha'];
        $fnr = $settings['first_name_required'];
        $mnr = $settings['middle_name_required'];
        $lnr = $settings['last_name_required'];
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $invalid[] = 'email';
        }
        if ($fnr && strlen($data['first_name']) < self::MIN_NAME_LEN) {
            $invalid[] = 'first_name';
        }
		//Changed from $fnr to $mnr
        if ($mnr && strlen($data['middle_name']) < self::MIN_NAME_LEN) {
            $invalid[] = 'middle_name';
        }
		//Changed from $fnr to $lnr
        if ($lnr && strlen($data['last_name']) < self::MIN_NAME_LEN) {
            $invalid[] = 'last_name';
        }
        if ($captcha && !$this->captcha->validate($data['captcha'], $data['captcha_prefix'])) {
            $invalid[] = 'captcha';
        }
        
        return $invalid;
    }
    
    /**
     * Render subscription checkbox in comment form
     * @param string $button comment button... which we don't actually change at all
     * @return string
     */
    public function filterCommentFormSubmitButton($button)
    {
        $contact = null;
        $subscribed = false;
        $user = wp_get_current_user();

        if ( is_object($user) && $user->ID > 0 ) {
            $contact = $this->client->getContact( $user->user_email );
            $subscribed = is_object($contact) && !$contact->unsubscribed;
        }

        if ( ! $subscribed ) {
            $this->subscribeCheckbox( $user, $subscribed );
        }

        return $button;
    }
    
    /**
     * This is ajax subscribe action handler. It will create new contact 
     * in configured BudgetMailer contact list.
     */
    public function onAjaxSubscribe()
    {
        $response = new \stdClass();
        
        try {
            $data = $this->getAjaxSubscribeData();//var_dump($data);die();
            $settings = $this->getAjaxSubscribeSettings($data['widget_id']);
            $invalid = $this->validateAjaxData($data, $settings);
            
            if (count($invalid)) {
                $response->invalid = $invalid;
                $response->success = false;
            } else {
                $contact = $this->client->getContact( $data['email'] );

                if ( ! $contact ) {
                    $contact = new \stdClass();
                    $new = true;
                } else {
                    $new = false;
                }

                $contact->email = $data['email'];
                $contact->companyName = $data['company'];
                $contact->firstName = $data['first_name'];
                $contact->insertion = $data['middle_name'];
                $contact->lastName = $data['last_name'];
                $contact->sex = $data['sex'];
                $contact->telephone = $data['telephone'];
                $contact->mobile = $data['mobile'];
                $contact->address = $data['address'];
                $contact->postalCode = $data['postalCode'];
                $contact->city = $data['city'];
                $contact->countryCode = $data['countryCode'];

                foreach($contact as $k => $v) {
                    if (is_null($v)) {
                        unset($contact->{$k});
                    }
                }

                if ('' === $contact->sex) {
                    unset($contact->sex);
                }
                
                $contact->unsubscribed = false;

                $this->addTags($contact, $settings['tags']);

                if ($new) {
                    $this->client->postContact( $contact );
                    $response->success = true;
                } else {
                    $this->client->putContact( $contact->email, $contact );
                    $response->success = true;
                }
            }
        } catch (\Exception $e) {
            $response->exception = $e->getMessage();
            $response->success = false;
            wp_die( json_encode( $response ) );
        }

        wp_die( json_encode( $response ) );
    }
    
    /**
     * Display subscription checkbox on user profile
     * @param \stdClass $user current user
     */
    public function onEditUserProfile($user = null)
    {
        $contact = $this->client->getContact($user->user_email);
        $this->subscribeCheckbox( $user, is_object($contact) && !$contact->unsubscribed );
    }
    
    /**
     * Helper method, call multiple on plug-ins loaded events
     */
    public function onPluginsLoaded()
    {
        \BudgetMailer\Wordpress\L10n::onPluginsLoaded();
        $this->settings->onPluginsLoaded();
    }
    
    /**
     * Display subscription checkbox on registration form
     */
    public function onRegisterForm( )
    {
        $this->subscribeCheckbox(false);
    }

    /**
     * Handle registration form data submission / subscription
     * @param int $user_id
     * @return mixed
     * @see handleSubscribe()
     */
    public function onUserRegister($user_id)
    {
        if ($this->getSubscribe()) {
            $user = get_userdata($user_id);
            
            return $this->handleSubscribe($user->user_email, $this->getSubscribe());
        }
        
        return null;
    }

    /**
     * Process subscription checkbox displayed on user profile.
     * @param integer $user_id user id
     * @param \stdClass $user_old old user data object
     * @return mixed
     * @see handleSubscribe()
     */
    public function onUserUpdate($user_id, $user_old)
    {
        $user = get_userdata($user_id);
        
        $email_old = null;
        
        if ($user->user_email != $user_old->user_email) {
            $email_old = $user_old->user_email;
        }
        
        return $this->handleSubscribe($user->user_email, $this->getSubscribe(), false, $email_old);
    }
    
    /**
     * Register subscription widget
     * @return null
     */
    public function onWidgetsInit()
    {
        return register_widget( 'BudgetMailer\Wordpress\Widget\Subscribe' );
    }
    
    /**
     * Make sure there is ajaxurl JS variable on front-end
     */
    public function onWpHead()
    {
        print new Template('head', array('url' => get_admin_url( null, 'admin-ajax.php' )));
    }
    
    /**
     * Handle subscription from comment form
     * @param integer $comment_id comment id
     * @param \stdClass $comment comment instance
     * @return mixed
     * @see handleSubscribe()
     */
    public function onWpInsertComment( $comment_id, $comment )
    {
        if ($comment && $this->getSubscribe()) {
            return $this->handleSubscribe($comment->comment_author_email, $this->getSubscribe());
        }
        
        return null;
    }
}
