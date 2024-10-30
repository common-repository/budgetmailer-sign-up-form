<?php

/**
 * @package BudgetMailer\Wordpress
 */
namespace BudgetMailer\Wordpress;

/**
 * Wordpress L10N API Implementation
 * @package BudgetMailer\Wordpress
 */
class L10n
{
    /**
     * @var string text domain
     */
    const DOMAIN = 'budgetmailer-sign-up-form';
    const DIR = 'l10n';
    
    /**
     * @see __()
     */
    public static function __( $text )
    {
        return __( $text, self::DOMAIN );
    }
    
    /**
     * @see _e()
     */
    public static function _e( $text )
    {
        _e( $text, self::DOMAIN );
    }
    
    /**
     * This method will run on plugins loaded action, and load plugin text domain
     */
    public static function onPluginsLoaded()
    {
        load_plugin_textdomain(
            self::DOMAIN, false, 
            self::DOMAIN . DIRECTORY_SEPARATOR . self::DIR . DIRECTORY_SEPARATOR 
        );
    }
}
