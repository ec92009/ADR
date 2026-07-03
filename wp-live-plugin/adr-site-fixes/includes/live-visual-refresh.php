<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class ADR_Site_Fixes_Live_Visual_Refresh {
    private const MARKER = 'adr-live-visual-refresh-v125-0';
    private const CONTACT_FORM_ID = 7487;
    private const ASSET_BASE = 'https://ec92009.github.io/ADR/assets/';
    private const THEME_SCRIPT = 'https://ec92009.github.io/ADR/adr-theme-persistence.js?v=125.0';
    private static $reading_contact_meta = false;

    public static function init() {
        add_filter( 'get_post_metadata', array( __CLASS__, 'filter_contact_elementor_data' ), 10, 4 );
        add_filter( 'metform_filter_before_store_form_data', array( __CLASS__, 'preserve_contact_form_alias_data' ), 10, 4 );
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
        $html = self::replace_consent_wording( $html );
        $html = self::ensure_quote_phone_input_mode( $html );
        $html = self::refresh_contact_form( $html );
        $html = self::ensure_theme_persistence( $html );
        $html = self::ensure_phone_preserver( $html );

        if ( strpos( $html, self::MARKER ) === false ) {
            $html = str_replace( '</head>', '<meta name="adr-live-visual-refresh" content="' . esc_attr( self::MARKER ) . '">' . "\n</head>", $html );
        }

        return $html;
    }

    public static function filter_contact_elementor_data( $value, $object_id, $meta_key, $single ) {
        if ( $value !== null || $meta_key !== '_elementor_data' || (int) $object_id !== self::CONTACT_FORM_ID || self::$reading_contact_meta ) {
            return $value;
        }

        self::$reading_contact_meta = true;
        $raw = get_metadata( 'post', $object_id, $meta_key, true );
        self::$reading_contact_meta = false;

        if ( ! is_string( $raw ) || $raw === '' ) {
            return $value;
        }

        $data = json_decode( $raw, true );
        if ( ! is_array( $data ) ) {
            return $value;
        }

        $data = self::strip_contact_recaptcha_nodes( $data );
        if ( ! self::elementor_data_has_field( $data, 'telephone' ) ) {
            $inserted = false;
            $data = self::insert_contact_phone_widget( $data, $inserted );
            if ( ! $inserted && self::is_list_array( $data ) ) {
                $data[] = self::contact_phone_meta_widget();
            }
        }
        $data = self::make_contact_detail_fields_optional( $data );

        $encoded = wp_json_encode( $data );
        if ( ! is_string( $encoded ) || $encoded === '' ) {
            return $value;
        }

        return $single ? $encoded : array( $encoded );
    }

    public static function preserve_contact_form_alias_data( $form_data, $form_id, $form_settings, $attributes ) {
        if ( (int) $form_id !== self::CONTACT_FORM_ID || ! is_array( $form_data ) ) {
            return $form_data;
        }

        $phone = self::posted_contact_phone();
        if ( $phone !== '' ) {
            $form_data['telephone'] = $phone;
        }

        $message = self::posted_contact_message( $form_data );
        if ( $message !== '' ) {
            $form_data['message'] = $message;

            if ( empty( $form_data['mf-textarea'] ) ) {
                $form_data['mf-textarea'] = $message;
            }
        }

        return $form_data;
    }

    private static function posted_contact_phone() {
        if ( ! isset( $_POST['telephone'] ) ) {
            return '';
        }

        $phone = wp_unslash( $_POST['telephone'] );
        if ( is_array( $phone ) ) {
            $phone = reset( $phone );
        }

        if ( ! is_scalar( $phone ) ) {
            return '';
        }

        return sanitize_text_field( (string) $phone );
    }

    private static function posted_contact_message( $form_data ) {
        foreach ( array( 'message', 'mf-textarea' ) as $key ) {
            if ( isset( $_POST[ $key ] ) ) {
                $message = wp_unslash( $_POST[ $key ] );
                if ( is_array( $message ) ) {
                    $message = reset( $message );
                }

                if ( is_scalar( $message ) ) {
                    $message = sanitize_textarea_field( (string) $message );
                    if ( $message !== '' ) {
                        return $message;
                    }
                }
            }
        }

        foreach ( array( 'message', 'mf-textarea' ) as $key ) {
            if ( isset( $form_data[ $key ] ) && is_scalar( $form_data[ $key ] ) ) {
                $message = sanitize_textarea_field( (string) $form_data[ $key ] );
                if ( $message !== '' ) {
                    return $message;
                }
            }
        }

        return '';
    }

    private static function strip_contact_recaptcha_nodes( $node ) {
        if ( ! is_array( $node ) ) {
            return $node;
        }

        if ( isset( $node['widgetType'] ) && $node['widgetType'] === 'mf-recaptcha' ) {
            return null;
        }

        if ( isset( $node['elements'] ) && is_array( $node['elements'] ) ) {
            $elements = array();
            foreach ( $node['elements'] as $child ) {
                $child = self::strip_contact_recaptcha_nodes( $child );
                if ( $child !== null ) {
                    $elements[] = $child;
                }
            }
            $node['elements'] = $elements;
            return $node;
        }

        if ( self::is_list_array( $node ) ) {
            $items = array();
            foreach ( $node as $child ) {
                $child = self::strip_contact_recaptcha_nodes( $child );
                if ( $child !== null ) {
                    $items[] = $child;
                }
            }
            return $items;
        }

        return $node;
    }

    private static function elementor_data_has_field( $node, $field_name ) {
        if ( ! is_array( $node ) ) {
            return false;
        }

        if ( self::node_field_name( $node ) === $field_name ) {
            return true;
        }

        foreach ( $node as $value ) {
            if ( is_array( $value ) && self::elementor_data_has_field( $value, $field_name ) ) {
                return true;
            }
        }

        return false;
    }

    private static function insert_contact_phone_widget( $node, &$inserted ) {
        if ( ! is_array( $node ) ) {
            return $node;
        }

        if ( self::is_list_array( $node ) ) {
            $items = array();
            foreach ( $node as $child ) {
                if ( ! $inserted && self::node_field_name( $child ) === 'adresse' ) {
                    $items[] = self::contact_phone_meta_widget();
                    $inserted = true;
                }
                $items[] = self::insert_contact_phone_widget( $child, $inserted );
            }
            return $items;
        }

        if ( isset( $node['elements'] ) && is_array( $node['elements'] ) ) {
            $node['elements'] = self::insert_contact_phone_widget( $node['elements'], $inserted );
        }

        return $node;
    }

    private static function make_contact_detail_fields_optional( $node ) {
        if ( ! is_array( $node ) ) {
            return $node;
        }

        if ( isset( $node['settings'] ) && is_array( $node['settings'] ) ) {
            $field_name = self::node_field_name( $node );
            if ( in_array( $field_name, array( 'telephone', 'adresse', 'code-postal', 'ville', 'mf-textarea' ), true ) ) {
                $node['settings']['mf_input_required'] = '';
                $node['settings']['mf_input_min_length'] = '';
            }
            if ( $field_name === 'mf-gdpr-consent' ) {
                $node['settings']['mf_input_required'] = 'yes';
                $node['settings']['mf_input_min_length'] = '1';
            }
        }

        foreach ( $node as $key => $value ) {
            if ( is_array( $value ) ) {
                $node[ $key ] = self::make_contact_detail_fields_optional( $value );
            }
        }

        return $node;
    }

    private static function node_field_name( $node ) {
        if ( ! is_array( $node ) || ! isset( $node['settings'] ) || ! is_array( $node['settings'] ) ) {
            return '';
        }

        return isset( $node['settings']['mf_input_name'] ) ? (string) $node['settings']['mf_input_name'] : '';
    }

    private static function contact_phone_meta_widget() {
        return array(
            'id'         => 'adrphone',
            'elType'     => 'widget',
            'settings'   => array(
                'mf_input_label'                 => 'Téléphone',
                'mf_input_name'                  => 'telephone',
                'mf_input_required'              => '',
                'mf_input_min_length'            => '',
                'mf_input_max_length'            => '',
                'mf_input_validation_type'       => 'none',
                'mf_input_validation_expression' => '',
            ),
            'elements'   => array(),
            'widgetType' => 'mf-text',
        );
    }

    private static function is_list_array( $value ) {
        if ( ! is_array( $value ) ) {
            return false;
        }

        if ( $value === array() ) {
            return true;
        }

        return array_keys( $value ) === range( 0, count( $value ) - 1 );
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
        $replacements = array(
            'adr_quote_consent_2026-06-28_v120.1' => 'adr_quote_consent_2026-06-28_v120.2',
            'adr_quote_consent_2026-06-28_v120.0' => 'adr_quote_consent_2026-06-28_v120.2',
            'adr_quote_consent_2026-06-27_v119.7' => 'adr_quote_consent_2026-06-28_v120.2',
            'adr_quote_consent_2026-06-27_v119.6' => 'adr_quote_consent_2026-06-28_v120.2',
            'adr_quote_consent_2026-06-27_v119.5' => 'adr_quote_consent_2026-06-28_v120.2',
            'adr_quote_consent_2026-06-27_v119.3' => 'adr_quote_consent_2026-06-28_v120.2',
            'adr-fr-only-v120-2'                 => 'adr-fr-only-v125-0',
            'adr-fr-only-v120-1'                 => 'adr-fr-only-v125-0',
            'adr-fr-only-v120-0'                 => 'adr-fr-only-v125-0',
            'adr-fr-only-v119-7'                 => 'adr-fr-only-v125-0',
            'adr-fr-only-v119-6'                 => 'adr-fr-only-v125-0',
            'adr-fr-only-v119-5'                 => 'adr-fr-only-v125-0',
            'adr-fr-only-v119-3'                 => 'adr-fr-only-v125-0',
            'adr-source-truth-residuals-v120-2'  => 'adr-source-truth-residuals-v125-0',
            'adr-source-truth-residuals-v120-1'  => 'adr-source-truth-residuals-v125-0',
            'adr-source-truth-residuals-v120-0'  => 'adr-source-truth-residuals-v125-0',
            'adr-source-truth-residuals-v119-7'  => 'adr-source-truth-residuals-v125-0',
            'adr-source-truth-residuals-v119-6'  => 'adr-source-truth-residuals-v125-0',
            'adr-source-truth-residuals-v119-5'  => 'adr-source-truth-residuals-v125-0',
            'adr-source-truth-residuals-v119-3'  => 'adr-source-truth-residuals-v125-0',
            'adr-live-quote-form-v120-2'         => 'adr-live-quote-form-v125-0',
            'adr-live-quote-form-v120-1'         => 'adr-live-quote-form-v125-0',
            'adr-live-quote-form-v120-0'         => 'adr-live-quote-form-v125-0',
            'adr-live-quote-form-v119-7'         => 'adr-live-quote-form-v125-0',
            'adr-live-quote-form-v119-6'         => 'adr-live-quote-form-v125-0',
            'adr-live-quote-form-v119-5'         => 'adr-live-quote-form-v125-0',
            'adr-live-quote-form-v119-3'         => 'adr-live-quote-form-v125-0',
            'adr-live-visual-refresh-v120-2'     => 'adr-live-visual-refresh-v125-0',
            'adr-live-visual-refresh-v120-1'     => 'adr-live-visual-refresh-v125-0',
            'adr-live-visual-refresh-v120-0'     => 'adr-live-visual-refresh-v125-0',
            'adr-live-visual-refresh-v119-7'     => 'adr-live-visual-refresh-v125-0',
            'adr-live-visual-refresh-v119-6'     => 'adr-live-visual-refresh-v125-0',
            'adr-live-visual-refresh-v119-5'     => 'adr-live-visual-refresh-v125-0',
            'adr-live-visual-refresh-v119-3'     => 'adr-live-visual-refresh-v125-0',
            'adr-theme-persistence-v120-2'       => 'adr-theme-persistence-v125-0',
            'adr-theme-persistence-v120-1'       => 'adr-theme-persistence-v125-0',
            'adr-theme-persistence-v120-0'       => 'adr-theme-persistence-v125-0',
            'adr-theme-persistence-v119-7'       => 'adr-theme-persistence-v125-0',
            'adr-form-phone-preserver-v120-2'    => 'adr-form-phone-preserver-v125-0',
            'adr-form-phone-preserver-v120-1'    => 'adr-form-phone-preserver-v125-0',
            'adr-form-phone-preserver-v120-0'    => 'adr-form-phone-preserver-v125-0',
            'adr-form-phone-preserver-v119-7'    => 'adr-form-phone-preserver-v125-0',
            "version = '120.2'"                  => "version = '125.0'",
            "version = '120.1'"                  => "version = '125.0'",
            "version = '120.0'"                  => "version = '125.0'",
            "version = '119.7'"                  => "version = '125.0'",
            "version = '119.6'"                  => "version = '125.0'",
            "version = '119.5'"                  => "version = '125.0'",
            "version = '119.3'"                  => "version = '125.0'",
            'v120.2'                             => 'v125.0',
            'v120.1'                             => 'v125.0',
            'v120.0'                             => 'v125.0',
            'v119.7'                             => 'v125.0',
            'v119.6'                             => 'v125.0',
            'v119.5'                             => 'v125.0',
            'v119.3'                             => 'v125.0',
            'adr_quote_consent_2026-06-28_v125.0' => 'adr_quote_consent_2026-06-28_v120.2',
        );

        return str_replace( array_keys( $replacements ), array_values( $replacements ), $html );
    }

    private static function replace_consent_wording( $html ) {
        return str_replace(
            array(
                'j’accepte qu’un conseiller Assurances de Rueil, m’appelle',
                'j’accepte qu’un conseiller Assurances de Rueil m’appelle',
                "j'accepte qu'un conseiller Assurances de Rueil, m'appelle",
                "j'accepte qu'un conseiller Assurances de Rueil m'appelle",
            ),
            "j'accepte qu'un conseiller Assurances de Rueil me contacte",
            $html
        );
    }

    private static function ensure_quote_phone_input_mode( $html ) {
        return str_replace(
            array(
                '<input type="tel" name="telephone" autocomplete="tel" placeholder="+33">',
                '<input type="tel" name="telephone" autocomplete="tel" inputmode="tel" placeholder="+33">',
            ),
            '<input type="text" name="telephone" autocomplete="tel" inputmode="tel" placeholder="+33">',
            $html
        );
    }

    private static function refresh_contact_form( $html ) {
        if ( strpos( $html, 'metform-wrap-7487-7487' ) === false ) {
            return $html;
        }

        $html = self::remove_contact_recaptcha( $html );
        $html = self::ensure_contact_phone_field( $html );
        $html = self::make_contact_detail_fields_optional_in_markup( $html );
        $html = self::make_contact_consents_required_in_markup( $html );
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
                    ${ parent.decodeEntities(`Téléphone`) } 					<span className="mf-input-required-indicator"></span>
                </label>

            <input
                type="text"
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
                                                parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":"","maxLength":"","type":"none","required":false,"expression":"null"}, el)
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

    private static function make_contact_detail_fields_optional_in_markup( $html ) {
        $html = preg_replace(
            '#(\$\{ parent\.decodeEntities\(`(?:Téléphone|Adresse|Code postal|Ville)`\) \}\s*)<span className="mf-input-required-indicator">\*</span>#',
            '$1<span className="mf-input-required-indicator"></span>',
            $html
        );

        foreach ( array( 'telephone', 'adresse', 'code-postal', 'ville', 'mf-textarea' ) as $field_name ) {
            $html = preg_replace_callback(
                '#aria-invalid=\$\{validation\.errors\[\'' . preg_quote( $field_name, '#' ) . '\'[^\n]*\}.*?parent\.activateValidation\(\{.*?\}, el\)#s',
                function ( $matches ) {
                    return str_replace(
                        array( '"minLength":6', '"minLength":1', '"required":true' ),
                        array( '"minLength":""', '"minLength":""', '"required":false' ),
                        $matches[0]
                    );
                },
                $html
            );
        }

        return $html;
    }

    private static function make_contact_consents_required_in_markup( $html ) {
        $html = preg_replace_callback(
            '#aria-invalid=\$\{validation\.errors\[\'mf-gdpr-consent\'[^\n]*\}.*?parent\.activateValidation\(\{.*?\}, el\)#s',
            function ( $matches ) {
                return str_replace(
                    array( '"minLength":""', '"minLength":1', '"required":false' ),
                    array( '"minLength":1', '"minLength":1', '"required":true' ),
                    $matches[0]
                );
            },
            $html
        );

        return preg_replace_callback(
            '#<input\b(?:(?!/?>).)*name="mf-gdpr-consent"(?:(?!/?>).)*\/>#s',
            function ( $matches ) {
                if ( strpos( $matches[0], ' required' ) !== false ) {
                    return $matches[0];
                }

                return preg_replace( '#\s*/>$#', ' required />', $matches[0] );
            },
            $html
        );
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
        $script = '<script id="adr-theme-persistence-v125-0" src="' . esc_url( self::THEME_SCRIPT ) . '"></script>';

        $html = preg_replace(
            '#<script id="adr-theme-persistence-v(?:119|120|125)-[^"]*" src="[^"]*"></script>#',
            $script,
            $html
        );

        if ( strpos( $html, 'id="adr-theme-toggle"' ) === false || strpos( $html, 'adr-theme-persistence-v125-0' ) !== false ) {
            return $html;
        }

        return preg_replace(
            '#(<input class="adr-switch-input" id="adr-theme-toggle" type="checkbox"[^>]*>)#',
            '$1' . "\n  " . $script,
            $html,
            1
        );
    }

    private static function ensure_phone_preserver( $html ) {
        if ( strpos( $html, 'name="telephone"' ) === false || strpos( $html, 'adr-form-phone-preserver-v125-0' ) !== false ) {
            return $html;
        }

        return str_replace( '</body>', self::phone_preserver_script() . "\n</body>", $html );
    }

    private static function phone_preserver_script() {
        return <<<'HTML'
<script id="adr-form-phone-preserver-v125-0">
(function () {
  var staticPreview = /(^|\.)github\.io$/.test(window.location.hostname) || window.location.hostname === '127.0.0.1' || window.location.hostname === 'localhost' || window.location.protocol === 'file:';
  var metformInsert = /\/wp-json\/metform\/v1\/entries\/insert\//;

  function phoneInputs(scope) {
    return Array.prototype.slice.call((scope || document).querySelectorAll('input[name="telephone"]'));
  }

  function primePhone(input) {
    if (!input) {
      return;
    }
    try {
      input.type = 'text';
    } catch (error) {}
    input.setAttribute('inputmode', 'tel');
    input.setAttribute('autocomplete', 'tel');
    if (!input.dataset.adrRawPhone) {
      input.dataset.adrRawPhone = input.value || '';
    }
  }

  function rememberPhone(event) {
    var input = event.target;
    if (!input || !input.matches || !input.matches('input[name="telephone"]')) {
      return;
    }
    primePhone(input);
    input.dataset.adrRawPhone = input.value || '';
  }

  function restorePhone(form) {
    phoneInputs(form).forEach(function (input) {
      primePhone(input);
      var raw = input.dataset.adrRawPhone || '';
      if (raw.charAt(0) === '+' && input.value.charAt(0) !== '+') {
        input.value = raw;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
      }
    });
  }

  function showStaticStatus(form) {
    var status = form.querySelector('[data-form-status]');
    if (!status) {
      var card = form.closest('.adr-form-card') || form.closest('.mf-form-shortcode') || form.parentElement;
      if (card) {
        status = card.querySelector('[data-adr-static-status]');
      }
      if (!status && card) {
        status = document.createElement('p');
        status.className = 'adr-static-status';
        status.dataset.adrStaticStatus = 'true';
        status.setAttribute('aria-live', 'polite');
        status.style.cssText = 'margin:16px 0 18px;padding:14px 16px;border:1px solid rgba(198,212,230,.95);border-radius:8px;background:#fff;color:#66758a;font-weight:800;text-align:center;';
        var intro = card.querySelector('.adr-form-intro');
        if (intro && intro.nextSibling) {
          card.insertBefore(status, intro.nextSibling);
        } else {
          card.insertBefore(status, form);
        }
      }
    }
    if (status) {
      status.hidden = false;
      status.textContent = "Prévisualisation : aucune demande n'a été envoyée.";
    }
  }

  document.addEventListener('input', rememberPhone, true);
  document.addEventListener('change', rememberPhone, true);
  document.addEventListener('blur', rememberPhone, true);

  if (staticPreview && typeof window.fetch === 'function') {
    var originalFetch = window.fetch;
    window.fetch = function (input, init) {
      var url = typeof input === 'string' ? input : input && input.url;
      if (url && metformInsert.test(url)) {
        return Promise.resolve(new Response(JSON.stringify({
          status: true,
          success: true,
          mock: true,
          data: { message: "Prévisualisation : aucune demande n'a été envoyée." }
        }), {
          status: 200,
          headers: { 'Content-Type': 'application/json' }
        }));
      }
      return originalFetch.apply(this, arguments);
    };
  }

  document.addEventListener('submit', function (event) {
    var form = event.target;
    if (!form || !form.matches || !form.matches('[data-adr-live-quote-form], .metform-form-content')) {
      return;
    }
    restorePhone(form);
    if (!staticPreview) {
      return;
    }
    event.preventDefault();
    event.stopImmediatePropagation();
    showStaticStatus(form);
  }, true);

  function initPhones() {
    phoneInputs(document).forEach(primePhone);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPhones, { once: true });
  } else {
    initPhones();
  }
}());
</script>
HTML;
    }
}

ADR_Site_Fixes_Live_Visual_Refresh::init();
