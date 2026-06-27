<?php
/**
 * Plugin Name: ADR Site Fixes
 * Description: Small live-site modules for Assurances de Rueil while the child theme is split out of functions.php.
 * Version: 119.7.1
 * Author: Web-By-Elie
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'ADR_SITE_FIXES_VERSION', '119.7.1' );
define( 'ADR_SITE_FIXES_DIR', plugin_dir_path( __FILE__ ) );

$adr_site_fixes_quote_email = ADR_SITE_FIXES_DIR . 'includes/quote-user-email.php';
if ( ! file_exists( $adr_site_fixes_quote_email ) ) {
    $adr_site_fixes_quote_email = ADR_SITE_FIXES_DIR . 'adr-site-fixes/includes/quote-user-email.php';
}

require_once $adr_site_fixes_quote_email;

$adr_site_fixes_visual_refresh = ADR_SITE_FIXES_DIR . 'includes/live-visual-refresh.php';
if ( ! file_exists( $adr_site_fixes_visual_refresh ) ) {
    $adr_site_fixes_visual_refresh = ADR_SITE_FIXES_DIR . 'adr-site-fixes/includes/live-visual-refresh.php';
}

require_once $adr_site_fixes_visual_refresh;
