<?php

/**
 * @package BudgetMailer\Wordpress
 */
namespace BudgetMailer\Wordpress;

/**
 * Wordpress Administration Menu API Implementation
 * @package BudgetMailer\Wordpress
 */
class AdminMenu
{
    /**
     * @var string 
     */
    const MANAGE_OPTIONS = 'manage_options';
    /**
     * @var string
     */
    const PAGE_SETTINGS = 'budgetmailer';
    /**
     * @var string
     */
    const TITLE_SETTINGS = 'BudgetMailer Sign Up Form for WordPress';
    /**
     * @var string
     */
    const TITLE_MENU_SETTINGS = 'BudgetMailer Sign Up';
    
    /**
     * @var array assoc. array of admin pages
     */
    protected $pages = array(
        self::PAGE_SETTINGS => array(
            'capability' => self::MANAGE_OPTIONS,
            'parent' => 'options-general.php',
            'title' => self::TITLE_SETTINGS,
            'title_menu' => self::TITLE_MENU_SETTINGS
        )
    );
    
    /**
     * On admin menu call back - add admin menu items.
     */
    public function onAdminMenu()
    {
        foreach( $this->pages as $slug => $page ) {
            add_submenu_page(
                $page['parent'], L10n::__( $page['title'] ), 
                L10n::__( $page['title_menu'] ), $page['capability'], 
                $slug, array( $this, 'page' . ucfirst( $slug ) )
            );
        }
    }
    
    /**
     * Admin settings page callback.
     */
    public function pageBudgetmailer()
    {
        print new Template( 'options' );
    }
    
    /**
     * Used only for gettext...
     * @see TITLE_SETTINGS, TITLE_MENU_SETTINGS
     */
    private function translationWorkaround()
    {
        L10n::__('BudgetMailer Sign Up Form for WordPress');
        L10n::__('BudgetMailer Sign Up');
    }
}
