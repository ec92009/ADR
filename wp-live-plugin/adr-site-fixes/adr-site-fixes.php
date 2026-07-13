<?php
/**
 * Plugin Name: ADR Site Fixes
 * Description: Small live-site modules for Assurances de Rueil while the child theme is split out of functions.php.
 * Version: 135.0
 * Author: Web-By-Elie
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'ADR_SITE_FIXES_VERSION', '135.0' );
define( 'ADR_SITE_FIXES_DIR', plugin_dir_path( __FILE__ ) );

function adr_site_fixes_require( $relative_path ) {
    $path = ADR_SITE_FIXES_DIR . $relative_path;

    if ( ! file_exists( $path ) ) {
        $path = ADR_SITE_FIXES_DIR . 'adr-site-fixes/' . $relative_path;
    }

    require_once $path;
}

adr_site_fixes_require( 'includes/quote-requests-export.php' );
adr_site_fixes_require( 'includes/request-spam-guard.php' );
adr_site_fixes_require( 'includes/page-shell-normalizer.php' );
adr_site_fixes_require( 'includes/form-adapters.php' );
adr_site_fixes_require( 'includes/public-user-email.php' );
adr_site_fixes_require( 'includes/live-visual-refresh.php' );
