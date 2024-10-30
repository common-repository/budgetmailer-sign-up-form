<?php

/**
 * Plugin Name: BudgetMailer Sign Up Form
 * Plugin URI: http://www.budgetmailer.nl
 * Description: Easily add BudgetMailer newsletter sign up forms to your WordPress website with the offical BudgetMailer Sign Up Form plugin.
 * Version: 1.0.5
 * Author: BudgetMailer
 * Author URI: http://www.budgetmailer.nl
 * License: MIT
 */

/**
 * @var string BudgetMailer Newsletter Sign Up Plug-in Name (File)
 */
define( 'WPBM_PLUGIN', __FILE__ );

// Required PSR-4 Autoloader
require_once 'src/AutoloaderPsr4.php';

// Initiate and configure PSR-4 Autoloader
$alpsr4 = new AutoloaderPsr4( array(
    'BudgetMailer\Api' => __DIR__ . '/vendor/budgetmailer-php-api/src/BudgetMailer/Api',
    'BudgetMailer\Wordpress' => __DIR__ . '/src/BudgetMailer/Wordpress'
) );

// Create new Instance of BudgetMailer WordPress Plugin
$GLOBALS['wpbm_plugin'] = \BudgetMailer\Wordpress\Plugin::getInstance();
