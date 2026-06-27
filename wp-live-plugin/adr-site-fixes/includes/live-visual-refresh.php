<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class ADR_Site_Fixes_Live_Visual_Refresh {
    private const MARKER = 'adr-live-visual-refresh-v119-6';
    private const ASSET_BASE = 'https://ec92009.github.io/ADR/assets/';
    private const THEME_SCRIPT = 'https://ec92009.github.io/ADR/adr-theme-persistence.js?v=119.6';

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
        $html = self::ensure_quote_phone_input_mode( $html );
        $html = self::refresh_contact_form( $html );
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

            $html = preg_replace_callback(
                '#((?:url|contentUrl)":"' . preg_quote( $url, '#' ) . '","width":)\d+(,"height":)\d+#',
                function ( $matches ) use ( $width, $height ) {
                    return $matches[1] . $width . $matches[2] . $height;
                },
                $html
            );
            $html = preg_replace_callback(
                '#("url":"' . preg_quote( $url, '#' ) . '"),"\d+,"caption"#',
                function ( $matches ) use ( $url, $width, $height ) {
                    return $matches[1] . ',"contentUrl":"' . $url . '","width":' . $width . ',"height":' . $height . ',"caption"';
                },
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
                'v119.5',
                'v119.3',
                "version = '119.5'",
                "version = '119.3'",
                'adr_quote_consent_2026-06-27_v119.5',
                'adr_quote_consent_2026-06-27_v119.3',
                'adr-fr-only-v119-5',
                'adr-fr-only-v119-3',
                'adr-source-truth-residuals-v119-5',
                'adr-source-truth-residuals-v119-3',
                'adr-live-quote-form-v119-5',
                'adr-live-quote-form-v119-3',
                'adr-live-visual-refresh-v119-5',
                'adr-live-visual-refresh-v119-3',
            ),
            array(
                'v119.6',
                'v119.6',
                "version = '119.6'",
                "version = '119.6'",
                'adr_quote_consent_2026-06-27_v119.6',
                'adr_quote_consent_2026-06-27_v119.6',
                'adr-fr-only-v119-6',
                'adr-fr-only-v119-6',
                'adr-source-truth-residuals-v119-6',
                'adr-source-truth-residuals-v119-6',
                'adr-live-quote-form-v119-6',
                'adr-live-quote-form-v119-6',
                'adr-live-visual-refresh-v119-6',
                'adr-live-visual-refresh-v119-6',
            ),
            $html
        );
    }

    private static function ensure_quote_phone_input_mode( $html ) {
        return str_replace(
            '<input type="tel" name="telephone" autocomplete="tel" placeholder="+33">',
            '<input type="tel" name="telephone" autocomplete="tel" inputmode="tel" placeholder="+33">',
            $html
        );
    }

    private static function refresh_contact_form( $html ) {
        if ( strpos( $html, 'metform-wrap-7487-7487' ) === false ) {
            return $html;
        }

        $html = self::remove_contact_recaptcha( $html );
        $html = self::ensure_contact_phone_field( $html );
        $html = self::ensure_contact_phone_alignment( $html );

        return $html;
    }

    private static function remove_contact_recaptcha( $html ) {
        $html = preg_replace(
            '#\s*<div className="elementor-element elementor-element-ebfc01d\b.*?(?=<div className="elementor-element elementor-element-3a46b2f\b)#s',
            "\n\t\t\t\t",
            $html,
            1
        );

        return preg_replace(
            '#<script id="recaptcha-(?:support|v2)-js"[^>]*></script>\s*#',
            '',
            $html
        );
    }

    private static function ensure_contact_phone_field( $html ) {
        if ( strpos( $html, 'elementor-element-adr-phone' ) !== false ) {
            return $html;
        }

        $address_widget = '<div className="elementor-element elementor-element-61eb343 elementor-widget elementor-widget-mf-text" data-id="61eb343" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;adresse&quot;}" data-widget_type="mf-text.default">';
        if ( strpos( $html, $address_widget ) === false ) {
            return $html;
        }

        return str_replace(
            $address_widget,
            self::contact_phone_field_markup() . "\n\t                " . $address_widget,
            $html
        );
    }

    private static function contact_phone_field_markup() {
        return <<<'HTML'
<div className="elementor-element elementor-element-adr-phone elementor-widget elementor-widget-mf-text" data-id="adr-phone" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;telephone&quot;}" data-widget_type="mf-text.default">
	                <div className="elementor-widget-container">

	        <div className="mf-input-wrapper">
	                            <label className="mf-input-label" htmlFor="mf-input-tel-adr-phone">
	                    ${ parent.decodeEntities(`Téléphone`) } 					<span className="mf-input-required-indicator">*</span>
	                </label>

	            <input
	                type="tel"
	                inputMode="tel"
	                autoComplete="tel"
	                className="mf-input "
	                id="mf-input-tel-adr-phone"
	                name="telephone"
	                placeholder="${ parent.decodeEntities(`+33 1 47 51 06 69`) } "
	                                    onInput=${parent.handleChange}
	                    onBlur=${parent.handleChange}
	                    aria-invalid=${validation.errors['telephone'] ? 'true' : 'false'}
	                    ref=${el =>{
	                                                parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":6,"maxLength":"","type":"none","required":true,"expression":"null"}, el)
	                    }}
	                                />

	                            <${validation.ErrorMessage}
	                    errors=${validation.errors}
	                    name="telephone"
	                    as=${html`<span className="mf-error-message"></span>`}
	                    />

	                    </div>

	                        </div>
	                </div>
HTML;
    }

    private static function ensure_contact_phone_alignment( $html ) {
        if ( strpos( $html, 'adr-contact-phone-align-v1' ) !== false ) {
            return $html;
        }

        return preg_replace_callback(
            '#</style>\s*(<input class="adr-switch-input" id="adr-theme-toggle")#',
            function ( $matches ) {
                return self::contact_phone_alignment_css() . "\n</style>\n  " . $matches[1];
            },
            $html,
            1
        );
    }

    private static function contact_phone_alignment_css() {
        return <<<'CSS'

    /* adr-contact-phone-align-v1: match the injected phone field to exported MetForm row geometry. */
    @media (min-width: 768px) {
      .adr-form-card .elementor-element-adr-phone .mf-input-label {
        display: inline-block !important;
        width: 20% !important;
        margin-bottom: 0 !important;
        vertical-align: middle !important;
      }
      .adr-form-card .elementor-element-adr-phone input[name="telephone"] {
        width: 78.9% !important;
      }
    }
    @media (max-width: 767px) {
      .adr-form-card .elementor-element-adr-phone .mf-input-label,
      .adr-form-card .elementor-element-adr-phone input[name="telephone"] {
        width: 100% !important;
      }
      .adr-form-card .elementor-element-adr-phone .mf-input-label {
        display: block !important;
        margin-bottom: 5px !important;
      }
    }
CSS;
    }

    private static function ensure_theme_persistence( $html ) {
        $script = '<script id="adr-theme-persistence-v119-6" src="' . esc_url( self::THEME_SCRIPT ) . '"></script>';

        $html = preg_replace(
            '#<script id="adr-theme-persistence-v119-[^"]*" src="[^"]*"></script>#',
            $script,
            $html
        );

        if ( strpos( $html, 'id="adr-theme-toggle"' ) === false || strpos( $html, 'adr-theme-persistence-v119-6' ) !== false ) {
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
