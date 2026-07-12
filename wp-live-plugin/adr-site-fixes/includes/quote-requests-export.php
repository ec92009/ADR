<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// adr-quote-requests-v134-0: private site-request CSV and ordered admin email.
define( 'ADR_QUOTE_REQUESTS_VERSION', '134.0' );
define( 'ADR_QUOTE_REQUESTS_FORM_ID', '2073' );
define( 'ADR_CONTACT_REQUESTS_FORM_ID', '7487' );
define( 'ADR_QUOTE_REQUESTS_MIN_DATE', '2026-01-01 00:00:00' );
define( 'ADR_QUOTE_REQUESTS_PATH', 'demandes-de-devis' );
define( 'ADR_QUOTE_REQUESTS_ACCESS_KEY', '57e957f2bb42963c872a28f1e061dbf6bc06757514bf97173ab571b3a31ee8c3' );
define( 'ADR_QUOTE_REQUESTS_ACCESS_KEY_HASH', '9fc8c2c9e7e00dae0fa807614cbbaa8953239c1454295b2dab7e88e3bedc2876' );
define( 'ADR_QUOTE_REQUESTS_ADMIN_RECIPIENT', 'contact@assurancesderueil.fr' );
define( 'ADR_SITE_REQUEST_TECHNICAL_DATA_RETENTION_DAYS', 30 );

add_action( 'template_redirect', 'adr_maybe_render_quote_requests_page', 0 );
add_action( 'init', 'adr_site_request_schedule_technical_data_cleanup' );
add_action( 'adr_site_request_cleanup_technical_data', 'adr_site_request_cleanup_technical_data' );
add_filter( 'wp_mail', 'adr_update_quote_admin_email', 20 );
add_action( 'added_post_meta', 'adr_site_request_preserve_live_payload_fields', 20, 4 );
add_action( 'updated_post_meta', 'adr_site_request_preserve_live_payload_fields', 20, 4 );

function adr_quote_requests_url() {
    return add_query_arg(
        'key',
        ADR_QUOTE_REQUESTS_ACCESS_KEY,
        home_url( '/' . ADR_QUOTE_REQUESTS_PATH . '/' )
    );
}

function adr_site_request_form_ids() {
    return array( ADR_QUOTE_REQUESTS_FORM_ID, ADR_CONTACT_REQUESTS_FORM_ID );
}

function adr_site_request_kind_for_form_id( $form_id ) {
    if ( (string) $form_id === ADR_CONTACT_REQUESTS_FORM_ID ) {
        return 'contact';
    }

    if ( (string) $form_id === ADR_QUOTE_REQUESTS_FORM_ID ) {
        return 'quote';
    }

    return '';
}

function adr_site_request_form_id_from_request() {
    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
    if ( preg_match( '#/metform/v1/entries/insert/(\d+)#', $request_uri, $matches ) ) {
        return (string) $matches[1];
    }

    foreach ( array( 'form_id', 'formId', 'mf_form_id' ) as $key ) {
        if ( isset( $_POST[ $key ] ) ) {
            return sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
        }
    }

    return '';
}

function adr_site_request_request_value( $key ) {
    if ( ! isset( $_POST[ $key ] ) ) {
        return '';
    }

    $value = wp_unslash( $_POST[ $key ] );
    if ( is_array( $value ) ) {
        $parts = array();
        array_walk_recursive(
            $value,
            function ( $item ) use ( &$parts ) {
                if ( is_scalar( $item ) ) {
                    $item = sanitize_text_field( (string) $item );
                    if ( $item !== '' ) {
                        $parts[] = $item;
                    }
                }
            }
        );

        return implode( ', ', $parts );
    }

    return sanitize_text_field( (string) $value );
}

function adr_site_request_server_value( $key ) {
    if ( ! isset( $_SERVER[ $key ] ) ) {
        return '';
    }

    return sanitize_text_field( wp_unslash( (string) $_SERVER[ $key ] ) );
}

function adr_site_request_first_valid_ip( $value ) {
    foreach ( explode( ',', (string) $value ) as $candidate ) {
        $candidate = trim( $candidate );
        if ( filter_var( $candidate, FILTER_VALIDATE_IP ) ) {
            return $candidate;
        }
    }

    return '';
}

function adr_site_request_requester_ip() {
    foreach ( array( 'HTTP_CF_CONNECTING_IP', 'HTTP_TRUE_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' ) as $key ) {
        $ip = adr_site_request_first_valid_ip( adr_site_request_server_value( $key ) );
        if ( $ip !== '' ) {
            return $ip;
        }
    }

    return '';
}

function adr_site_request_request_metadata() {
    $metadata = array(
        'requester_ip'               => adr_site_request_requester_ip(),
        'requester_geo_country'      => adr_site_request_server_value( 'HTTP_CF_IPCOUNTRY' ),
        'requester_geo_city'         => adr_site_request_server_value( 'HTTP_CF_IPCITY' ),
        'requester_geo_region'       => adr_site_request_server_value( 'HTTP_CF_REGION' ),
        'requester_geo_region_code'  => adr_site_request_server_value( 'HTTP_CF_REGION_CODE' ),
        'requester_geo_postal_code'  => adr_site_request_server_value( 'HTTP_CF_POSTAL_CODE' ),
        'requester_geo_timezone'     => adr_site_request_server_value( 'HTTP_CF_TIMEZONE' ),
        'requester_geo_latitude'     => adr_site_request_server_value( 'HTTP_CF_IPLATITUDE' ),
        'requester_geo_longitude'    => adr_site_request_server_value( 'HTTP_CF_IPLONGITUDE' ),
        'requester_cf_ray'           => adr_site_request_server_value( 'HTTP_CF_RAY' ),
    );

    return array_filter(
        $metadata,
        function ( $value ) {
            return $value !== '';
        }
    );
}

function adr_site_request_technical_metadata_keys() {
    return array(
        'requester_ip',
        'requester_geo_country',
        'requester_geo_city',
        'requester_geo_region',
        'requester_geo_region_code',
        'requester_geo_postal_code',
        'requester_geo_timezone',
        'requester_geo_latitude',
        'requester_geo_longitude',
        'requester_cf_ray',
    );
}

function adr_site_request_schedule_technical_data_cleanup() {
    if ( wp_next_scheduled( 'adr_site_request_cleanup_technical_data' ) ) {
        return;
    }

    wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'adr_site_request_cleanup_technical_data' );
}

function adr_site_request_cleanup_technical_data() {
    $cutoff = date(
        'Y-m-d H:i:s',
        current_time( 'timestamp' ) - ADR_SITE_REQUEST_TECHNICAL_DATA_RETENTION_DAYS * DAY_IN_SECONDS
    );

    foreach ( adr_quote_requests_entries( ADR_QUOTE_REQUESTS_MIN_DATE ) as $entry ) {
        if ( strtotime( $entry->post_date ) >= strtotime( $cutoff ) ) {
            continue;
        }

        $data = adr_quote_request_data_for_post( $entry->ID );
        if ( empty( $data ) ) {
            continue;
        }

        $changed = false;
        foreach ( adr_site_request_technical_metadata_keys() as $key ) {
            if ( array_key_exists( $key, $data ) ) {
                unset( $data[ $key ] );
                $changed = true;
            }
        }

        if ( $changed ) {
            update_post_meta( (int) $entry->ID, 'metform_entries__form_data', $data );
        }
    }
}

function adr_site_request_live_payload_field_keys( $form_id ) {
    $keys = array(
        'schema_version',
        'source_url',
        'consent_version',
        'nom',
        'prenom',
        'mf-email',
        'email',
        'telephone',
        'contact_consent',
        'rgpd_consent',
        'mf-gdpr-consent',
    );

    if ( adr_site_request_kind_for_form_id( $form_id ) === 'contact' ) {
        return array_merge(
            $keys,
            array(
                'adresse',
                'code-postal',
                'code_postal',
                'mf-text',
                'ville',
                'message',
                'mf-textarea',
            )
        );
    }

    return array_merge(
        $keys,
        array(
            'mf-checkbox',
            'civilite',
            'type_devis',
            'jour_naissance',
            'mois_naissance',
            'annee_naissance',
            'mf-date',
            'date_naissance',
            'contact_preference',
            'fumeur',
            'banque',
            'mf-select',
            'profession',
            'adresse',
            'code-postal',
            'code_postal',
            'mf-text',
            'ville',
        )
    );
}

function adr_site_request_merge_live_payload_fields( $data, $form_id = '' ) {
    if ( ! is_array( $data ) ) {
        $data = array();
    }

    if ( $form_id === '' ) {
        $form_id = adr_site_request_form_id_from_request();
    }

    if ( adr_site_request_kind_for_form_id( $form_id ) === '' ) {
        return $data;
    }

    foreach ( adr_site_request_live_payload_field_keys( $form_id ) as $key ) {
        $value = adr_site_request_request_value( $key );
        if ( $value === '' ) {
            continue;
        }

        if ( isset( $data[ $key ] ) && adr_quote_request_flatten_value( $data[ $key ] ) !== '' ) {
            continue;
        }

        $data[ $key ] = $value;
    }

    foreach ( adr_site_request_request_metadata() as $key => $value ) {
        if ( isset( $data[ $key ] ) && adr_quote_request_flatten_value( $data[ $key ] ) !== '' ) {
            continue;
        }

        $data[ $key ] = $value;
    }

    return $data;
}

function adr_site_request_preserve_live_payload_fields( $meta_id, $post_id, $meta_key, $meta_value ) {
    static $updating = false;

    if ( $updating || $meta_key !== 'metform_entries__form_data' ) {
        return;
    }

    $form_id = adr_site_request_form_id_from_request();
    if ( adr_site_request_kind_for_form_id( $form_id ) === '' ) {
        return;
    }

    $data = is_array( $meta_value ) ? $meta_value : maybe_unserialize( $meta_value );
    if ( ! is_array( $data ) ) {
        return;
    }

    $merged = adr_site_request_merge_live_payload_fields( $data, $form_id );
    if ( $merged === $data ) {
        return;
    }

    $updating = true;
    update_post_meta( (int) $post_id, 'metform_entries__form_data', $merged );
    $updating = false;
}

function adr_is_quote_requests_path() {
    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
    $path = wp_parse_url( $request_uri, PHP_URL_PATH );
    $path = trim( (string) $path, '/' );

    return $path === ADR_QUOTE_REQUESTS_PATH;
}

function adr_quote_requests_authorized() {
    $key = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';

    if ( $key === '' ) {
        return false;
    }

    return hash_equals( ADR_QUOTE_REQUESTS_ACCESS_KEY_HASH, hash( 'sha256', $key ) );
}

function adr_maybe_render_quote_requests_page() {
    if ( ! adr_is_quote_requests_path() ) {
        return;
    }

    nocache_headers();
    header( 'X-Robots-Tag: noindex, nofollow', true );

    if ( ! adr_quote_requests_authorized() ) {
        status_header( 403 );
        adr_render_quote_requests_forbidden();
        exit;
    }

    status_header( 200 );
    adr_render_quote_requests_csv();
    exit;
}

function adr_render_quote_requests_forbidden() {
    ?>
    <!doctype html>
    <html lang="fr">
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="robots" content="noindex,nofollow">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Accès refusé - Assurances de Rueil</title>
        <style>
            body {
                display: grid;
                min-height: 100vh;
                margin: 0;
                place-items: center;
                background: #eef5fb;
                color: #07192f;
                font: 16px/1.5 -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            }
            main {
                width: min(520px, calc(100vw - 32px));
                border: 1px solid #c6d4e6;
                border-radius: 8px;
                padding: 28px;
                background: #ffffff;
                box-shadow: 0 18px 44px rgba(7, 25, 47, 0.12);
            }
            h1 {
                margin: 0 0 8px;
                color: #0a3f81;
                font-size: 28px;
                line-height: 1.1;
            }
            p {
                margin: 0;
                color: #66758a;
            }
        </style>
    </head>
    <body>
        <main>
            <h1>Accès refusé</h1>
            <p>Le lien utilisé ne permet pas de télécharger ce fichier.</p>
        </main>
    </body>
    </html>
    <?php
}

function adr_quote_requests_export_after() {
    $days = isset( $_GET['days'] ) ? absint( wp_unslash( $_GET['days'] ) ) : 0;
    if ( $days > 0 ) {
        return date( 'Y-m-d H:i:s', current_time( 'timestamp' ) - $days * DAY_IN_SECONDS );
    }

    return ADR_QUOTE_REQUESTS_MIN_DATE;
}

function adr_quote_requests_export_format() {
    $format = isset( $_GET['format'] ) ? strtolower( sanitize_key( wp_unslash( $_GET['format'] ) ) ) : 'csv';

    return $format === 'tsv' ? 'tsv' : 'csv';
}

function adr_quote_requests_entries( $after = '' ) {
    if ( $after === '' ) {
        $after = ADR_QUOTE_REQUESTS_MIN_DATE;
    }

    return get_posts(
        array(
            'post_type'        => 'metform-entry',
            'post_status'      => 'any',
            'posts_per_page'   => -1,
            'orderby'          => 'date',
            'order'            => 'DESC',
            'suppress_filters' => true,
            'date_query'       => array(
                array(
                    'after'     => $after,
                    'inclusive' => true,
                    'column'    => 'post_date',
                ),
            ),
            'meta_query'       => array(
                array(
                    'key'   => 'metform_entries__form_id',
                    'value' => adr_site_request_form_ids(),
                    'compare' => 'IN',
                ),
            ),
        )
    );
}

function adr_quote_request_form_id_for_post( $post_id ) {
    return (string) get_post_meta( (int) $post_id, 'metform_entries__form_id', true );
}

function adr_quote_request_data_for_post( $post_id ) {
    $data = get_post_meta( (int) $post_id, 'metform_entries__form_data', true );

    return is_array( $data ) ? $data : array();
}

function adr_quote_request_flatten_value( $value ) {
    if ( is_array( $value ) ) {
        $parts = array();
        array_walk_recursive(
            $value,
            function ( $item ) use ( &$parts ) {
                if ( is_scalar( $item ) ) {
                    $item = trim( (string) $item );
                    if ( $item !== '' ) {
                        $parts[] = $item;
                    }
                }
            }
        );

        return implode( ', ', $parts );
    }

    return trim( (string) $value );
}

function adr_quote_request_value( $data, $keys ) {
    foreach ( $keys as $key ) {
        if ( ! isset( $data[ $key ] ) ) {
            continue;
        }

        $value = adr_quote_request_flatten_value( $data[ $key ] );
        if ( $value !== '' ) {
            return $value;
        }
    }

    return '';
}

function adr_quote_request_contact_label( $value ) {
    if ( $value === 'telephone' ) {
        return 'Téléphone';
    }

    if ( $value === 'whatsapp' ) {
        return 'WhatsApp';
    }

    if ( $value === '' || $value === 'email' ) {
        return 'E-mail';
    }

    return $value;
}

function adr_quote_request_type_label( $value ) {
    $types = array(
        'pret'          => 'Assurance de prêt',
        'habitation'    => 'Assurance habitation',
        'auto'          => 'Assurance automobile',
        'sante'         => 'Santé / prévoyance',
        'professionnel' => 'Assurance professionnelle',
        'loyers'        => 'Loyers impayés',
        'autre'         => 'Autre demande',
    );

    return isset( $types[ $value ] ) ? $types[ $value ] : $value;
}

function adr_quote_request_month_number( $month ) {
    $normalized = trim( (string) $month );
    $normalized = strtr(
        $normalized,
        array(
            'à' => 'a',
            'â' => 'a',
            'ä' => 'a',
            'À' => 'A',
            'Â' => 'A',
            'Ä' => 'A',
            'é' => 'e',
            'è' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'É' => 'E',
            'È' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'î' => 'i',
            'ï' => 'i',
            'Î' => 'I',
            'Ï' => 'I',
            'ô' => 'o',
            'ö' => 'o',
            'Ô' => 'O',
            'Ö' => 'O',
            'ù' => 'u',
            'û' => 'u',
            'ü' => 'u',
            'Ù' => 'U',
            'Û' => 'U',
            'Ü' => 'U',
            'ç' => 'c',
            'Ç' => 'C',
        )
    );
    $normalized = strtoupper( preg_replace( '/[^A-Za-z0-9]/', '', $normalized ) );

    if ( ctype_digit( $normalized ) ) {
        $month_number = (int) $normalized;
        return $month_number >= 1 && $month_number <= 12 ? $month_number : 0;
    }

    $months = array(
        'JAN'       => 1,
        'JANV'      => 1,
        'JANVIER'   => 1,
        'FEV'       => 2,
        'FEVR'      => 2,
        'FEVRIER'   => 2,
        'FEB'       => 2,
        'MAR'       => 3,
        'MARS'      => 3,
        'AVR'       => 4,
        'AVRIL'     => 4,
        'APR'       => 4,
        'MAI'       => 5,
        'MAY'       => 5,
        'JUIN'      => 6,
        'JUN'       => 6,
        'JUIL'      => 7,
        'JUILLET'   => 7,
        'JUL'       => 7,
        'AOU'       => 8,
        'AOUT'      => 8,
        'AUG'       => 8,
        'SEP'       => 9,
        'SEPT'      => 9,
        'SEPTEMBRE' => 9,
        'OCT'       => 10,
        'OCTOBRE'   => 10,
        'NOV'       => 11,
        'NOVEMBRE'  => 11,
        'DEC'       => 12,
        'DECEMBRE'  => 12,
    );

    return isset( $months[ $normalized ] ) ? $months[ $normalized ] : 0;
}

function adr_quote_request_format_french_date_parts( $year, $month, $day ) {
    $year = (int) $year;
    if ( $year >= 0 && $year < 100 ) {
        $year += $year >= 30 ? 1900 : 2000;
    }

    $month = adr_quote_request_month_number( $month );
    $day = (int) $day;

    if ( $year < 1000 || ! checkdate( $month, $day, $year ) ) {
        return '';
    }

    $months = array(
        1  => 'JAN',
        2  => 'FEV',
        3  => 'MAR',
        4  => 'AVR',
        5  => 'MAI',
        6  => 'JUIN',
        7  => 'JUIL',
        8  => 'AOUT',
        9  => 'SEP',
        10 => 'OCT',
        11 => 'NOV',
        12 => 'DEC',
    );

    return sprintf( '%02d-%s-%04d', $day, $months[ $month ], $year );
}

function adr_quote_request_format_french_date_value( $value, $hint = 'auto' ) {
    $value = trim( (string) $value );
    if ( $value === '' ) {
        return '';
    }

    if ( preg_match( '/^(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})$/', $value, $matches ) ) {
        return adr_quote_request_format_french_date_parts( $matches[1], $matches[2], $matches[3] );
    }

    if ( preg_match( '/^(\d{1,2})[-\/\s.]([A-Za-zÀ-ÿ.]+)[-\/\s.](\d{2,4})$/u', $value, $matches ) ) {
        return adr_quote_request_format_french_date_parts( $matches[3], $matches[2], $matches[1] );
    }

    if ( preg_match( '/^(\d{1,2})\/(\d{1,2})\/(\d{2,4})$/', $value, $matches ) ) {
        return adr_quote_request_format_french_date_parts( $matches[3], $matches[2], $matches[1] );
    }

    if ( preg_match( '/^(\d{1,2})-(\d{1,2})-(\d{2,4})$/', $value, $matches ) ) {
        if ( $hint === 'legacy_mdy' ) {
            return adr_quote_request_format_french_date_parts( $matches[3], $matches[1], $matches[2] );
        }

        return adr_quote_request_format_french_date_parts( $matches[3], $matches[2], $matches[1] );
    }

    return $value;
}

function adr_quote_request_format_french_datetime( $mysql_date ) {
    $mysql_date = trim( (string) $mysql_date );
    if ( ! preg_match( '/^(\d{4})-(\d{1,2})-(\d{1,2})(?:[ T](\d{2}):(\d{2}))?/', $mysql_date, $matches ) ) {
        return $mysql_date;
    }

    $date = adr_quote_request_format_french_date_parts( $matches[1], $matches[2], $matches[3] );
    if ( $date === '' ) {
        return $mysql_date;
    }

    if ( isset( $matches[4], $matches[5] ) && $matches[4] !== '' && $matches[5] !== '' ) {
        return $date . ' ' . $matches[4] . ':' . $matches[5];
    }

    return $date;
}

function adr_quote_request_birthdate( $data ) {
    $canonical = adr_quote_request_format_french_date_value( adr_quote_request_value( $data, array( 'date_naissance' ) ) );
    if ( $canonical !== '' ) {
        return $canonical;
    }

    $legacy = adr_quote_request_format_french_date_value( adr_quote_request_value( $data, array( 'mf-date' ) ), 'legacy_mdy' );
    if ( $legacy !== '' ) {
        return $legacy;
    }

    return '';
}

function adr_quote_request_address( $data ) {
    $street = adr_quote_request_value( $data, array( 'adresse' ) );
    $postal = adr_quote_request_value( $data, array( 'code-postal', 'code_postal' ) );
    $city = adr_quote_request_value( $data, array( 'mf-text', 'ville' ) );
    $postal_city = trim( $postal . ' ' . $city );

    return trim( implode( "\n", array_filter( array( $street, $postal_city ) ) ) );
}

function adr_site_request_source_label( $form_id ) {
    return adr_site_request_kind_for_form_id( $form_id ) === 'contact' ? 'Contact' : 'Devis';
}

function adr_quote_request_consent( $data ) {
    $lines = array();
    $contact = adr_quote_request_value( $data, array( 'contact_consent' ) );
    $rgpd = adr_quote_request_value( $data, array( 'rgpd_consent', 'mf-gdpr-consent' ) );

    if ( $contact !== '' ) {
        $lines[] = 'Contact: ' . $contact;
    }

    if ( $rgpd !== '' ) {
        $lines[] = 'RGPD: ' . $rgpd;
    }

    return implode( "\n", $lines );
}

function adr_quote_request_geolocation( $data ) {
    $parts = array();

    foreach ( array(
        'requester_geo_city',
        'requester_geo_region',
        'requester_geo_postal_code',
        'requester_geo_country',
    ) as $key ) {
        $value = adr_quote_request_value( $data, array( $key ) );
        if ( $value !== '' ) {
            $parts[] = $value;
        }
    }

    $location = implode( ', ', array_unique( $parts ) );
    $coordinates = trim(
        adr_quote_request_value( $data, array( 'requester_geo_latitude' ) )
        . ', '
        . adr_quote_request_value( $data, array( 'requester_geo_longitude' ) ),
        " \t\n\r\0\x0B,"
    );
    $timezone = adr_quote_request_value( $data, array( 'requester_geo_timezone' ) );

    $lines = array();
    if ( $location !== '' ) {
        $lines[] = $location;
    }
    if ( $coordinates !== '' ) {
        $lines[] = 'Coordonnées: ' . $coordinates;
    }
    if ( $timezone !== '' ) {
        $lines[] = 'Fuseau: ' . $timezone;
    }

    return implode( "\n", $lines );
}

function adr_quote_request_normalized( $data, $post = null ) {
    $type_devis = adr_quote_request_value( $data, array( 'type_devis' ) );
    $date = '';
    $form_id = ADR_QUOTE_REQUESTS_FORM_ID;

    if ( $post instanceof WP_Post ) {
        $date = adr_quote_request_format_french_datetime( mysql2date( 'Y-m-d H:i:s', $post->post_date ) );
        $form_id = adr_quote_request_form_id_for_post( $post->ID );
    }

    return array(
        'id'                  => $post instanceof WP_Post ? (string) $post->ID : '',
        'date'                => $date,
        'source'              => adr_site_request_source_label( $form_id ),
        'civilite'            => adr_quote_request_value( $data, array( 'mf-checkbox', 'civilite' ) ),
        'nom'                 => adr_quote_request_value( $data, array( 'nom' ) ),
        'prenom'              => adr_quote_request_value( $data, array( 'prenom' ) ),
        'email'               => adr_quote_request_value( $data, array( 'mf-email', 'email' ) ),
        'telephone'           => adr_quote_request_value( $data, array( 'telephone', 'tel', 'phone' ) ),
        'contact_preference'  => adr_quote_request_contact_label( adr_quote_request_value( $data, array( 'contact_preference' ) ) ),
        'type_devis'          => adr_quote_request_type_label( $type_devis ),
        'date_naissance'      => adr_quote_request_birthdate( $data ),
        'fumeur'              => adr_quote_request_value( $data, array( 'fumeur' ) ),
        'banque'              => adr_quote_request_value( $data, array( 'banque' ) ),
        'profession'          => adr_quote_request_value( $data, array( 'mf-select', 'profession' ) ),
        'adresse'             => adr_quote_request_address( $data ),
        'message'             => adr_quote_request_value( $data, array( 'message', 'mf-textarea' ) ),
        'consentement'        => adr_quote_request_consent( $data ),
        'requester_ip'        => adr_quote_request_value( $data, array( 'requester_ip' ) ),
        'requester_geolocation' => adr_quote_request_geolocation( $data ),
        'requester_cf_ray'    => adr_quote_request_value( $data, array( 'requester_cf_ray' ) ),
    );
}

function adr_quote_requests_rows() {
    $rows = array();
    $after = adr_quote_requests_export_after();

    foreach ( adr_quote_requests_entries( $after ) as $entry ) {
        $rows[] = adr_quote_request_normalized( adr_quote_request_data_for_post( $entry->ID ), $entry );
    }

    return $rows;
}

function adr_quote_requests_headers() {
    return array(
        'date'               => 'Date',
        'id'                 => 'ID',
        'source'             => 'Source',
        'civilite'           => 'Civilité',
        'nom'                => 'Nom',
        'prenom'             => 'Prénom',
        'email'              => 'E-mail',
        'telephone'          => 'Téléphone',
        'contact_preference' => 'Communication',
        'type_devis'         => 'Type de devis',
        'date_naissance'     => 'Naissance',
        'fumeur'             => 'Fumeur',
        'banque'             => 'Banque',
        'profession'         => 'Profession',
        'adresse'            => 'Adresse',
        'message'            => 'Message',
        'consentement'       => 'Consentement',
        'requester_ip'       => 'IP demandeur',
        'requester_geolocation' => 'Géolocalisation IP',
        'requester_cf_ray'   => 'Cloudflare Ray ID',
    );
}

function adr_render_quote_requests_csv() {
    $headers = adr_quote_requests_headers();
    $rows = adr_quote_requests_rows();
    $format = adr_quote_requests_export_format();

    if ( $format === 'tsv' ) {
        header( 'Content-Type: text/tab-separated-values; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="demandes-site-' . gmdate( 'Ymd-His' ) . '.tsv"' );
    } else {
        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="demandes-site-' . gmdate( 'Ymd-His' ) . '.csv"' );
    }

    $output = fopen( 'php://output', 'w' );
    fprintf( $output, "\xEF\xBB\xBF" );
    fputcsv( $output, array_values( $headers ), $format === 'tsv' ? "\t" : ';' );

    foreach ( $rows as $row ) {
        $line = array();
        foreach ( $headers as $key => $label ) {
            $line[] = isset( $row[ $key ] ) ? $row[ $key ] : '';
        }
        fputcsv( $output, $line, $format === 'tsv' ? "\t" : ';' );
    }

    fclose( $output );
}

function adr_update_quote_admin_email( $args ) {
    $subject = isset( $args['subject'] ) ? (string) $args['subject'] : '';
    $message = isset( $args['message'] ) ? (string) $args['message'] : '';
    $form_id = adr_quote_admin_email_form_id( $subject, $message );

    if ( $form_id === '' ) {
        return $args;
    }

    if ( ! adr_quote_admin_email_has_admin_recipient( $args ) ) {
        return $args;
    }

    $data = adr_quote_admin_email_submission_data( $form_id );
    if ( empty( $data ) ) {
        return $args;
    }

    $args['to'] = ADR_QUOTE_REQUESTS_ADMIN_RECIPIENT;
    $args['subject'] = adr_quote_admin_email_subject( $form_id );
    $args['headers'] = adr_quote_admin_email_headers();
    $args['message'] = adr_build_quote_admin_email_message( $data, $form_id );

    return $args;
}

function adr_quote_admin_email_form_id( $subject, $message ) {
    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
    if ( preg_match( '#/metform/v1/entries/insert/(\d+)#', $request_uri, $matches ) ) {
        $form_id = (string) $matches[1];
        if ( adr_site_request_kind_for_form_id( $form_id ) !== '' ) {
            return $form_id;
        }
    }

    if ( $subject === "Contact pour devis d'assurance sur votre site internet" && stripos( $message, 'Demande de devis' ) !== false ) {
        return ADR_QUOTE_REQUESTS_FORM_ID;
    }

    if ( stripos( $subject, 'message' ) !== false || stripos( $message, 'ENVOYER UN MESSAGE' ) !== false ) {
        return ADR_CONTACT_REQUESTS_FORM_ID;
    }

    return '';
}

function adr_quote_admin_email_has_admin_recipient( $args ) {
    $aliases = array(
        ADR_QUOTE_REQUESTS_ADMIN_RECIPIENT,
        'contacts@assurancesderueil.fr',
    );
    $to = isset( $args['to'] ) ? $args['to'] : array();
    $recipients = is_array( $to ) ? $to : explode( ',', (string) $to );

    foreach ( $recipients as $recipient ) {
        foreach ( $aliases as $alias ) {
            if ( stripos( trim( $recipient ), $alias ) !== false ) {
                return true;
            }
        }
    }

    return false;
}

function adr_quote_admin_email_submission_data( $form_id ) {
    if ( ! empty( $_POST ) && is_array( $_POST ) ) {
        return adr_site_request_merge_live_payload_fields( wp_unslash( $_POST ), $form_id );
    }

    return adr_quote_admin_email_latest_entry_data( $form_id );
}

function adr_quote_admin_email_latest_entry_data( $form_id ) {
    global $wpdb;

    $post_id = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT p.ID
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} m
                ON p.ID = m.post_id
               AND m.meta_key = %s
               AND m.meta_value = %s
             WHERE p.post_type = %s
               AND p.post_status = %s
               AND p.post_date_gmt >= %s
             ORDER BY p.post_date_gmt DESC
             LIMIT 1",
            'metform_entries__form_id',
            $form_id,
            'metform-entry',
            'publish',
            gmdate( 'Y-m-d H:i:s', time() - 10 * MINUTE_IN_SECONDS )
        )
    );

    if ( ! $post_id ) {
        return array();
    }

    return adr_quote_request_data_for_post( (int) $post_id );
}

function adr_quote_admin_email_subject( $form_id ) {
    return adr_site_request_kind_for_form_id( $form_id ) === 'contact'
        ? 'Nouveau message depuis le site - Assurances de Rueil'
        : 'Nouvelle demande de devis - Assurances de Rueil';
}

function adr_quote_admin_email_headers() {
    return array(
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: Assurances de Rueil <' . ADR_QUOTE_REQUESTS_ADMIN_RECIPIENT . '>',
        'Reply-To: Assurances de Rueil <' . ADR_QUOTE_REQUESTS_ADMIN_RECIPIENT . '>',
    );
}

function adr_quote_admin_email_rows( $data, $form_id = ADR_QUOTE_REQUESTS_FORM_ID ) {
    $request = adr_quote_request_normalized( $data );
    $rows = array(
        array( 'Nom', $request['nom'] ),
        array( 'Prénom', $request['prenom'] ),
        array( 'E-mail', $request['email'] ),
    );

    if ( $request['telephone'] !== '' ) {
        $rows[] = array( 'Téléphone', $request['telephone'] );
    }

    if ( $request['requester_ip'] !== '' ) {
        $rows[] = array( 'IP demandeur', $request['requester_ip'] );
    }

    if ( $request['requester_geolocation'] !== '' ) {
        $rows[] = array( 'Géolocalisation IP', $request['requester_geolocation'] );
    }

    if ( adr_site_request_kind_for_form_id( $form_id ) === 'contact' ) {
        $rows[] = array( 'Adresse', $request['adresse'] );
        $rows[] = array( 'Message', $request['message'] );
        $rows[] = array( 'Consentement', $request['consentement'] );

        return $rows;
    }

    array_unshift( $rows, array( 'Civilité', $request['civilite'] ) );
    $rows[] = array( 'Choix de communication', $request['contact_preference'] );

    if ( $request['type_devis'] !== '' ) {
        $rows[] = array( 'Type de devis', $request['type_devis'] );
    }

    $rows[] = array( 'Date de naissance', $request['date_naissance'] );
    $rows[] = array( 'Êtes-vous fumeur ?', $request['fumeur'] );
    $rows[] = array( 'Votre banque', $request['banque'] );
    $rows[] = array( 'Votre profession', $request['profession'] );
    $rows[] = array( 'Adresse', $request['adresse'] );
    $rows[] = array( 'Consentement', $request['consentement'] );

    return $rows;
}

function adr_build_quote_admin_email_message( $data, $form_id = ADR_QUOTE_REQUESTS_FORM_ID ) {
    $is_contact = adr_site_request_kind_for_form_id( $form_id ) === 'contact';
    $body  = "<html><body><h2 style='text-align: center;'>" . ( $is_contact ? 'Message depuis le site' : 'Demande de devis' ) . '</h2>';
    $body .= "<h4 style='text-align: center;'>" . ( $is_contact ? 'Nouveau message du formulaire contact' : 'Nouveau contact pour un devis' ) . '</h4>';
    $body .= '<p style="text-align:center;margin:16px 0 20px;"><a href="' . esc_url( adr_quote_requests_url() ) . '" style="display:inline-block;border-radius:6px;background:#0A4464;color:#ffffff;padding:10px 16px;text-decoration:none;font-weight:bold;">Télécharger le CSV des demandes du site</a></p>';
    $body .= '<div style="border-left:5px solid #2EB5AB;padding-left:5px;">';
    $body .= '<table width="100%" cellpadding="5" cellspacing="0" bgcolor="#FFFFFF" style="border: 1px solid #EAF2FA; word-break: break-word;"><tbody>';

    foreach ( adr_quote_admin_email_rows( $data, $form_id ) as $row ) {
        $value = $row[1] === '' ? 'Non renseigné' : $row[1];
        $body .= '<tr bgcolor="#EAF2FA"><td colspan="2"><strong>' . esc_html( $row[0] ) . '</strong></td></tr>';
        $body .= '<tr bgcolor="#FFFFFF"><td width="20">&nbsp;</td><td>' . nl2br( esc_html( $value ) ) . '</td></tr>';
    }

    $body .= '</tbody></table></div>';
    $body .= '<p style="color:#66758a;font-size:12px;text-align:center;">Lien confidentiel destiné à ' . esc_html( ADR_QUOTE_REQUESTS_ADMIN_RECIPIENT ) . '.</p>';
    $body .= '</body></html>';

    return $body;
}
