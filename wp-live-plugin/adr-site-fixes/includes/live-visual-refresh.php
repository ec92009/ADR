<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class ADR_Site_Fixes_Live_Visual_Refresh {
    private const MARKER = 'adr-live-visual-refresh-v119-5';
    private const ASSET_BASE = 'https://ec92009.github.io/ADR/assets/';
    private const THEME_SCRIPT = 'https://ec92009.github.io/ADR/adr-theme-persistence.js?v=119.4';

    public static function init() {
        add_action( 'template_redirect', array( __CLASS__, 'start_buffer' ), -100 );
    }

    public static function start_buffer() {
        if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
            return;
        }

        ob_start( array( __CLASS__, 'apply' ) );
    }

    public static function apply( $html ) {
        if ( ! is_string( $html ) || $html === '' ) {
            return $html;
        }

        if ( strpos( $html, 'adr-refresh-stage' ) === false && strpos( $html, 'adr-page-stage' ) === false ) {
            return $html;
        }

        $html = self::replace_photos( $html );
        $html = self::replace_photo_dimensions( $html );
        $html = self::replace_versions( $html );
        $html = self::ensure_theme_persistence( $html );

        if ( strpos( $html, self::MARKER ) === false ) {
            $html = str_replace( '</head>', '<meta name="adr-live-visual-refresh" content="' . esc_attr( self::MARKER ) . '">' . "\n</head>", $html );
        }

        return $html;
    }

    private static function replace_photos( $html ) {
        return str_replace(
            array(
                'https://assurancesderueil.fr/wp-content/uploads/2022/09/Assurance-Rueil-Malmaison-Assurance-pret-souscription.jpg',
                'https://assurancesderueil.fr/wp-content/uploads/2022/09/Assurance-Rueil-Malmaison-Assurance-pret-souscription-1024x683.jpg',
                'https://assurancesderueil.fr/wp-content/uploads/2019/09/image.jpg',
                'https://assurancesderueil.fr/wp-content/uploads/2021/02/quote_form_image.jpg',
                'https://assurancesderueil.fr/wp-content/uploads/2019/08/feature_image2.jpg',
                'https://assurancesderueil.fr/wp-content/uploads/2019/09/offer_image3.jpg',
                'https://assurancesderueil.fr/wp-content/uploads/2022/08/Responsabilite-civile-Assurance-Rueil-Malmaison.jpg',
                'https://assurancesderueil.fr/wp-content/uploads/2022/08/Responsabilite-civile-Assurance-Rueil-Malmaison-1024x683.jpg',
                'https://assurancesderueil.fr/wp-content/uploads/2022/08/Assurance-Rueil-Malmaison-Simulation-tarifaire.jpg',
                'https://assurancesderueil.fr/wp-content/uploads/2022/08/Assurance-Rueil-Malmaison-cabinet-de-courtage.jpg',
                'https://assurancesderueil.fr/wp-content/uploads/2022/08/Assurance-Rueil-Malmaison-cabinet-de-courtage-1024x683.jpg',
                'https://assurancesderueil.fr/wp-content/uploads/2019/09/blog_image.jpg',
            ),
            array(
                self::ASSET_BASE . 'adr-photo-home-hero-v119-5.jpg',
                self::ASSET_BASE . 'adr-photo-home-hero-v119-5.jpg',
                self::ASSET_BASE . 'adr-photo-partners-v119-5.jpg',
                self::ASSET_BASE . 'adr-photo-loan-hero-v119-5.jpg',
                self::ASSET_BASE . 'adr-photo-cabinet-hero-v119-5.jpg',
                self::ASSET_BASE . 'adr-photo-family-hero-v119-5.jpg',
                self::ASSET_BASE . 'adr-photo-business-hero-v119-5.jpg',
                self::ASSET_BASE . 'adr-photo-business-hero-v119-5.jpg',
                self::ASSET_BASE . 'adr-photo-quote-hero-v119-5.jpg',
                self::ASSET_BASE . 'adr-photo-quote-hero-v119-5.jpg',
                self::ASSET_BASE . 'adr-photo-quote-hero-v119-5.jpg',
                self::ASSET_BASE . 'adr-photo-contact-hero-v119-5.jpg',
            ),
            $html
        );
    }

    private static function replace_photo_dimensions( $html ) {
        $photos = array(
            'adr-photo-home-hero-v119-5.jpg'     => array( 1586, 992 ),
            'adr-photo-partners-v119-5.jpg'      => array( 1672, 941 ),
            'adr-photo-loan-hero-v119-5.jpg'     => array( 1672, 941 ),
            'adr-photo-cabinet-hero-v119-5.jpg'  => array( 1578, 997 ),
            'adr-photo-family-hero-v119-5.jpg'   => array( 1535, 1025 ),
            'adr-photo-business-hero-v119-5.jpg' => array( 1586, 992 ),
            'adr-photo-quote-hero-v119-5.jpg'    => array( 1672, 941 ),
            'adr-photo-contact-hero-v119-5.jpg'  => array( 1586, 992 ),
        );

        foreach ( $photos as $filename => $size ) {
            $url = self::ASSET_BASE . $filename;
            $width = (string) $size[0];
            $height = (string) $size[1];

            $html = preg_replace(
                '#((?:url|contentUrl)":"' . preg_quote( $url, '#' ) . '","width":)\d+(,"height":)\d+#',
                '$1' . $width . '$2' . $height,
                $html
            );

            if ( strpos( $html, '<meta property="og:image" content="' . $url . '"' ) !== false ) {
                $html = preg_replace(
                    '#<meta property="og:image:width" content="\d+" />#',
                    '<meta property="og:image:width" content="' . $width . '" />',
                    $html,
                    1
                );
                $html = preg_replace(
                    '#<meta property="og:image:height" content="\d+" />#',
                    '<meta property="og:image:height" content="' . $height . '" />',
                    $html,
                    1
                );
            }
        }

        return $html;
    }

    private static function replace_versions( $html ) {
        return str_replace(
            array(
                'v119.3',
                'v119-3',
                "version = '119.3'",
                'adr_quote_consent_2026-06-27_v119.3',
            ),
            array(
                'v119.5',
                'v119-5',
                "version = '119.5'",
                'adr_quote_consent_2026-06-27_v119.5',
            ),
            $html
        );
    }

    private static function ensure_theme_persistence( $html ) {
        $script = '<script id="adr-theme-persistence-v119-5" src="' . esc_url( self::THEME_SCRIPT ) . '"></script>';

        $html = preg_replace(
            '#<script id="adr-theme-persistence-v119-[^"]*" src="[^"]*"></script>#',
            $script,
            $html
        );

        if ( strpos( $html, 'id="adr-theme-toggle"' ) === false || strpos( $html, 'adr-theme-persistence-v119-5' ) !== false ) {
            return $html;
        }

        return preg_replace(
            '#(<input class="adr-switch-input" id="adr-theme-toggle" type="checkbox"[^>]*>)#',
            '$1' . "\n  " . $script,
            $html,
            1
        );
    }
}

ADR_Site_Fixes_Live_Visual_Refresh::init();
