<?php

if ( !defined( 'WP_DEBUG' ) ) {
	die( 'Direct access forbidden.' );
}


add_action( 'wp_enqueue_scripts', 'instive_child_enqueue_styles', 99 );
function instive_child_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_stylesheet_directory_uri() . '/style.css' );
 
}

// adr-webbyelie-credits-v118-9: discreet creator credits on legal and contact pages.
add_action( 'wp_head', 'adr_render_webbyelie_credit_styles' );
add_filter( 'the_content', 'adr_append_webbyelie_credit', 30 );

function adr_is_webbyelie_credit_page() {
    return is_page( 'mentions-legales' ) || is_page( 'courtier-en-assurances-de-rueil-malmaison' );
}

function adr_render_webbyelie_credit_styles() {
    if ( is_admin() || ! adr_is_webbyelie_credit_page() ) {
        return;
    }
    ?>
    <style id="adr-webbyelie-credits-v118-9">
        .adr-webbyelie-credit {
            width: min(100%, 1180px);
            margin: clamp(18px, 3vw, 28px) auto 0;
            border: 1px solid rgba(198, 212, 230, 0.92);
            border-radius: 8px;
            padding: clamp(18px, 3vw, 26px);
            background: rgba(255, 255, 255, 0.78);
            color: #07192f;
            box-shadow: 0 18px 44px rgba(7, 25, 47, 0.08);
        }
        .adr-webbyelie-credit h2 {
            margin: 0 0 8px;
            color: #0a3f81;
            font-size: clamp(20px, 2.4vw, 28px);
            line-height: 1.15;
            letter-spacing: 0;
        }
        .adr-webbyelie-credit p {
            margin: 0;
            color: #66758a;
            font-size: 15px;
            line-height: 1.55;
        }
        .adr-webbyelie-credit a {
            color: #0a3f81;
            font-weight: 800;
            text-decoration-thickness: 2px;
            text-underline-offset: 3px;
        }
    </style>
    <?php
}

function adr_append_webbyelie_credit( $content ) {
    if ( is_admin() || ! is_main_query() || ! in_the_loop() || ! adr_is_webbyelie_credit_page() ) {
        return $content;
    }

    if ( strpos( $content, 'adr-webbyelie-credit' ) !== false || stripos( $content, 'Web-By-Elie' ) !== false ) {
        return $content;
    }

    if ( is_page( 'mentions-legales' ) ) {
        return $content . adr_webbyelie_credit_html(
            'legal',
            'Crédits du site',
            'Refonte du site et maintenance : <a href="https://web-by-elie.com/" rel="external noopener">Web-By-Elie.com</a>.'
        );
    }

    return $content . adr_webbyelie_credit_html(
        'contact',
        'Crédits du site',
        'Site rafraîchi et maintenu par <a href="https://web-by-elie.com/" rel="external noopener">Web-By-Elie</a>.'
    );
}

function adr_webbyelie_credit_html( $context, $title, $body ) {
    return '<section class="adr-webbyelie-credit adr-webbyelie-credit-' . esc_attr( $context ) . '" aria-label="Crédits du site"><h2>' . esc_html( $title ) . '</h2><p>' . wp_kses_post( $body ) . '</p></section>';
}

// adr-quote-requests-v118-9: private quote-request CSV and ordered admin email.
define( 'ADR_QUOTE_REQUESTS_VERSION', '119.3' );
define( 'ADR_QUOTE_REQUESTS_FORM_ID', '2073' );
define( 'ADR_QUOTE_REQUESTS_MIN_DATE', '2026-01-01 00:00:00' );
define( 'ADR_QUOTE_REQUESTS_PATH', 'demandes-de-devis' );
define( 'ADR_QUOTE_REQUESTS_ACCESS_KEY', '57e957f2bb42963c872a28f1e061dbf6bc06757514bf97173ab571b3a31ee8c3' );
define( 'ADR_QUOTE_REQUESTS_ACCESS_KEY_HASH', '9fc8c2c9e7e00dae0fa807614cbbaa8953239c1454295b2dab7e88e3bedc2876' );
define( 'ADR_QUOTE_REQUESTS_ADMIN_RECIPIENT', 'contact@assurancesderueil.fr' );

add_action( 'template_redirect', 'adr_maybe_render_quote_requests_page', 0 );
add_filter( 'wp_mail', 'adr_update_quote_admin_email', 20 );

function adr_quote_requests_url() {
    return add_query_arg(
        'key',
        ADR_QUOTE_REQUESTS_ACCESS_KEY,
        home_url( '/' . ADR_QUOTE_REQUESTS_PATH . '/' )
    );
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

function adr_quote_requests_entries() {
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
                    'after'     => ADR_QUOTE_REQUESTS_MIN_DATE,
                    'inclusive' => true,
                    'column'    => 'post_date',
                ),
            ),
            'meta_query'       => array(
                array(
                    'key'   => 'metform_entries__form_id',
                    'value' => ADR_QUOTE_REQUESTS_FORM_ID,
                ),
            ),
        )
    );
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

function adr_quote_request_birthdate( $data ) {
    $legacy = adr_quote_request_value( $data, array( 'mf-date' ) );
    if ( $legacy !== '' ) {
        return $legacy;
    }

    $canonical = adr_quote_request_value( $data, array( 'date_naissance' ) );
    if ( preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $canonical, $matches ) ) {
        return $matches[3] . '/' . $matches[2] . '/' . $matches[1];
    }

    return $canonical;
}

function adr_quote_request_address( $data ) {
    $street = adr_quote_request_value( $data, array( 'adresse' ) );
    $postal = adr_quote_request_value( $data, array( 'code-postal', 'code_postal' ) );
    $city = adr_quote_request_value( $data, array( 'mf-text', 'ville' ) );
    $postal_city = trim( $postal . ' ' . $city );

    return trim( implode( "\n", array_filter( array( $street, $postal_city ) ) ) );
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

function adr_quote_request_normalized( $data, $post = null ) {
    $type_devis = adr_quote_request_value( $data, array( 'type_devis' ) );
    $date = '';

    if ( $post instanceof WP_Post ) {
        $date = mysql2date( 'd/m/Y H:i', $post->post_date );
    }

    return array(
        'id'                  => $post instanceof WP_Post ? (string) $post->ID : '',
        'date'                => $date,
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
        'consentement'        => adr_quote_request_consent( $data ),
    );
}

function adr_quote_requests_rows() {
    $rows = array();

    foreach ( adr_quote_requests_entries() as $entry ) {
        $rows[] = adr_quote_request_normalized( adr_quote_request_data_for_post( $entry->ID ), $entry );
    }

    return $rows;
}

function adr_quote_requests_headers() {
    return array(
        'date'               => 'Date',
        'id'                 => 'ID',
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
        'consentement'       => 'Consentement',
    );
}

function adr_render_quote_requests_csv() {
    $headers = adr_quote_requests_headers();
    $rows = adr_quote_requests_rows();

    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename="demandes-de-devis-' . gmdate( 'Ymd-His' ) . '.csv"' );

    $output = fopen( 'php://output', 'w' );
    fprintf( $output, "\xEF\xBB\xBF" );
    fputcsv( $output, array_values( $headers ), ';' );

    foreach ( $rows as $row ) {
        $line = array();
        foreach ( $headers as $key => $label ) {
            $line[] = isset( $row[ $key ] ) ? $row[ $key ] : '';
        }
        fputcsv( $output, $line, ';' );
    }

    fclose( $output );
}

function adr_update_quote_admin_email( $args ) {
    $subject = isset( $args['subject'] ) ? (string) $args['subject'] : '';
    $message = isset( $args['message'] ) ? (string) $args['message'] : '';

    if ( $subject !== "Contact pour devis d'assurance sur votre site internet" ) {
        return $args;
    }

    if ( stripos( $message, 'Demande de devis' ) === false || ! adr_quote_admin_email_has_recipient( $args, ADR_QUOTE_REQUESTS_ADMIN_RECIPIENT ) ) {
        return $args;
    }

    $data = adr_quote_admin_email_submission_data();
    if ( empty( $data ) ) {
        return $args;
    }

    $args['message'] = adr_build_quote_admin_email_message( $data );

    return $args;
}

function adr_quote_admin_email_has_recipient( $args, $needle ) {
    $to = isset( $args['to'] ) ? $args['to'] : array();
    $recipients = is_array( $to ) ? $to : explode( ',', (string) $to );

    foreach ( $recipients as $recipient ) {
        if ( stripos( trim( $recipient ), $needle ) !== false ) {
            return true;
        }
    }

    return false;
}

function adr_quote_admin_email_submission_data() {
    if ( ! empty( $_POST ) && is_array( $_POST ) ) {
        return wp_unslash( $_POST );
    }

    return adr_quote_admin_email_latest_entry_data();
}

function adr_quote_admin_email_latest_entry_data() {
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
            ADR_QUOTE_REQUESTS_FORM_ID,
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

function adr_quote_admin_email_rows( $data ) {
    $request = adr_quote_request_normalized( $data );
    $rows = array(
        array( 'Civilité', $request['civilite'] ),
        array( 'Nom', $request['nom'] ),
        array( 'Prénom', $request['prenom'] ),
        array( 'E-mail', $request['email'] ),
    );

    if ( $request['telephone'] !== '' ) {
        $rows[] = array( 'Téléphone', $request['telephone'] );
    }

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

function adr_build_quote_admin_email_message( $data ) {
    $body  = "<html><body><h2 style='text-align: center;'>Demande de devis</h2>";
    $body .= "<h4 style='text-align: center;'>Nouveau contact pour un devis</h4>";
    $body .= '<p style="text-align:center;margin:16px 0 20px;"><a href="' . esc_url( adr_quote_requests_url() ) . '" style="display:inline-block;border-radius:6px;background:#0A4464;color:#ffffff;padding:10px 16px;text-decoration:none;font-weight:bold;">Télécharger le CSV des demandes</a></p>';
    $body .= '<div style="border-left:5px solid #2EB5AB;padding-left:5px;">';
    $body .= '<table width="100%" cellpadding="5" cellspacing="0" bgcolor="#FFFFFF" style="border: 1px solid #EAF2FA; word-break: break-word;"><tbody>';

    foreach ( adr_quote_admin_email_rows( $data ) as $row ) {
        $value = $row[1] === '' ? 'Non renseigné' : $row[1];
        $body .= '<tr bgcolor="#EAF2FA"><td colspan="2"><strong>' . esc_html( $row[0] ) . '</strong></td></tr>';
        $body .= '<tr bgcolor="#FFFFFF"><td width="20">&nbsp;</td><td>' . nl2br( esc_html( $value ) ) . '</td></tr>';
    }

    $body .= '</tbody></table></div>';
    $body .= '<p style="color:#66758a;font-size:12px;text-align:center;">Lien confidentiel destiné à ' . esc_html( ADR_QUOTE_REQUESTS_ADMIN_RECIPIENT ) . '.</p>';
    $body .= '</body></html>';

    return $body;
}

// adr-public-wording-normalization-v119-3: keep the agency brand plural and align the approved public wording/visual cleanup.
add_action( 'template_redirect', 'adr_start_brand_phrase_buffer', 0 );
function adr_start_brand_phrase_buffer() {
    if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
        return;
    }

    ob_start( 'adr_normalize_brand_phrase' );
}

function adr_normalize_brand_phrase( $html ) {
    $html = str_replace(
        array(
            'Besoin d’une société de courtage en assurances à Rueil-Malmaison ?',
            'Besoin d&#8217;une société de courtage en assurances à Rueil-Malmaison ?',
            'Quel est le rôle d’une société de courtage en assurances à Rueil-Malmaison ?',
            'Quel est le rôle d&#8217;une société de courtage en assurances à Rueil-Malmaison ?',
            'Société de courtage indépendante depuis quatre générations',
            'société de courtage indépendante depuis quatre générations',
            'Société de courtage indépendante au service des intérêts de l’assuré.',
            'Société de courtage indépendante au service des intérêts de l&#8217;assuré.',
            'société de courtage indépendante au service des intérêts de l’assuré.',
            'société de courtage indépendante au service des intérêts de l&#8217;assuré.',
            'Société de courtage en assurances à Rueil-Malmaison',
            'société de courtage en assurances à Rueil-Malmaison',
            'Société de courtage en assurances',
            'société de courtage en assurances',
            'Société de courtage',
            'société de courtage',
            'Societe de courtage',
            'societe de courtage',
            'Assurance à Rueil-Malmaison',
            'Assurance à Rueil Malmaison',
            'Assurance De Rueil-Malmaison',
            'Assurance de Rueil-Malmaison',
            'Assurance De Rueil',
            'Assurance de Rueil',
        ),
        array(
            'Besoin d’un courtier en assurances à Rueil-Malmaison ?',
            'Besoin d&#8217;un courtier en assurances à Rueil-Malmaison ?',
            'Quel est le rôle d’un courtier en assurances ?',
            'Quel est le rôle d&#8217;un courtier en assurances ?',
            'Courtier indépendant depuis quatre générations',
            'courtier indépendant depuis quatre générations',
            'Courtier indépendant au service des intérêts de l’assuré.',
            'Courtier indépendant au service des intérêts de l&#8217;assuré.',
            'courtier indépendant au service des intérêts de l’assuré.',
            'courtier indépendant au service des intérêts de l&#8217;assuré.',
            'Courtier en assurances à Rueil-Malmaison',
            'courtier en assurances à Rueil-Malmaison',
            'Courtier en assurances',
            'courtier en assurances',
            'Courtier',
            'courtier',
            'Courtier',
            'courtier',
            'Assurances de Rueil',
            'Assurances de Rueil',
            'Assurances De Rueil-Malmaison',
            'Assurances de Rueil-Malmaison',
            'Assurances De Rueil',
            'Assurances de Rueil',
        ),
        $html
    );

    $html = str_replace(
        array(
            'Courtier indépendante',
            'courtier indépendante',
            'd’une courtier',
            'd&#8217;une courtier',
            'd&rsquo;une courtier',
            'D’une courtier',
            'D&#8217;une courtier',
            'D&rsquo;une courtier',
            'Une courtier',
            'une courtier',
            'Quel est le rôle d’un courtier en assurances à Rueil-Malmaison ?',
            'Quel est le rôle d&#8217;un courtier en assurances à Rueil-Malmaison ?',
            'Quel est le rôle d&rsquo;un courtier en assurances à Rueil-Malmaison ?',
            'Quels contrats Assurances de Rueil peut-il accompagner ?',
            'Quels contrats Assurances de Rueil peut-il accompagner?',
            'Quels contrats Assurances de Rueil peut-il accompagner',
            '<h1><span class="adr-fr">Courtier en assurances à Rueil-Malmaison</span><span class="adr-en adr-block">Insurance brokerage firm in Rueil-Malmaison</span></h1>',
            'Ouvert du lundi au vendredi, de 9H00 à 12H30 et de 14H00 à 18H30.',
            'Ouvert du lundi au vendredi, de 9H00 à 12H30 et de 14H00 à 18H30',
            'Open Monday to Friday, 9:00-12:30 and 14:00-18:30.',
            'Open Monday to Friday, 9:00-12:30 and 14:00-18:30',
            'Le cabinet est ouvert du lundi au vendredi, de 9H00 à 12H30 et de 14H00 à 18H30.',
            'The agency is open Monday to Friday, 9:00-12:30 and 14:00-18:30.',
            'de 9H00 à 12H30 et de 14H00 à 18H30',
            '9H00-12H30 et 14H00-18H30',
            '9:00-12:30 and 14:00-18:30',
            '<p><strong>Fax</strong><br>+33 1 47 51 00 78</p>',
            '<span>+33 1 47 51 00 78</span>',
            '"faxNumber":"+33 1 47 51 00 78",',
            '"faxNumber": "+33 1 47 51 00 78",',
            '"faxNumber":"+33 1 47 51 00 78"',
            '"faxNumber": "+33 1 47 51 00 78"',
            '<p class="adr-kicker"><span class="adr-fr">Assurance de prêt</span><span class="adr-en">Loan insurance</span></p>',
            '<p class="adr-kicker"><span class="adr-fr">Particuliers</span><span class="adr-en">Individuals</span></p>',
            '<p class="adr-kicker"><span class="adr-fr">Professionnels</span><span class="adr-en">Professionals</span></p>',
            '<h1><span class="adr-fr">Assurance de prêt à Rueil-Malmaison</span><span class="adr-en adr-block">Loan insurance in Rueil-Malmaison</span></h1>',
            '<h1><span class="adr-fr">Assurance particuliers à Rueil-Malmaison</span><span class="adr-en adr-block">Personal insurance in Rueil-Malmaison</span></h1>',
            '<h1><span class="adr-fr">Assurance entreprise à Rueil-Malmaison</span><span class="adr-en adr-block">Business insurance in Rueil-Malmaison</span></h1>',
            'Assurances de prêts',
            'Assurance de Prêt à Rueil-Malmaison',
            'Assurance de prêt à Rueil-Malmaison',
            'Demande de devis assurance',
            'Demande de devis assurance à Rueil-Malmaison',
            'Demande de Devis Assurance à Rueil-Malmaison',
            'Demande de devis d&rsquo;assurance',
            'Demande de devis d&#8217;assurance',
            'Assurance des particuliers',
            'Assurance particuliers à Rueil-Malmaison',
            'Assurance particuliers',
            'Assurance entreprise à Rueil-Malmaison',
            'Assurance entreprise',
            'Protégez votre activité avec Assurances de Rueil : multirisque, flotte auto, RC pro, loyers impayés, prévoyance collective.',
        ),
        array(
            'Courtier indépendant',
            'courtier indépendant',
            'd’un courtier',
            'd&#8217;un courtier',
            'd&rsquo;un courtier',
            'D’un courtier',
            'D&#8217;un courtier',
            'D&rsquo;un courtier',
            'Un courtier',
            'un courtier',
            'Quel est le rôle d’un courtier en assurances ?',
            'Quel est le rôle d&#8217;un courtier en assurances ?',
            'Quel est le rôle d&rsquo;un courtier en assurances ?',
            'Quels types de contrats sont proposés par Assurances de Rueil ?',
            'Quels types de contrats sont proposés par Assurances de Rueil ?',
            'Quels types de contrats sont proposés par Assurances de Rueil ?',
            '<h1><span class="adr-fr">Courtier en assurances</span><span class="adr-en adr-block">Insurance broker</span></h1>',
            'Ouvert du lundi au vendredi, de 9:00 à 12:30 et de 14:00 à 18:00.',
            'Ouvert du lundi au vendredi, de 9:00 à 12:30 et de 14:00 à 18:00',
            'Open Monday to Friday, 9:00-12:30 and 14:00-18:00.',
            'Open Monday to Friday, 9:00-12:30 and 14:00-18:00',
            'Le cabinet est ouvert du lundi au vendredi, de 9:00 à 12:30 et de 14:00 à 18:00.',
            'The agency is open Monday to Friday, 9:00-12:30 and 14:00-18:00.',
            'de 9:00 à 12:30 et de 14:00 à 18:00',
            '9:00-12:30 et 14:00-18:00',
            '9:00-12:30 and 14:00-18:00',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '<h1><span class="adr-fr">Assurance de prêt</span><span class="adr-en adr-block">Loan insurance</span></h1>',
            '<h1><span class="adr-fr">Assurances des particuliers</span><span class="adr-en adr-block">Personal insurance</span></h1>',
            '<h1><span class="adr-fr">Assurances entreprise</span><span class="adr-en adr-block">Business insurance</span></h1>',
            'Assurance de prêt',
            'Assurance de prêt',
            'Assurance de prêt',
            'Demande de devis d’assurance',
            'Demande de devis d’assurance',
            'Demande de devis d’assurance',
            'Demande de devis d’assurance',
            'Demande de devis d’assurance',
            'Assurances des particuliers',
            'Assurances des particuliers',
            'Assurances des particuliers',
            'Assurances entreprise',
            'Assurances entreprise',
            'Protégez votre activité avec Assurances de Rueil : multirisque, flotte auto, RC pro, prévoyance collective.',
        ),
        $html
    );

    $html = str_replace(
        array(
            'https://assurancesderueil.fr/wp-content/uploads/2022/08/assurance-biens.gif',
            'https://assurancesderueil.fr/wp-content/uploads/2022/08/assurance-personnelle.gif',
            'https://assurancesderueil.fr/wp-content/uploads/2022/08/assurance-professionnelle.gif',
            'src="wp-content/uploads/2022/08/assurance-biens.gif"',
            'src="wp-content/uploads/2022/08/assurance-personnelle.gif"',
            'src="wp-content/uploads/2022/08/assurance-professionnelle.gif"',
        ),
        array(
            'https://ec92009.github.io/ADR/mock-assets/adr-icon-house-gold.svg',
            'https://ec92009.github.io/ADR/mock-assets/adr-icon-people-gold.svg',
            'https://ec92009.github.io/ADR/mock-assets/adr-icon-briefcase-gold.svg',
            'src="https://ec92009.github.io/ADR/mock-assets/adr-icon-house-gold.svg"',
            'src="https://ec92009.github.io/ADR/mock-assets/adr-icon-people-gold.svg"',
            'src="https://ec92009.github.io/ADR/mock-assets/adr-icon-briefcase-gold.svg"',
        ),
        $html
    );

    $html = preg_replace(
        '#\s*<input class="adr-switch-input" id="adr-lang-toggle" type="checkbox"[^>]*>#',
        '',
        $html
    );

    $html = preg_replace(
        '#\s*<label class="adr-switch-label" for="adr-lang-toggle">FR/EN</label>#',
        '',
        $html
    );

    $html = preg_replace(
        '#\s*<span class="adr-en(?:\s+adr-block)?">.*?</span>#s',
        '',
        $html
    );

    $html = str_replace(
        '</head>',
        '<style id="adr-fr-only-v119-3">#adr-lang-toggle,label[for="adr-lang-toggle"],.adr-en{display:none!important;}</style>' . "\n</head>",
        $html
    );

    $html = preg_replace(
        '#\s*<a[^>]+href=["\']tel:\+?33147510078["\'][^>]*>\s*\+33 1 47 51 00 78\s*</a>#',
        '',
        $html
    );

    $html = preg_replace(
        '#\s*<article class="adr-detail-card">\s*<h2><span class="adr-fr">Loyers impayés</span><span class="adr-en">Unpaid rent</span></h2>\s*<ul>\s*<li><span class="adr-fr">garantie des loyers impayés jusqu&rsquo;à 30 mois ;</span>.*?</ul>\s*</article>#s',
        '',
        $html
    );

    $html = preg_replace(
        '#\s*<details><summary><span class="adr-fr">Le cabinet est-il situé à Rueil-Malmaison \?</span><span class="adr-en">Is the agency located in Rueil-Malmaison\?</span></summary><p><span class="adr-fr">Oui\. Assurances de Rueil est situé au 75 avenue Victor Hugo, 92500 Rueil-Malmaison, dans les Hauts-de-Seine\.</span><span class="adr-en adr-block">Yes\. Assurances de Rueil is located at 75 avenue Victor Hugo, 92500 Rueil-Malmaison, in Hauts-de-Seine\.</span></p></details>#s',
        '',
        $html
    );

    $html = str_replace(
        '<details><summary><span class="adr-fr">Le cabinet est-il situé à Rueil-Malmaison ?</span><span class="adr-en">Is the agency located in Rueil-Malmaison?</span></summary><p><span class="adr-fr">Oui. Assurances de Rueil est situé au 75 avenue Victor Hugo, 92500 Rueil-Malmaison, dans les Hauts-de-Seine.</span><span class="adr-en adr-block">Yes. Assurances de Rueil is located at 75 avenue Victor Hugo, 92500 Rueil-Malmaison, in Hauts-de-Seine.</span></p></details>',
        '',
        $html
    );

    $html = str_replace(
        '<article class="adr-detail-card">
        <h2><span class="adr-fr">Loyers impayés</span><span class="adr-en">Unpaid rent</span></h2>
        <ul>
          <li><span class="adr-fr">garantie des loyers impayés jusqu&rsquo;à 30 mois ;</span><span class="adr-en">unpaid rent cover up to 30 months;</span></li>
          <li><span class="adr-fr">garantie des frais de contentieux, huissier et avocat ;</span><span class="adr-en">legal-cost cover, bailiff and lawyer;</span></li>
          <li><span class="adr-fr">garantie des détériorations immobilières ;</span><span class="adr-en">property-damage cover;</span></li>
          <li><span class="adr-fr">assistance dans la composition du dossier jusqu&rsquo;à la souscription.</span><span class="adr-en">support preparing the file through to subscription.</span></li>
        </ul>
      </article>',
        '',
        $html
    );

    if ( is_front_page() || is_home() ) {
        $html = preg_replace(
            '#\s*<nav class="adr-mini-nav" aria-label="Pages reprises du site original">.*?(?=<section class="adr-bottom")#s',
            "\n",
            $html
        );

        if ( strpos( $html, 'adr-partners-v119-3' ) === false && strpos( $html, 'adr-partners' ) === false ) {
            $partner_style = '<style id="adr-partners-v119-3">
                .adr-partners {
                    width: min(100%, 1180px);
                    margin: 24px auto 0;
                    display: grid;
                    grid-template-columns: minmax(220px, 0.55fr) minmax(0, 1fr);
                    gap: clamp(18px, 3vw, 36px);
                    align-items: center;
                    padding: clamp(20px, 3vw, 32px);
                    border: 1px solid var(--adr-line);
                    border-radius: 8px;
                    background: var(--adr-paper);
                    box-shadow: var(--adr-soft-shadow);
                    backdrop-filter: blur(18px);
                }
                .adr-partners h2 {
                    margin: 0;
                    color: var(--adr-blue);
                    line-height: 1.12;
                    font-size: clamp(24px, 3vw, 34px);
                    letter-spacing: 0;
                }
                .adr-partners p {
                    margin: 0;
                    color: var(--adr-muted);
                    line-height: 1.58;
                }
                .adr-partners-copy {
                    display: grid;
                    gap: 8px;
                }
                .adr-partner-logos {
                    display: grid;
                    grid-template-columns: repeat(4, minmax(120px, 1fr));
                    gap: clamp(12px, 2vw, 20px);
                    align-items: center;
                }
                .adr-partner-logos img {
                    width: 100%;
                    max-width: 180px;
                    margin: 0 auto;
                    border-radius: 4px;
                    object-fit: contain;
                    filter: saturate(0.96);
                }
                @media (max-width: 980px) {
                    .adr-partners {
                        grid-template-columns: 1fr;
                    }
                }
                @media (max-width: 640px) {
                    .adr-partner-logos {
                        grid-template-columns: repeat(2, minmax(120px, 1fr));
                    }
                }
            </style>';

            $partner_html = '<section class="adr-partners" aria-label="Partenaires assureurs">
                <div class="adr-partners-copy">
                    <h2><span class="adr-fr">Partenaires assureurs</span><span class="adr-en">Insurance partners</span></h2>
                    <p><span class="adr-fr">Des partenaires reconnus pour comparer les solutions selon votre profil.</span><span class="adr-en adr-block">Recognized partners to compare solutions according to your profile.</span></p>
                </div>
                <div class="adr-partner-logos" aria-label="Thelem, AXA, April et Generali">
                    <img decoding="async" src="https://ec92009.github.io/ADR/accueil-mock-assets/thelem-assurance-rueil.jpg" alt="Thelem assurances">
                    <img decoding="async" src="https://ec92009.github.io/ADR/accueil-mock-assets/axa-assurance-rueil.jpg" alt="AXA">
                    <img decoding="async" src="https://ec92009.github.io/ADR/accueil-mock-assets/april-assurance-rueil.jpg" alt="April">
                    <img decoding="async" src="https://ec92009.github.io/ADR/accueil-mock-assets/generali-assurance-rueil.jpg" alt="Generali">
                </div>
            </section>';

            $html = str_replace( '</head>', $partner_style . "\n</head>", $html );
            $html = str_replace(
                '<section class="adr-bottom" aria-label="Demande de devis">',
                $partner_html . "\n<section class=\"adr-bottom\" aria-label=\"Demande de devis\">",
                $html
            );
        }
    }

    $html = preg_replace(
        '#<title>.*?</title>#s',
        '<title>Assurances de Rueil</title>',
        $html
    );

    $html = str_replace(
        array( 'v119.2', 'v119-2' ),
        array( 'v119.3', 'v119-3' ),
        $html
    );

    $html = adr_apply_source_truth_page_stage_v119_3( $html );
    $html = adr_apply_source_truth_detail_sections_v119_3( $html );
    $html = adr_apply_source_truth_visual_residuals_v119_3( $html );
    $html = adr_apply_source_truth_dynamic_residuals_v119_3( $html );

    $html = preg_replace(
        '~(<li\b[^>]*>(?:(?!</li>).)*?)(?:\s|&nbsp;|&#160;)+[,;:](\s*(?:</span>|</p>)?\s*</li>)~su',
        '$1$2',
        $html
    );

    return $html;
}

// adr-source-truth-page-stages-v119-3: replace WordPress mock shells with the approved GH.io v119.3 shells.
function adr_apply_source_truth_page_stage_v119_3( $html ) {
    $stage_key = adr_source_truth_stage_key_v119_3();

    if ( $stage_key === '' ) {
        return $html;
    }

    $stages = adr_source_truth_page_stages_v119_3();

    if ( ! isset( $stages[ $stage_key ] ) ) {
        return $html;
    }

    return preg_replace(
        '#<div class="adr-(?:refresh|page)-stage[^"]*">.*?</div>\s*(?=(?:<section class="adr-webbyelie-credit[\s\S]*?</section>\s*)?<div class="copy-right">)#s',
        trim( $stages[ $stage_key ] ) . "\n",
        $html,
        1
    );
}

function adr_source_truth_stage_key_v119_3() {
    if ( is_front_page() || is_home() ) {
        return 'home';
    }

    if ( is_page( 7427 ) || is_page( 'demande-de-devis-assurance-a-rueil-malmaison' ) ) {
        return 'devis';
    }

    if ( is_page( 7358 ) || is_page( 'cabinet-de-courtage-en-assurances-rueil-malmaison' ) ) {
        return 'cabinet';
    }

    if ( is_page( 7754 ) || is_page( 'assurance-de-pret-a-rueil-malmaison' ) ) {
        return 'pret';
    }

    if ( is_page( 7331 ) || is_page( 'assurance-particuliers-rueil-malmaison' ) ) {
        return 'particuliers';
    }

    if ( is_page( 2180 ) || is_page( 'assurance-entreprise-rueil-malmaison' ) ) {
        return 'professionnels';
    }

    if ( is_page( 'courtier-en-assurances-de-rueil-malmaison' ) ) {
        return 'contact';
    }

    if ( is_page( 'mentions-legales' ) ) {
        return 'mentions';
    }

    if ( is_page( 'politique-de-confidentialite' ) ) {
        return 'privacy';
    }

    if ( is_page( 'cookies-traceurs' ) ) {
        return 'cookies';
    }

    return '';
}

function adr_source_truth_page_stages_v119_3() {
    return array(
        'home' => <<<'ADR_STAGE_HOME'
<div class="adr-refresh-stage adr-home-clean-mock">
  <style>
    body.page-id-1887 .ekit-template-content-header { display: none !important; }
    body.page-id-1887 .site-footer,
    body.page-id-1887 footer.footer,
    body.page-id-1887 .ekit-template-content-footer { display: none !important; }
    .adr-refresh-stage,
    .adr-refresh-stage * { box-sizing: border-box; }
    .adr-refresh-stage {
      --adr-blue: #003478;
      --adr-blue-soft: #6da9ff;
      --adr-ink: #07192f;
      --adr-muted: #657386;
      --adr-line: rgba(0, 52, 120, 0.18);
      --adr-paper: rgba(255, 255, 255, 0.86);
      --adr-solid: #ffffff;
      --adr-wash: #eef5fb;
      --adr-gold: #d7a53c;
      --adr-shadow: 0 24px 70px rgba(0, 28, 64, 0.16);
      --adr-soft-shadow: 0 14px 36px rgba(0, 28, 64, 0.1);
      width: 100vw;
      margin-left: calc(50% - 50vw);
      margin-right: calc(50% - 50vw);
      margin-top: -1px;
      padding: clamp(14px, 2vw, 24px) clamp(14px, 2vw, 24px) 64px;
      background: var(--adr-wash);
      color: var(--adr-ink);
      font-family: Rubik, "Open Sans", Arial, sans-serif;
      overflow: clip;
    }
    .adr-refresh-stage:has(#adr-theme-toggle:checked) {
      --adr-blue: #83b7ff;
      --adr-blue-soft: #74a9f7;
      --adr-ink: #edf6ff;
      --adr-muted: #a8b8c8;
      --adr-line: rgba(169, 201, 234, 0.24);
      --adr-paper: rgba(12, 29, 49, 0.82);
      --adr-solid: #0e2135;
      --adr-wash: #06111d;
      --adr-shadow: 0 24px 70px rgba(0, 0, 0, 0.34);
      --adr-soft-shadow: 0 14px 36px rgba(0, 0, 0, 0.24);
    }
    .adr-refresh-stage a { color: inherit; }
    .adr-refresh-stage img { display: block; max-width: 100%; }
    .adr-refresh-stage [id] { scroll-margin-top: 140px; }
    .adr-switch-input { position: absolute; opacity: 0; pointer-events: none; }
    .adr-floating-panel {
      position: sticky;
      top: clamp(12px, 2vw, 24px);
      z-index: 9;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      width: min(100%, 1288px);
      margin: 0 auto 18px;
      border: 1px solid var(--adr-line);
      border-radius: 8px;
      padding: 7px 8px 7px 12px;
      background: var(--adr-paper);
      box-shadow: 0 18px 54px rgba(0, 0, 0, 0.18);
      backdrop-filter: blur(18px);
    }
    .adr-brand { display: inline-flex; align-items: center; gap: 13px; min-width: 0; color: var(--adr-blue); text-decoration: none; }
    .adr-brand img { width: clamp(48px, 5vw, 78px); height: clamp(48px, 5vw, 78px); border-radius: 50%; }
    .adr-brand strong { font-size: clamp(24px, 3vw, 44px); line-height: 1; letter-spacing: 0; white-space: nowrap; }
    .adr-panel-actions { display: flex; align-items: stretch; justify-content: flex-end; gap: 8px; }
    body.admin-bar .adr-floating-panel { top: calc(32px + clamp(12px, 2vw, 24px)); }
    .adr-panel-actions .adr-button,
    .adr-panel-actions .adr-switch-label { height: clamp(44px, 4.6vw, 64px); margin: 0 !important; align-self: stretch; line-height: 1.05; }
    @media (max-width: 782px) { body.admin-bar .adr-floating-panel { top: calc(46px + 10px); } }
    .adr-button,
    .adr-switch-label {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 44px;
      border-radius: 8px;
      padding: 12px 18px;
      border: 1px solid var(--adr-line);
      font-weight: 900;
      line-height: 1.1;
      text-decoration: none;
      cursor: pointer;
      white-space: nowrap;
    }
    .adr-button-primary,
    .adr-button-primary:visited,
    .adr-button-primary:hover,
    .adr-button-primary:focus { border-color: transparent; background: var(--adr-blue); color: #fff !important; box-shadow: 0 16px 34px rgba(0, 52, 120, 0.22); }
    .adr-refresh-stage:has(#adr-theme-toggle:checked) .adr-button-primary { color: #06111d !important; }
    .adr-switch-label { min-width: 58px; background: color-mix(in srgb, var(--adr-blue) 7%, var(--adr-solid)); color: var(--adr-blue); font-size: 13px; }
    .adr-refresh-stage:has(#adr-theme-toggle:checked) label[for="adr-theme-toggle"] { background: var(--adr-blue); color: #06111d; }
    .adr-hero {
      display: grid;
      grid-template-columns: minmax(320px, 0.9fr) minmax(0, 1.1fr);
      width: min(100%, 1288px);
      min-height: min(680px, calc(100vh - 120px));
      margin: 0 auto;
      overflow: hidden;
      border: 1px solid var(--adr-line);
      border-radius: 8px;
      background: var(--adr-solid);
      box-shadow: var(--adr-shadow);
    }
    .adr-hero-media { min-height: 440px; overflow: hidden; background: #d9e7f2; }
    .adr-hero-media img { width: 100%; height: 100%; object-fit: cover; }
    .adr-hero-copy { display: grid; align-content: center; gap: 22px; padding: clamp(28px, 5vw, 72px); }
    .adr-kicker { margin: 0; color: var(--adr-gold); font-size: 14px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.08em; }
    .adr-hero h1 { margin: 0; color: var(--adr-blue); font-size: clamp(25px, 3.36vw, 52px); line-height: 0.97; letter-spacing: 0; }
    .adr-hero p { max-width: 650px; margin: 0; color: var(--adr-muted); font-size: clamp(18px, 2.2vw, 24px); line-height: 1.55; }
    .adr-hero-note {
      display: grid;
      gap: 4px;
      max-width: 620px;
      border-left: 4px solid var(--adr-gold);
      padding: 12px 0 12px 18px;
      background: color-mix(in srgb, var(--adr-gold) 8%, transparent);
    }
    .adr-hero-note strong { color: var(--adr-blue); font-size: clamp(22px, 2.5vw, 34px); line-height: 1.05; }
    .adr-hero-note span { color: var(--adr-muted); line-height: 1.55; }
    .adr-contact-row { display: flex; flex-wrap: wrap; gap: 10px; }
    .adr-contact-row a { color: var(--adr-blue); font-weight: 900; text-decoration: none; }
    .adr-contact-pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      border: 1px solid var(--adr-line);
      border-radius: 999px;
      padding: 9px 13px;
      background: color-mix(in srgb, var(--adr-blue) 6%, transparent);
    }
    .adr-contact-pill span { color: var(--adr-muted); font-size: 13px; text-transform: uppercase; letter-spacing: 0.04em; }
    .adr-service-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
      margin-top: 8px;
    }
    .adr-service-tags a,
    .adr-service-tags span {
      border: 1px solid var(--adr-line);
      border-radius: 999px;
      padding: 5px 9px;
      color: var(--adr-blue);
      background: color-mix(in srgb, var(--adr-blue) 5%, transparent);
      font-size: 12px;
      font-weight: 900;
      text-decoration: none;
      white-space: nowrap;
    }
    .adr-service-tags a:hover,
    .adr-service-tags a:focus { background: color-mix(in srgb, var(--adr-blue) 12%, transparent); }
	    .adr-icon-list,
	    .adr-page-list,
	    .adr-partners,
	    .adr-bottom { width: min(100%, 1180px); margin-left: auto; margin-right: auto; }
    .adr-icon-list { position: relative; z-index: 2; display: grid; gap: 12px; margin-top: -54px; }
	    .adr-icon-panel,
	    .adr-page-panel,
	    .adr-partners,
	    .adr-bottom {
	      border: 1px solid var(--adr-line);
	      border-radius: 8px;
	      background: var(--adr-paper);
	      box-shadow: var(--adr-soft-shadow);
	      backdrop-filter: blur(18px);
	    }
    .adr-icon-panel {
      display: grid;
      grid-template-columns: 58px minmax(0, 1fr) auto;
      gap: 18px;
      align-items: center;
      min-height: 104px;
      padding: 18px;
      text-decoration: none;
      transition: transform 180ms ease, box-shadow 180ms ease;
    }
    .adr-icon-panel:hover { transform: translateY(-2px); box-shadow: var(--adr-shadow); }
    .adr-icon-panel img,
    .adr-page-panel > img { width: 52px; height: 52px; border-radius: 50%; padding: 8px; background: color-mix(in srgb, var(--adr-blue) 8%, white); }
	    .adr-icon-panel h2,
	    .adr-page-panel h2,
	    .adr-partners h2,
	    .adr-bottom h2 { margin: 0; color: var(--adr-blue); line-height: 1.12; letter-spacing: 0; }
    .adr-icon-panel h2 { font-size: clamp(24px, 3vw, 36px); }
    .adr-icon-panel p,
    .adr-page-panel p,
	    .adr-page-panel li,
	    .adr-partners p,
	    .adr-bottom p { margin: 0; color: var(--adr-muted); line-height: 1.58; }
    .adr-icon-panel .adr-link-text,
    .adr-page-panel .adr-link-text { justify-self: end; color: var(--adr-blue); font-weight: 900; white-space: nowrap; }
    .adr-mini-nav { position: sticky; top: 116px; z-index: 8; display: flex; flex-wrap: wrap; justify-content: center; gap: 6px; width: min(100%, 1180px); margin: 18px auto; border: 1px solid var(--adr-line); border-radius: 8px; padding: 8px; background: var(--adr-paper); backdrop-filter: blur(18px); }
    .adr-mini-nav a { border-radius: 999px; padding: 9px 12px; color: var(--adr-blue); font-size: 13px; font-weight: 900; text-decoration: none; }
    .adr-mini-nav a:hover { background: color-mix(in srgb, var(--adr-blue) 10%, transparent); }
    .adr-page-list { display: grid; gap: 12px; margin-top: 18px; }
    .adr-page-panel { display: grid; grid-template-columns: 58px minmax(0, 1fr) auto; gap: 18px; align-items: center; padding: 18px; }
    .adr-page-copy { display: grid; gap: 9px; }
    .adr-page-panel h2 { font-size: clamp(22px, 2.4vw, 31px); }
    .adr-page-panel ul { display: grid; gap: 6px; margin: 0; padding-left: 20px; }
    .adr-tags,
    .adr-contact-chips,
    .adr-legal-links { display: flex; flex-wrap: wrap; gap: 8px; }
    .adr-tags span,
    .adr-contact-chips a,
    .adr-contact-chips span,
    .adr-legal-links a { border: 1px solid var(--adr-line); border-radius: 999px; padding: 8px 11px; color: var(--adr-blue); background: color-mix(in srgb, var(--adr-blue) 5%, transparent); font-size: 13px; font-weight: 900; text-decoration: none; }
	    .adr-partners {
	      display: grid;
	      grid-template-columns: minmax(220px, 0.55fr) minmax(0, 1fr);
	      gap: clamp(18px, 3vw, 36px);
	      align-items: center;
	      margin-top: 24px;
	      padding: clamp(20px, 3vw, 32px);
	    }
	    .adr-partners h2 { font-size: clamp(24px, 3vw, 34px); }
	    .adr-partners-copy { display: grid; gap: 8px; }
	    .adr-partner-logos {
	      display: grid;
	      grid-template-columns: repeat(4, minmax(120px, 1fr));
	      gap: clamp(12px, 2vw, 20px);
	      align-items: center;
	    }
	    .adr-partner-logos img {
	      width: 100%;
	      max-width: 180px;
	      margin: 0 auto;
	      border-radius: 4px;
	      object-fit: contain;
	      filter: saturate(0.96);
	    }
	    .adr-bottom { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(280px, 0.9fr); gap: 24px; align-items: center; margin-top: 24px; padding: 20px; }
    .adr-bottom img { width: 100%; border-radius: 8px; box-shadow: var(--adr-soft-shadow); }
    .adr-bottom-copy { display: grid; gap: 12px; border-left: 4px solid var(--adr-gold); padding-left: 22px; }
    .adr-bottom-actions { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
	    @media (max-width: 980px) {
	      .adr-bottom { grid-template-columns: 1fr; }
	      .adr-partners { grid-template-columns: 1fr; }
	      .adr-icon-list { margin-top: 16px; }
	      .adr-mini-nav { position: static; justify-content: flex-start; }
	    }
    @media (max-width: 820px) {
      .adr-hero { grid-template-columns: 1fr; }
    }
    @media (max-width: 760px) {
      .adr-floating-panel,
      .adr-panel-actions { align-items: stretch; flex-direction: column; }
      .adr-brand strong { white-space: normal; }
      .adr-panel-actions,
      .adr-button,
      .adr-switch-label { width: 100%; }
    }
    @media (max-width: 640px) {
      .adr-refresh-stage { padding-left: 10px; padding-right: 10px; }
      .adr-hero h1 { font-size: 19px; }
	      .adr-hero-media { min-height: 220px; }
	      .adr-partner-logos { grid-template-columns: repeat(2, minmax(120px, 1fr)); }
	      .adr-hero-copy { padding: 22px 18px; gap: 14px; }
      .adr-icon-panel,
      .adr-page-panel { grid-template-columns: 52px minmax(0, 1fr); }
      .adr-icon-panel .adr-link-text,
      .adr-page-panel .adr-link-text { grid-column: 2; justify-self: start; white-space: normal; }
      .adr-hero-media { min-height: 220px; }
	    }
	    .adr-home-clean-mock .adr-icon-list { margin-bottom: 24px; }
	    .adr-home-clean-mock .adr-bottom { margin-top: 24px; }
	  </style>

  <input class="adr-switch-input" id="adr-theme-toggle" type="checkbox" aria-label="Basculer le thème jour/nuit">
  <header class="adr-floating-panel" aria-label="Navigation principale">
    <a class="adr-brand" href="/">
      <img decoding="async" src="https://assurancesderueil.fr/wp-content/uploads/2022/08/cropped-flaticon2.png" alt="">
      <strong>Assurances de Rueil</strong>
    </a>
    <div class="adr-panel-actions">
      <a class="adr-button adr-button-primary" href="/demande-de-devis-assurance-a-rueil-malmaison/">Demander un devis</a>
      <label class="adr-switch-label" for="adr-theme-toggle">Jour/Nuit</label>
    </div>
  </header>

  <section class="adr-hero" aria-label="Présentation">
    <div class="adr-hero-media"><img decoding="async" src="https://assurancesderueil.fr/wp-content/uploads/2022/09/Assurance-Rueil-Malmaison-Assurance-pret-souscription.jpg" alt="Vie locale à Rueil-Malmaison"></div>
    <div class="adr-hero-copy">
      <p class="adr-kicker">Courtier indépendant depuis quatre générations</p>
      <h1>Courtier en assurances</h1>
      <p>Assurances de Rueil accompagne les particuliers, professionnels et emprunteurs avec des garanties adaptées, un suivi humain et des partenaires reconnus.</p>
      <div class="adr-hero-note">
        <strong>Faites des économies</strong>
        <span>En changeant d&rsquo;assurance de prêt, le cabinet compare les solutions disponibles et vous aide à choisir des garanties adaptées à votre profil.</span>
      </div>
      <div class="adr-contact-row">
        <a class="adr-contact-pill" href="tel:+33147510669"><span>Par téléphone</span> +33 1 47 51 06 69</a>
        <a class="adr-contact-pill" href="/cabinet-de-courtage-en-assurances-rueil-malmaison/#notre-cabinet">Nous découvrir</a>
      </div>
    </div>
  </section>

  <div class="adr-icon-list" aria-label="Services principaux">
    <article class="adr-icon-panel">
      <img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-house-gold.svg" alt="">
      <div><h2>Assurance de prêt</h2><p>Vous êtes sur le point de souscrire un crédit ? Découvrez alors tout ce que vous devez savoir sur l&rsquo;assurance de prêt.</p>
        <div class="adr-service-tags" aria-label="Garanties assurance de prêt">
          <a href="/assurance-de-pret-a-rueil-malmaison/#fonctionnement">PTIA</a><a href="/assurance-de-pret-a-rueil-malmaison/#fonctionnement">ITT / IPT / IPP</a><a href="/assurance-de-pret-a-rueil-malmaison/#duree-garanties">Décès</a><a href="/assurance-de-pret-a-rueil-malmaison/#comparer-les-offres">Comparer</a>
        </div>
      </div>
      <a class="adr-link-text" href="/assurance-de-pret-a-rueil-malmaison/">En savoir plus</a>
    </article>
    <article class="adr-icon-panel">
      <img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-people-gold.svg" alt="">
      <div><h2>Assurances des particuliers</h2><p>Assurance de prêt, santé, prévoyance, épargne, retraite, habitation, véhicule et protection de la famille.</p>
        <div class="adr-service-tags" aria-label="Garanties particuliers">
          <a href="/assurance-particuliers-rueil-malmaison/#sante">Santé</a><a href="/assurance-particuliers-rueil-malmaison/#prevoyance">Prévoyance</a><a href="/assurance-particuliers-rueil-malmaison/#habitation">Habitation</a><a href="/assurance-particuliers-rueil-malmaison/#loyers-impayes">Loyers impayés</a><a href="/assurance-particuliers-rueil-malmaison/#automobile-mobilite">Véhicule</a>
        </div>
      </div>
      <a class="adr-link-text" href="/assurance-particuliers-rueil-malmaison/">En savoir plus</a>
    </article>
    <article class="adr-icon-panel">
      <img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-briefcase-gold.svg" alt="">
      <div><h2>Assurances des professionnels</h2><p>Locaux, bureaux, flotte automobile, prévoyance collective, artisans, commerçants, professions libérales et entreprises industrielles.</p>
        <div class="adr-service-tags" aria-label="Garanties professionnels">
          <a href="/assurance-entreprise-rueil-malmaison/#multirisques-professionnelle">Locaux</a><a href="/assurance-entreprise-rueil-malmaison/#profils">Profils</a><a href="/assurance-entreprise-rueil-malmaison/#flotte-automobile">Flotte automobile</a><a href="/assurance-entreprise-rueil-malmaison/#prevoyance-collective">Prévoyance collective</a>
        </div>
      </div>
      <a class="adr-link-text" href="/assurance-entreprise-rueil-malmaison/">En savoir plus</a>
		    </article>
	  </div>
	  <section class="adr-partners" aria-label="Partenaires assureurs">
	    <div class="adr-partners-copy">
	      <h2>Partenaires assureurs</h2>
	      <p>Des partenaires reconnus pour comparer les solutions selon votre profil.</p>
	    </div>
	    <div class="adr-partner-logos" aria-label="Thelem, AXA, April et Generali">
	      <img decoding="async" src="https://ec92009.github.io/ADR/accueil-mock-assets/thelem-assurance-rueil.jpg" alt="Th&eacute;lem assurances">
	      <img decoding="async" src="https://ec92009.github.io/ADR/accueil-mock-assets/axa-assurance-rueil.jpg" alt="AXA">
	      <img decoding="async" src="https://ec92009.github.io/ADR/accueil-mock-assets/april-assurance-rueil.jpg" alt="April">
	      <img decoding="async" src="https://ec92009.github.io/ADR/accueil-mock-assets/generali-assurance-rueil.jpg" alt="Generali">
	    </div>
	  </section>
	<section class="adr-bottom" aria-label="Demande de devis">
    <img decoding="async" src="https://assurancesderueil.fr/wp-content/uploads/2019/09/image.jpg" alt="Partenaires assureurs">
    <div class="adr-bottom-copy">
      <h2>Par formulaire</h2>
      <p>Complétez notre formulaire, nous vous contacterons dans les meilleurs délais.</p>
      <div class="adr-bottom-actions">
        <a class="adr-button adr-button-primary" href="/demande-de-devis-assurance-a-rueil-malmaison/">C&rsquo;est par ici !</a>
        <a class="adr-contact-pill" href="tel:+33147510669"><span>Par téléphone</span> +33 1 47 51 06 69</a>
      </div>
    </div>
  </section>
</div>
ADR_STAGE_HOME
        ,
        'devis' => <<<'ADR_STAGE_DEVIS'
<div class="adr-page-stage adr-quote-stage">
  <style>
    body.page-id-7427 .ekit-template-content-header,
    body.page-id-7427 .site-footer,
    body.page-id-7427 footer.footer,
    body.page-id-7427 .ekit-template-content-footer,
    body.page-id-7427 .page-banner-area,
    body.page-id-7427 .breadcrumb { display: none !important; }
    .adr-page-stage, .adr-page-stage * { box-sizing: border-box; }
    .adr-page-stage {
      --adr-blue: #003478;
      --adr-blue-soft: #6da9ff;
      --adr-ink: #07192f;
      --adr-muted: #657386;
      --adr-line: rgba(0, 52, 120, 0.18);
      --adr-paper: rgba(255, 255, 255, 0.88);
      --adr-solid: #ffffff;
      --adr-wash: #eef5fb;
      --adr-gold: #d7a53c;
      --adr-shadow: 0 24px 70px rgba(0, 28, 64, 0.16);
      --adr-soft-shadow: 0 14px 36px rgba(0, 28, 64, 0.1);
      width: 100vw;
      margin-left: calc(50% - 50vw);
      margin-right: calc(50% - 50vw);
      margin-top: -1px;
      padding: clamp(14px, 2vw, 24px) clamp(14px, 2vw, 24px) 64px;
      background: radial-gradient(circle at 10% 0%, rgba(109,169,255,.2), transparent 30%), var(--adr-wash);
      color: var(--adr-ink);
      font-family: Rubik, "Open Sans", Arial, sans-serif;
      overflow: clip;
    }
    .adr-page-stage:has(#adr-theme-toggle:checked) {
      --adr-blue: #83b7ff;
      --adr-blue-soft: #74a9f7;
      --adr-ink: #edf6ff;
      --adr-muted: #a8b8c8;
      --adr-line: rgba(169, 201, 234, 0.24);
      --adr-paper: rgba(12, 29, 49, 0.84);
      --adr-solid: #0e2135;
      --adr-wash: #06111d;
      --adr-shadow: 0 24px 70px rgba(0, 0, 0, 0.34);
      --adr-soft-shadow: 0 14px 36px rgba(0, 0, 0, 0.24);
    }
    .adr-page-stage a { color: inherit; }
    .adr-page-stage img { display: block; max-width: 100%; }
    .adr-page-stage [id] { scroll-margin-top: 140px; }
    .adr-switch-input { position: absolute; opacity: 0; pointer-events: none; }
    .adr-floating-panel {
      position: sticky;
      top: clamp(12px, 2vw, 24px);
      z-index: 9;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      width: min(100%, 1288px);
      margin: 0 auto 18px;
      border: 1px solid var(--adr-line);
      border-radius: 8px;
      padding: 7px 8px 7px 12px;
      background: var(--adr-paper);
      box-shadow: 0 18px 54px rgba(0, 0, 0, 0.18);
      backdrop-filter: blur(18px);
    }
    body.admin-bar .adr-floating-panel { top: calc(32px + clamp(12px, 2vw, 24px)); }
    .adr-brand { display: inline-flex; align-items: center; gap: 13px; min-width: 0; color: var(--adr-blue); text-decoration: none; }
    .adr-brand img { width: clamp(48px, 5vw, 78px); height: clamp(48px, 5vw, 78px); border-radius: 50%; }
    .adr-brand strong { font-size: clamp(24px, 3vw, 42px); line-height: 1; letter-spacing: 0; white-space: nowrap; }
    .adr-panel-actions { display: flex; align-items: stretch; justify-content: flex-end; gap: 8px; }
    .adr-button, .adr-switch-label {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 44px;
      height: clamp(44px, 4.6vw, 64px);
      border-radius: 8px;
      padding: 12px 18px;
      border: 1px solid var(--adr-line);
      font-weight: 900;
      line-height: 1.05;
      text-decoration: none;
      cursor: pointer;
      white-space: nowrap;
      margin: 0 !important;
    }
    .adr-button-primary, .adr-button-primary:visited, .adr-button-primary:hover, .adr-button-primary:focus {
      border-color: transparent;
      background: var(--adr-blue);
      color: #fff !important;
      box-shadow: 0 16px 34px rgba(0, 52, 120, 0.22);
    }
    .adr-page-stage:has(#adr-theme-toggle:checked) .adr-button-primary { color: #06111d !important; }
    .adr-switch-label { min-width: 58px; background: color-mix(in srgb, var(--adr-blue) 7%, var(--adr-solid)); color: var(--adr-blue); font-size: 13px; }
    .adr-page-stage:has(#adr-theme-toggle:checked) label[for="adr-theme-toggle"] { background: var(--adr-blue); color: #06111d; }
    .adr-hero, .adr-form-section, .adr-next-steps, .adr-mini-nav { width: min(100%, 1180px); margin-left: auto; margin-right: auto; }
    .adr-hero {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(320px, .75fr);
      min-height: 430px;
      overflow: hidden;
      border: 1px solid var(--adr-line);
      border-radius: 8px;
      background: var(--adr-solid);
      box-shadow: var(--adr-shadow);
    }
    .adr-hero-copy { display: grid; align-content: center; gap: 18px; padding: clamp(28px, 5vw, 70px); }
    .adr-kicker { margin: 0; color: var(--adr-gold); font-size: 14px; font-weight: 900; text-transform: uppercase; letter-spacing: .08em; }
    .adr-hero h1 { margin: 0; color: var(--adr-blue); font-size: clamp(22px, 3.02vw, 41px); line-height: 1; letter-spacing: 0; }
    .adr-hero p { max-width: 680px; margin: 0; color: var(--adr-muted); font-size: clamp(18px, 2vw, 23px); line-height: 1.55; }
    .adr-hero-media { min-height: 340px; background: #d9e7f2; }
    .adr-hero-media img { width: 100%; height: 100%; object-fit: cover; }
    .adr-mini-nav { position: sticky; top: 116px; z-index: 8; display: flex; flex-wrap: wrap; justify-content: center; gap: 6px; margin-top: 18px; margin-bottom: 18px; border: 1px solid var(--adr-line); border-radius: 8px; padding: 8px; background: var(--adr-paper); backdrop-filter: blur(18px); }
    .adr-mini-nav a { border-radius: 999px; padding: 9px 12px; color: var(--adr-blue); font-size: 13px; font-weight: 900; text-decoration: none; }
    .adr-form-section { display: grid; grid-template-columns: minmax(270px, .52fr) minmax(0, 1fr); gap: 16px; align-items: start; }
    .adr-side-card, .adr-form-card, .adr-step-card { border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); }
    .adr-side-card { position: sticky; top: 178px; display: grid; gap: 14px; padding: clamp(22px, 3vw, 34px); }
    .adr-side-card h2, .adr-form-card h2, .adr-step-card h2 { margin: 0; color: var(--adr-blue); line-height: 1.12; letter-spacing: 0; }
    .adr-side-card p, .adr-form-card p, .adr-step-card p, .adr-side-card li { margin: 0; color: var(--adr-muted); line-height: 1.58; }
    .adr-side-card ol { display: grid; gap: 12px; margin: 6px 0 0; padding-left: 22px; font-weight: 900; color: var(--adr-muted); }
    .adr-form-card { padding: clamp(18px, 3vw, 28px); }
    .adr-form-intro { display: grid; gap: 8px; margin-bottom: 16px; }
    .adr-form-card .mf-form-wrapper,
    .adr-form-card .metform-form-content,
    .adr-form-card .elementor,
    .adr-form-card .elementor-section,
    .adr-form-card .elementor-container { max-width: none !important; width: 100% !important; }
    .adr-form-card input:not([type="checkbox"]):not([type="radio"]),
    .adr-form-card select,
    .adr-form-card textarea {
      min-height: 46px !important;
      border: 1px solid var(--adr-line) !important;
      border-radius: 8px !important;
      background: color-mix(in srgb, var(--adr-solid) 88%, transparent) !important;
      color: var(--adr-ink) !important;
      box-shadow: none !important;
    }
    .adr-form-card label,
    .adr-form-card .mf-input-label { color: var(--adr-ink) !important; font-weight: 800 !important; }
    .adr-form-card .metform-btn,
    .adr-form-card .metform-submit-btn { min-height: 50px !important; border-radius: 8px !important; background: var(--adr-blue) !important; color: #fff !important; font-weight: 900 !important; }
    .adr-next-steps { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-top: 18px; }
    .adr-step-card { display: grid; gap: 8px; padding: 20px; }
    .adr-step-card img { width: 48px; height: 48px; border-radius: 50%; padding: 8px; background: color-mix(in srgb, var(--adr-blue) 8%, white); }
    @media (max-width: 980px) { .adr-form-section, .adr-hero { grid-template-columns: 1fr; } .adr-side-card, .adr-mini-nav { position: static; } .adr-hero-media { order: -1; min-height: 240px; } }
    @media (max-width: 760px) { .adr-floating-panel, .adr-panel-actions { align-items: stretch; flex-direction: column; } .adr-brand strong { white-space: normal; } .adr-panel-actions, .adr-button, .adr-switch-label { width: 100%; } }
    @media (max-width: 640px) { .adr-page-stage { padding-left: 10px; padding-right: 10px; } .adr-hero h1 { font-size: 19px; } .adr-hero-copy { padding: 22px 18px; } .adr-next-steps { grid-template-columns: 1fr; } }

    /* adr-hero-media-patch-v1: keep stacked hero images compact on tablet and narrow desktop. */
    @media (max-width: 980px) {
      .adr-page-stage .adr-hero-media { height: clamp(210px, 32vw, 300px) !important; min-height: 0 !important; }
      .adr-page-stage .adr-hero-media img { width: 100% !important; height: 100% !important; object-fit: cover !important; }
    }

    /* adr-form-contrast-patch-v1: improve MetForm contrast on dark internal panels. */
    .adr-form-card .metform-form-content,
    .adr-form-card .metform-form-main-wrapper,
    .adr-form-card .mf-form-wrapper,
    .adr-form-card .elementor-widget-wrap { color: var(--adr-ink); }
    .adr-form-card .elementor-section,
    .adr-form-card .elementor-container,
    .adr-form-card .elementor-widget-wrap,
    .adr-form-card .mf-input-wrapper { border-radius: 8px; }
    .adr-form-card .mf-input-label,
    .adr-form-card .mf-input-label span:not(.mf-input-required-indicator),
    .adr-form-card .mf-checkbox-option label,
    .adr-form-card .mf-checkbox-option label span,
    .adr-form-card .mf-checkbox-option label a { color: #f7fbff !important; }
    .adr-form-card .mf-input-required-indicator { color: #ff6b4a !important; }
    .adr-form-card .mf-input,
    .adr-form-card input:not([type="checkbox"]):not([type="radio"]),
    .adr-form-card select,
    .adr-form-card textarea {
      background: #ffffff !important;
      color: #07192f !important;
      border-color: rgba(255,255,255,.72) !important;
      box-shadow: 0 10px 24px rgba(0,0,0,.16) !important;
    }
    .adr-form-card .mf-input::placeholder,
    .adr-form-card input::placeholder,
    .adr-form-card textarea::placeholder { color: rgba(7,25,47,.58) !important; }
    .adr-form-card .mf-error-message { color: #ffd6cc !important; font-weight: 800 !important; }

    /* adr-flatpickr-layout-patch-v1: keep month/year controls legible in the date picker. */
    body.page-id-7427 .flatpickr-calendar {
      width: min(620px, calc(100vw - 28px)) !important;
      border-radius: 8px !important;
      box-shadow: 0 24px 70px rgba(0, 28, 64, 0.24) !important;
      overflow: hidden !important;
    }
    body.page-id-7427 .flatpickr-calendar .flatpickr-months {
      display: flex !important;
      align-items: center !important;
      min-height: 70px !important;
      padding: 8px 54px 4px !important;
      background: #ffffff !important;
    }
    body.page-id-7427 .flatpickr-calendar .flatpickr-month {
      height: 54px !important;
      overflow: visible !important;
      color: #07192f !important;
      background: transparent !important;
      flex: 1 1 auto !important;
    }
    body.page-id-7427 .flatpickr-calendar .flatpickr-current-month {
      position: relative !important;
      left: auto !important;
      top: auto !important;
      width: 100% !important;
      height: auto !important;
      padding: 0 !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 12px !important;
      transform: none !important;
    }
    body.page-id-7427 .flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months {
      flex: 0 0 190px !important;
      width: 190px !important;
      min-width: 190px !important;
      max-width: 190px !important;
      height: 44px !important;
      padding: 0 36px 0 14px !important;
      border: 1px solid rgba(0,52,120,.2) !important;
      border-radius: 8px !important;
      background-color: #ffffff !important;
      color: #07192f !important;
      font-size: 22px !important;
      font-weight: 500 !important;
      line-height: 44px !important;
      box-shadow: 0 10px 24px rgba(0,28,64,.08) !important;
      opacity: 1 !important;
    }
    body.page-id-7427 .flatpickr-calendar .flatpickr-current-month .numInputWrapper {
      flex: 0 0 112px !important;
      width: 112px !important;
      min-width: 112px !important;
      height: 44px !important;
      border: 1px solid rgba(0,52,120,.2) !important;
      border-radius: 8px !important;
      background: #ffffff !important;
      box-shadow: 0 10px 24px rgba(0,28,64,.08) !important;
      overflow: hidden !important;
    }
    body.page-id-7427 .flatpickr-calendar .flatpickr-current-month input.cur-year {
      width: 100% !important;
      height: 44px !important;
      padding: 0 12px !important;
      color: #07192f !important;
      font-size: 22px !important;
      font-weight: 500 !important;
      line-height: 44px !important;
      background: #ffffff !important;
    }
    body.page-id-7427 .flatpickr-calendar .flatpickr-prev-month,
    body.page-id-7427 .flatpickr-calendar .flatpickr-next-month {
      top: 18px !important;
      width: 38px !important;
      height: 38px !important;
      border-radius: 999px !important;
      color: #07192f !important;
      fill: #07192f !important;
    }
    body.page-id-7427 .flatpickr-calendar .flatpickr-prev-month { left: 14px !important; }
    body.page-id-7427 .flatpickr-calendar .flatpickr-next-month { right: 14px !important; }
    body.page-id-7427 .flatpickr-calendar .flatpickr-weekdays,
    body.page-id-7427 .flatpickr-calendar .flatpickr-days { width: 100% !important; }
    body.page-id-7427 .flatpickr-calendar .dayContainer {
      width: 100% !important;
      min-width: 100% !important;
      max-width: 100% !important;
    }
    body.page-id-7427 .flatpickr-calendar .flatpickr-day {
      max-width: none !important;
      height: 42px !important;
      line-height: 42px !important;
      font-size: 18px !important;
    }
    @media (max-width: 560px) {
      body.page-id-7427 .flatpickr-calendar .flatpickr-months { padding-left: 44px !important; padding-right: 44px !important; }
      body.page-id-7427 .flatpickr-calendar .flatpickr-current-month { gap: 8px !important; }
      body.page-id-7427 .flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months { flex-basis: 150px !important; width: 150px !important; min-width: 150px !important; font-size: 18px !important; }
      body.page-id-7427 .flatpickr-calendar .flatpickr-current-month .numInputWrapper { flex-basis: 92px !important; width: 92px !important; min-width: 92px !important; }
      body.page-id-7427 .flatpickr-calendar .flatpickr-current-month input.cur-year { font-size: 18px !important; }
    }

    /* adr-flatpickr-year-dropdown-only-v1: one year control, limited to borrower ages 16-100. */
    body.page-id-7427 .flatpickr-calendar {
      width: min(700px, calc(100vw - 28px)) !important;
    }
    body.page-id-7427 .flatpickr-calendar .flatpickr-current-month {
      flex-wrap: nowrap !important;
    }
    body.page-id-7427 .flatpickr-calendar .flatpickr-current-month .numInputWrapper,
    body.page-id-7427 .flatpickr-calendar .flatpickr-current-month input.cur-year {
      position: absolute !important;
      width: 1px !important;
      min-width: 1px !important;
      height: 1px !important;
      margin: 0 !important;
      padding: 0 !important;
      border: 0 !important;
      overflow: hidden !important;
      clip: rect(0 0 0 0) !important;
      clip-path: inset(50%) !important;
      opacity: 0 !important;
      pointer-events: none !important;
    }
    body.page-id-7427 .flatpickr-calendar .adr-birth-year-select {
      flex: 0 0 132px !important;
      width: 132px !important;
      min-width: 132px !important;
      height: 44px !important;
      padding: 0 34px 0 12px !important;
      border: 1px solid rgba(0,52,120,.2) !important;
      border-radius: 8px !important;
      background: #ffffff !important;
      color: #07192f !important;
      font: 700 18px/44px Rubik, "Open Sans", Arial, sans-serif !important;
      box-shadow: 0 10px 24px rgba(0,28,64,.08) !important;
      cursor: pointer !important;
    }
    body.page-id-7427 .flatpickr-calendar .adr-birth-year-select:focus {
      outline: 3px solid rgba(109,169,255,.35) !important;
      outline-offset: 2px !important;
    }
    @media (max-width: 640px) {
      body.page-id-7427 .flatpickr-calendar .flatpickr-months {
        min-height: 72px !important;
        padding: 10px 46px 8px !important;
      }
      body.page-id-7427 .flatpickr-calendar .adr-birth-year-select {
        flex-basis: 112px !important;
        width: 112px !important;
        min-width: 112px !important;
        font-size: 16px !important;
      }
    }

    /* adr-birthdate-selects-v1: replace fragile calendar year picker with simple selects. */
    body.page-id-7427 .flatpickr-calendar {
      display: none !important;
      visibility: hidden !important;
      pointer-events: none !important;
    }
    .adr-form-card .adr-native-date-source {
      position: absolute !important;
      width: 1px !important;
      height: 1px !important;
      min-height: 1px !important;
      margin: 0 !important;
      padding: 0 !important;
      border: 0 !important;
      overflow: hidden !important;
      clip: rect(0 0 0 0) !important;
      clip-path: inset(50%) !important;
      opacity: 0 !important;
      pointer-events: none !important;
    }
    .adr-form-card .adr-birthdate-selects {
      display: grid !important;
      grid-template-columns: minmax(88px, .75fr) minmax(112px, 1fr) minmax(110px, .9fr);
      gap: 10px;
      width: 100%;
    }
    .adr-form-card .adr-birthdate-selects select {
      min-height: 46px !important;
      width: 100% !important;
      border: 1px solid rgba(255,255,255,.72) !important;
      border-radius: 8px !important;
      background: #ffffff !important;
      color: #07192f !important;
      font: 800 15px/1.2 Rubik, "Open Sans", Arial, sans-serif !important;
      box-shadow: 0 10px 24px rgba(0,0,0,.16) !important;
    }
    .adr-form-card .adr-birthdate-selects select:focus {
      outline: 3px solid rgba(109,169,255,.35) !important;
      outline-offset: 2px !important;
    }
    @media (max-width: 560px) {
      .adr-form-card .adr-birthdate-selects {
        grid-template-columns: 1fr;
      }
    }
  </style>
  <input class="adr-switch-input" id="adr-theme-toggle" type="checkbox" aria-label="Basculer le thème jour/nuit">
  <header class="adr-floating-panel" aria-label="Navigation principale">
    <a class="adr-brand" href="/"><img decoding="async" src="https://assurancesderueil.fr/wp-content/uploads/2022/08/cropped-flaticon2.png" alt=""><strong>Assurances de Rueil</strong></a>
    <div class="adr-panel-actions">
      <a class="adr-button adr-button-primary" href="#formulaire">Demander un devis</a>
      <label class="adr-switch-label" for="adr-theme-toggle">Jour/Nuit</label>
    </div>
  </header>
  <section class="adr-hero" aria-label="Demande de devis">
    <div class="adr-hero-copy">
      <p class="adr-kicker">Simulation personnalisée</p>
      <h1>Demande de devis d’assurance</h1>
      <p>Transmettez les informations utiles à votre dossier. Un conseiller Assurances de Rueil vous recontacte pour préparer une réponse adaptée.</p>
      <a class="adr-button adr-button-primary" href="#formulaire">Accéder au formulaire</a>
    </div>
    <div class="adr-hero-media"><img decoding="async" src="https://assurancesderueil.fr/wp-content/uploads/2022/08/Assurance-Rueil-Malmaison-Simulation-tarifaire.jpg" alt="Simulation d'assurance avec Assurances de Rueil"></div>
  </section>
  <nav class="adr-mini-nav" aria-label="Pages principales">
    <a href="/">Accueil</a>
    <a href="/cabinet-de-courtage-en-assurances-rueil-malmaison/">Cabinet</a>
    <a href="/assurance-de-pret-a-rueil-malmaison/">Assurance de prêt</a>
    <a href="/assurance-particuliers-rueil-malmaison/">Particuliers</a>
    <a href="/assurance-entreprise-rueil-malmaison/">Professionnels</a>
    <a href="/courtier-en-assurances-de-rueil-malmaison/">Contact</a>
  </nav>
  <section class="adr-form-section" id="formulaire" aria-label="Formulaire de demande de devis">
    <aside class="adr-side-card">
      <h2>Votre demande</h2>
      <p>Le formulaire reprend les informations nécessaires pour comparer les garanties et préparer un échange utile avec le cabinet.</p>
      <ol>
        <li>Profil emprunteur</li>
        <li>Coordonnées</li>
        <li>Consentement RGPD</li>
      </ol>
      <p><a href="tel:+33147510669">+33 1 47 51 06 69</a><br><a href="mailto:contact@assurancesderueil.fr">contact@assurancesderueil.fr</a></p>
    </aside>
    <div class="adr-form-card">
      <div class="adr-form-intro">
        <h2>Formulaire sécurisé</h2>
        <p>Ces champs sont transmis au cabinet afin qu&rsquo;un conseiller puisse vous rappeler et vous accompagner dans le choix de votre assurance.</p>
      </div>


<div class="mf-form-shortcode">
        <div
            id="metform-wrap-2073-2073"
            class="mf-form-wrapper"
            data-form-id="2073"
            data-action="https://assurancesderueil.fr/wp-json/metform/v1/entries/insert/2073"
            data-wp-nonce="b28afe1c5a"
            data-form-nonce="a19498c989"
            data-quiz-summery = "false"
            data-save-progress = "false"
            data-form-type="contact_form"
            data-stop-vertical-effect=""
            ></div>


        <!-----------------------------
            * controls_data : find the the props passed indie of data attribute
            * props.SubmitResponseMarkup : contains the markup of error or success message
            * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Template_literals
        --------------------------- -->

                <script type="text/mf" class="mf-template">
            function controls_data (value){
                let currentWrapper = "mf-response-props-id-2073";
                let currentEl = document.getElementById(currentWrapper);

                return currentEl ? currentEl.dataset[value] : false
            }


            let is_edit_mode = '' ? true : false;
            let message_position = controls_data('messageposition') || 'top';


            let message_successIcon = controls_data('successicon') || '';
            let message_errorIcon = controls_data('erroricon') || '';
            let message_editSwitch = controls_data('editswitchopen') === 'yes' ? true : false;
            let message_proClass = controls_data('editswitchopen') === 'yes' ? 'mf_pro_activated' : '';

            let is_dummy_markup = is_edit_mode && message_editSwitch ? true : false;


            return html`
                <form
                    className="metform-form-content"
                    ref=${parent.formContainerRef}
                    onSubmit=${ validation.handleSubmit( parent.handleFormSubmit ) }

                    >


                    ${is_dummy_markup ? message_position === 'top' ?  props.ResponseDummyMarkup(message_successIcon, message_proClass) : '' : ''}
                    ${is_dummy_markup ? ' ' :  message_position === 'top' ? props.SubmitResponseMarkup`${parent}${state}${message_successIcon}${message_errorIcon}${message_proClass}` : ''}

                    <!--------------------------------------------------------
                    *** IMPORTANT / DANGEROUS ***
                    ${html``} must be used as in immediate child of "metform-form-main-wrapper"
                    class otherwise multistep form will not run at all
                    ---------------------------------------------------------->

                    <div className="metform-form-main-wrapper" key=${'hide-form-after-submit'} ref=${parent.formRef}>
                    ${html`
                                <div data-elementor-type="wp-post" key="2" data-elementor-id="2073" className="elementor elementor-2073" data-elementor-post-type="metform-form">
                        <section className="elementor-section elementor-top-section elementor-element elementor-element-7dc44e9 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="7dc44e9" data-element_type="section">
                        <div className="elementor-container elementor-column-gap-default">
                    <div className="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-ed44f00" data-id="ed44f00" data-element_type="column">
            <div className="elementor-widget-wrap">
                            </div>
        </div>
                    </div>
        </section>
                <section className="elementor-section elementor-top-section elementor-element elementor-element-c64f6f7 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="c64f6f7" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
                        <div className="elementor-container elementor-column-gap-default">
                    <div className="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-5f43826" data-id="5f43826" data-element_type="column" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
            <div className="elementor-widget-wrap elementor-element-populated">
                        <div className="elementor-element elementor-element-773e1b4 elementor-widget elementor-widget-mf-checkbox" data-id="773e1b4" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;mf-checkbox&quot;,&quot;mf_input_list&quot;:[{&quot;mf_input_option_text&quot;:&quot;Madame&quot;,&quot;mf_input_option_value&quot;:&quot;Madame&quot;,&quot;_id&quot;:&quot;17fbfa1&quot;,&quot;mf_input_option_status&quot;:&quot;&quot;,&quot;mf_input_option_selected&quot;:&quot;&quot;},{&quot;mf_input_option_text&quot;:&quot;Monsieur&quot;,&quot;mf_input_option_value&quot;:&quot;Monsieur&quot;,&quot;_id&quot;:&quot;320b9aa&quot;,&quot;mf_input_option_status&quot;:&quot;&quot;,&quot;mf_input_option_selected&quot;:&quot;&quot;}]}" data-widget_type="mf-checkbox.default">
                <div className="elementor-widget-container">

        <div className="mf-input-wrapper">
                            <label className="mf-input-label" htmlFor="mf-input-checkbox-773e1b4">
                    ${ parent.decodeEntities(`Civilité `) } 					<span className="mf-input-required-indicator">*</span>
                </label>

            <div
                                    ref=${el => parent.handleCheckbox(el, 'onLoad')}
                                    className="mf-checkbox multi-option-input-type"
                    id="mf-input-checkbox-773e1b4">
                                    <div className="mf-checkbox-option ">
                        <label>
                                                        <input type="checkbox"
                                className="mf-input mf-checkbox-input "
                                name="mf-checkbox"
                                value="Madame"
                                defaultChecked=""

                                                                                                    onInput=${ el =>  parent.handleCheckbox(el.target, 'onClick') }
                                    aria-invalid=${validation.errors['mf-checkbox'] ? 'true' : 'false'}
                                    ref=${el => {
                                                                                parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":1,"maxLength":"","type":"none","required":true,"expression":"null"}, el)}
                                    }
                                                                />
                            <span>
                                ${ parent.decodeEntities(`Madame`) } 							</span>
                        </label>
                    </div>
                                        <div className="mf-checkbox-option ">
                        <label>
                                                        <input type="checkbox"
                                className="mf-input mf-checkbox-input "
                                name="mf-checkbox"
                                value="Monsieur"
                                defaultChecked=""

                                                                                                    onInput=${ el =>  parent.handleCheckbox(el.target, 'onClick') }
                                    aria-invalid=${validation.errors['mf-checkbox'] ? 'true' : 'false'}
                                    ref=${el => {
                                                                                parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":1,"maxLength":"","type":"none","required":true,"expression":"null"}, el)}
                                    }
                                                                />
                            <span>
                                ${ parent.decodeEntities(`Monsieur`) } 							</span>
                        </label>
                    </div>
                                </div>
            <input type="hidden" name="mf-checkbox" value="" />

                            <${validation.ErrorMessage}
                    errors=${validation.errors}
                    name="mf-checkbox"
                    as=${html`<span className="mf-error-message"></span>`}
                    />
                                </div>

                        </div>
                </div>
                <div className="elementor-element elementor-element-3892e3b elementor-widget elementor-widget-mf-date" data-id="3892e3b" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;mf-date&quot;}" data-widget_type="mf-date.default">
                <div className="elementor-widget-container">

        <div className="mf-input-wrapper">
                            <label className="mf-input-label" htmlFor="mf-input-date-3892e3b">
                    ${ parent.decodeEntities(`Votre date de naissance`) } 					<span className="mf-input-required-indicator">*</span>
                </label>

            <${props.Flatpickr}
                    name="mf-date"
                    className="mf-input mf-date-input mf-left-parent  "
                    placeholder="${ parent.decodeEntities(`Date`) } "
                    options=${{"minDate":"","maxDate":"","dateFormat":"m-d-Y","enableTime":"","disable":[],"mode":"single","static":true,"disableMobile":true,"time_24hr":false}}
                    value=${parent.getValue('mf-date')}
                    onInput=${parent.handleDateTime}
                    aria-invalid=${validation.errors['mf-date'] ? 'true' : 'false'}
                    ref=${el => props.DateWidget(
                            el,
                            '',
                            {"message":"Ce champ est n\u00e9cessaire.","required":true},
                            register,
                            parent
                        )}
                    />

                            <${validation.ErrorMessage}
                    errors=${validation.errors}
                    name="mf-date"
                    as=${html`<span className="mf-error-message"></span>`}
                    />
                                </div>


                        </div>
                </div>
                <div className="elementor-element elementor-element-3eab9f5 elementor-widget elementor-widget-mf-checkbox" data-id="3eab9f5" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;fumeur&quot;,&quot;mf_input_list&quot;:[{&quot;mf_input_option_text&quot;:&quot;Oui&quot;,&quot;mf_input_option_value&quot;:&quot;Oui&quot;,&quot;_id&quot;:&quot;f2ff94c&quot;,&quot;mf_input_option_status&quot;:&quot;&quot;,&quot;mf_input_option_selected&quot;:&quot;&quot;},{&quot;mf_input_option_text&quot;:&quot;Non&quot;,&quot;mf_input_option_value&quot;:&quot;Non&quot;,&quot;_id&quot;:&quot;dea0762&quot;,&quot;mf_input_option_status&quot;:&quot;&quot;,&quot;mf_input_option_selected&quot;:&quot;&quot;}]}" data-widget_type="mf-checkbox.default">
                <div className="elementor-widget-container">

        <div className="mf-input-wrapper">
                            <label className="mf-input-label" htmlFor="mf-input-checkbox-3eab9f5">
                    ${ parent.decodeEntities(`Etes-vous fumeur ?`) } 					<span className="mf-input-required-indicator">*</span>
                </label>

            <div
                                    ref=${el => parent.handleCheckbox(el, 'onLoad')}
                                    className="mf-checkbox multi-option-input-type"
                    id="mf-input-checkbox-3eab9f5">
                                    <div className="mf-checkbox-option ">
                        <label>
                                                        <input type="checkbox"
                                className="mf-input mf-checkbox-input "
                                name="fumeur"
                                value="Oui"
                                defaultChecked=""

                                                                                                    onInput=${ el =>  parent.handleCheckbox(el.target, 'onClick') }
                                    aria-invalid=${validation.errors['fumeur'] ? 'true' : 'false'}
                                    ref=${el => {
                                                                                parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":1,"maxLength":"","type":"none","required":true,"expression":"null"}, el)}
                                    }
                                                                />
                            <span>
                                ${ parent.decodeEntities(`Oui`) } 							</span>
                        </label>
                    </div>
                                        <div className="mf-checkbox-option ">
                        <label>
                                                        <input type="checkbox"
                                className="mf-input mf-checkbox-input "
                                name="fumeur"
                                value="Non"
                                defaultChecked=""

                                                                                                    onInput=${ el =>  parent.handleCheckbox(el.target, 'onClick') }
                                    aria-invalid=${validation.errors['fumeur'] ? 'true' : 'false'}
                                    ref=${el => {
                                                                                parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":1,"maxLength":"","type":"none","required":true,"expression":"null"}, el)}
                                    }
                                                                />
                            <span>
                                ${ parent.decodeEntities(`Non`) } 							</span>
                        </label>
                    </div>
                                </div>
            <input type="hidden" name="fumeur" value="" />

                            <${validation.ErrorMessage}
                    errors=${validation.errors}
                    name="fumeur"
                    as=${html`<span className="mf-error-message"></span>`}
                    />
                        <span className="mf-input-help"> ${ parent.decodeEntities(`Est non-fumeur toute personne certifiant qu’elle n’a fumé ni cigarette,
       ni cigarette électronique, ni pipe, ni cigare, ni consommé de produits contenant de la nicotine (patch, gomme…)  au cours des 24 derniers mois, et qu’elle n’a pas arrêté de fumer à la demande
       expresse du corps médical.`) }  </span>		</div>

                        </div>
                </div>
                <div className="elementor-element elementor-element-ef7a7df elementor-widget elementor-widget-mf-select" data-id="ef7a7df" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;mf-select&quot;,&quot;mf_input_list&quot;:[{&quot;mf_input_option_text&quot;:&quot;Cadres&quot;,&quot;mf_input_option_value&quot;:&quot;Cadres&quot;,&quot;_id&quot;:&quot;d1f3959&quot;,&quot;mf_input_option_status&quot;:&quot;&quot;,&quot;mf_input_option_selected&quot;:&quot;&quot;},{&quot;mf_input_option_text&quot;:&quot;Employ\u00e9s, A. de ma\u00eetrise&quot;,&quot;mf_input_option_value&quot;:&quot;Employ\u00e9s, A. de ma\u00eetrise&quot;,&quot;_id&quot;:&quot;4f6f00e&quot;,&quot;mf_input_option_status&quot;:&quot;&quot;,&quot;mf_input_option_selected&quot;:&quot;&quot;},{&quot;mf_input_option_text&quot;:&quot;Ouvriers&quot;,&quot;mf_input_option_value&quot;:&quot;Ouvriers&quot;,&quot;_id&quot;:&quot;b7083a5&quot;,&quot;mf_input_option_status&quot;:&quot;&quot;,&quot;mf_input_option_selected&quot;:&quot;&quot;},{&quot;mf_input_option_text&quot;:&quot;Professions m\u00e9dicales&quot;,&quot;mf_input_option_value&quot;:&quot;Professions m\u00e9dicales&quot;,&quot;_id&quot;:&quot;e386b7e&quot;,&quot;mf_input_option_status&quot;:&quot;&quot;,&quot;mf_input_option_selected&quot;:&quot;&quot;},{&quot;mf_input_option_text&quot;:&quot;Professions param\u00e9dicales&quot;,&quot;mf_input_option_value&quot;:&quot;Professions param\u00e9dicales&quot;,&quot;_id&quot;:&quot;0a2fe34&quot;,&quot;mf_input_option_status&quot;:&quot;&quot;,&quot;mf_input_option_selected&quot;:&quot;&quot;},{&quot;mf_input_option_text&quot;:&quot;Professions lib\u00e9rales&quot;,&quot;mf_input_option_value&quot;:&quot;Professions lib\u00e9rales&quot;,&quot;_id&quot;:&quot;8ea874c&quot;,&quot;mf_input_option_status&quot;:&quot;&quot;,&quot;mf_input_option_selected&quot;:&quot;&quot;},{&quot;mf_input_option_text&quot;:&quot;Commer\u00e7ants et leurs salari\u00e9s&quot;,&quot;mf_input_option_value&quot;:&quot;Commer\u00e7ants et leurs salari\u00e9s&quot;,&quot;_id&quot;:&quot;a6f7e99&quot;,&quot;mf_input_option_status&quot;:&quot;&quot;,&quot;mf_input_option_selected&quot;:&quot;&quot;},{&quot;mf_input_option_text&quot;:&quot;Artisans hors BTP et leurs salari\u00e9s&quot;,&quot;mf_input_option_value&quot;:&quot;Artisans hors BTP et leurs salari\u00e9s&quot;,&quot;_id&quot;:&quot;69558b5&quot;,&quot;mf_input_option_status&quot;:&quot;&quot;,&quot;mf_input_option_selected&quot;:&quot;&quot;},{&quot;mf_input_option_text&quot;:&quot;Artisans du BTP&quot;,&quot;mf_input_option_value&quot;:&quot;Artisans du BTP&quot;,&quot;_id&quot;:&quot;ab90d34&quot;,&quot;mf_input_option_status&quot;:&quot;&quot;,&quot;mf_input_option_selected&quot;:&quot;&quot;},{&quot;mf_input_option_text&quot;:&quot;Professions agricoles et p\u00e9ri-agricoles&quot;,&quot;mf_input_option_value&quot;:&quot;Professions agricoles et p\u00e9ri-agricoles&quot;,&quot;_id&quot;:&quot;cf4fa81&quot;,&quot;mf_input_option_status&quot;:&quot;&quot;,&quot;mf_input_option_selected&quot;:&quot;&quot;},{&quot;mf_input_option_text&quot;:&quot;Professions du transport&quot;,&quot;mf_input_option_value&quot;:&quot;Professions du transport&quot;,&quot;_id&quot;:&quot;aec4679&quot;,&quot;mf_input_option_status&quot;:&quot;&quot;,&quot;mf_input_option_selected&quot;:&quot;&quot;},{&quot;mf_input_option_text&quot;:&quot;Retrait\u00e9s&quot;,&quot;mf_input_option_value&quot;:&quot;Retrait\u00e9s&quot;,&quot;_id&quot;:&quot;6fce541&quot;,&quot;mf_input_option_status&quot;:&quot;&quot;,&quot;mf_input_option_selected&quot;:&quot;&quot;},{&quot;mf_input_option_text&quot;:&quot;Sans profession&quot;,&quot;mf_input_option_value&quot;:&quot;Sans profession&quot;,&quot;_id&quot;:&quot;310419e&quot;,&quot;mf_input_option_status&quot;:&quot;&quot;,&quot;mf_input_option_selected&quot;:&quot;&quot;}]}" data-widget_type="mf-select.default">
                <div className="elementor-widget-container">


        <div className="mf-input-wrapper">
                            <label className="mf-input-label" htmlFor="mf-input-select-ef7a7df">
                    ${ parent.decodeEntities(`Votre profession`) } 					<span className="mf-input-required-indicator">*</span>
                </label>

            <${props.Select}
                className=${"mf-input mf-input-select  " + ( validation.errors['mf-select'] ? 'mf-invalid' : '' )}
                classNamePrefix="mf_select"
                name="mf-select"
                placeholder="${ parent.decodeEntities(`Sélectionner`) } "
                isSearchable=${false}
                options=${[{"label":"Cadres","value":"Cadres","isDisabled":false},{"label":"Employ\u00e9s, A. de ma\u00eetrise","value":"Employ\u00e9s, A. de ma\u00eetrise","isDisabled":false},{"label":"Ouvriers","value":"Ouvriers","isDisabled":false},{"label":"Professions m\u00e9dicales","value":"Professions m\u00e9dicales","isDisabled":false},{"label":"Professions param\u00e9dicales","value":"Professions param\u00e9dicales","isDisabled":false},{"label":"Professions lib\u00e9rales","value":"Professions lib\u00e9rales","isDisabled":false},{"label":"Commer\u00e7ants et leurs salari\u00e9s","value":"Commer\u00e7ants et leurs salari\u00e9s","isDisabled":false},{"label":"Artisans hors BTP et leurs salari\u00e9s","value":"Artisans hors BTP et leurs salari\u00e9s","isDisabled":false},{"label":"Artisans du BTP","value":"Artisans du BTP","isDisabled":false},{"label":"Professions agricoles et p\u00e9ri-agricoles","value":"Professions agricoles et p\u00e9ri-agricoles","isDisabled":false},{"label":"Professions du transport","value":"Professions du transport","isDisabled":false},{"label":"Retrait\u00e9s","value":"Retrait\u00e9s","isDisabled":false},{"label":"Sans profession","value":"Sans profession","isDisabled":false}]}
                value=${parent.getValue("mf-select") ? [{"label":"Cadres","value":"Cadres","isDisabled":false},{"label":"Employ\u00e9s, A. de ma\u00eetrise","value":"Employ\u00e9s, A. de ma\u00eetrise","isDisabled":false},{"label":"Ouvriers","value":"Ouvriers","isDisabled":false},{"label":"Professions m\u00e9dicales","value":"Professions m\u00e9dicales","isDisabled":false},{"label":"Professions param\u00e9dicales","value":"Professions param\u00e9dicales","isDisabled":false},{"label":"Professions lib\u00e9rales","value":"Professions lib\u00e9rales","isDisabled":false},{"label":"Commer\u00e7ants et leurs salari\u00e9s","value":"Commer\u00e7ants et leurs salari\u00e9s","isDisabled":false},{"label":"Artisans hors BTP et leurs salari\u00e9s","value":"Artisans hors BTP et leurs salari\u00e9s","isDisabled":false},{"label":"Artisans du BTP","value":"Artisans du BTP","isDisabled":false},{"label":"Professions agricoles et p\u00e9ri-agricoles","value":"Professions agricoles et p\u00e9ri-agricoles","isDisabled":false},{"label":"Professions du transport","value":"Professions du transport","isDisabled":false},{"label":"Retrait\u00e9s","value":"Retrait\u00e9s","isDisabled":false},{"label":"Sans profession","value":"Sans profession","isDisabled":false}].filter(item => item.value === parent.getValue("mf-select"))[0] : []}
                onChange=${(e)=> parent.handleSelect(e, "mf-select")}
                ref=${() => {
                                    register({ name: "mf-select" }, parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":1,"maxLength":"","type":"none","required":true}));
                    if ( parent.getValue("mf-select") === '' && false ) {
                    parent.setValue( 'mf-select', '', true );
                        parent.handleChange({
                            target: {
                                name: 'mf-select',
                                value: ''
                            }
                        });
                    }
                }}
                />

                            <${validation.ErrorMessage}
                    errors=${validation.errors}
                    name="mf-select"
                    as=${html`<span className="mf-error-message"></span>`}
                    />
                                </div>


                        </div>
                </div>
                <div className="elementor-element elementor-element-12a0ac9 elementor-widget elementor-widget-mf-text" data-id="12a0ac9" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;banque&quot;}" data-widget_type="mf-text.default">
                <div className="elementor-widget-container">

        <div className="mf-input-wrapper">
                            <label className="mf-input-label" htmlFor="mf-input-text-12a0ac9">
                    ${ parent.decodeEntities(`Votre banque`) } 					<span className="mf-input-required-indicator">*</span>
                </label>

            <input
                type="text"
                className="mf-input "
                id="mf-input-text-12a0ac9"
                name="banque"
                placeholder="${ parent.decodeEntities(`Votre banque`) } "
                                    onInput=${parent.handleChange}
                    onBlur=${parent.handleChange}
                    aria-invalid=${validation.errors['banque'] ? 'true' : 'false'}
                    ref=${el =>{
                                                parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":1,"maxLength":"","type":"none","required":true,"expression":"null"}, el)
                    }}
                                />

                            <${validation.ErrorMessage}
                    errors=${validation.errors}
                    name="banque"
                    as=${html`<span className="mf-error-message"></span>`}
                    />

                    </div>

                        </div>
                </div>
                <div className="elementor-element elementor-element-a7d326f elementor-widget elementor-widget-mf-text" data-id="a7d326f" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;nom&quot;}" data-widget_type="mf-text.default">
                <div className="elementor-widget-container">

        <div className="mf-input-wrapper">
                            <label className="mf-input-label" htmlFor="mf-input-text-a7d326f">
                    ${ parent.decodeEntities(`Nom`) } 					<span className="mf-input-required-indicator">*</span>
                </label>

            <input
                type="text"
                className="mf-input "
                id="mf-input-text-a7d326f"
                name="nom"
                placeholder="${ parent.decodeEntities(`Nom`) } "
                                    onInput=${parent.handleChange}
                    onBlur=${parent.handleChange}
                    aria-invalid=${validation.errors['nom'] ? 'true' : 'false'}
                    ref=${el =>{
                                                parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":1,"maxLength":"","type":"none","required":true,"expression":"null"}, el)
                    }}
                                />

                            <${validation.ErrorMessage}
                    errors=${validation.errors}
                    name="nom"
                    as=${html`<span className="mf-error-message"></span>`}
                    />

                    </div>

                        </div>
                </div>
                <div className="elementor-element elementor-element-8bc3d3f elementor-widget elementor-widget-mf-text" data-id="8bc3d3f" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;prenom&quot;}" data-widget_type="mf-text.default">
                <div className="elementor-widget-container">

        <div className="mf-input-wrapper">
                            <label className="mf-input-label" htmlFor="mf-input-text-8bc3d3f">
                    ${ parent.decodeEntities(`Prénom`) } 					<span className="mf-input-required-indicator">*</span>
                </label>

            <input
                type="text"
                className="mf-input "
                id="mf-input-text-8bc3d3f"
                name="prenom"
                placeholder="${ parent.decodeEntities(`Prénom`) } "
                                    onInput=${parent.handleChange}
                    onBlur=${parent.handleChange}
                    aria-invalid=${validation.errors['prenom'] ? 'true' : 'false'}
                    ref=${el =>{
                                                parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":1,"maxLength":"","type":"none","required":true,"expression":"null"}, el)
                    }}
                                />

                            <${validation.ErrorMessage}
                    errors=${validation.errors}
                    name="prenom"
                    as=${html`<span className="mf-error-message"></span>`}
                    />

                    </div>

                        </div>
                </div>
                <div className="elementor-element elementor-element-f3516ab elementor-widget elementor-widget-mf-email" data-id="f3516ab" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;mf-email&quot;}" data-widget_type="mf-email.default">
                <div className="elementor-widget-container">

        <div className="mf-input-wrapper">
                            <label className="mf-input-label" htmlFor="mf-input-email-f3516ab">
                    ${ parent.decodeEntities(`E-mail`) } 					<span className="mf-input-required-indicator">*</span>
                </label>

            <input
                type="email"

                defaultValue=""
                className="mf-input "
                id="mf-input-email-f3516ab"
                name="mf-email"
                placeholder="${ parent.decodeEntities(`E-mail`) } "

                onBlur=${parent.handleChange} onFocus=${parent.handleChange} aria-invalid=${validation.errors['mf-email'] ? 'true' : 'false' }
                ref=${el=> parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","emailMessage":"Veuillez saisir une adresse de messagerie valide","minLength":1,"maxLength":"","type":"none","required":true,"expression":"null"}, el)}
                            />

                        <${validation.ErrorMessage}
                errors=${validation.errors}
                name="mf-email"
                as=${html`<span className="mf-error-message"></span>`}
            />

                    </div>

                </div>
                </div>
                <div className="elementor-element elementor-element-61eb343 elementor-widget elementor-widget-mf-text" data-id="61eb343" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;adresse&quot;}" data-widget_type="mf-text.default">
                <div className="elementor-widget-container">

        <div className="mf-input-wrapper">
                            <label className="mf-input-label" htmlFor="mf-input-text-61eb343">
                    ${ parent.decodeEntities(`Adresse`) } 					<span className="mf-input-required-indicator">*</span>
                </label>

            <input
                type="text"
                className="mf-input "
                id="mf-input-text-61eb343"
                name="adresse"
                placeholder="${ parent.decodeEntities(`Adresse`) } "
                                    onInput=${parent.handleChange}
                    onBlur=${parent.handleChange}
                    aria-invalid=${validation.errors['adresse'] ? 'true' : 'false'}
                    ref=${el =>{
                                                parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":1,"maxLength":"","type":"none","required":true,"expression":"null"}, el)
                    }}
                                />

                            <${validation.ErrorMessage}
                    errors=${validation.errors}
                    name="adresse"
                    as=${html`<span className="mf-error-message"></span>`}
                    />

                    </div>

                        </div>
                </div>
                <div className="elementor-element elementor-element-63dcd8c elementor-widget elementor-widget-mf-text" data-id="63dcd8c" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;code-postal&quot;}" data-widget_type="mf-text.default">
                <div className="elementor-widget-container">

        <div className="mf-input-wrapper">
                            <label className="mf-input-label" htmlFor="mf-input-text-63dcd8c">
                    ${ parent.decodeEntities(`Code postal`) } 					<span className="mf-input-required-indicator">*</span>
                </label>

            <input
                type="text"
                className="mf-input "
                id="mf-input-text-63dcd8c"
                name="code-postal"
                placeholder="${ parent.decodeEntities(`Code postal`) } "
                                    onInput=${parent.handleChange}
                    onBlur=${parent.handleChange}
                    aria-invalid=${validation.errors['code-postal'] ? 'true' : 'false'}
                    ref=${el =>{
                                                parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":1,"maxLength":"","type":"none","required":true,"expression":"null"}, el)
                    }}
                                />

                            <${validation.ErrorMessage}
                    errors=${validation.errors}
                    name="code-postal"
                    as=${html`<span className="mf-error-message"></span>`}
                    />

                    </div>

                        </div>
                </div>
                <div className="elementor-element elementor-element-6a07518 elementor-widget elementor-widget-mf-text" data-id="6a07518" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;mf-text&quot;}" data-widget_type="mf-text.default">
                <div className="elementor-widget-container">

        <div className="mf-input-wrapper">
                            <label className="mf-input-label" htmlFor="mf-input-text-6a07518">
                    ${ parent.decodeEntities(`Ville`) } 					<span className="mf-input-required-indicator">*</span>
                </label>

            <input
                type="text"
                className="mf-input "
                id="mf-input-text-6a07518"
                name="mf-text"
                placeholder="${ parent.decodeEntities(`Ville`) } "
                                    onInput=${parent.handleChange}
                    onBlur=${parent.handleChange}
                    aria-invalid=${validation.errors['mf-text'] ? 'true' : 'false'}
                    ref=${el =>{
                                                parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":1,"maxLength":"","type":"none","required":true,"expression":"null"}, el)
                    }}
                                />

                            <${validation.ErrorMessage}
                    errors=${validation.errors}
                    name="mf-text"
                    as=${html`<span className="mf-error-message"></span>`}
                    />

                    </div>

                        </div>
                </div>
                <div className="elementor-element elementor-element-bf0613b elementor-widget elementor-widget-mf-gdpr-consent" data-id="bf0613b" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;mf-gdpr-consent&quot;}" data-widget_type="mf-gdpr-consent.default">
                <div className="elementor-widget-container">

        <div className="mf-input-wrapper">

            <div className="mf-checkbox multi-option-input-type" id="mf-input-gdpr-bf0613b">
                <div className="mf-checkbox-option">
                    <label>
                                                <input
                            type="checkbox"
                            className="mf-input mf-checkbox-input "
                            name="mf-gdpr-consent"
                                                            onInput=${ parent.handleOptin }
                                aria-invalid=${validation.errors['mf-gdpr-consent'] ? 'true' : 'false'}
                                ref=${ el => parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":1,"maxLength":"","type":"none","required":false,"expression":"null"}, el) }
                                                        />
                        <span>
                            En cliquant sur « Envoyer », j’accepte qu’un conseiller Assurances de Rueil, m’appelle pour m’accompagner dans le choix de mon assurance.						</span>
                    </label>
                </div>
            </div>

                            <${validation.ErrorMessage}
                    errors=${validation.errors}
                    name="mf-gdpr-consent"
                    as=${html`<span className="mf-error-message"></span>`}
                    />

                    </div>

                        </div>
                </div>
                <div className="elementor-element elementor-element-a432854 elementor-widget elementor-widget-mf-gdpr-consent" data-id="a432854" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;mf-gdpr-consent&quot;}" data-widget_type="mf-gdpr-consent.default">
                <div className="elementor-widget-container">

        <div className="mf-input-wrapper">

            <div className="mf-checkbox multi-option-input-type" id="mf-input-gdpr-a432854">
                <div className="mf-checkbox-option">
                    <label>
                                                <input
                            type="checkbox"
                            className="mf-input mf-checkbox-input "
                            name="mf-gdpr-consent"
                                                            onInput=${ parent.handleOptin }
                                aria-invalid=${validation.errors['mf-gdpr-consent'] ? 'true' : 'false'}
                                ref=${ el => parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":1,"maxLength":"","type":"none","required":true,"expression":"null"}, el) }
                                                        />
                        <span>
                            J'accepte le traitement de mes données personnelles conformément au RGPD. <a href="/politique-de-confidentialite/">EN SAVOIR PLUS</a>						</span>
                    </label>
                </div>
            </div>

                            <${validation.ErrorMessage}
                    errors=${validation.errors}
                    name="mf-gdpr-consent"
                    as=${html`<span className="mf-error-message"></span>`}
                    />

                    </div>

                        </div>
                </div>
                <div className="elementor-element elementor-element-3a46b2f mf-btn--center elementor-widget elementor-widget-mf-button" data-id="3a46b2f" data-element_type="widget" data-widget_type="mf-button.default">
                <div className="elementor-widget-container">
                            <div className="mf-btn-wraper " data-mf-form-conditional-logic-requirement="">
                            <button type="submit" className="metform-btn metform-submit-btn " id="">
                    <span>${ parent.decodeEntities(`Envoyer`) } </span>
                </button>
                    </div>
                        </div>
                </div>
                    </div>
        </div>
                    </div>
        </section>
                </div>
                            `}
                    </div>

                    ${is_dummy_markup ? message_position === 'bottom' ? props.ResponseDummyMarkup(message_successIcon, message_proClass) : '' : ''}
                    ${is_dummy_markup ? ' ' : message_position === 'bottom' ? props.SubmitResponseMarkup`${parent}${state}${message_successIcon}${message_errorIcon}${message_proClass}` : ''}

                </form>
            `
        </script>

        </div>



    </div>
  </section>
  <section class="adr-next-steps" aria-label="Après votre demande">
    <article class="adr-step-card"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-house-gold.svg" alt=""><h2>Analyse</h2><p>Le cabinet étudie votre profil, votre banque et les garanties attendues.</p></article>
    <article class="adr-step-card"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-people-gold.svg" alt=""><h2>Comparaison</h2><p>Les solutions sont comparées auprès de partenaires reconnus.</p></article>
    <article class="adr-step-card"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-briefcase-gold.svg" alt=""><h2>Accompagnement</h2><p>Un conseiller vous aide à choisir une proposition adaptée à votre situation.</p></article>
  </section>

  <script>
      /* adr-quote-form-fr-v12: stable birthdate selects and exclusive choice pairs. */
      (function () {
        if (window.adrQuoteFormFrV12) return;
        window.adrQuoteFormFrV12 = true;

        function normalizeDateValue(value) {
      var raw = String(value || '').trim();
      var dashMatch = raw.match(/^(\d{1,2})-(\d{1,2})-(\d{4})$/);
      if (dashMatch) return dashMatch[2].padStart(2, '0') + '/' + dashMatch[1].padStart(2, '0') + '/' + dashMatch[3];
      var slashMatch = raw.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
      if (slashMatch && Number(slashMatch[1]) > 12) return slashMatch[1].padStart(2, '0') + '/' + slashMatch[2].padStart(2, '0') + '/' + slashMatch[3];
      return raw;
    }



    function getBirthYearBounds() {
      var currentYear = new Date().getFullYear();
      return { min: currentYear - 100, max: currentYear - 16 };
    }

        function buildBirthdateSelects(input) {
          if (!input) return;
      var wrapper = input.closest('.mf-input-wrapper') || input.parentElement;
      if (!wrapper) return;
      input.classList.add('adr-native-date-source');
      input.setAttribute('aria-hidden', 'true');
      input.setAttribute('tabindex', '-1');
      input.setAttribute('autocomplete', 'bday');
      input.readOnly = true;
      document.querySelectorAll('.flatpickr-calendar').forEach(function (calendar) {
        calendar.setAttribute('aria-hidden', 'true');
      });
          var lang = 'fr';
          var ui = wrapper.querySelector('.adr-birthdate-selects');
          if (ui && ui.dataset.lang === lang && ui.querySelectorAll('select').length === 3) {
            ui.setAttribute('aria-label', 'Date de naissance');
            return;
      }
      var existing = ui ? {
        day: ui.querySelector('.adr-birthdate-day') && ui.querySelector('.adr-birthdate-day').value,
        month: ui.querySelector('.adr-birthdate-month') && ui.querySelector('.adr-birthdate-month').value,
        year: ui.querySelector('.adr-birthdate-year') && ui.querySelector('.adr-birthdate-year').value
      } : null;
      if (!ui) {
        ui = document.createElement('div');
        ui.className = 'adr-birthdate-selects';
        ui.setAttribute('role', 'group');
        input.insertAdjacentElement('afterend', ui);
          }
          ui.dataset.lang = lang;
          ui.setAttribute('aria-label', 'Date de naissance');
          var current = parseDateParts(input.dataset.adrDisplayDate || input.value) || existing;
          ui.innerHTML = '';
          var day = makeBirthSelect('day', 'Jour');
          for (var d = 1; d <= 31; d += 1) appendOption(day, String(d).padStart(2, '0'), String(d).padStart(2, '0'));
          var month = makeBirthSelect('month', 'Mois');
          var monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
          for (var m = 1; m <= 12; m += 1) appendOption(month, String(m).padStart(2, '0'), monthNames[m - 1]);
          var year = makeBirthSelect('year', 'Année');
      var bounds = getBirthYearBounds();
      for (var y = bounds.max; y >= bounds.min; y -= 1) appendOption(year, String(y), String(y));
      ui.append(day, month, year);
      if (current) {
        day.value = current.day || '';
        month.value = current.month || '';
        year.value = current.year || '';
      }
      [day, month, year].forEach(function (select) {
        select.addEventListener('change', function () { syncBirthdateInput(input, day, month, year); });
      });
      syncBirthdateInput(input, day, month, year, true);
    }

    function makeBirthSelect(part, placeholder) {
      var select = document.createElement('select');
      select.className = 'adr-birthdate-select adr-birthdate-' + part;
      select.dataset.birthPart = part;
      select.setAttribute('aria-label', placeholder);
      appendOption(select, '', placeholder);
      return select;
    }

    function appendOption(select, value, text) {
      var option = document.createElement('option');
      option.value = value;
      option.textContent = text;
      select.appendChild(option);
    }

    function parseDateParts(value) {
      var match = normalizeDateValue(value).match(/^(\d{1,2})[\/.-](\d{1,2})[\/.-](\d{4})$/);
      if (!match) return null;
      var day = match[1].padStart(2, '0');
      var month = match[2].padStart(2, '0');
      var year = match[3];
      var bounds = getBirthYearBounds();
      if (Number(year) < bounds.min || Number(year) > bounds.max) return null;
      return { day: day, month: month, year: year };
    }

    function syncBirthdateInput(input, day, month, year, preserveEmpty) {
      if (!input) return;
      var hasFullDate = day.value && month.value && year.value;
      if (hasFullDate) {
        input.value = month.value + '-' + day.value + '-' + year.value;
        input.dataset.adrDisplayDate = day.value + '/' + month.value + '/' + year.value;
      } else if (!preserveEmpty) {
        input.value = '';
      }
      input.dispatchEvent(new Event('input', { bubbles: true }));
      input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function installExclusiveChoiceGroups(root) {
      if (!root) return;
      ['mf-checkbox', 'fumeur'].forEach(function (name) {
        var selector = 'input[type="checkbox"][name="' + name + '"]';
        Array.prototype.forEach.call(root.querySelectorAll(selector), function (box) {
          if (box.dataset.adrExclusiveInstalled === 'true') return;
          box.dataset.adrExclusiveInstalled = 'true';
          box.addEventListener('change', function () {
            if (!box.checked) return;
            Array.prototype.forEach.call(root.querySelectorAll(selector), function (other) {
              if (other === box || other.type !== 'checkbox') return;
              if (!other.checked) return;
              other.checked = false;
              other.removeAttribute('checked');
              other.dispatchEvent(new Event('input', { bubbles: true }));
              other.dispatchEvent(new Event('change', { bubbles: true }));
            });
          });
        });
      });
    }

        function configureDateField(root) {
          var input = root.querySelector('input[name="mf-date"], #mf-input-date-3892e3b, .flatpickr-input');
          if (!input) return;
          input.setAttribute('placeholder', 'JJ/MM/AAAA');
      input.setAttribute('inputmode', 'numeric');
      input.setAttribute('autocomplete', 'bday');
      input.dataset.adrDateFormat = 'd/m/Y';
      if (input.value) {
        var normalized = normalizeDateValue(input.value);
        if (normalized && normalized !== input.value) input.value = normalized;
      }
          buildBirthdateSelects(input);
        }

            function applyQuoteEnhancements() {
              var root = document.querySelector('.adr-quote-stage');
              if (!root) return;
              root.dataset.formLang = 'fr';
          var formScope = root.querySelector('.adr-form-card') || root;
          configureDateField(formScope);
          installExclusiveChoiceGroups(formScope);
          document.querySelectorAll('.react-select__menu, [class*="menu"], [id^="react-select-"]').forEach(function (scope) {
            if (root.contains(scope)) {
              configureDateField(formScope);
            }
          });
    }

        function install() {
          var root = document.querySelector('.adr-quote-stage');
          if (!root) return;
          var pending = false;
          var observer = new MutationObserver(function () {
            if (pending) return;
            pending = true;
            setTimeout(function () { pending = false; applyQuoteEnhancements(); }, 40);
          });
          observer.observe(root, { childList: true, subtree: true, characterData: true, attributes: true, attributeFilter: ['placeholder', 'value'] });
          applyQuoteEnhancements();
          [250, 800, 1600, 3200].forEach(function (delay) { setTimeout(applyQuoteEnhancements, delay); });
        }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', install);
    else install();
  }());
  </script>
</div>
ADR_STAGE_DEVIS
        ,
        'cabinet' => <<<'ADR_STAGE_CABINET'
<div class="adr-page-stage adr-cabinet-stage">
  <style>
    body.page-id-7358 .ekit-template-content-header,
    body.page-id-7358 .site-footer,
    body.page-id-7358 footer.footer,
    body.page-id-7358 .ekit-template-content-footer,
    body.page-id-7358 .page-banner-area,
    body.page-id-7358 .breadcrumb { display: none !important; }
    .adr-page-stage, .adr-page-stage * { box-sizing: border-box; }
    .adr-page-stage {
      --adr-blue: #003478;
      --adr-blue-soft: #6da9ff;
      --adr-ink: #07192f;
      --adr-muted: #657386;
      --adr-line: rgba(0, 52, 120, 0.18);
      --adr-paper: rgba(255, 255, 255, 0.88);
      --adr-solid: #ffffff;
      --adr-wash: #eef5fb;
      --adr-gold: #d7a53c;
      --adr-shadow: 0 24px 70px rgba(0, 28, 64, 0.16);
      --adr-soft-shadow: 0 14px 36px rgba(0, 28, 64, 0.1);
      width: 100vw;
      margin-left: calc(50% - 50vw);
      margin-right: calc(50% - 50vw);
      margin-top: -1px;
      padding: clamp(14px, 2vw, 24px) clamp(14px, 2vw, 24px) 64px;
      background: radial-gradient(circle at 10% 0%, rgba(109,169,255,.2), transparent 30%), var(--adr-wash);
      color: var(--adr-ink);
      font-family: Rubik, "Open Sans", Arial, sans-serif;
      overflow: clip;
    }
    .adr-page-stage:has(#adr-theme-toggle:checked) {
      --adr-blue: #83b7ff;
      --adr-blue-soft: #74a9f7;
      --adr-ink: #edf6ff;
      --adr-muted: #a8b8c8;
      --adr-line: rgba(169, 201, 234, 0.24);
      --adr-paper: rgba(12, 29, 49, 0.84);
      --adr-solid: #0e2135;
      --adr-wash: #06111d;
      --adr-shadow: 0 24px 70px rgba(0, 0, 0, 0.34);
      --adr-soft-shadow: 0 14px 36px rgba(0, 0, 0, 0.24);
    }
    .adr-page-stage a { color: inherit; }
    .adr-page-stage img { display: block; max-width: 100%; }
    .adr-page-stage [id] { scroll-margin-top: 140px; }
    .adr-switch-input { position: absolute; opacity: 0; pointer-events: none; }
    .adr-floating-panel {
      position: sticky;
      top: clamp(12px, 2vw, 24px);
      z-index: 9;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      width: min(100%, 1288px);
      margin: 0 auto 18px;
      border: 1px solid var(--adr-line);
      border-radius: 8px;
      padding: 7px 8px 7px 12px;
      background: var(--adr-paper);
      box-shadow: 0 18px 54px rgba(0, 0, 0, 0.18);
      backdrop-filter: blur(18px);
    }
    body.admin-bar .adr-floating-panel { top: calc(32px + clamp(12px, 2vw, 24px)); }
    .adr-brand { display: inline-flex; align-items: center; gap: 13px; min-width: 0; color: var(--adr-blue); text-decoration: none; }
    .adr-brand img { width: clamp(48px, 5vw, 78px); height: clamp(48px, 5vw, 78px); border-radius: 50%; }
    .adr-brand strong { font-size: clamp(24px, 3vw, 42px); line-height: 1; letter-spacing: 0; white-space: nowrap; }
    .adr-panel-actions { display: flex; align-items: stretch; justify-content: flex-end; gap: 8px; }
    .adr-button, .adr-switch-label {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 44px;
      height: clamp(44px, 4.6vw, 64px);
      border-radius: 8px;
      padding: 12px 18px;
      border: 1px solid var(--adr-line);
      font-weight: 900;
      line-height: 1.05;
      text-decoration: none;
      cursor: pointer;
      white-space: nowrap;
      margin: 0 !important;
    }
    .adr-button-primary, .adr-button-primary:visited, .adr-button-primary:hover, .adr-button-primary:focus {
      border-color: transparent;
      background: var(--adr-blue);
      color: #fff !important;
      box-shadow: 0 16px 34px rgba(0, 52, 120, 0.22);
    }
    .adr-page-stage:has(#adr-theme-toggle:checked) .adr-button-primary { color: #06111d !important; }
    .adr-switch-label { min-width: 58px; background: color-mix(in srgb, var(--adr-blue) 7%, var(--adr-solid)); color: var(--adr-blue); font-size: 13px; }
    .adr-page-stage:has(#adr-theme-toggle:checked) label[for="adr-theme-toggle"] { background: var(--adr-blue); color: #06111d; }
    .adr-hero, .adr-form-section, .adr-next-steps, .adr-mini-nav { width: min(100%, 1180px); margin-left: auto; margin-right: auto; }
    .adr-hero {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(320px, .75fr);
      min-height: 430px;
      overflow: hidden;
      border: 1px solid var(--adr-line);
      border-radius: 8px;
      background: var(--adr-solid);
      box-shadow: var(--adr-shadow);
    }
    .adr-hero-copy { display: grid; align-content: center; gap: 18px; padding: clamp(28px, 5vw, 70px); }
    .adr-kicker { margin: 0; color: var(--adr-gold); font-size: 14px; font-weight: 900; text-transform: uppercase; letter-spacing: .08em; }
    .adr-hero h1 { margin: 0; color: var(--adr-blue); font-size: clamp(22px, 3.02vw, 41px); line-height: 1; letter-spacing: 0; }
    .adr-hero p { max-width: 680px; margin: 0; color: var(--adr-muted); font-size: clamp(18px, 2vw, 23px); line-height: 1.55; }
    .adr-hero-media { min-height: 340px; background: #d9e7f2; }
    .adr-hero-media img { width: 100%; height: 100%; object-fit: cover; }
    .adr-mini-nav { position: sticky; top: 116px; z-index: 8; display: flex; flex-wrap: wrap; justify-content: center; gap: 6px; margin-top: 18px; margin-bottom: 18px; border: 1px solid var(--adr-line); border-radius: 8px; padding: 8px; background: var(--adr-paper); backdrop-filter: blur(18px); }
    .adr-mini-nav a { border-radius: 999px; padding: 9px 12px; color: var(--adr-blue); font-size: 13px; font-weight: 900; text-decoration: none; }
    .adr-form-section { display: grid; grid-template-columns: minmax(270px, .52fr) minmax(0, 1fr); gap: 16px; align-items: start; }
    .adr-side-card, .adr-form-card, .adr-step-card { border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); }
    .adr-side-card { position: sticky; top: 178px; display: grid; gap: 14px; padding: clamp(22px, 3vw, 34px); }
    .adr-side-card h2, .adr-form-card h2, .adr-step-card h2 { margin: 0; color: var(--adr-blue); line-height: 1.12; letter-spacing: 0; }
    .adr-side-card p, .adr-form-card p, .adr-step-card p, .adr-side-card li { margin: 0; color: var(--adr-muted); line-height: 1.58; }
    .adr-side-card ol { display: grid; gap: 12px; margin: 6px 0 0; padding-left: 22px; font-weight: 900; color: var(--adr-muted); }
    .adr-form-card { padding: clamp(18px, 3vw, 28px); }
    .adr-form-intro { display: grid; gap: 8px; margin-bottom: 16px; }
    .adr-form-card .mf-form-wrapper,
    .adr-form-card .metform-form-content,
    .adr-form-card .elementor,
    .adr-form-card .elementor-section,
    .adr-form-card .elementor-container { max-width: none !important; width: 100% !important; }
    .adr-form-card input:not([type="checkbox"]):not([type="radio"]),
    .adr-form-card select,
    .adr-form-card textarea {
      min-height: 46px !important;
      border: 1px solid var(--adr-line) !important;
      border-radius: 8px !important;
      background: color-mix(in srgb, var(--adr-solid) 88%, transparent) !important;
      color: var(--adr-ink) !important;
      box-shadow: none !important;
    }
    .adr-form-card label,
    .adr-form-card .mf-input-label { color: var(--adr-ink) !important; font-weight: 800 !important; }
    .adr-form-card .metform-btn,
    .adr-form-card .metform-submit-btn { min-height: 50px !important; border-radius: 8px !important; background: var(--adr-blue) !important; color: #fff !important; font-weight: 900 !important; }
    .adr-next-steps { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-top: 18px; }
    .adr-step-card { display: grid; gap: 8px; padding: 20px; }
    .adr-step-card img { width: 48px; height: 48px; border-radius: 50%; padding: 8px; background: color-mix(in srgb, var(--adr-blue) 8%, white); }
    @media (max-width: 980px) { .adr-form-section, .adr-hero { grid-template-columns: 1fr; } .adr-side-card, .adr-mini-nav { position: static; } .adr-hero-media { order: -1; min-height: 240px; } }
    @media (max-width: 760px) { .adr-floating-panel, .adr-panel-actions { align-items: stretch; flex-direction: column; } .adr-brand strong { white-space: normal; } .adr-panel-actions, .adr-button, .adr-switch-label { width: 100%; } }
    @media (max-width: 640px) { .adr-page-stage { padding-left: 10px; padding-right: 10px; } .adr-hero h1 { font-size: 19px; } .adr-hero-copy { padding: 22px 18px; } .adr-next-steps { grid-template-columns: 1fr; } }
    .adr-info-grid { width: min(100%, 1180px); margin: 18px auto 0; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
    .adr-info-card { display: grid; gap: 10px; min-height: 250px; border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); padding: clamp(20px, 3vw, 30px); }
    .adr-info-card img { width: 52px; height: 52px; border-radius: 50%; padding: 8px; background: color-mix(in srgb, var(--adr-blue) 8%, white); }
    .adr-info-card h2, .adr-cta-strip h2 { margin: 0; color: var(--adr-blue); line-height: 1.12; letter-spacing: 0; }
    .adr-info-card p, .adr-cta-strip p { margin: 0; color: var(--adr-muted); line-height: 1.58; }
    .adr-cta-strip { width: min(100%, 1180px); margin: 18px auto 0; display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 18px; align-items: center; border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); padding: clamp(22px, 4vw, 38px); }
    @media (max-width: 900px) { .adr-info-grid { grid-template-columns: 1fr; } .adr-cta-strip { grid-template-columns: 1fr; } }


    /* adr-hero-media-patch-v1: keep stacked hero images compact on tablet and narrow desktop. */
    @media (max-width: 980px) {
      .adr-page-stage .adr-hero-media { height: clamp(210px, 32vw, 300px) !important; min-height: 0 !important; }
      .adr-page-stage .adr-hero-media img { width: 100% !important; height: 100% !important; object-fit: cover !important; }
    }

    /* adr-original-detail-v1: restored detailed copy from the original site for owner review. */
    .adr-detail-section { width: min(100%, 1180px); margin: 18px auto 0; display: grid; gap: 12px; }
    .adr-detail-header, .adr-detail-card { border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); }
    .adr-detail-header { display: grid; gap: 8px; padding: clamp(22px, 4vw, 34px); }
    .adr-detail-header h2, .adr-detail-card h2 { margin: 0; color: var(--adr-blue); line-height: 1.12; letter-spacing: 0; }
    .adr-detail-header p, .adr-detail-card p, .adr-detail-card li { margin: 0; color: var(--adr-muted); line-height: 1.58; }
    .adr-detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
    .adr-detail-card { display: grid; align-content: start; gap: 10px; padding: clamp(20px, 3vw, 30px); }
    .adr-detail-card ul { display: grid; gap: 7px; margin: 0; padding-left: 20px; }
    .adr-detail-note { color: var(--adr-gold) !important; font-weight: 900; text-transform: uppercase; font-size: 12px; letter-spacing: .08em; }
    @media (max-width: 840px) { .adr-detail-grid { grid-template-columns: 1fr; } }

</style>
  <input class="adr-switch-input" id="adr-theme-toggle" type="checkbox" aria-label="Basculer le thème jour/nuit">
  <header class="adr-floating-panel" aria-label="Navigation principale">
    <a class="adr-brand" href="/"><img decoding="async" src="https://assurancesderueil.fr/wp-content/uploads/2022/08/cropped-flaticon2.png" alt=""><strong>Assurances de Rueil</strong></a>
    <div class="adr-panel-actions">
      <a class="adr-button adr-button-primary" href="/demande-de-devis-assurance-a-rueil-malmaison/">Demander un devis</a>
      <label class="adr-switch-label" for="adr-theme-toggle">Jour/Nuit</label>
    </div>
  </header>
  <section class="adr-hero" aria-label="Cabinet">
    <div class="adr-hero-copy">
      <p class="adr-kicker">Cabinet</p>
      <h1>Cabinet de courtage en assurances</h1>
      <p>Les Assurances de Rueil sont un cabinet de courtage en assurances implanté à Rueil-Malmaison, dans les Hauts-de-Seine, depuis quatre générations.</p>
      <a class="adr-button adr-button-primary" href="/demande-de-devis-assurance-a-rueil-malmaison/">Demander un devis</a>
    </div>
    <div class="adr-hero-media"><img decoding="async" src="https://assurancesderueil.fr/wp-content/uploads/2019/08/feature_image2.jpg" alt="Cabinet de courtage en assurances à Rueil-Malmaison"></div>
  </section>
  <nav class="adr-mini-nav" aria-label="Pages principales">
    <a href="/">Accueil</a>
    <a href="/cabinet-de-courtage-en-assurances-rueil-malmaison/">Cabinet</a>
    <a href="/assurance-de-pret-a-rueil-malmaison/">Assurance de prêt</a>
    <a href="/assurance-particuliers-rueil-malmaison/">Particuliers</a>
    <a href="/assurance-entreprise-rueil-malmaison/">Professionnels</a>
    <a href="/courtier-en-assurances-de-rueil-malmaison/">Contact</a>
  </nav>
  <section class="adr-info-grid" id="notre-cabinet" aria-label="Cabinet de courtage en assurances à Rueil-Malmaison">
    <article class="adr-info-card" id="independance"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-house-gold.svg" alt=""><h2>Indépendance</h2><p>Son statut de courtier lui permet de sélectionner, en toute indépendance, les contrats les plus performants parmi ceux proposés par des partenaires reconnus comme Axa, April ou Generali.</p></article>
    <article class="adr-info-card" id="defense-assure"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-people-gold.svg" alt=""><h2>Défense de l&rsquo;assuré</h2><p>Le cabinet est le défenseur privilégié des intérêts de l&rsquo;assuré lors de la négociation de la garantie et des prix.</p></article>
    <article class="adr-info-card" id="suivi-complet"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-briefcase-gold.svg" alt=""><h2>Suivi complet</h2><p>Il s&rsquo;engage dans un suivi complet des prestations qu&rsquo;il propose, notamment dans le bon déroulement de la gestion d&rsquo;un sinistre.</p></article>
  </section>

  <section class="adr-detail-section" aria-label="Détails cabinet de courtage">
    <div class="adr-detail-header">
      <p class="adr-detail-note">Détails repris du site original</p>
      <h2>Notre cabinet de courtage</h2>
      <p>Assureur à Rueil-Malmaison, notre cabinet, composé d&rsquo;une équipe à taille humaine et bénéficiant d&rsquo;une forte expérience, met son expertise au service d&rsquo;une clientèle variée.</p>
    </div>
    <div class="adr-detail-grid">
      <article class="adr-detail-card" id="equipe">
        <h2>Une équipe à taille humaine</h2>
        <p>Nous accompagnons aussi bien les particuliers que les entreprises et professionnels, notamment les artisans, commerçants, professions libérales, ainsi que les petites et moyennes entreprises.</p>
      </article>
      <article class="adr-detail-card" id="objectifs">
        <h2>Nos objectifs</h2>
        <p>Notre objectif est de proposer des solutions d&rsquo;assurance sur mesure, adaptées aux besoins spécifiques de chaque client.</p>
      </article>
      <article class="adr-detail-card" id="disponibilite">
        <h2>Disponibilité</h2>
        <p>L&rsquo;équipe du cabinet s&rsquo;efforce d&rsquo;être la plus disponible et la plus réactive possible en toutes circonstances, dans un climat de confiance mutuelle.</p>
      </article>
      <article class="adr-detail-card" id="horaires">
        <h2>Horaires</h2>
        <p>Nos bureaux sont ouverts du lundi au vendredi, de 9:00 à 12:30 et de 14:00 à 18:00.</p>
      </article>
    </div>
  </section>

  <section class="adr-cta-strip" aria-label="Demande de devis">
    <div><h2>Contactez-nous</h2><p>Un échange avec le cabinet permet de comparer les solutions disponibles, d&rsquo;adapter les options à votre situation et de choisir les bonnes garanties.</p></div>
    <a class="adr-button adr-button-primary" href="/demande-de-devis-assurance-a-rueil-malmaison/">Demander un devis</a>
  </section>
</div>
ADR_STAGE_CABINET
        ,
        'pret' => <<<'ADR_STAGE_PRET'
<div class="adr-page-stage adr-loan-stage">
  <style>
    body.page-id-7754 .ekit-template-content-header,
    body.page-id-7754 .site-footer,
    body.page-id-7754 footer.footer,
    body.page-id-7754 .ekit-template-content-footer,
    body.page-id-7754 .page-banner-area,
    body.page-id-7754 .breadcrumb { display: none !important; }
    .adr-page-stage, .adr-page-stage * { box-sizing: border-box; }
    .adr-page-stage {
      --adr-blue: #003478;
      --adr-blue-soft: #6da9ff;
      --adr-ink: #07192f;
      --adr-muted: #657386;
      --adr-line: rgba(0, 52, 120, 0.18);
      --adr-paper: rgba(255, 255, 255, 0.88);
      --adr-solid: #ffffff;
      --adr-wash: #eef5fb;
      --adr-gold: #d7a53c;
      --adr-shadow: 0 24px 70px rgba(0, 28, 64, 0.16);
      --adr-soft-shadow: 0 14px 36px rgba(0, 28, 64, 0.1);
      width: 100vw;
      margin-left: calc(50% - 50vw);
      margin-right: calc(50% - 50vw);
      margin-top: -1px;
      padding: clamp(14px, 2vw, 24px) clamp(14px, 2vw, 24px) 64px;
      background: radial-gradient(circle at 10% 0%, rgba(109,169,255,.2), transparent 30%), var(--adr-wash);
      color: var(--adr-ink);
      font-family: Rubik, "Open Sans", Arial, sans-serif;
      overflow: clip;
    }
    .adr-page-stage:has(#adr-theme-toggle:checked) {
      --adr-blue: #83b7ff;
      --adr-blue-soft: #74a9f7;
      --adr-ink: #edf6ff;
      --adr-muted: #a8b8c8;
      --adr-line: rgba(169, 201, 234, 0.24);
      --adr-paper: rgba(12, 29, 49, 0.84);
      --adr-solid: #0e2135;
      --adr-wash: #06111d;
      --adr-shadow: 0 24px 70px rgba(0, 0, 0, 0.34);
      --adr-soft-shadow: 0 14px 36px rgba(0, 0, 0, 0.24);
    }
    .adr-page-stage a { color: inherit; }
    .adr-page-stage img { display: block; max-width: 100%; }
    .adr-page-stage [id] { scroll-margin-top: 140px; }
    .adr-switch-input { position: absolute; opacity: 0; pointer-events: none; }
    .adr-floating-panel {
      position: sticky;
      top: clamp(12px, 2vw, 24px);
      z-index: 9;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      width: min(100%, 1288px);
      margin: 0 auto 18px;
      border: 1px solid var(--adr-line);
      border-radius: 8px;
      padding: 7px 8px 7px 12px;
      background: var(--adr-paper);
      box-shadow: 0 18px 54px rgba(0, 0, 0, 0.18);
      backdrop-filter: blur(18px);
    }
    body.admin-bar .adr-floating-panel { top: calc(32px + clamp(12px, 2vw, 24px)); }
    .adr-brand { display: inline-flex; align-items: center; gap: 13px; min-width: 0; color: var(--adr-blue); text-decoration: none; }
    .adr-brand img { width: clamp(48px, 5vw, 78px); height: clamp(48px, 5vw, 78px); border-radius: 50%; }
    .adr-brand strong { font-size: clamp(24px, 3vw, 42px); line-height: 1; letter-spacing: 0; white-space: nowrap; }
    .adr-panel-actions { display: flex; align-items: stretch; justify-content: flex-end; gap: 8px; }
    .adr-button, .adr-switch-label {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 44px;
      height: clamp(44px, 4.6vw, 64px);
      border-radius: 8px;
      padding: 12px 18px;
      border: 1px solid var(--adr-line);
      font-weight: 900;
      line-height: 1.05;
      text-decoration: none;
      cursor: pointer;
      white-space: nowrap;
      margin: 0 !important;
    }
    .adr-button-primary, .adr-button-primary:visited, .adr-button-primary:hover, .adr-button-primary:focus {
      border-color: transparent;
      background: var(--adr-blue);
      color: #fff !important;
      box-shadow: 0 16px 34px rgba(0, 52, 120, 0.22);
    }
    .adr-page-stage:has(#adr-theme-toggle:checked) .adr-button-primary { color: #06111d !important; }
    .adr-switch-label { min-width: 58px; background: color-mix(in srgb, var(--adr-blue) 7%, var(--adr-solid)); color: var(--adr-blue); font-size: 13px; }
    .adr-page-stage:has(#adr-theme-toggle:checked) label[for="adr-theme-toggle"] { background: var(--adr-blue); color: #06111d; }
    .adr-hero, .adr-form-section, .adr-next-steps, .adr-mini-nav { width: min(100%, 1180px); margin-left: auto; margin-right: auto; }
    .adr-hero {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(320px, .75fr);
      min-height: 430px;
      overflow: hidden;
      border: 1px solid var(--adr-line);
      border-radius: 8px;
      background: var(--adr-solid);
      box-shadow: var(--adr-shadow);
    }
    .adr-hero-copy { display: grid; align-content: center; gap: 18px; padding: clamp(28px, 5vw, 70px); }
    .adr-kicker { margin: 0; color: var(--adr-gold); font-size: 14px; font-weight: 900; text-transform: uppercase; letter-spacing: .08em; }
    .adr-hero h1 { margin: 0; color: var(--adr-blue); font-size: clamp(22px, 3.02vw, 41px); line-height: 1; letter-spacing: 0; }
    .adr-hero p { max-width: 680px; margin: 0; color: var(--adr-muted); font-size: clamp(18px, 2vw, 23px); line-height: 1.55; }
    .adr-hero-media { min-height: 340px; background: #d9e7f2; }
    .adr-hero-media img { width: 100%; height: 100%; object-fit: cover; }
    .adr-mini-nav { position: sticky; top: 116px; z-index: 8; display: flex; flex-wrap: wrap; justify-content: center; gap: 6px; margin-top: 18px; margin-bottom: 18px; border: 1px solid var(--adr-line); border-radius: 8px; padding: 8px; background: var(--adr-paper); backdrop-filter: blur(18px); }
    .adr-mini-nav a { border-radius: 999px; padding: 9px 12px; color: var(--adr-blue); font-size: 13px; font-weight: 900; text-decoration: none; }
    .adr-form-section { display: grid; grid-template-columns: minmax(270px, .52fr) minmax(0, 1fr); gap: 16px; align-items: start; }
    .adr-side-card, .adr-form-card, .adr-step-card { border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); }
    .adr-side-card { position: sticky; top: 178px; display: grid; gap: 14px; padding: clamp(22px, 3vw, 34px); }
    .adr-side-card h2, .adr-form-card h2, .adr-step-card h2 { margin: 0; color: var(--adr-blue); line-height: 1.12; letter-spacing: 0; }
    .adr-side-card p, .adr-form-card p, .adr-step-card p, .adr-side-card li { margin: 0; color: var(--adr-muted); line-height: 1.58; }
    .adr-side-card ol { display: grid; gap: 12px; margin: 6px 0 0; padding-left: 22px; font-weight: 900; color: var(--adr-muted); }
    .adr-form-card { padding: clamp(18px, 3vw, 28px); }
    .adr-form-intro { display: grid; gap: 8px; margin-bottom: 16px; }
    .adr-form-card .mf-form-wrapper,
    .adr-form-card .metform-form-content,
    .adr-form-card .elementor,
    .adr-form-card .elementor-section,
    .adr-form-card .elementor-container { max-width: none !important; width: 100% !important; }
    .adr-form-card input:not([type="checkbox"]):not([type="radio"]),
    .adr-form-card select,
    .adr-form-card textarea {
      min-height: 46px !important;
      border: 1px solid var(--adr-line) !important;
      border-radius: 8px !important;
      background: color-mix(in srgb, var(--adr-solid) 88%, transparent) !important;
      color: var(--adr-ink) !important;
      box-shadow: none !important;
    }
    .adr-form-card label,
    .adr-form-card .mf-input-label { color: var(--adr-ink) !important; font-weight: 800 !important; }
    .adr-form-card .metform-btn,
    .adr-form-card .metform-submit-btn { min-height: 50px !important; border-radius: 8px !important; background: var(--adr-blue) !important; color: #fff !important; font-weight: 900 !important; }
    .adr-next-steps { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-top: 18px; }
    .adr-step-card { display: grid; gap: 8px; padding: 20px; }
    .adr-step-card img { width: 48px; height: 48px; border-radius: 50%; padding: 8px; background: color-mix(in srgb, var(--adr-blue) 8%, white); }
    @media (max-width: 980px) { .adr-form-section, .adr-hero { grid-template-columns: 1fr; } .adr-side-card, .adr-mini-nav { position: static; } .adr-hero-media { order: -1; min-height: 240px; } }
    @media (max-width: 760px) { .adr-floating-panel, .adr-panel-actions { align-items: stretch; flex-direction: column; } .adr-brand strong { white-space: normal; } .adr-panel-actions, .adr-button, .adr-switch-label { width: 100%; } }
    @media (max-width: 640px) { .adr-page-stage { padding-left: 10px; padding-right: 10px; } .adr-hero h1 { font-size: 19px; } .adr-hero-copy { padding: 22px 18px; } .adr-next-steps { grid-template-columns: 1fr; } }
    .adr-info-grid { width: min(100%, 1180px); margin: 18px auto 0; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
    .adr-info-card { display: grid; gap: 10px; min-height: 250px; border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); padding: clamp(20px, 3vw, 30px); }
    .adr-info-card img { width: 52px; height: 52px; border-radius: 50%; padding: 8px; background: color-mix(in srgb, var(--adr-blue) 8%, white); }
    .adr-info-card h2, .adr-cta-strip h2 { margin: 0; color: var(--adr-blue); line-height: 1.12; letter-spacing: 0; }
    .adr-info-card p, .adr-cta-strip p { margin: 0; color: var(--adr-muted); line-height: 1.58; }
    .adr-cta-strip { width: min(100%, 1180px); margin: 18px auto 0; display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 18px; align-items: center; border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); padding: clamp(22px, 4vw, 38px); }
    @media (max-width: 900px) { .adr-info-grid { grid-template-columns: 1fr; } .adr-cta-strip { grid-template-columns: 1fr; } }


    /* adr-hero-media-patch-v1: keep stacked hero images compact on tablet and narrow desktop. */
    @media (max-width: 980px) {
      .adr-page-stage .adr-hero-media { height: clamp(210px, 32vw, 300px) !important; min-height: 0 !important; }
      .adr-page-stage .adr-hero-media img { width: 100% !important; height: 100% !important; object-fit: cover !important; }
    }

    /* adr-original-detail-v1: restored detailed copy from the original site for owner review. */
    .adr-detail-section { width: min(100%, 1180px); margin: 18px auto 0; display: grid; gap: 12px; }
    .adr-detail-header, .adr-detail-card { border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); }
    .adr-detail-header { display: grid; gap: 8px; padding: clamp(22px, 4vw, 34px); }
    .adr-detail-header h2, .adr-detail-card h2 { margin: 0; color: var(--adr-blue); line-height: 1.12; letter-spacing: 0; }
    .adr-detail-header p, .adr-detail-card p, .adr-detail-card li { margin: 0; color: var(--adr-muted); line-height: 1.58; }
    .adr-detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
    .adr-detail-card { display: grid; align-content: start; gap: 10px; padding: clamp(20px, 3vw, 30px); }
    .adr-detail-card ul { display: grid; gap: 7px; margin: 0; padding-left: 20px; }
    .adr-detail-note { color: var(--adr-gold) !important; font-weight: 900; text-transform: uppercase; font-size: 12px; letter-spacing: .08em; }
    @media (max-width: 840px) { .adr-detail-grid { grid-template-columns: 1fr; } }

</style>
  <input class="adr-switch-input" id="adr-theme-toggle" type="checkbox" aria-label="Basculer le thème jour/nuit">
  <header class="adr-floating-panel" aria-label="Navigation principale">
    <a class="adr-brand" href="/"><img decoding="async" src="https://assurancesderueil.fr/wp-content/uploads/2022/08/cropped-flaticon2.png" alt=""><strong>Assurances de Rueil</strong></a>
    <div class="adr-panel-actions">
      <a class="adr-button adr-button-primary" href="/demande-de-devis-assurance-a-rueil-malmaison/">Demander un devis</a>
      <label class="adr-switch-label" for="adr-theme-toggle">Jour/Nuit</label>
    </div>
  </header>
  <section class="adr-hero" aria-label="Assurance de prêt">
    <div class="adr-hero-copy">

      <h1>Assurance de prêt</h1>
      <p>Vous êtes sur le point de souscrire un crédit ? Découvrez les points essentiels à connaître pour choisir, comparer ou changer votre assurance emprunteur.</p>
      <a class="adr-button adr-button-primary" href="/demande-de-devis-assurance-a-rueil-malmaison/">Simuler mon assurance</a>
    </div>
    <div class="adr-hero-media"><img decoding="async" src="https://assurancesderueil.fr/wp-content/uploads/2021/02/quote_form_image.jpg" alt="Assurance de prêt"></div>
  </section>
  <nav class="adr-mini-nav" aria-label="Pages principales">
    <a href="/">Accueil</a>
    <a href="/cabinet-de-courtage-en-assurances-rueil-malmaison/">Cabinet</a>
    <a href="/assurance-de-pret-a-rueil-malmaison/">Assurance de prêt</a>
    <a href="/assurance-particuliers-rueil-malmaison/">Particuliers</a>
    <a href="/assurance-entreprise-rueil-malmaison/">Professionnels</a>
    <a href="/courtier-en-assurances-de-rueil-malmaison/">Contact</a>
  </nav>
  <section class="adr-info-grid" aria-label="Assurance de prêt">
    <article class="adr-info-card" id="garanties"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-house-gold.svg" alt=""><h2>Garanties</h2><p>Une assurance de prêt, aussi appelée assurance hypothécaire, prend le relais en cas de défaillance et peut couvrir PTIA, ITT, IPT, IPP, décès ou perte d'emploi selon les garanties.</p></article>
    <article class="adr-info-card" id="liberte-de-choix"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-people-gold.svg" alt=""><h2>Liberté de choix</h2><p>L'emprunteur peut souscrire son crédit auprès de sa banque et choisir une assurance externe, à condition qu'elle respecte les garanties demandées.</p></article>
    <article class="adr-info-card" id="accompagnement"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-briefcase-gold.svg" alt=""><h2>Accompagnement</h2><p>À partir d'un large panel de garanties et d'options, le cabinet adapte l'offre aux besoins et au profil de chaque emprunteur.</p></article>
  </section>

  <section class="adr-detail-section" aria-label="Détails assurance de prêt">
    <div class="adr-detail-header">
      <p class="adr-detail-note">Détails repris du site original</p>
      <h2>Comprendre l'assurance de prêt</h2>
      <p>L'assurance emprunteur n'est pas imposée par la loi, mais elle est généralement exigée par les organismes prêteurs, surtout pour les crédits immobiliers. Le courtier aide à comparer les contrats et à préserver l'équivalence des garanties.</p>
    </div>
    <div class="adr-detail-grid">
      <article class="adr-detail-card" id="fonctionnement">
        <h2>Fonctionnement</h2>
        <p>Le contrat couvre l'emprunteur face à différents risques : perte totale et irréversible d'autonomie, incapacité temporaire totale de travail, invalidité permanente totale ou partielle, décès, voire perte d'emploi.</p>
        <p>Dans ces situations, l'assurance se substitue à l'emprunteur pour le paiement des mensualités, même si l'emprunteur ne subit aucune perte de revenus.</p>
      </article>
      <article class="adr-detail-card" id="souscription">
        <h2>Souscription</h2>
        <p>Lors de la souscription, plusieurs informations peuvent être demandées :</p>
        <ul>
          <li>profil : âge, fumeur ou non-fumeur, profession</li>
          <li>crédit : type de prêt, taux, durée et capital emprunté</li>
          <li>couverture exigée ou souhaitée : options, garanties, quotités</li>
          <li>habitudes de vie et état de santé, sauf en cas d'application de la Loi Lemoine.</li>
        </ul>
        <p>Les informations renseignées doivent être véridiques afin d'éviter un retard de prise d'effet ou une absence de prise en charge lors d'un sinistre. Tout savoir sur la Loi Lemoine permet aussi de vérifier si le questionnaire médical s'applique.</p>
      </article>
      <article class="adr-detail-card" id="comparer-les-offres">
        <h2>Comparer les offres</h2>
        <p>La fiche standardisée d'information précise les garanties exigées par la banque ou l'organisme prêteur. Elle permet de comparer les offres du marché sur une base claire.</p>
        <ul>
          <li>mensualités fixes ou décroissantes</li>
          <li>prestation indemnitaire ou forfaitaire</li>
          <li>durée de validité des garanties</li>
          <li>quotité couverte pour chaque emprunteur</li>
          <li>maintien des primes et garanties en cas de changement de situation.</li>
        </ul>
      </article>
      <article class="adr-detail-card" id="mensualites-prestations">
        <h2>Mensualités et prestations</h2>
        <p>Certains contrats prévoient des mensualités fixes, d'autres des mensualités décroissantes au fil du temps.</p>
        <p>Un contrat forfaitaire couvre le montant des mensualités multiplié par la quotité assurée. Un contrat indemnitaire limite la prise en charge à la perte de revenus. Les prestations forfaitaires assurent la même couverture, même en l'absence de perte de revenus.</p>
      </article>
      <article class="adr-detail-card" id="duree-garanties">
        <h2>Durée des garanties</h2>
        <p>Les garanties peuvent être limitées dans le temps, s'arrêter à un âge donné, évoluer lors d'un changement de situation ou prendre fin au moment du départ en retraite.</p>
        <p>La garantie décès proposée par le cabinet couvre l'emprunteur jusqu'à son 90e anniversaire, bien au-delà de nombreux contrats groupe proposés par les banques.</p>
      </article>
      <article class="adr-detail-card" id="quotite">
        <h2>Quotité</h2>
        <p>Lorsqu'un crédit est souscrit à deux, chaque emprunteur peut être assuré sur une partie du prêt. Une répartition à 50 % chacun laisse, en cas de décès d'un assuré, la moitié du capital restant due au survivant.</p>
        <p>La couverture peut aussi garantir jusqu'à 100 % chacun des emprunteurs selon le besoin de protection recherché.</p>
      </article>
      <article class="adr-detail-card" id="particularites-contrat">
        <h2>Particularités du contrat</h2>
        <p>Le dossier médical, les sports pratiqués, le métier ou les évolutions de vie peuvent faire varier le montant de la prime.</p>
        <p>Il faut donc vérifier la stabilité des prestations et des mensualités. La clause d'irrévocabilité permet de maintenir primes et garanties même si le style de vie change.</p>
      </article>
      <article class="adr-detail-card" id="exclusions-accompagnement">
        <h2>Exclusions et accompagnement</h2>
        <p>Les exclusions prévues par le contrat doivent être lues attentivement : elles définissent les situations dans lesquelles la prise en charge du remboursement ne serait pas assurée.</p>
        <p>Le rôle du courtier est de proposer la meilleure solution d'assurance aux emprunteurs, en fonction de leur profil de risque et des garanties demandées par la banque.</p>
      </article>
    </div>
  </section>

  <section class="adr-cta-strip" aria-label="Demande de devis">
    <div><h2>Faites confiance aux Assurances de Rueil</h2><p>Implanté dans les Hauts-de-Seine depuis quatre générations, le cabinet propose des solutions performantes et personnalisées. Il est possible de souscrire une assurance externe, puis d'en changer au cours du contrat si les garanties exigées par la banque sont respectées.</p></div>
    <a class="adr-button adr-button-primary" href="/demande-de-devis-assurance-a-rueil-malmaison/">Demander un devis</a>
  </section>
</div>
ADR_STAGE_PRET
        ,
        'particuliers' => <<<'ADR_STAGE_PARTICULIERS'
<div class="adr-page-stage adr-personal-stage">
  <style>
    body.page-id-7331 .ekit-template-content-header,
    body.page-id-7331 .site-footer,
    body.page-id-7331 footer.footer,
    body.page-id-7331 .ekit-template-content-footer,
    body.page-id-7331 .page-banner-area,
    body.page-id-7331 .breadcrumb { display: none !important; }
    .adr-page-stage, .adr-page-stage * { box-sizing: border-box; }
    .adr-page-stage {
      --adr-blue: #003478;
      --adr-blue-soft: #6da9ff;
      --adr-ink: #07192f;
      --adr-muted: #657386;
      --adr-line: rgba(0, 52, 120, 0.18);
      --adr-paper: rgba(255, 255, 255, 0.88);
      --adr-solid: #ffffff;
      --adr-wash: #eef5fb;
      --adr-gold: #d7a53c;
      --adr-shadow: 0 24px 70px rgba(0, 28, 64, 0.16);
      --adr-soft-shadow: 0 14px 36px rgba(0, 28, 64, 0.1);
      width: 100vw;
      margin-left: calc(50% - 50vw);
      margin-right: calc(50% - 50vw);
      margin-top: -1px;
      padding: clamp(14px, 2vw, 24px) clamp(14px, 2vw, 24px) 64px;
      background: radial-gradient(circle at 10% 0%, rgba(109,169,255,.2), transparent 30%), var(--adr-wash);
      color: var(--adr-ink);
      font-family: Rubik, "Open Sans", Arial, sans-serif;
      overflow: clip;
    }
    .adr-page-stage:has(#adr-theme-toggle:checked) {
      --adr-blue: #83b7ff;
      --adr-blue-soft: #74a9f7;
      --adr-ink: #edf6ff;
      --adr-muted: #a8b8c8;
      --adr-line: rgba(169, 201, 234, 0.24);
      --adr-paper: rgba(12, 29, 49, 0.84);
      --adr-solid: #0e2135;
      --adr-wash: #06111d;
      --adr-shadow: 0 24px 70px rgba(0, 0, 0, 0.34);
      --adr-soft-shadow: 0 14px 36px rgba(0, 0, 0, 0.24);
    }
    .adr-page-stage a { color: inherit; }
    .adr-page-stage img { display: block; max-width: 100%; }
    .adr-page-stage [id] { scroll-margin-top: 140px; }
    .adr-switch-input { position: absolute; opacity: 0; pointer-events: none; }
    .adr-floating-panel {
      position: sticky;
      top: clamp(12px, 2vw, 24px);
      z-index: 9;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      width: min(100%, 1288px);
      margin: 0 auto 18px;
      border: 1px solid var(--adr-line);
      border-radius: 8px;
      padding: 7px 8px 7px 12px;
      background: var(--adr-paper);
      box-shadow: 0 18px 54px rgba(0, 0, 0, 0.18);
      backdrop-filter: blur(18px);
    }
    body.admin-bar .adr-floating-panel { top: calc(32px + clamp(12px, 2vw, 24px)); }
    .adr-brand { display: inline-flex; align-items: center; gap: 13px; min-width: 0; color: var(--adr-blue); text-decoration: none; }
    .adr-brand img { width: clamp(48px, 5vw, 78px); height: clamp(48px, 5vw, 78px); border-radius: 50%; }
    .adr-brand strong { font-size: clamp(24px, 3vw, 42px); line-height: 1; letter-spacing: 0; white-space: nowrap; }
    .adr-panel-actions { display: flex; align-items: stretch; justify-content: flex-end; gap: 8px; }
    .adr-button, .adr-switch-label {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 44px;
      height: clamp(44px, 4.6vw, 64px);
      border-radius: 8px;
      padding: 12px 18px;
      border: 1px solid var(--adr-line);
      font-weight: 900;
      line-height: 1.05;
      text-decoration: none;
      cursor: pointer;
      white-space: nowrap;
      margin: 0 !important;
    }
    .adr-button-primary, .adr-button-primary:visited, .adr-button-primary:hover, .adr-button-primary:focus {
      border-color: transparent;
      background: var(--adr-blue);
      color: #fff !important;
      box-shadow: 0 16px 34px rgba(0, 52, 120, 0.22);
    }
    .adr-page-stage:has(#adr-theme-toggle:checked) .adr-button-primary { color: #06111d !important; }
    .adr-switch-label { min-width: 58px; background: color-mix(in srgb, var(--adr-blue) 7%, var(--adr-solid)); color: var(--adr-blue); font-size: 13px; }
    .adr-page-stage:has(#adr-theme-toggle:checked) label[for="adr-theme-toggle"] { background: var(--adr-blue); color: #06111d; }
    .adr-hero, .adr-form-section, .adr-next-steps, .adr-mini-nav { width: min(100%, 1180px); margin-left: auto; margin-right: auto; }
    .adr-hero {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(320px, .75fr);
      min-height: 430px;
      overflow: hidden;
      border: 1px solid var(--adr-line);
      border-radius: 8px;
      background: var(--adr-solid);
      box-shadow: var(--adr-shadow);
    }
    .adr-hero-copy { display: grid; align-content: center; gap: 18px; padding: clamp(28px, 5vw, 70px); }
    .adr-kicker { margin: 0; color: var(--adr-gold); font-size: 14px; font-weight: 900; text-transform: uppercase; letter-spacing: .08em; }
    .adr-hero h1 { margin: 0; color: var(--adr-blue); font-size: clamp(22px, 3.02vw, 41px); line-height: 1; letter-spacing: 0; }
    .adr-hero p { max-width: 680px; margin: 0; color: var(--adr-muted); font-size: clamp(18px, 2vw, 23px); line-height: 1.55; }
    .adr-hero-media { min-height: 340px; background: #d9e7f2; }
    .adr-hero-media img { width: 100%; height: 100%; object-fit: cover; }
    .adr-mini-nav { position: sticky; top: 116px; z-index: 8; display: flex; flex-wrap: wrap; justify-content: center; gap: 6px; margin-top: 18px; margin-bottom: 18px; border: 1px solid var(--adr-line); border-radius: 8px; padding: 8px; background: var(--adr-paper); backdrop-filter: blur(18px); }
    .adr-mini-nav a { border-radius: 999px; padding: 9px 12px; color: var(--adr-blue); font-size: 13px; font-weight: 900; text-decoration: none; }
    .adr-form-section { display: grid; grid-template-columns: minmax(270px, .52fr) minmax(0, 1fr); gap: 16px; align-items: start; }
    .adr-side-card, .adr-form-card, .adr-step-card { border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); }
    .adr-side-card { position: sticky; top: 178px; display: grid; gap: 14px; padding: clamp(22px, 3vw, 34px); }
    .adr-side-card h2, .adr-form-card h2, .adr-step-card h2 { margin: 0; color: var(--adr-blue); line-height: 1.12; letter-spacing: 0; }
    .adr-side-card p, .adr-form-card p, .adr-step-card p, .adr-side-card li { margin: 0; color: var(--adr-muted); line-height: 1.58; }
    .adr-side-card ol { display: grid; gap: 12px; margin: 6px 0 0; padding-left: 22px; font-weight: 900; color: var(--adr-muted); }
    .adr-form-card { padding: clamp(18px, 3vw, 28px); }
    .adr-form-intro { display: grid; gap: 8px; margin-bottom: 16px; }
    .adr-form-card .mf-form-wrapper,
    .adr-form-card .metform-form-content,
    .adr-form-card .elementor,
    .adr-form-card .elementor-section,
    .adr-form-card .elementor-container { max-width: none !important; width: 100% !important; }
    .adr-form-card input:not([type="checkbox"]):not([type="radio"]),
    .adr-form-card select,
    .adr-form-card textarea {
      min-height: 46px !important;
      border: 1px solid var(--adr-line) !important;
      border-radius: 8px !important;
      background: color-mix(in srgb, var(--adr-solid) 88%, transparent) !important;
      color: var(--adr-ink) !important;
      box-shadow: none !important;
    }
    .adr-form-card label,
    .adr-form-card .mf-input-label { color: var(--adr-ink) !important; font-weight: 800 !important; }
    .adr-form-card .metform-btn,
    .adr-form-card .metform-submit-btn { min-height: 50px !important; border-radius: 8px !important; background: var(--adr-blue) !important; color: #fff !important; font-weight: 900 !important; }
    .adr-next-steps { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-top: 18px; }
    .adr-step-card { display: grid; gap: 8px; padding: 20px; }
    .adr-step-card img { width: 48px; height: 48px; border-radius: 50%; padding: 8px; background: color-mix(in srgb, var(--adr-blue) 8%, white); }
    @media (max-width: 980px) { .adr-form-section, .adr-hero { grid-template-columns: 1fr; } .adr-side-card, .adr-mini-nav { position: static; } .adr-hero-media { order: -1; min-height: 240px; } }
    @media (max-width: 760px) { .adr-floating-panel, .adr-panel-actions { align-items: stretch; flex-direction: column; } .adr-brand strong { white-space: normal; } .adr-panel-actions, .adr-button, .adr-switch-label { width: 100%; } }
    @media (max-width: 640px) { .adr-page-stage { padding-left: 10px; padding-right: 10px; } .adr-hero h1 { font-size: 19px; } .adr-hero-copy { padding: 22px 18px; } .adr-next-steps { grid-template-columns: 1fr; } }
    .adr-info-grid { width: min(100%, 1180px); margin: 18px auto 0; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
    .adr-info-card { display: grid; gap: 10px; min-height: 250px; border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); padding: clamp(20px, 3vw, 30px); }
    .adr-info-card img { width: 52px; height: 52px; border-radius: 50%; padding: 8px; background: color-mix(in srgb, var(--adr-blue) 8%, white); }
    .adr-info-card h2, .adr-cta-strip h2 { margin: 0; color: var(--adr-blue); line-height: 1.12; letter-spacing: 0; }
    .adr-info-card p, .adr-cta-strip p { margin: 0; color: var(--adr-muted); line-height: 1.58; }
    .adr-cta-strip { width: min(100%, 1180px); margin: 18px auto 0; display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 18px; align-items: center; border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); padding: clamp(22px, 4vw, 38px); }
    @media (max-width: 900px) { .adr-info-grid { grid-template-columns: 1fr; } .adr-cta-strip { grid-template-columns: 1fr; } }


    /* adr-hero-media-patch-v1: keep stacked hero images compact on tablet and narrow desktop. */
    @media (max-width: 980px) {
      .adr-page-stage .adr-hero-media { height: clamp(210px, 32vw, 300px) !important; min-height: 0 !important; }
      .adr-page-stage .adr-hero-media img { width: 100% !important; height: 100% !important; object-fit: cover !important; }
    }

    /* adr-original-detail-v1: restored detailed copy from the original site for owner review. */
    .adr-detail-section { width: min(100%, 1180px); margin: 18px auto 0; display: grid; gap: 12px; }
    .adr-detail-header, .adr-detail-card { border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); }
    .adr-detail-header { display: grid; gap: 8px; padding: clamp(22px, 4vw, 34px); }
    .adr-detail-header h2, .adr-detail-card h2 { margin: 0; color: var(--adr-blue); line-height: 1.12; letter-spacing: 0; }
    .adr-detail-header p, .adr-detail-card p, .adr-detail-card li { margin: 0; color: var(--adr-muted); line-height: 1.58; }
    .adr-detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
    .adr-detail-card { display: grid; align-content: start; gap: 10px; padding: clamp(20px, 3vw, 30px); }
    .adr-detail-card ul { display: grid; gap: 7px; margin: 0; padding-left: 20px; }
    .adr-detail-note { color: var(--adr-gold) !important; font-weight: 900; text-transform: uppercase; font-size: 12px; letter-spacing: .08em; }
    @media (max-width: 840px) { .adr-detail-grid { grid-template-columns: 1fr; } }

</style>
  <input class="adr-switch-input" id="adr-theme-toggle" type="checkbox" aria-label="Basculer le thème jour/nuit">
  <header class="adr-floating-panel" aria-label="Navigation principale">
    <a class="adr-brand" href="/"><img decoding="async" src="https://assurancesderueil.fr/wp-content/uploads/2022/08/cropped-flaticon2.png" alt=""><strong>Assurances de Rueil</strong></a>
    <div class="adr-panel-actions">
      <a class="adr-button adr-button-primary" href="/demande-de-devis-assurance-a-rueil-malmaison/">Demander un devis</a>
      <label class="adr-switch-label" for="adr-theme-toggle">Jour/Nuit</label>
    </div>
  </header>
  <section class="adr-hero" aria-label="Particuliers">
    <div class="adr-hero-copy">

      <h1>Assurances des particuliers</h1>
      <p>La protection de vos biens, de votre personne et de vos proches exige rigueur et professionnalisme. Le cabinet propose une gamme complète de contrats adaptés à vos besoins spécifiques, avec des tarifs compétitifs.</p>
      <a class="adr-button adr-button-primary" href="/demande-de-devis-assurance-a-rueil-malmaison/">Protéger mon foyer</a>
    </div>
    <div class="adr-hero-media"><img decoding="async" src="https://assurancesderueil.fr/wp-content/uploads/2019/09/offer_image3.jpg" alt="Assurances des particuliers"></div>
  </section>
  <nav class="adr-mini-nav" aria-label="Pages principales">
    <a href="/">Accueil</a>
    <a href="/cabinet-de-courtage-en-assurances-rueil-malmaison/">Cabinet</a>
    <a href="/assurance-de-pret-a-rueil-malmaison/">Assurance de prêt</a>
    <a href="/assurance-particuliers-rueil-malmaison/">Particuliers</a>
    <a href="/assurance-entreprise-rueil-malmaison/">Professionnels</a>
    <a href="/courtier-en-assurances-de-rueil-malmaison/">Contact</a>
  </nav>
  <section class="adr-info-grid" aria-label="Assurances des particuliers">
    <article class="adr-info-card" id="habitation-resume"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-house-gold.svg" alt=""><h2>Habitation</h2><p>Locataire ou propriétaire, maison ou appartement, le cabinet conseille les garanties répondant à vos besoins et aux obligations légales.</p></article>
    <article class="adr-info-card" id="loyers-impayes-resume"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-people-gold.svg" alt=""><h2>Loyers impayés</h2><p>La garantie protège le propriétaire en cas d'interruption de versement, avec loyers impayés, frais de contentieux et détériorations immobilières selon le dossier.</p></article>
    <article class="adr-info-card" id="famille-mobilite"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-briefcase-gold.svg" alt=""><h2>Famille et mobilité</h2><p>Santé, garantie des accidents de la vie, prévoyance, automobile et assurance emprunteur complètent la protection du foyer.</p></article>
  </section>

  <section class="adr-detail-section" aria-label="Détails assurance particuliers">
    <div class="adr-detail-header">
      <p class="adr-detail-note">Détails repris du site original</p>
      <h2>Solutions pour les particuliers</h2>
      <p>Les formules couvrent le patrimoine, la santé, la mobilité, les prêts et les aléas de la vie. Les garanties sont ajustées à la situation familiale, au bien assuré et au niveau de protection recherché.</p>
    </div>
    <div class="adr-detail-grid">
      <article class="adr-detail-card" id="habitation">
        <h2>Habitation</h2>
        <p>Des formules attractives sont proposées pour les propriétaires occupants, non occupants et résidences secondaires, avec de solides garanties protégeant le patrimoine en cas d'incendie, dégâts des eaux, vol, tempête et catastrophes naturelles.</p>
        <p>Le contrat est adapté aux particularités du bien : véranda, piscine, tennis, jardin et équipements spécifiques.</p>
        <ul>
          <li>indemnisation en valeur à neuf des biens</li>
          <li>garanties des équipements d'énergie renouvelable</li>
          <li>responsabilité civile étendue au monde entier</li>
          <li>assistance aux membres de la famille en France et à l'étranger.</li>
        </ul>
      </article>
      <article class="adr-detail-card" id="loyers-impayes">
        <h2>Loyers impayés</h2>
        <p>La location d'un bien peut générer de sérieux problèmes en cas d'interruption de versement des loyers. La garantie des loyers impayés permet de bénéficier d'une couverture complète.</p>
        <ul>
          <li>garantie des loyers impayés jusqu'à 30 mois</li>
          <li>garantie des frais de contentieux, huissier et avocat</li>
          <li>garantie des détériorations immobilières</li>
          <li>assistance dans la composition du dossier jusqu'à la souscription.</li>
        </ul>
        <p>La prime étant déductible des revenus fonciers, un devis personnalisé peut aider à mesurer le coût réel de cette protection.</p>
      </article>
      <article class="adr-detail-card" id="assurance-de-pret">
        <h2>Assurance de prêt</h2>
        <p>Quel que soit le type de prêt ou d'investissement, l'assurance emprunteur peut rembourser le capital restant dû en cas de décès, perte d'autonomie ou invalidité, et prendre en charge les mensualités en cas d'incapacité de travail.</p>
        <p>Choisir son assurance emprunteur en dehors de la banque peut permettre de réaliser des économies selon le profil : emprunteurs de moins de 30 ans, non-fumeurs de plus de 54 ans, fumeurs ou emprunteurs atteints de maladies graves acceptées.</p>
        <p>La loi Hamon autorise, depuis le 26 Juillet 2014, la résiliation de l'assurance de prêt groupe dans les 12 mois suivant la signature de l'offre, sous réserve de garanties au moins équivalentes.</p>
      </article>
      <article class="adr-detail-card" id="sante">
        <h2>Santé</h2>
        <p>Les dépassements d'honoraires, le forfait hospitalier et l'écart entre le remboursement de la Sécurité Sociale et la dépense réelle rendent l'assurance complémentaire santé primordiale.</p>
        <p>Les formules complètent les prestations des caisses professionnelles selon l'âge et la situation familiale : moins de 25 ans, seniors, célibataire, marié avec ou sans enfant.</p>
        <ul>
          <li>formules allant de 100 % à 400 %</li>
          <li>pas de délais d'attente</li>
          <li>option optique et dentaire pouvant être renforcée</li>
          <li>médecine douce.</li>
        </ul>
      </article>
      <article class="adr-detail-card" id="accidents-vie">
        <h2>Garantie des accidents de la vie</h2>
        <p>La GAV couvre les conséquences des accidents de la vie courante : blessure à la main empêchant de travailler, mauvaise chute d'un enfant, pratique d'une activité, erreur médicale ou invalidité perturbant toute la vie de la famille.</p>
        <p>Le cabinet propose une couverture personnalisée et efficace pour parer aux besoins du foyer.</p>
      </article>
      <article class="adr-detail-card" id="automobile-mobilite">
        <h2>Automobile et mobilité</h2>
        <p>En deux roues comme en quatre roues, les contrats couvrent le véhicule et ses occupants. Le cabinet accompagne la mise en place des solutions, les actes de gestion et la survenance d'un sinistre, sans passage par une plateforme téléphonique.</p>
        <ul>
          <li>garages agréés avec prise de rendez-vous selon votre convenance et prêt d'un véhicule lors des réparations</li>
          <li>service à domicile : prise en charge du véhicule chez vous et mise à disposition d'un véhicule de prêt le temps de l'expertise et de la réparation</li>
          <li>assistance dépannage sans franchise kilométrique en cas de panne, accident ou crevaison</li>
          <li>passagers couverts suite à un accident corporel</li>
          <li>sécurité du conducteur avec capital en cas de décès ou de blessure.</li>
        </ul>
      </article>
      <article class="adr-detail-card" id="prevoyance">
        <h2>Prévoyance</h2>
        <p>La vie peut réserver des coups durs. L'assurance prévoyance apporte une protection financière, pour vous ou pour vos proches.</p>
        <ul>
          <li>décès et obsèques</li>
          <li>hospitalisation</li>
          <li>accident de la vie et du sport</li>
          <li>dépendance.</li>
        </ul>
      </article>
    </div>
  </section>

  <section class="adr-cta-strip" aria-label="Demande de devis">
    <div><h2>Un échange pour choisir les bonnes garanties</h2><p>Assurances de Rueil compare les solutions disponibles et adapte les options à votre situation, à votre foyer et à votre patrimoine.</p></div>
    <a class="adr-button adr-button-primary" href="/demande-de-devis-assurance-a-rueil-malmaison/">Demander un devis</a>
  </section>
</div>
ADR_STAGE_PARTICULIERS
        ,
        'professionnels' => <<<'ADR_STAGE_PROFESSIONNELS'
<div class="adr-page-stage adr-business-stage">
  <style>
    body.page-id-2180 .ekit-template-content-header,
    body.page-id-2180 .site-footer,
    body.page-id-2180 footer.footer,
    body.page-id-2180 .ekit-template-content-footer,
    body.page-id-2180 .page-banner-area,
    body.page-id-2180 .breadcrumb { display: none !important; }
    .adr-page-stage, .adr-page-stage * { box-sizing: border-box; }
    .adr-page-stage {
      --adr-blue: #003478;
      --adr-blue-soft: #6da9ff;
      --adr-ink: #07192f;
      --adr-muted: #657386;
      --adr-line: rgba(0, 52, 120, 0.18);
      --adr-paper: rgba(255, 255, 255, 0.88);
      --adr-solid: #ffffff;
      --adr-wash: #eef5fb;
      --adr-gold: #d7a53c;
      --adr-shadow: 0 24px 70px rgba(0, 28, 64, 0.16);
      --adr-soft-shadow: 0 14px 36px rgba(0, 28, 64, 0.1);
      width: 100vw;
      margin-left: calc(50% - 50vw);
      margin-right: calc(50% - 50vw);
      margin-top: -1px;
      padding: clamp(14px, 2vw, 24px) clamp(14px, 2vw, 24px) 64px;
      background: radial-gradient(circle at 10% 0%, rgba(109,169,255,.2), transparent 30%), var(--adr-wash);
      color: var(--adr-ink);
      font-family: Rubik, "Open Sans", Arial, sans-serif;
      overflow: clip;
    }
    .adr-page-stage:has(#adr-theme-toggle:checked) {
      --adr-blue: #83b7ff;
      --adr-blue-soft: #74a9f7;
      --adr-ink: #edf6ff;
      --adr-muted: #a8b8c8;
      --adr-line: rgba(169, 201, 234, 0.24);
      --adr-paper: rgba(12, 29, 49, 0.84);
      --adr-solid: #0e2135;
      --adr-wash: #06111d;
      --adr-shadow: 0 24px 70px rgba(0, 0, 0, 0.34);
      --adr-soft-shadow: 0 14px 36px rgba(0, 0, 0, 0.24);
    }
    .adr-page-stage a { color: inherit; }
    .adr-page-stage img { display: block; max-width: 100%; }
    .adr-page-stage [id] { scroll-margin-top: 140px; }
    .adr-switch-input { position: absolute; opacity: 0; pointer-events: none; }
    .adr-floating-panel {
      position: sticky;
      top: clamp(12px, 2vw, 24px);
      z-index: 9;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      width: min(100%, 1288px);
      margin: 0 auto 18px;
      border: 1px solid var(--adr-line);
      border-radius: 8px;
      padding: 7px 8px 7px 12px;
      background: var(--adr-paper);
      box-shadow: 0 18px 54px rgba(0, 0, 0, 0.18);
      backdrop-filter: blur(18px);
    }
    body.admin-bar .adr-floating-panel { top: calc(32px + clamp(12px, 2vw, 24px)); }
    .adr-brand { display: inline-flex; align-items: center; gap: 13px; min-width: 0; color: var(--adr-blue); text-decoration: none; }
    .adr-brand img { width: clamp(48px, 5vw, 78px); height: clamp(48px, 5vw, 78px); border-radius: 50%; }
    .adr-brand strong { font-size: clamp(24px, 3vw, 42px); line-height: 1; letter-spacing: 0; white-space: nowrap; }
    .adr-panel-actions { display: flex; align-items: stretch; justify-content: flex-end; gap: 8px; }
    .adr-button, .adr-switch-label {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 44px;
      height: clamp(44px, 4.6vw, 64px);
      border-radius: 8px;
      padding: 12px 18px;
      border: 1px solid var(--adr-line);
      font-weight: 900;
      line-height: 1.05;
      text-decoration: none;
      cursor: pointer;
      white-space: nowrap;
      margin: 0 !important;
    }
    .adr-button-primary, .adr-button-primary:visited, .adr-button-primary:hover, .adr-button-primary:focus {
      border-color: transparent;
      background: var(--adr-blue);
      color: #fff !important;
      box-shadow: 0 16px 34px rgba(0, 52, 120, 0.22);
    }
    .adr-page-stage:has(#adr-theme-toggle:checked) .adr-button-primary { color: #06111d !important; }
    .adr-switch-label { min-width: 58px; background: color-mix(in srgb, var(--adr-blue) 7%, var(--adr-solid)); color: var(--adr-blue); font-size: 13px; }
    .adr-page-stage:has(#adr-theme-toggle:checked) label[for="adr-theme-toggle"] { background: var(--adr-blue); color: #06111d; }
    .adr-hero, .adr-form-section, .adr-next-steps, .adr-mini-nav { width: min(100%, 1180px); margin-left: auto; margin-right: auto; }
    .adr-hero {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(320px, .75fr);
      min-height: 430px;
      overflow: hidden;
      border: 1px solid var(--adr-line);
      border-radius: 8px;
      background: var(--adr-solid);
      box-shadow: var(--adr-shadow);
    }
    .adr-hero-copy { display: grid; align-content: center; gap: 18px; padding: clamp(28px, 5vw, 70px); }
    .adr-kicker { margin: 0; color: var(--adr-gold); font-size: 14px; font-weight: 900; text-transform: uppercase; letter-spacing: .08em; }
    .adr-hero h1 { margin: 0; color: var(--adr-blue); font-size: clamp(22px, 3.02vw, 41px); line-height: 1; letter-spacing: 0; }
    .adr-hero p { max-width: 680px; margin: 0; color: var(--adr-muted); font-size: clamp(18px, 2vw, 23px); line-height: 1.55; }
    .adr-hero-media { min-height: 340px; background: #d9e7f2; }
    .adr-hero-media img { width: 100%; height: 100%; object-fit: cover; }
    .adr-mini-nav { position: sticky; top: 116px; z-index: 8; display: flex; flex-wrap: wrap; justify-content: center; gap: 6px; margin-top: 18px; margin-bottom: 18px; border: 1px solid var(--adr-line); border-radius: 8px; padding: 8px; background: var(--adr-paper); backdrop-filter: blur(18px); }
    .adr-mini-nav a { border-radius: 999px; padding: 9px 12px; color: var(--adr-blue); font-size: 13px; font-weight: 900; text-decoration: none; }
    .adr-form-section { display: grid; grid-template-columns: minmax(270px, .52fr) minmax(0, 1fr); gap: 16px; align-items: start; }
    .adr-side-card, .adr-form-card, .adr-step-card { border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); }
    .adr-side-card { position: sticky; top: 178px; display: grid; gap: 14px; padding: clamp(22px, 3vw, 34px); }
    .adr-side-card h2, .adr-form-card h2, .adr-step-card h2 { margin: 0; color: var(--adr-blue); line-height: 1.12; letter-spacing: 0; }
    .adr-side-card p, .adr-form-card p, .adr-step-card p, .adr-side-card li { margin: 0; color: var(--adr-muted); line-height: 1.58; }
    .adr-side-card ol { display: grid; gap: 12px; margin: 6px 0 0; padding-left: 22px; font-weight: 900; color: var(--adr-muted); }
    .adr-form-card { padding: clamp(18px, 3vw, 28px); }
    .adr-form-intro { display: grid; gap: 8px; margin-bottom: 16px; }
    .adr-form-card .mf-form-wrapper,
    .adr-form-card .metform-form-content,
    .adr-form-card .elementor,
    .adr-form-card .elementor-section,
    .adr-form-card .elementor-container { max-width: none !important; width: 100% !important; }
    .adr-form-card input:not([type="checkbox"]):not([type="radio"]),
    .adr-form-card select,
    .adr-form-card textarea {
      min-height: 46px !important;
      border: 1px solid var(--adr-line) !important;
      border-radius: 8px !important;
      background: color-mix(in srgb, var(--adr-solid) 88%, transparent) !important;
      color: var(--adr-ink) !important;
      box-shadow: none !important;
    }
    .adr-form-card label,
    .adr-form-card .mf-input-label { color: var(--adr-ink) !important; font-weight: 800 !important; }
    .adr-form-card .metform-btn,
    .adr-form-card .metform-submit-btn { min-height: 50px !important; border-radius: 8px !important; background: var(--adr-blue) !important; color: #fff !important; font-weight: 900 !important; }
    .adr-next-steps { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-top: 18px; }
    .adr-step-card { display: grid; gap: 8px; padding: 20px; }
    .adr-step-card img { width: 48px; height: 48px; border-radius: 50%; padding: 8px; background: color-mix(in srgb, var(--adr-blue) 8%, white); }
    @media (max-width: 980px) { .adr-form-section, .adr-hero { grid-template-columns: 1fr; } .adr-side-card, .adr-mini-nav { position: static; } .adr-hero-media { order: -1; min-height: 240px; } }
    @media (max-width: 760px) { .adr-floating-panel, .adr-panel-actions { align-items: stretch; flex-direction: column; } .adr-brand strong { white-space: normal; } .adr-panel-actions, .adr-button, .adr-switch-label { width: 100%; } }
    @media (max-width: 640px) { .adr-page-stage { padding-left: 10px; padding-right: 10px; } .adr-hero h1 { font-size: 19px; } .adr-hero-copy { padding: 22px 18px; } .adr-next-steps { grid-template-columns: 1fr; } }
    .adr-info-grid { width: min(100%, 1180px); margin: 18px auto 0; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
    .adr-info-card { display: grid; gap: 10px; min-height: 250px; border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); padding: clamp(20px, 3vw, 30px); }
    .adr-info-card img { width: 52px; height: 52px; border-radius: 50%; padding: 8px; background: color-mix(in srgb, var(--adr-blue) 8%, white); }
    .adr-info-card h2, .adr-cta-strip h2 { margin: 0; color: var(--adr-blue); line-height: 1.12; letter-spacing: 0; }
    .adr-info-card p, .adr-cta-strip p { margin: 0; color: var(--adr-muted); line-height: 1.58; }
    .adr-cta-strip { width: min(100%, 1180px); margin: 18px auto 0; display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 18px; align-items: center; border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); padding: clamp(22px, 4vw, 38px); }
    @media (max-width: 900px) { .adr-info-grid { grid-template-columns: 1fr; } .adr-cta-strip { grid-template-columns: 1fr; } }


    /* adr-hero-media-patch-v1: keep stacked hero images compact on tablet and narrow desktop. */
    @media (max-width: 980px) {
      .adr-page-stage .adr-hero-media { height: clamp(210px, 32vw, 300px) !important; min-height: 0 !important; }
      .adr-page-stage .adr-hero-media img { width: 100% !important; height: 100% !important; object-fit: cover !important; }
    }

    /* adr-original-detail-v1: restored detailed copy from the original site for owner review. */
    .adr-detail-section { width: min(100%, 1180px); margin: 18px auto 0; display: grid; gap: 12px; }
    .adr-detail-header, .adr-detail-card { border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); }
    .adr-detail-header { display: grid; gap: 8px; padding: clamp(22px, 4vw, 34px); }
    .adr-detail-header h2, .adr-detail-card h2 { margin: 0; color: var(--adr-blue); line-height: 1.12; letter-spacing: 0; }
    .adr-detail-header p, .adr-detail-card p, .adr-detail-card li { margin: 0; color: var(--adr-muted); line-height: 1.58; }
    .adr-detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
    .adr-detail-card { display: grid; align-content: start; gap: 10px; padding: clamp(20px, 3vw, 30px); }
    .adr-detail-card ul { display: grid; gap: 7px; margin: 0; padding-left: 20px; }
    .adr-detail-note { color: var(--adr-gold) !important; font-weight: 900; text-transform: uppercase; font-size: 12px; letter-spacing: .08em; }
    @media (max-width: 840px) { .adr-detail-grid { grid-template-columns: 1fr; } }

</style>
  <input class="adr-switch-input" id="adr-theme-toggle" type="checkbox" aria-label="Basculer le thème jour/nuit">
  <header class="adr-floating-panel" aria-label="Navigation principale">
    <a class="adr-brand" href="/"><img decoding="async" src="https://assurancesderueil.fr/wp-content/uploads/2022/08/cropped-flaticon2.png" alt=""><strong>Assurances de Rueil</strong></a>
    <div class="adr-panel-actions">
      <a class="adr-button adr-button-primary" href="/demande-de-devis-assurance-a-rueil-malmaison/">Demander un devis</a>
      <label class="adr-switch-label" for="adr-theme-toggle">Jour/Nuit</label>
    </div>
  </header>
  <section class="adr-hero" aria-label="Professionnels">
    <div class="adr-hero-copy">

      <h1>Assurances entreprise</h1>
      <p>La gestion d'une entreprise nécessite un fort investissement personnel. Le cabinet propose une large gamme de contrats adaptés à votre activité pour travailler en toute sérénité.</p>
      <a class="adr-button adr-button-primary" href="/demande-de-devis-assurance-a-rueil-malmaison/">Assurer mon activité</a>
    </div>
    <div class="adr-hero-media"><img decoding="async" src="https://assurancesderueil.fr/wp-content/uploads/2022/08/Responsabilite-civile-Assurance-Rueil-Malmaison.jpg" alt="Assurances entreprise"></div>
  </section>
  <nav class="adr-mini-nav" aria-label="Pages principales">
    <a href="/">Accueil</a>
    <a href="/cabinet-de-courtage-en-assurances-rueil-malmaison/">Cabinet</a>
    <a href="/assurance-de-pret-a-rueil-malmaison/">Assurance de prêt</a>
    <a href="/assurance-particuliers-rueil-malmaison/">Particuliers</a>
    <a href="/assurance-entreprise-rueil-malmaison/">Professionnels</a>
    <a href="/courtier-en-assurances-de-rueil-malmaison/">Contact</a>
  </nav>
  <section class="adr-info-grid" aria-label="Assurances entreprise">
    <article class="adr-info-card" id="multirisques"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-house-gold.svg" alt=""><h2>Multirisques</h2><p>Le contrat couvre l'activité contre incendies, événements climatiques, dégâts des eaux, vol, vandalisme, bris de glace et bris de machine.</p></article>
    <article class="adr-info-card" id="profils"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-people-gold.svg" alt=""><h2>Profils</h2><p>Des solutions sont proposées aux artisans, commerçants, professions libérales et entreprises industrielles, selon les locaux et le matériel professionnel.</p></article>
    <article class="adr-info-card" id="responsabilites"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-briefcase-gold.svg" alt=""><h2>Responsabilités</h2><p>Responsabilité civile professionnelle, prévoyance collective, flotte automobile et assurance emprunteur protègent l'entreprise, ses salariés et ses investissements.</p></article>
  </section>

  <section class="adr-detail-section" aria-label="Détails assurances entreprise">
    <div class="adr-detail-header">
      <p class="adr-detail-note">Détails repris du site original</p>
      <h2>Garanties professionnelles détaillées</h2>
      <p>Les garanties sont présentées par situation professionnelle afin de préserver la précision commerciale de l'offre et de clarifier les protections utiles à l'activité.</p>
    </div>
    <div class="adr-detail-grid">
      <article class="adr-detail-card" id="multirisques-professionnelle">
        <h2>Multirisques professionnelle</h2>
        <p>Que vous soyez artisan, commerçant, profession libérale ou entreprise industrielle, les solutions sont adaptées à la nature et à la superficie des locaux ainsi qu'au matériel professionnel.</p>
        <p>Elles prennent en compte les machines, marchandises, matériel informatique, stocks et autres équipements nécessaires à l'activité.</p>
      </article>
      <article class="adr-detail-card" id="loyers-impayes">
        <h2>Loyers impayés</h2>
        <p>La location d'un bien peut générer de sérieux problèmes en cas d'interruption de versement des loyers. La garantie des loyers impayés permet de bénéficier d'une couverture complète.</p>
        <ul>
          <li>garantie des loyers impayés jusqu'à 30 mois</li>
          <li>garantie des frais de contentieux, huissier et avocat</li>
          <li>garantie des détériorations immobilières</li>
          <li>assistance dans la composition du dossier jusqu'à la souscription du contrat.</li>
        </ul>
        <p>La prime étant déductible des revenus fonciers, un devis personnalisé peut aider à mesurer le coût réel de cette protection.</p>
      </article>
      <article class="adr-detail-card" id="assurance-emprunteur-professionnelle">
        <h2>Assurance emprunteur professionnelle</h2>
        <p>Quel que soit le type de prêt et d'investissement, l'assurance emprunteur propose le remboursement du capital restant dû en cas de décès, perte d'autonomie ou invalidité, mais aussi la prise en charge des mensualités du prêt en cas d'incapacité de travail.</p>
        <p>Choisir son assurance emprunteur en dehors de la banque peut permettre d'importantes économies selon le profil : emprunteurs de moins de 30 ans, non-fumeurs de plus de 54 ans, fumeurs ou emprunteurs atteints de maladies graves acceptées.</p>
        <p>La loi Hamon autorise, depuis le 26 Juillet 2014, la résiliation de l'assurance de prêt groupe dans les 12 mois suivant la signature de l'offre, avec des garanties au moins équivalentes au contrat initial.</p>
      </article>
      <article class="adr-detail-card" id="responsabilite-civile-professionnelle">
        <h2>Responsabilité civile et professionnelle</h2>
        <p>Dans un environnement économique et social tendu, l'assurance responsabilité civile professionnelle peut être considérée comme une réelle assurance vie pour votre entreprise.</p>
        <p>La responsabilité peut être engagée dans de nombreuses circonstances : faute inexcusable de l'employeur, responsabilité du produit livré, activité de l'entreprise, locaux, dirigeant ou salariés.</p>
        <p>Face à ces risques, des garanties spécifiques sur mesure, adaptées au corps de métier, contribuent à préserver la pérennité de l'entreprise.</p>
      </article>
      <article class="adr-detail-card" id="prevoyance-collective">
        <h2>Prévoyance collective</h2>
        <p>En complément des prestations des régimes obligatoires de Sécurité Sociale, la prévoyance collective apporte aux salariés et à leur famille une sécurité indispensable, notamment pour des risques lourds comme le décès ou l'invalidité.</p>
        <ul>
          <li>risques de dommages corporels résultant d'une maladie ou d'un accident : complémentaire santé, indemnités journalières en cas d'arrêt de travail, rentes d'invalidité</li>
          <li>engagements liés à la durée de vie : capital décès, rentes de conjoint et d'éducation, épargne retraite, dépendance.</li>
        </ul>
      </article>
      <article class="adr-detail-card" id="flotte-automobile">
        <h2>Flotte automobile</h2>
        <p>Si le parc automobile se compose d'au moins 5 véhicules, il peut être assuré par un seul contrat. Les garanties sont évaluées selon la diversité de la flotte.</p>
        <p>L'offre peut couvrir les véhicules, camionnettes et remorques. Le véhicule personnel du dirigeant peut intégrer le contrat lorsqu'il est utilisé dans le cadre de l'activité professionnelle.</p>
        <ul>
          <li>Tiers simple</li>
          <li>Tiers avec Vol et Incendie</li>
          <li>Dommages tous accidents</li>
          <li>assistance sans franchise kilométrique et sécurité du conducteur incluses</li>
          <li>services sur mesure : domicile, garages agréés, véhicule de remplacement.</li>
        </ul>
      </article>
      <article class="adr-detail-card" id="chomage-dirigeant">
        <h2>Assurance chômage du dirigeant</h2>
        <p>Nul dirigeant n'est à l'abri d'une faillite ou d'une baisse d'activité de son entreprise. Pour affronter les coups du sort, une assurance chômage privée peut protéger ses arrières.</p>
      </article>
    </div>
  </section>

  <section class="adr-cta-strip" aria-label="Demande de devis">
    <div><h2>Étudier votre projet d'assurance professionnelle</h2><p>Assurances de Rueil compare les solutions disponibles et adapte les options à votre activité, vos responsabilités, vos locaux et vos investissements.</p></div>
    <a class="adr-button adr-button-primary" href="/demande-de-devis-assurance-a-rueil-malmaison/">Demander un devis</a>
  </section>
</div>
ADR_STAGE_PROFESSIONNELS
        ,
        'contact' => <<<'ADR_STAGE_CONTACT'
<div class="adr-page-stage adr-contact-stage">
  <style>
    body.page-id-105 .ekit-template-content-header,
    body.page-id-105 .site-footer,
    body.page-id-105 footer.footer,
    body.page-id-105 .ekit-template-content-footer,
    body.page-id-105 .page-banner-area,
    body.page-id-105 .breadcrumb { display: none !important; }
    .adr-page-stage, .adr-page-stage * { box-sizing: border-box; }
    .adr-page-stage {
      --adr-blue: #003478;
      --adr-blue-soft: #6da9ff;
      --adr-ink: #07192f;
      --adr-muted: #657386;
      --adr-line: rgba(0, 52, 120, 0.18);
      --adr-paper: rgba(255, 255, 255, 0.88);
      --adr-solid: #ffffff;
      --adr-wash: #eef5fb;
      --adr-gold: #d7a53c;
      --adr-shadow: 0 24px 70px rgba(0, 28, 64, 0.16);
      --adr-soft-shadow: 0 14px 36px rgba(0, 28, 64, 0.1);
      width: 100vw;
      margin-left: calc(50% - 50vw);
      margin-right: calc(50% - 50vw);
      margin-top: -1px;
      padding: clamp(14px, 2vw, 24px) clamp(14px, 2vw, 24px) 64px;
      background: radial-gradient(circle at 10% 0%, rgba(109,169,255,.2), transparent 30%), var(--adr-wash);
      color: var(--adr-ink);
      font-family: Rubik, "Open Sans", Arial, sans-serif;
      overflow: clip;
    }
    .adr-page-stage:has(#adr-theme-toggle:checked) {
      --adr-blue: #83b7ff;
      --adr-blue-soft: #74a9f7;
      --adr-ink: #edf6ff;
      --adr-muted: #a8b8c8;
      --adr-line: rgba(169, 201, 234, 0.24);
      --adr-paper: rgba(12, 29, 49, 0.84);
      --adr-solid: #0e2135;
      --adr-wash: #06111d;
      --adr-shadow: 0 24px 70px rgba(0, 0, 0, 0.34);
      --adr-soft-shadow: 0 14px 36px rgba(0, 0, 0, 0.24);
    }
    .adr-page-stage a { color: inherit; }
    .adr-page-stage img { display: block; max-width: 100%; }
    .adr-page-stage [id] { scroll-margin-top: 140px; }
    .adr-switch-input { position: absolute; opacity: 0; pointer-events: none; }
    .adr-floating-panel {
      position: sticky;
      top: clamp(12px, 2vw, 24px);
      z-index: 9;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      width: min(100%, 1288px);
      margin: 0 auto 18px;
      border: 1px solid var(--adr-line);
      border-radius: 8px;
      padding: 7px 8px 7px 12px;
      background: var(--adr-paper);
      box-shadow: 0 18px 54px rgba(0, 0, 0, 0.18);
      backdrop-filter: blur(18px);
    }
    body.admin-bar .adr-floating-panel { top: calc(32px + clamp(12px, 2vw, 24px)); }
    .adr-brand { display: inline-flex; align-items: center; gap: 13px; min-width: 0; color: var(--adr-blue); text-decoration: none; }
    .adr-brand img { width: clamp(48px, 5vw, 78px); height: clamp(48px, 5vw, 78px); border-radius: 50%; }
    .adr-brand strong { font-size: clamp(24px, 3vw, 42px); line-height: 1; letter-spacing: 0; white-space: nowrap; }
    .adr-panel-actions { display: flex; align-items: stretch; justify-content: flex-end; gap: 8px; }
    .adr-button, .adr-switch-label {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 44px;
      height: clamp(44px, 4.6vw, 64px);
      border-radius: 8px;
      padding: 12px 18px;
      border: 1px solid var(--adr-line);
      font-weight: 900;
      line-height: 1.05;
      text-decoration: none;
      cursor: pointer;
      white-space: nowrap;
      margin: 0 !important;
    }
    .adr-button-primary, .adr-button-primary:visited, .adr-button-primary:hover, .adr-button-primary:focus {
      border-color: transparent;
      background: var(--adr-blue);
      color: #fff !important;
      box-shadow: 0 16px 34px rgba(0, 52, 120, 0.22);
    }
    .adr-page-stage:has(#adr-theme-toggle:checked) .adr-button-primary { color: #06111d !important; }
    .adr-switch-label { min-width: 58px; background: color-mix(in srgb, var(--adr-blue) 7%, var(--adr-solid)); color: var(--adr-blue); font-size: 13px; }
    .adr-page-stage:has(#adr-theme-toggle:checked) label[for="adr-theme-toggle"] { background: var(--adr-blue); color: #06111d; }
    .adr-hero, .adr-form-section, .adr-next-steps, .adr-mini-nav { width: min(100%, 1180px); margin-left: auto; margin-right: auto; }
    .adr-hero {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(320px, .75fr);
      min-height: 430px;
      overflow: hidden;
      border: 1px solid var(--adr-line);
      border-radius: 8px;
      background: var(--adr-solid);
      box-shadow: var(--adr-shadow);
    }
    .adr-hero-copy { display: grid; align-content: center; gap: 18px; padding: clamp(28px, 5vw, 70px); }
    .adr-kicker { margin: 0; color: var(--adr-gold); font-size: 14px; font-weight: 900; text-transform: uppercase; letter-spacing: .08em; }
    .adr-hero h1 { margin: 0; color: var(--adr-blue); font-size: clamp(22px, 3.02vw, 41px); line-height: 1; letter-spacing: 0; }
    .adr-hero p { max-width: 680px; margin: 0; color: var(--adr-muted); font-size: clamp(18px, 2vw, 23px); line-height: 1.55; }
    .adr-hero-media { min-height: 340px; background: #d9e7f2; }
    .adr-hero-media img { width: 100%; height: 100%; object-fit: cover; }
    .adr-mini-nav { position: sticky; top: 116px; z-index: 8; display: flex; flex-wrap: wrap; justify-content: center; gap: 6px; margin-top: 18px; margin-bottom: 18px; border: 1px solid var(--adr-line); border-radius: 8px; padding: 8px; background: var(--adr-paper); backdrop-filter: blur(18px); }
    .adr-mini-nav a { border-radius: 999px; padding: 9px 12px; color: var(--adr-blue); font-size: 13px; font-weight: 900; text-decoration: none; }
    .adr-form-section { display: grid; grid-template-columns: minmax(270px, .52fr) minmax(0, 1fr); gap: 16px; align-items: start; }
    .adr-side-card, .adr-form-card, .adr-step-card { border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); }
    .adr-side-card { position: sticky; top: 178px; display: grid; gap: 14px; padding: clamp(22px, 3vw, 34px); }
    .adr-side-card h2, .adr-form-card h2, .adr-step-card h2 { margin: 0; color: var(--adr-blue); line-height: 1.12; letter-spacing: 0; }
    .adr-side-card p, .adr-form-card p, .adr-step-card p, .adr-side-card li { margin: 0; color: var(--adr-muted); line-height: 1.58; }
    .adr-side-card ol { display: grid; gap: 12px; margin: 6px 0 0; padding-left: 22px; font-weight: 900; color: var(--adr-muted); }
    .adr-form-card { padding: clamp(18px, 3vw, 28px); }
    .adr-form-intro { display: grid; gap: 8px; margin-bottom: 16px; }
    .adr-form-card .mf-form-wrapper,
    .adr-form-card .metform-form-content,
    .adr-form-card .elementor,
    .adr-form-card .elementor-section,
    .adr-form-card .elementor-container { max-width: none !important; width: 100% !important; }
    .adr-form-card input:not([type="checkbox"]):not([type="radio"]),
    .adr-form-card select,
    .adr-form-card textarea {
      min-height: 46px !important;
      border: 1px solid var(--adr-line) !important;
      border-radius: 8px !important;
      background: color-mix(in srgb, var(--adr-solid) 88%, transparent) !important;
      color: var(--adr-ink) !important;
      box-shadow: none !important;
    }
    .adr-form-card label,
    .adr-form-card .mf-input-label { color: var(--adr-ink) !important; font-weight: 800 !important; }
    .adr-form-card .metform-btn,
    .adr-form-card .metform-submit-btn { min-height: 50px !important; border-radius: 8px !important; background: var(--adr-blue) !important; color: #fff !important; font-weight: 900 !important; }
    .adr-next-steps { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-top: 18px; }
    .adr-step-card { display: grid; gap: 8px; padding: 20px; }
    .adr-step-card img { width: 48px; height: 48px; border-radius: 50%; padding: 8px; background: color-mix(in srgb, var(--adr-blue) 8%, white); }
    @media (max-width: 980px) { .adr-form-section, .adr-hero { grid-template-columns: 1fr; } .adr-side-card, .adr-mini-nav { position: static; } .adr-hero-media { order: -1; min-height: 240px; } }
    @media (max-width: 760px) { .adr-floating-panel, .adr-panel-actions { align-items: stretch; flex-direction: column; } .adr-brand strong { white-space: normal; } .adr-panel-actions, .adr-button, .adr-switch-label { width: 100%; } }
    @media (max-width: 640px) { .adr-page-stage { padding-left: 10px; padding-right: 10px; } .adr-hero h1 { font-size: 19px; } .adr-hero-copy { padding: 22px 18px; } .adr-next-steps { grid-template-columns: 1fr; } }

    /* adr-hero-media-patch-v1: keep stacked hero images compact on tablet and narrow desktop. */
    @media (max-width: 980px) {
      .adr-page-stage .adr-hero-media { height: clamp(210px, 32vw, 300px) !important; min-height: 0 !important; }
      .adr-page-stage .adr-hero-media img { width: 100% !important; height: 100% !important; object-fit: cover !important; }
    }

    /* adr-form-contrast-patch-v1: improve MetForm contrast on dark internal panels. */
    .adr-form-card .metform-form-content,
    .adr-form-card .metform-form-main-wrapper,
    .adr-form-card .mf-form-wrapper,
    .adr-form-card .elementor-widget-wrap { color: var(--adr-ink); }
    .adr-form-card .elementor-section,
    .adr-form-card .elementor-container,
    .adr-form-card .elementor-widget-wrap,
    .adr-form-card .mf-input-wrapper { border-radius: 8px; }
    .adr-form-card .mf-input-label,
    .adr-form-card .mf-input-label span:not(.mf-input-required-indicator),
    .adr-form-card .mf-checkbox-option label,
    .adr-form-card .mf-checkbox-option label span,
    .adr-form-card .mf-checkbox-option label a { color: #f7fbff !important; }
    .adr-form-card .mf-input-required-indicator { color: #ff6b4a !important; }
    .adr-form-card .mf-input,
    .adr-form-card input:not([type="checkbox"]):not([type="radio"]),
    .adr-form-card select,
    .adr-form-card textarea {
      background: #ffffff !important;
      color: #07192f !important;
      border-color: rgba(255,255,255,.72) !important;
      box-shadow: 0 10px 24px rgba(0,0,0,.16) !important;
    }
    .adr-form-card .mf-input::placeholder,
    .adr-form-card input::placeholder,
    .adr-form-card textarea::placeholder { color: rgba(7,25,47,.58) !important; }
    .adr-form-card .mf-error-message { color: #ffd6cc !important; font-weight: 800 !important; }
</style>
  <input class="adr-switch-input" id="adr-theme-toggle" type="checkbox" aria-label="Basculer le thème jour/nuit">
  <header class="adr-floating-panel" aria-label="Navigation principale">
    <a class="adr-brand" href="/"><img decoding="async" src="https://assurancesderueil.fr/wp-content/uploads/2022/08/cropped-flaticon2.png" alt=""><strong>Assurances de Rueil</strong></a>
    <div class="adr-panel-actions">
      <a class="adr-button adr-button-primary" href="/demande-de-devis-assurance-a-rueil-malmaison/">Demander un devis</a>
      <label class="adr-switch-label" for="adr-theme-toggle">Jour/Nuit</label>
    </div>
  </header>
  <section class="adr-hero" aria-label="Contact">
    <div class="adr-hero-copy">
      <p class="adr-kicker">Contact</p>
      <h1>Les Assurances de Rueil</h1>
      <p>Retrouvez le cabinet au 75 avenue Victor Hugo, 92500 Rueil-Malmaison. L&rsquo;équipe vous accueille du lundi au vendredi, de 9:00 à 12:30 et de 14:00 à 18:00.</p>
      <a class="adr-button adr-button-primary" href="#formulaire">Écrire au cabinet</a>
    </div>
    <div class="adr-hero-media"><img decoding="async" src="https://assurancesderueil.fr/wp-content/uploads/2019/09/blog_image.jpg" alt="Assurances de Rueil à Rueil-Malmaison"></div>
  </section>
  <nav class="adr-mini-nav" aria-label="Pages principales">
    <a href="/">Accueil</a>
    <a href="/cabinet-de-courtage-en-assurances-rueil-malmaison/">Cabinet</a>
    <a href="/assurance-de-pret-a-rueil-malmaison/">Assurance de prêt</a>
    <a href="/assurance-particuliers-rueil-malmaison/">Particuliers</a>
    <a href="/assurance-entreprise-rueil-malmaison/">Professionnels</a>
    <a href="/demande-de-devis-assurance-a-rueil-malmaison/">Devis</a>
  </nav>
  <section class="adr-form-section" id="formulaire" aria-label="Contact cabinet">
    <aside class="adr-side-card">
      <h2>Coordonnées</h2>
      <p><strong>Adresse</strong><br>75 avenue Victor Hugo<br>92500 Rueil-Malmaison</p>
      <p><strong>Téléphone</strong><br><a href="tel:+33147510669">+33 1 47 51 06 69</a></p>

      <p><strong>E-mail</strong><br><a href="mailto:contact@assurancesderueil.fr">contact@assurancesderueil.fr</a></p>
      <p><strong>Horaires</strong><br>Lundi au vendredi, 9:00-12:30 et 14:00-18:00.</p>
    </aside>
    <div class="adr-form-card">
      <div class="adr-form-intro">
        <h2>Envoyer un message</h2>
        <p>Votre message est transmis au cabinet afin qu&rsquo;un conseiller puisse vous répondre ou vous rappeler.</p>
      </div>


<div class="mf-form-shortcode">
        <div
            id="metform-wrap-7487-7487"
            class="mf-form-wrapper"
            data-form-id="7487"
            data-action="https://assurancesderueil.fr/wp-json/metform/v1/entries/insert/7487"
            data-wp-nonce="5f486eb0ec"
            data-form-nonce="7fff10ef5b"
            data-quiz-summery = "false"
            data-save-progress = "false"
            data-form-type="contact_form"
            data-stop-vertical-effect=""
            ></div>


        <!-----------------------------
            * controls_data : find the the props passed indie of data attribute
            * props.SubmitResponseMarkup : contains the markup of error or success message
            * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Template_literals
        --------------------------- -->

                <script type="text/mf" class="mf-template">
            function controls_data (value){
                let currentWrapper = "mf-response-props-id-7487";
                let currentEl = document.getElementById(currentWrapper);

                return currentEl ? currentEl.dataset[value] : false
            }


            let is_edit_mode = '' ? true : false;
            let message_position = controls_data('messageposition') || 'top';


            let message_successIcon = controls_data('successicon') || '';
            let message_errorIcon = controls_data('erroricon') || '';
            let message_editSwitch = controls_data('editswitchopen') === 'yes' ? true : false;
            let message_proClass = controls_data('editswitchopen') === 'yes' ? 'mf_pro_activated' : '';

            let is_dummy_markup = is_edit_mode && message_editSwitch ? true : false;


            return html`
                <form
                    className="metform-form-content"
                    ref=${parent.formContainerRef}
                    onSubmit=${ validation.handleSubmit( parent.handleFormSubmit ) }

                    >


                    ${is_dummy_markup ? message_position === 'top' ?  props.ResponseDummyMarkup(message_successIcon, message_proClass) : '' : ''}
                    ${is_dummy_markup ? ' ' :  message_position === 'top' ? props.SubmitResponseMarkup`${parent}${state}${message_successIcon}${message_errorIcon}${message_proClass}` : ''}

                    <!--------------------------------------------------------
                    *** IMPORTANT / DANGEROUS ***
                    ${html``} must be used as in immediate child of "metform-form-main-wrapper"
                    class otherwise multistep form will not run at all
                    ---------------------------------------------------------->

                    <div className="metform-form-main-wrapper" key=${'hide-form-after-submit'} ref=${parent.formRef}>
                    ${html`
                                <div data-elementor-type="wp-post" key="2" data-elementor-id="7487" className="elementor elementor-7487" data-elementor-post-type="metform-form">
                        <section className="elementor-section elementor-top-section elementor-element elementor-element-7dc44e9 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="7dc44e9" data-element_type="section">
                        <div className="elementor-container elementor-column-gap-default">
                    <div className="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-ed44f00" data-id="ed44f00" data-element_type="column">
            <div className="elementor-widget-wrap">
                            </div>
        </div>
                    </div>
        </section>
                <section className="elementor-section elementor-top-section elementor-element elementor-element-c64f6f7 elementor-section-boxed elementor-section-height-default elementor-section-height-default" data-id="c64f6f7" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
                        <div className="elementor-container elementor-column-gap-default">
                    <div className="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-5f43826" data-id="5f43826" data-element_type="column" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
            <div className="elementor-widget-wrap elementor-element-populated">
                        <div className="elementor-element elementor-element-12a0ac9 elementor-widget elementor-widget-mf-text" data-id="12a0ac9" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;nom&quot;}" data-widget_type="mf-text.default">
                <div className="elementor-widget-container">

        <div className="mf-input-wrapper">
                            <label className="mf-input-label" htmlFor="mf-input-text-12a0ac9">
                    ${ parent.decodeEntities(`Nom`) } 					<span className="mf-input-required-indicator">*</span>
                </label>

            <input
                type="text"
                className="mf-input "
                id="mf-input-text-12a0ac9"
                name="nom"
                placeholder="${ parent.decodeEntities(`Nom`) } "
                                    onInput=${parent.handleChange}
                    onBlur=${parent.handleChange}
                    aria-invalid=${validation.errors['nom'] ? 'true' : 'false'}
                    ref=${el =>{
                                                parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":1,"maxLength":"","type":"none","required":true,"expression":"null"}, el)
                    }}
                                />

                            <${validation.ErrorMessage}
                    errors=${validation.errors}
                    name="nom"
                    as=${html`<span className="mf-error-message"></span>`}
                    />

                    </div>

                        </div>
                </div>
                <div className="elementor-element elementor-element-8bc3d3f elementor-widget elementor-widget-mf-text" data-id="8bc3d3f" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;prenom&quot;}" data-widget_type="mf-text.default">
                <div className="elementor-widget-container">

        <div className="mf-input-wrapper">
                            <label className="mf-input-label" htmlFor="mf-input-text-8bc3d3f">
                    ${ parent.decodeEntities(`Prénom`) } 					<span className="mf-input-required-indicator">*</span>
                </label>

            <input
                type="text"
                className="mf-input "
                id="mf-input-text-8bc3d3f"
                name="prenom"
                placeholder="${ parent.decodeEntities(`Prénom`) } "
                                    onInput=${parent.handleChange}
                    onBlur=${parent.handleChange}
                    aria-invalid=${validation.errors['prenom'] ? 'true' : 'false'}
                    ref=${el =>{
                                                parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":1,"maxLength":"","type":"none","required":true,"expression":"null"}, el)
                    }}
                                />

                            <${validation.ErrorMessage}
                    errors=${validation.errors}
                    name="prenom"
                    as=${html`<span className="mf-error-message"></span>`}
                    />

                    </div>

                        </div>
                </div>
                <div className="elementor-element elementor-element-f3516ab elementor-widget elementor-widget-mf-email" data-id="f3516ab" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;mf-email&quot;}" data-widget_type="mf-email.default">
                <div className="elementor-widget-container">

        <div className="mf-input-wrapper">
                            <label className="mf-input-label" htmlFor="mf-input-email-f3516ab">
                    ${ parent.decodeEntities(`E-mail`) } 					<span className="mf-input-required-indicator">*</span>
                </label>

            <input
                type="email"

                defaultValue=""
                className="mf-input "
                id="mf-input-email-f3516ab"
                name="mf-email"
                placeholder="${ parent.decodeEntities(`E-mail`) } "

                onBlur=${parent.handleChange} onFocus=${parent.handleChange} aria-invalid=${validation.errors['mf-email'] ? 'true' : 'false' }
                ref=${el=> parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","emailMessage":"Veuillez saisir une adresse de messagerie valide","minLength":1,"maxLength":"","type":"none","required":true,"expression":"null"}, el)}
                            />

                        <${validation.ErrorMessage}
                errors=${validation.errors}
                name="mf-email"
                as=${html`<span className="mf-error-message"></span>`}
            />

                    </div>

                </div>
                </div>
                <div className="elementor-element elementor-element-61eb343 elementor-widget elementor-widget-mf-text" data-id="61eb343" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;adresse&quot;}" data-widget_type="mf-text.default">
                <div className="elementor-widget-container">

        <div className="mf-input-wrapper">
                            <label className="mf-input-label" htmlFor="mf-input-text-61eb343">
                    ${ parent.decodeEntities(`Adresse`) } 					<span className="mf-input-required-indicator">*</span>
                </label>

            <input
                type="text"
                className="mf-input "
                id="mf-input-text-61eb343"
                name="adresse"
                placeholder="${ parent.decodeEntities(`Adresse`) } "
                                    onInput=${parent.handleChange}
                    onBlur=${parent.handleChange}
                    aria-invalid=${validation.errors['adresse'] ? 'true' : 'false'}
                    ref=${el =>{
                                                parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":1,"maxLength":"","type":"none","required":true,"expression":"null"}, el)
                    }}
                                />

                            <${validation.ErrorMessage}
                    errors=${validation.errors}
                    name="adresse"
                    as=${html`<span className="mf-error-message"></span>`}
                    />

                    </div>

                        </div>
                </div>
                <div className="elementor-element elementor-element-63dcd8c elementor-widget elementor-widget-mf-text" data-id="63dcd8c" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;code-postal&quot;}" data-widget_type="mf-text.default">
                <div className="elementor-widget-container">

        <div className="mf-input-wrapper">
                            <label className="mf-input-label" htmlFor="mf-input-text-63dcd8c">
                    ${ parent.decodeEntities(`Code postal`) } 					<span className="mf-input-required-indicator">*</span>
                </label>

            <input
                type="text"
                className="mf-input "
                id="mf-input-text-63dcd8c"
                name="code-postal"
                placeholder="${ parent.decodeEntities(`Code postal`) } "
                                    onInput=${parent.handleChange}
                    onBlur=${parent.handleChange}
                    aria-invalid=${validation.errors['code-postal'] ? 'true' : 'false'}
                    ref=${el =>{
                                                parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":1,"maxLength":"","type":"none","required":true,"expression":"null"}, el)
                    }}
                                />

                            <${validation.ErrorMessage}
                    errors=${validation.errors}
                    name="code-postal"
                    as=${html`<span className="mf-error-message"></span>`}
                    />

                    </div>

                        </div>
                </div>
                <div className="elementor-element elementor-element-6a07518 elementor-widget elementor-widget-mf-text" data-id="6a07518" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;ville&quot;}" data-widget_type="mf-text.default">
                <div className="elementor-widget-container">

        <div className="mf-input-wrapper">
                            <label className="mf-input-label" htmlFor="mf-input-text-6a07518">
                    ${ parent.decodeEntities(`Ville`) } 					<span className="mf-input-required-indicator">*</span>
                </label>

            <input
                type="text"
                className="mf-input "
                id="mf-input-text-6a07518"
                name="ville"
                placeholder="${ parent.decodeEntities(`Ville`) } "
                                    onInput=${parent.handleChange}
                    onBlur=${parent.handleChange}
                    aria-invalid=${validation.errors['ville'] ? 'true' : 'false'}
                    ref=${el =>{
                                                parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":1,"maxLength":"","type":"none","required":true,"expression":"null"}, el)
                    }}
                                />

                            <${validation.ErrorMessage}
                    errors=${validation.errors}
                    name="ville"
                    as=${html`<span className="mf-error-message"></span>`}
                    />

                    </div>

                        </div>
                </div>
                <div className="elementor-element elementor-element-b501060 elementor-widget elementor-widget-mf-textarea" data-id="b501060" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;mf-textarea&quot;}" data-widget_type="mf-textarea.default">
                <div className="elementor-widget-container">

        <div className="mf-input-wrapper">
                            <label className="mf-input-label" htmlFor="mf-input-text-area-b501060">
                    ${ parent.decodeEntities(`Message`) } 					<span className="mf-input-required-indicator"></span>
                </label>

            <textarea className="mf-input mf-textarea " id="mf-input-text-area-b501060"
                name="mf-textarea"
                placeholder="${ parent.decodeEntities(`Message`) } "
                cols="30" rows="10"
                                    onInput=${ parent.handleChange }
                    aria-invalid=${validation.errors['mf-textarea'] ? 'true' : 'false'}
                    ref=${ el => parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":1,"maxLength":"","type":"none","required":false,"expression":"null"}, el)}
                                ></textarea>

                            <${validation.ErrorMessage}
                    errors=${validation.errors}
                    name="mf-textarea"
                    as=${html`<span className="mf-error-message"></span>`}
                    />
                                </div>

                        </div>
                </div>
                <div className="elementor-element elementor-element-bf0613b elementor-widget elementor-widget-mf-gdpr-consent" data-id="bf0613b" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;mf-gdpr-consent&quot;}" data-widget_type="mf-gdpr-consent.default">
                <div className="elementor-widget-container">

        <div className="mf-input-wrapper">

            <div className="mf-checkbox multi-option-input-type" id="mf-input-gdpr-bf0613b">
                <div className="mf-checkbox-option">
                    <label>
                                                <input
                            type="checkbox"
                            className="mf-input mf-checkbox-input "
                            name="mf-gdpr-consent"
                                                            onInput=${ parent.handleOptin }
                                aria-invalid=${validation.errors['mf-gdpr-consent'] ? 'true' : 'false'}
                                ref=${ el => parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":1,"maxLength":"","type":"none","required":false,"expression":"null"}, el) }
                                                        />
                        <span>
                            En cliquant sur « Envoyer », j’accepte qu’un conseiller Assurances de Rueil, m’appelle pour m’accompagner dans le choix de mon assurance.						</span>
                    </label>
                </div>
            </div>

                            <${validation.ErrorMessage}
                    errors=${validation.errors}
                    name="mf-gdpr-consent"
                    as=${html`<span className="mf-error-message"></span>`}
                    />

                    </div>

                        </div>
                </div>
                <div className="elementor-element elementor-element-8d921ac elementor-widget elementor-widget-mf-gdpr-consent" data-id="8d921ac" data-element_type="widget" data-settings="{&quot;mf_input_name&quot;:&quot;mf-gdpr-consent&quot;}" data-widget_type="mf-gdpr-consent.default">
                <div className="elementor-widget-container">

        <div className="mf-input-wrapper">

            <div className="mf-checkbox multi-option-input-type" id="mf-input-gdpr-8d921ac">
                <div className="mf-checkbox-option">
                    <label>
                                                <input
                            type="checkbox"
                            className="mf-input mf-checkbox-input "
                            name="mf-gdpr-consent"
                                                            onInput=${ parent.handleOptin }
                                aria-invalid=${validation.errors['mf-gdpr-consent'] ? 'true' : 'false'}
                                ref=${ el => parent.activateValidation({"message":"Ce champ est n\u00e9cessaire.","minLength":1,"maxLength":"","type":"none","required":true,"expression":"null"}, el) }
                                                        />
                        <span>
                            J'accepte le traitement de mes données personnelles conformément au RGPD. <a href="/politique-de-confidentialite/">EN SAVOIR PLUS</a>						</span>
                    </label>
                </div>
            </div>

                            <${validation.ErrorMessage}
                    errors=${validation.errors}
                    name="mf-gdpr-consent"
                    as=${html`<span className="mf-error-message"></span>`}
                    />

                    </div>

                        </div>
                </div>
                <div className="elementor-element elementor-element-ebfc01d elementor-widget elementor-widget-mf-recaptcha" data-id="ebfc01d" data-element_type="widget" data-widget_type="mf-recaptcha.default">
                <div className="elementor-widget-container">
                            <div className="mf-input-wrapper">

                    <div
                        className="g-recaptcha centre"
                        id="g-recaptcha"
                        data-sitekey="6LcL1RgqAAAAAGaJrpOCsaogH7U397pCt1y4izHG"
                                                    data-callback="handleReCAPTCHA_${this.state.recaptcha_uid}"
                            data-expired-callback="handleReCAPTCHA_${this.state.recaptcha_uid}"
                            data-error-callback="handleReCAPTCHA_${this.state.recaptcha_uid}"
                            aria-invalid=${validation.errors['g-recaptcha-response'] ? 'true' : 'false'}
                                                ></div>

                                            <input type="hidden"
                            name="g-recaptcha-response"
                            className="mf-input mf-mobile-hidden"
                            value=${parent.getValue('g-recaptcha-response')}
                            ref=${el => parent.activateValidation({"message":"reCAPTCHA est n\u00e9cessaire.","required":true}, el)}
                            />

                        <${validation.ErrorMessage} errors=${validation.errors} name="g-recaptcha-response" as=${html`<span className="mf-error-message"></span>`} />

                            </div>

                        </div>
                </div>
                <div className="elementor-element elementor-element-3a46b2f mf-btn--center elementor-widget elementor-widget-mf-button" data-id="3a46b2f" data-element_type="widget" data-widget_type="mf-button.default">
                <div className="elementor-widget-container">
                            <div className="mf-btn-wraper " data-mf-form-conditional-logic-requirement="">
                            <button type="submit" className="metform-btn metform-submit-btn " id="">
                    <span>${ parent.decodeEntities(`Envoyer`) } </span>
                </button>
                    </div>
                        </div>
                </div>
                    </div>
        </div>
                    </div>
        </section>
                </div>
                            `}
                    </div>

                    ${is_dummy_markup ? message_position === 'bottom' ? props.ResponseDummyMarkup(message_successIcon, message_proClass) : '' : ''}
                    ${is_dummy_markup ? ' ' : message_position === 'bottom' ? props.SubmitResponseMarkup`${parent}${state}${message_successIcon}${message_errorIcon}${message_proClass}` : ''}

                </form>
            `
        </script>

        </div>



    </div>
  </section>
  <section class="adr-next-steps" aria-label="Moyens de contact">
    <article class="adr-step-card"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-people-gold.svg" alt=""><h2>Venir au cabinet</h2><p>Un accueil local, au coeur de Rueil-Malmaison.</p></article>
    <article class="adr-step-card"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-house-gold.svg" alt=""><h2>Appeler</h2><p>Pour une question rapide ou une demande urgente, appelez le cabinet.</p></article>
    <article class="adr-step-card"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-briefcase-gold.svg" alt=""><h2>Préparer un devis</h2><p>Le formulaire de devis permet de préparer une réponse plus complète.</p></article>
  </section>
</div>
ADR_STAGE_CONTACT
        ,
        'mentions' => <<<'ADR_STAGE_MENTIONS'
<div class="adr-page-stage adr-legal-stage adr-mentions-stage">
  <style>    body.page-id-6909 .ekit-template-content-header,
    body.page-id-6909 .site-footer,
    body.page-id-6909 footer.footer,
    body.page-id-6909 .ekit-template-content-footer,
    body.page-id-6909 .page-banner-area,
    body.page-id-6909 .breadcrumb { display: none !important; }
    .adr-page-stage, .adr-page-stage * { box-sizing: border-box; }
    .adr-page-stage {
      --adr-blue: #003478;
      --adr-blue-soft: #6da9ff;
      --adr-ink: #07192f;
      --adr-muted: #657386;
      --adr-line: rgba(0, 52, 120, 0.18);
      --adr-paper: rgba(255, 255, 255, 0.88);
      --adr-solid: #ffffff;
      --adr-wash: #eef5fb;
      --adr-gold: #d7a53c;
      --adr-shadow: 0 24px 70px rgba(0, 28, 64, 0.16);
      --adr-soft-shadow: 0 14px 36px rgba(0, 28, 64, 0.1);
      width: 100vw;
      margin-left: calc(50% - 50vw);
      margin-right: calc(50% - 50vw);
      margin-top: -1px;
      padding: clamp(14px, 2vw, 24px) clamp(14px, 2vw, 24px) 64px;
      background: radial-gradient(circle at 10% 0%, rgba(109,169,255,.2), transparent 30%), var(--adr-wash);
      color: var(--adr-ink);
      font-family: Rubik, "Open Sans", Arial, sans-serif;
      overflow: clip;
    }
    .adr-page-stage:has(#adr-theme-toggle:checked) {
      --adr-blue: #83b7ff;
      --adr-blue-soft: #74a9f7;
      --adr-ink: #edf6ff;
      --adr-muted: #a8b8c8;
      --adr-line: rgba(169, 201, 234, 0.24);
      --adr-paper: rgba(12, 29, 49, 0.84);
      --adr-solid: #0e2135;
      --adr-wash: #06111d;
      --adr-shadow: 0 24px 70px rgba(0, 0, 0, 0.34);
      --adr-soft-shadow: 0 14px 36px rgba(0, 0, 0, 0.24);
    }
    .adr-page-stage a { color: inherit; }
    .adr-page-stage img { display: block; max-width: 100%; }
    .adr-page-stage [id] { scroll-margin-top: 140px; }
    .adr-switch-input { position: absolute; opacity: 0; pointer-events: none; }
    .adr-floating-panel {
      position: sticky;
      top: clamp(12px, 2vw, 24px);
      z-index: 9;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      width: min(100%, 1288px);
      margin: 0 auto 18px;
      border: 1px solid var(--adr-line);
      border-radius: 8px;
      padding: 7px 8px 7px 12px;
      background: var(--adr-paper);
      box-shadow: 0 18px 54px rgba(0, 0, 0, 0.18);
      backdrop-filter: blur(18px);
    }
    body.admin-bar .adr-floating-panel { top: calc(32px + clamp(12px, 2vw, 24px)); }
    .adr-brand { display: inline-flex; align-items: center; gap: 13px; min-width: 0; color: var(--adr-blue); text-decoration: none; }
    .adr-brand img { width: clamp(48px, 5vw, 78px); height: clamp(48px, 5vw, 78px); border-radius: 50%; }
    .adr-brand strong { font-size: clamp(24px, 3vw, 42px); line-height: 1; letter-spacing: 0; white-space: nowrap; }
    .adr-panel-actions { display: flex; align-items: stretch; justify-content: flex-end; gap: 8px; }
    .adr-button, .adr-switch-label {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 44px;
      height: clamp(44px, 4.6vw, 64px);
      border-radius: 8px;
      padding: 12px 18px;
      border: 1px solid var(--adr-line);
      font-weight: 900;
      line-height: 1.05;
      text-decoration: none;
      cursor: pointer;
      white-space: nowrap;
      margin: 0 !important;
    }
    .adr-button-primary, .adr-button-primary:visited, .adr-button-primary:hover, .adr-button-primary:focus {
      border-color: transparent;
      background: var(--adr-blue);
      color: #fff !important;
      box-shadow: 0 16px 34px rgba(0, 52, 120, 0.22);
    }
    .adr-page-stage:has(#adr-theme-toggle:checked) .adr-button-primary { color: #06111d !important; }
    .adr-switch-label { min-width: 58px; background: color-mix(in srgb, var(--adr-blue) 7%, var(--adr-solid)); color: var(--adr-blue); font-size: 13px; }
    .adr-page-stage:has(#adr-theme-toggle:checked) label[for="adr-theme-toggle"] { background: var(--adr-blue); color: #06111d; }
    .adr-hero, .adr-form-section, .adr-next-steps, .adr-mini-nav { width: min(100%, 1180px); margin-left: auto; margin-right: auto; }
    .adr-hero {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(320px, .75fr);
      min-height: 430px;
      overflow: hidden;
      border: 1px solid var(--adr-line);
      border-radius: 8px;
      background: var(--adr-solid);
      box-shadow: var(--adr-shadow);
    }
    .adr-hero-copy { display: grid; align-content: center; gap: 18px; padding: clamp(28px, 5vw, 70px); }
    .adr-kicker { margin: 0; color: var(--adr-gold); font-size: 14px; font-weight: 900; text-transform: uppercase; letter-spacing: .08em; }
    .adr-hero h1 { margin: 0; color: var(--adr-blue); font-size: clamp(22px, 3.02vw, 41px); line-height: 1; letter-spacing: 0; }
    .adr-hero p { max-width: 680px; margin: 0; color: var(--adr-muted); font-size: clamp(18px, 2vw, 23px); line-height: 1.55; }
    .adr-hero-media { min-height: 340px; background: #d9e7f2; }
    .adr-hero-media img { width: 100%; height: 100%; object-fit: cover; }
    .adr-mini-nav { position: sticky; top: 116px; z-index: 8; display: flex; flex-wrap: wrap; justify-content: center; gap: 6px; margin-top: 18px; margin-bottom: 18px; border: 1px solid var(--adr-line); border-radius: 8px; padding: 8px; background: var(--adr-paper); backdrop-filter: blur(18px); }
    .adr-mini-nav a { border-radius: 999px; padding: 9px 12px; color: var(--adr-blue); font-size: 13px; font-weight: 900; text-decoration: none; }
    .adr-form-section { display: grid; grid-template-columns: minmax(270px, .52fr) minmax(0, 1fr); gap: 16px; align-items: start; }
    .adr-side-card, .adr-form-card, .adr-step-card { border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); }
    .adr-side-card { position: sticky; top: 178px; display: grid; gap: 14px; padding: clamp(22px, 3vw, 34px); }
    .adr-side-card h2, .adr-form-card h2, .adr-step-card h2 { margin: 0; color: var(--adr-blue); line-height: 1.12; letter-spacing: 0; }
    .adr-side-card p, .adr-form-card p, .adr-step-card p, .adr-side-card li { margin: 0; color: var(--adr-muted); line-height: 1.58; }
    .adr-side-card ol { display: grid; gap: 12px; margin: 6px 0 0; padding-left: 22px; font-weight: 900; color: var(--adr-muted); }
    .adr-form-card { padding: clamp(18px, 3vw, 28px); }
    .adr-form-intro { display: grid; gap: 8px; margin-bottom: 16px; }
    .adr-form-card .mf-form-wrapper,
    .adr-form-card .metform-form-content,
    .adr-form-card .elementor,
    .adr-form-card .elementor-section,
    .adr-form-card .elementor-container { max-width: none !important; width: 100% !important; }
    .adr-form-card input:not([type="checkbox"]):not([type="radio"]),
    .adr-form-card select,
    .adr-form-card textarea {
      min-height: 46px !important;
      border: 1px solid var(--adr-line) !important;
      border-radius: 8px !important;
      background: color-mix(in srgb, var(--adr-solid) 88%, transparent) !important;
      color: var(--adr-ink) !important;
      box-shadow: none !important;
    }
    .adr-form-card label,
    .adr-form-card .mf-input-label { color: var(--adr-ink) !important; font-weight: 800 !important; }
    .adr-form-card .metform-btn,
    .adr-form-card .metform-submit-btn { min-height: 50px !important; border-radius: 8px !important; background: var(--adr-blue) !important; color: #fff !important; font-weight: 900 !important; }
    .adr-next-steps { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-top: 18px; }
    .adr-step-card { display: grid; gap: 8px; padding: 20px; }
    .adr-step-card img { width: 48px; height: 48px; border-radius: 50%; padding: 8px; background: color-mix(in srgb, var(--adr-blue) 8%, white); }
    @media (max-width: 980px) { .adr-form-section, .adr-hero { grid-template-columns: 1fr; } .adr-side-card, .adr-mini-nav { position: static; } .adr-hero-media { order: -1; min-height: 240px; } }
    @media (max-width: 760px) { .adr-floating-panel, .adr-panel-actions { align-items: stretch; flex-direction: column; } .adr-brand strong { white-space: normal; } .adr-panel-actions, .adr-button, .adr-switch-label { width: 100%; } }
    @media (max-width: 640px) { .adr-page-stage { padding-left: 10px; padding-right: 10px; } .adr-hero h1 { font-size: 19px; } .adr-hero-copy { padding: 22px 18px; } .adr-next-steps { grid-template-columns: 1fr; } }
    body.page-id-6909 .ekit-template-content-header,
    body.page-id-6909 .site-footer,
    body.page-id-6909 footer.footer,
    body.page-id-6909 .ekit-template-content-footer,
    body.page-id-6909 .page-banner-area,
    body.page-id-6909 .breadcrumb { display: none !important; }
    .adr-legal-stage .adr-hero { grid-template-columns: 1fr; min-height: 0; }
    .adr-legal-stage .adr-hero-copy { padding: clamp(24px, 4vw, 48px); }
    .adr-legal-stage .adr-hero h1 { font-size: clamp(20px, 2.35vw, 34px); }
    .adr-legal-stage .adr-info-card { min-height: 0; }
    .adr-legal-stage .adr-legal-layout { width: min(100%, 1180px); margin: 18px auto 0; display: grid; grid-template-columns: minmax(0, .34fr) minmax(0, 1fr); gap: 14px; align-items: start; }
    .adr-legal-stage .adr-legal-index, .adr-legal-stage .adr-legal-copy { border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); }
    .adr-legal-stage .adr-legal-index { position: sticky; top: 176px; display: grid; gap: 10px; padding: clamp(18px, 2.6vw, 28px); }
    .adr-legal-stage .adr-legal-index h2, .adr-legal-stage .adr-legal-copy h2, .adr-legal-stage .adr-legal-copy h3, .adr-legal-stage .adr-legal-copy h4 { margin: 0; color: var(--adr-blue); line-height: 1.15; letter-spacing: 0; }
    .adr-legal-stage .adr-legal-index p, .adr-legal-stage .adr-legal-index li { margin: 0; color: var(--adr-muted); line-height: 1.55; }
    .adr-legal-stage .adr-legal-index ul { display: grid; gap: 8px; margin: 0; padding-left: 18px; }
    .adr-legal-stage .adr-legal-copy { padding: clamp(20px, 3vw, 34px); color: var(--adr-muted); line-height: 1.65; }
    .adr-legal-stage .adr-legal-copy > *:first-child { margin-top: 0 !important; }
    .adr-legal-stage .adr-legal-copy h4 { margin-top: 22px; margin-bottom: 8px; font-size: clamp(18px, 1.8vw, 24px); }
    .adr-legal-stage .adr-legal-copy p { margin: 0 0 14px; color: var(--adr-muted); }
    .adr-legal-stage .adr-legal-copy ul { margin: 0 0 16px; padding-left: 22px; color: var(--adr-muted); }
    .adr-legal-stage .adr-legal-copy li { margin: 0 0 8px; }
    .adr-legal-stage .adr-legal-copy a { color: var(--adr-blue); font-weight: 800; overflow-wrap: anywhere; }
    .adr-legal-stage .adr-legal-copy code { border: 1px solid var(--adr-line); border-radius: 6px; padding: 2px 5px; background: color-mix(in srgb, var(--adr-blue) 8%, transparent); color: var(--adr-blue); }
    .adr-legal-stage .adr-official-note { margin-top: 10px; font-size: 13px; font-weight: 800; color: var(--adr-muted); }
    @media (max-width: 900px) { .adr-legal-stage .adr-legal-layout { grid-template-columns: 1fr; } .adr-legal-stage .adr-legal-index { position: static; } }
  </style>
  <input class="adr-switch-input" id="adr-theme-toggle" type="checkbox" aria-label="Basculer le thème jour/nuit">
  <header class="adr-floating-panel" aria-label="Navigation principale">
    <a class="adr-brand" href="/"><img decoding="async" src="https://assurancesderueil.fr/wp-content/uploads/2022/08/cropped-flaticon2.png" alt=""><strong>Assurances de Rueil</strong></a>
    <div class="adr-panel-actions">
      <a class="adr-button adr-button-primary" href="/demande-de-devis-assurance-a-rueil-malmaison/">Demander un devis</a>
      <label class="adr-switch-label" for="adr-theme-toggle">Jour/Nuit</label>
    </div>
  </header>
  <section class="adr-hero" aria-label="Mentions légales">
    <div class="adr-hero-copy">
      <p class="adr-kicker">Informations légales</p>
      <h1>Mentions légales</h1>
      <p>Les informations réglementaires du cabinet sont conservées dans leur version complète, avec une mise en page plus lisible.</p>
      <p class="adr-official-note">Texte officiel conservé en français.</p>
    </div>
  </section>
  <nav class="adr-mini-nav" aria-label="Pages principales">
    <a href="/">Accueil</a>
    <a href="/cabinet-de-courtage-en-assurances-rueil-malmaison/">Cabinet</a>
    <a href="/assurance-de-pret-a-rueil-malmaison/">Assurance de prêt</a>
    <a href="/assurance-particuliers-rueil-malmaison/">Particuliers</a>
    <a href="/assurance-entreprise-rueil-malmaison/">Professionnels</a>
    <a href="/courtier-en-assurances-de-rueil-malmaison/">Contact</a>
  </nav>
  <section class="adr-info-grid" aria-label="Repères">
    <article class="adr-info-card"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-house-gold.svg" alt=""><h2>Éditeur</h2><p>ASSURANCES DE RUEIL, courtier en assurances à Rueil-Malmaison.</p></article><article class="adr-info-card"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-house-gold.svg" alt=""><h2>Réglementation</h2><p>RCS Nanterre, ORIAS et autorité de contrôle prudentiel et de résolution.</p></article><article class="adr-info-card"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-house-gold.svg" alt=""><h2>Réclamations</h2><p>Coordonnées et médiation de la consommation.</p></article>
  </section>
  <section class="adr-legal-layout" aria-label="Mentions légales">
    <aside class="adr-legal-index">
      <h2>Repères</h2>
      <ul><li>Éditeur</li><li>Réglementation</li><li>Réclamations</li></ul>
      <a class="adr-button adr-button-primary" href="/courtier-en-assurances-de-rueil-malmaison/">Contacter le cabinet</a>
    </aside>
    <article class="adr-legal-copy"><p>Ce site est publié par : <br />ASSURANCES DE RUEIL<br />75 avenue Victor Hugo <br />92500 RUEIL MALMAISON<br />Tél. : +33 1 47 51 06 69 <br /><a href="mailto:contact@assurancesderueil.fr">contact@assurancesderueil.fr</a> &#8211; <br />Courtier en assurances – SARL au capital de 16 007 euros &#8211; RCS Nanterre 689 801 769 &#8211; ORIAS N° 07 001948 &#8211; www.orias.fr Courtier en assurances soumis à l&rsquo;autorité de contrôle prudentiel et de résolution (ACPR) – 4 Place de Budapest CS 92459 – 75436 PARIS Cedex 09. <br /><br /></p><h4>HÉBERGEMENT</h4><p>Le site est hébergé par Infomaniak<br /><br /></p><h4>PROTECTION DES DONNÉES</h4><p>Les informations recueillies sur le site sont nécessaires pour permettre à l&rsquo;utilisateur de devenir membre du Site, d&rsquo;accéder aux fonctionnalités afférentes à la qualité de membre et/ou de traiter les demandes d&rsquo;informations de l&rsquo;utilisateur. La collecte et le traitement d&rsquo;informations personnelles sont effectués au sein du présent site conformément à la loi n°78-17 du 6 janvier 1978 relative à l&rsquo;informatique, aux fichiers et aux libertés dite Loi « Informatique et Libertés », modifiée par la Loi n°2004-801 du 06 août 2004. Les informations saisies par les utilisateurs du site internet ne font pas l&rsquo;objet d&rsquo;un traitement informatique. Conformément à la loi du 6 janvier 1978 modifiée par la Loi du 06 août 2004 relative à l&rsquo;informatique, aux fichiers et aux libertés, l&rsquo;utilisateur dispose d&rsquo;un droit d&rsquo;accès, de rectification et de suppression des données le concernant en écrivant à l&rsquo;adresse suivante : Les Assurances de Rueil 75 avenue Victor Hugo 92500 RUEIL-MALMAISON en précisant ses coordonnées (identité, adresse électronique, justificatif d&rsquo;identité). Les hyperliens présents sur le site permettent d&rsquo;accéder à des sites susceptibles de recueillir des informations nominatives. Dès lors, il appartient à l&rsquo;utilisateur de consulter les mentions légales de chacun de ces sites. <br /><br /></p><h4>RESPONSABILITÉS</h4><p>Bien que le propriétaire du Site s&rsquo;efforce raisonnablement de mettre à jour les informations publiées sur le site, il ne saurait être tenu pour responsable de toute erreur ou omission. Le propriétaire du site ne saurait être tenu pour responsable des dommages directs ou indirects qui pourraient résulter de l&rsquo;accès au site ou de son utilisation, y compris de l&rsquo;inaccessibilité, des pertes de données, détériorations, destructions ou virus qui pourraient affecter l&rsquo;équipement informatique de l&rsquo;utilisateur, et/ou de la présence de virus sur son site. <br /><br /></p><h4>PROPRIÉTÉ INTELLECTUELLE</h4><p>Les informations présentes sur le site sont la propriété du propriétaire du site. De ce fait, toute reproduction, modification, distribution de ces éléments est interdite sans l&rsquo;accord préalable écrit de son propriétaire. Les images ou photographies de personnes ou de lieux figurant sur le site sont utilisées par ce dernier avec l&rsquo;accord des titulaires des droits. Certaines images ou photos utilisées proviennent de banques d&rsquo;images libres de droits. L&rsquo;utilisation de ces images ou photographies par des tiers est interdite sans autorisation spécifique, écrite et expresse du propriétaire. Tous les droits de reproduction sont réservés, y compris pour les documents téléchargeables et les représentations des images et des photographies. <br /><br /></p><h4>UTILISATION DE GOOGLE ANALYTICS</h4><p>Ce site utilise Google Analytics, un service d&rsquo;analyse de site internet fourni par Google Inc. (« Google»). Google Analytics utilise des cookies, qui sont des fichiers texte placés sur votre ordinateur, pour aider le site internet à analyser l&rsquo;utilisation du site par ses utilisateurs. Les données générées par les cookies concernant votre utilisation du site (y compris votre adresse IP) seront transmises et stockées par Google sur des serveurs situés aux États-Unis. Google utilisera cette information dans le but d&rsquo;évaluer votre utilisation du site, de compiler des rapports sur l&rsquo;activité du site à destination de son éditeur et de fournir d&rsquo;autres services relatifs à l&rsquo;activité du site et à l&rsquo;utilisation d&rsquo;Internet. Google est susceptible de communiquer ces données à des tiers en cas d&rsquo;obligation légale ou lorsque ces tiers traitent ces données pour le compte de Google, y compris notamment l&rsquo;éditeur de ce site. Google ne recoupera pas votre adresse IP avec toute autre donnée détenue par Google. Vous pouvez désactiver l&rsquo;utilisation de cookies en sélectionnant les paramètres appropriés de votre navigateur. Cependant, une telle désactivation pourrait empêcher l&rsquo;utilisation de certaines fonctionnalités de ce site. En utilisant ce site internet, vous consentez expressément au traitement de vos données nominatives par Google dans les conditions et pour les finalités décrites ci-dessus. <br /><br /></p><h4>CONTENUS OU COMPORTEMENTS ILLICITES</h4><p>Vous pouvez transmettre des signalements de contenus ou de comportements illicites que vous auriez rencontrés au cours de votre utilisation de ce site en adressant un e-mail à l&rsquo;adresse suivante : <br />Les Assurances de Rueil<br />75 avenue Victor Hugo <br />92500 RUEIL-MALMAISON en précisant ses coordonnées (identité, adresse électronique, justificatif d&rsquo;identité). <br /><br /></p><h4>DROIT APPLICABLE</h4><p>Le droit applicable au présent site et à ses mentions légales est le droit français. <br /><br /></p><h4>MODIFICATION DES CONDITIONS</h4><p>Le Propriétaire du Site se réserve le droit de modifier ou d&rsquo;actualiser les présentes mentions légales à tout moment et sans information préalable des utilisateurs <br /><br /></p><h4>RÉCLAMATIONS</h4><p>En cas de réclamation, vous pouvez adresser votre courrier à : <br /><span style="color: var( --e-global-color-text ); font-family: var( --e-global-typography-text-font-family ), Sans-serif; font-weight: var( --e-global-typography-text-font-weight );">ASSURANCES DE RUEIL <br />Service Réclamation <br />75 avenue Victor Hugo <br />92500 RUEIL MALMAISON <br />Si au bout de 2 mois, vous considérez ne pas avoir obtenu satisfaction, vous pouvez saisir par la suite le Médiateur de PALNETE COURTIER : <br />&#8211; à partir du site <a href="http://www.mediation-planetecourtier.com">www.mediation-planetecourtier.com</a> <br />&#8211; par voie électronique à <a href="mailto:mediation@planetecourtier.com">mediation@planetecourtier.com</a> <br />&#8211; par courrier simple adressé à : <br />Médiateur de la consommation de PLANTE COURTIER <br />12/14 Rond-point des Champs Elysées <br />75008 PARIS <br />Vous avez également la possibilité de vous adresser à l’ACPR (Autorité de contrôle prudentiel et de résolution) 4 Place de Budapest – CS 92459 – 75436 PARIS Cedex 09. <a href="https://acpr.banque-france.fr">https://acpr.banque-france.fr</a></span></p><p> </p><h4>CRÉDITS</h4><p>Réalisation technique, développement et webdesign : Emavista<br />Photos : shutterstock</p></article>
  </section>
</div>
ADR_STAGE_MENTIONS
        ,
        'privacy' => <<<'ADR_STAGE_PRIVACY'
<div class="adr-page-stage adr-legal-stage adr-privacy-stage">
  <style>    body.page-id-3 .ekit-template-content-header,
    body.page-id-3 .site-footer,
    body.page-id-3 footer.footer,
    body.page-id-3 .ekit-template-content-footer,
    body.page-id-3 .page-banner-area,
    body.page-id-3 .breadcrumb { display: none !important; }
    .adr-page-stage, .adr-page-stage * { box-sizing: border-box; }
    .adr-page-stage {
      --adr-blue: #003478;
      --adr-blue-soft: #6da9ff;
      --adr-ink: #07192f;
      --adr-muted: #657386;
      --adr-line: rgba(0, 52, 120, 0.18);
      --adr-paper: rgba(255, 255, 255, 0.88);
      --adr-solid: #ffffff;
      --adr-wash: #eef5fb;
      --adr-gold: #d7a53c;
      --adr-shadow: 0 24px 70px rgba(0, 28, 64, 0.16);
      --adr-soft-shadow: 0 14px 36px rgba(0, 28, 64, 0.1);
      width: 100vw;
      margin-left: calc(50% - 50vw);
      margin-right: calc(50% - 50vw);
      margin-top: -1px;
      padding: clamp(14px, 2vw, 24px) clamp(14px, 2vw, 24px) 64px;
      background: radial-gradient(circle at 10% 0%, rgba(109,169,255,.2), transparent 30%), var(--adr-wash);
      color: var(--adr-ink);
      font-family: Rubik, "Open Sans", Arial, sans-serif;
      overflow: clip;
    }
    .adr-page-stage:has(#adr-theme-toggle:checked) {
      --adr-blue: #83b7ff;
      --adr-blue-soft: #74a9f7;
      --adr-ink: #edf6ff;
      --adr-muted: #a8b8c8;
      --adr-line: rgba(169, 201, 234, 0.24);
      --adr-paper: rgba(12, 29, 49, 0.84);
      --adr-solid: #0e2135;
      --adr-wash: #06111d;
      --adr-shadow: 0 24px 70px rgba(0, 0, 0, 0.34);
      --adr-soft-shadow: 0 14px 36px rgba(0, 0, 0, 0.24);
    }
    .adr-page-stage a { color: inherit; }
    .adr-page-stage img { display: block; max-width: 100%; }
    .adr-page-stage [id] { scroll-margin-top: 140px; }
    .adr-switch-input { position: absolute; opacity: 0; pointer-events: none; }
    .adr-floating-panel {
      position: sticky;
      top: clamp(12px, 2vw, 24px);
      z-index: 9;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      width: min(100%, 1288px);
      margin: 0 auto 18px;
      border: 1px solid var(--adr-line);
      border-radius: 8px;
      padding: 7px 8px 7px 12px;
      background: var(--adr-paper);
      box-shadow: 0 18px 54px rgba(0, 0, 0, 0.18);
      backdrop-filter: blur(18px);
    }
    body.admin-bar .adr-floating-panel { top: calc(32px + clamp(12px, 2vw, 24px)); }
    .adr-brand { display: inline-flex; align-items: center; gap: 13px; min-width: 0; color: var(--adr-blue); text-decoration: none; }
    .adr-brand img { width: clamp(48px, 5vw, 78px); height: clamp(48px, 5vw, 78px); border-radius: 50%; }
    .adr-brand strong { font-size: clamp(24px, 3vw, 42px); line-height: 1; letter-spacing: 0; white-space: nowrap; }
    .adr-panel-actions { display: flex; align-items: stretch; justify-content: flex-end; gap: 8px; }
    .adr-button, .adr-switch-label {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 44px;
      height: clamp(44px, 4.6vw, 64px);
      border-radius: 8px;
      padding: 12px 18px;
      border: 1px solid var(--adr-line);
      font-weight: 900;
      line-height: 1.05;
      text-decoration: none;
      cursor: pointer;
      white-space: nowrap;
      margin: 0 !important;
    }
    .adr-button-primary, .adr-button-primary:visited, .adr-button-primary:hover, .adr-button-primary:focus {
      border-color: transparent;
      background: var(--adr-blue);
      color: #fff !important;
      box-shadow: 0 16px 34px rgba(0, 52, 120, 0.22);
    }
    .adr-page-stage:has(#adr-theme-toggle:checked) .adr-button-primary { color: #06111d !important; }
    .adr-switch-label { min-width: 58px; background: color-mix(in srgb, var(--adr-blue) 7%, var(--adr-solid)); color: var(--adr-blue); font-size: 13px; }
    .adr-page-stage:has(#adr-theme-toggle:checked) label[for="adr-theme-toggle"] { background: var(--adr-blue); color: #06111d; }
    .adr-hero, .adr-form-section, .adr-next-steps, .adr-mini-nav { width: min(100%, 1180px); margin-left: auto; margin-right: auto; }
    .adr-hero {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(320px, .75fr);
      min-height: 430px;
      overflow: hidden;
      border: 1px solid var(--adr-line);
      border-radius: 8px;
      background: var(--adr-solid);
      box-shadow: var(--adr-shadow);
    }
    .adr-hero-copy { display: grid; align-content: center; gap: 18px; padding: clamp(28px, 5vw, 70px); }
    .adr-kicker { margin: 0; color: var(--adr-gold); font-size: 14px; font-weight: 900; text-transform: uppercase; letter-spacing: .08em; }
    .adr-hero h1 { margin: 0; color: var(--adr-blue); font-size: clamp(22px, 3.02vw, 41px); line-height: 1; letter-spacing: 0; }
    .adr-hero p { max-width: 680px; margin: 0; color: var(--adr-muted); font-size: clamp(18px, 2vw, 23px); line-height: 1.55; }
    .adr-hero-media { min-height: 340px; background: #d9e7f2; }
    .adr-hero-media img { width: 100%; height: 100%; object-fit: cover; }
    .adr-mini-nav { position: sticky; top: 116px; z-index: 8; display: flex; flex-wrap: wrap; justify-content: center; gap: 6px; margin-top: 18px; margin-bottom: 18px; border: 1px solid var(--adr-line); border-radius: 8px; padding: 8px; background: var(--adr-paper); backdrop-filter: blur(18px); }
    .adr-mini-nav a { border-radius: 999px; padding: 9px 12px; color: var(--adr-blue); font-size: 13px; font-weight: 900; text-decoration: none; }
    .adr-form-section { display: grid; grid-template-columns: minmax(270px, .52fr) minmax(0, 1fr); gap: 16px; align-items: start; }
    .adr-side-card, .adr-form-card, .adr-step-card { border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); }
    .adr-side-card { position: sticky; top: 178px; display: grid; gap: 14px; padding: clamp(22px, 3vw, 34px); }
    .adr-side-card h2, .adr-form-card h2, .adr-step-card h2 { margin: 0; color: var(--adr-blue); line-height: 1.12; letter-spacing: 0; }
    .adr-side-card p, .adr-form-card p, .adr-step-card p, .adr-side-card li { margin: 0; color: var(--adr-muted); line-height: 1.58; }
    .adr-side-card ol { display: grid; gap: 12px; margin: 6px 0 0; padding-left: 22px; font-weight: 900; color: var(--adr-muted); }
    .adr-form-card { padding: clamp(18px, 3vw, 28px); }
    .adr-form-intro { display: grid; gap: 8px; margin-bottom: 16px; }
    .adr-form-card .mf-form-wrapper,
    .adr-form-card .metform-form-content,
    .adr-form-card .elementor,
    .adr-form-card .elementor-section,
    .adr-form-card .elementor-container { max-width: none !important; width: 100% !important; }
    .adr-form-card input:not([type="checkbox"]):not([type="radio"]),
    .adr-form-card select,
    .adr-form-card textarea {
      min-height: 46px !important;
      border: 1px solid var(--adr-line) !important;
      border-radius: 8px !important;
      background: color-mix(in srgb, var(--adr-solid) 88%, transparent) !important;
      color: var(--adr-ink) !important;
      box-shadow: none !important;
    }
    .adr-form-card label,
    .adr-form-card .mf-input-label { color: var(--adr-ink) !important; font-weight: 800 !important; }
    .adr-form-card .metform-btn,
    .adr-form-card .metform-submit-btn { min-height: 50px !important; border-radius: 8px !important; background: var(--adr-blue) !important; color: #fff !important; font-weight: 900 !important; }
    .adr-next-steps { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-top: 18px; }
    .adr-step-card { display: grid; gap: 8px; padding: 20px; }
    .adr-step-card img { width: 48px; height: 48px; border-radius: 50%; padding: 8px; background: color-mix(in srgb, var(--adr-blue) 8%, white); }
    @media (max-width: 980px) { .adr-form-section, .adr-hero { grid-template-columns: 1fr; } .adr-side-card, .adr-mini-nav { position: static; } .adr-hero-media { order: -1; min-height: 240px; } }
    @media (max-width: 760px) { .adr-floating-panel, .adr-panel-actions { align-items: stretch; flex-direction: column; } .adr-brand strong { white-space: normal; } .adr-panel-actions, .adr-button, .adr-switch-label { width: 100%; } }
    @media (max-width: 640px) { .adr-page-stage { padding-left: 10px; padding-right: 10px; } .adr-hero h1 { font-size: 19px; } .adr-hero-copy { padding: 22px 18px; } .adr-next-steps { grid-template-columns: 1fr; } }
    body.page-id-3 .ekit-template-content-header,
    body.page-id-3 .site-footer,
    body.page-id-3 footer.footer,
    body.page-id-3 .ekit-template-content-footer,
    body.page-id-3 .page-banner-area,
    body.page-id-3 .breadcrumb { display: none !important; }
    .adr-legal-stage .adr-hero { grid-template-columns: 1fr; min-height: 0; }
    .adr-legal-stage .adr-hero-copy { padding: clamp(24px, 4vw, 48px); }
    .adr-legal-stage .adr-hero h1 { font-size: clamp(20px, 2.35vw, 34px); }
    .adr-legal-stage .adr-info-card { min-height: 0; }
    .adr-legal-stage .adr-legal-layout { width: min(100%, 1180px); margin: 18px auto 0; display: grid; grid-template-columns: minmax(0, .34fr) minmax(0, 1fr); gap: 14px; align-items: start; }
    .adr-legal-stage .adr-legal-index, .adr-legal-stage .adr-legal-copy { border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); }
    .adr-legal-stage .adr-legal-index { position: sticky; top: 176px; display: grid; gap: 10px; padding: clamp(18px, 2.6vw, 28px); }
    .adr-legal-stage .adr-legal-index h2, .adr-legal-stage .adr-legal-copy h2, .adr-legal-stage .adr-legal-copy h3, .adr-legal-stage .adr-legal-copy h4 { margin: 0; color: var(--adr-blue); line-height: 1.15; letter-spacing: 0; }
    .adr-legal-stage .adr-legal-index p, .adr-legal-stage .adr-legal-index li { margin: 0; color: var(--adr-muted); line-height: 1.55; }
    .adr-legal-stage .adr-legal-index ul { display: grid; gap: 8px; margin: 0; padding-left: 18px; }
    .adr-legal-stage .adr-legal-copy { padding: clamp(20px, 3vw, 34px); color: var(--adr-muted); line-height: 1.65; }
    .adr-legal-stage .adr-legal-copy > *:first-child { margin-top: 0 !important; }
    .adr-legal-stage .adr-legal-copy h4 { margin-top: 22px; margin-bottom: 8px; font-size: clamp(18px, 1.8vw, 24px); }
    .adr-legal-stage .adr-legal-copy p { margin: 0 0 14px; color: var(--adr-muted); }
    .adr-legal-stage .adr-legal-copy ul { margin: 0 0 16px; padding-left: 22px; color: var(--adr-muted); }
    .adr-legal-stage .adr-legal-copy li { margin: 0 0 8px; }
    .adr-legal-stage .adr-legal-copy a { color: var(--adr-blue); font-weight: 800; overflow-wrap: anywhere; }
    .adr-legal-stage .adr-legal-copy code { border: 1px solid var(--adr-line); border-radius: 6px; padding: 2px 5px; background: color-mix(in srgb, var(--adr-blue) 8%, transparent); color: var(--adr-blue); }
    .adr-legal-stage .adr-official-note { margin-top: 10px; font-size: 13px; font-weight: 800; color: var(--adr-muted); }
    @media (max-width: 900px) { .adr-legal-stage .adr-legal-layout { grid-template-columns: 1fr; } .adr-legal-stage .adr-legal-index { position: static; } }
  </style>
  <input class="adr-switch-input" id="adr-theme-toggle" type="checkbox" aria-label="Basculer le thème jour/nuit">
  <header class="adr-floating-panel" aria-label="Navigation principale">
    <a class="adr-brand" href="/"><img decoding="async" src="https://assurancesderueil.fr/wp-content/uploads/2022/08/cropped-flaticon2.png" alt=""><strong>Assurances de Rueil</strong></a>
    <div class="adr-panel-actions">
      <a class="adr-button adr-button-primary" href="/demande-de-devis-assurance-a-rueil-malmaison/">Demander un devis</a>
      <label class="adr-switch-label" for="adr-theme-toggle">Jour/Nuit</label>
    </div>
  </header>
  <section class="adr-hero" aria-label="Politique de confidentialité">
    <div class="adr-hero-copy">
      <p class="adr-kicker">Confidentialité</p>
      <h1>Politique de confidentialité</h1>
      <p>La politique RGPD explique les données traitées, les finalités, les durées de conservation et les droits des utilisateurs.</p>
      <p class="adr-official-note">Texte officiel conservé en français.</p>
    </div>
  </section>
  <nav class="adr-mini-nav" aria-label="Pages principales">
    <a href="/">Accueil</a>
    <a href="/cabinet-de-courtage-en-assurances-rueil-malmaison/">Cabinet</a>
    <a href="/assurance-de-pret-a-rueil-malmaison/">Assurance de prêt</a>
    <a href="/assurance-particuliers-rueil-malmaison/">Particuliers</a>
    <a href="/assurance-entreprise-rueil-malmaison/">Professionnels</a>
    <a href="/courtier-en-assurances-de-rueil-malmaison/">Contact</a>
  </nav>
  <section class="adr-info-grid" aria-label="Repères">
    <article class="adr-info-card"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-house-gold.svg" alt=""><h2>Données collectées</h2><p>Coordonnées, informations de dossier et éléments utiles à la mission de courtier.</p></article><article class="adr-info-card"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-house-gold.svg" alt=""><h2>Consentement</h2><p>Transmission aux partenaires uniquement dans le cadre accepté par le client.</p></article><article class="adr-info-card"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-house-gold.svg" alt=""><h2>Droits</h2><p>Accès, modification, suppression et opposition par courrier au siège.</p></article>
  </section>
  <section class="adr-legal-layout" aria-label="Politique de confidentialité">
    <aside class="adr-legal-index">
      <h2>Repères</h2>
      <ul><li>Données collectées</li><li>Consentement</li><li>Droits</li></ul>
      <a class="adr-button adr-button-primary" href="/courtier-en-assurances-de-rueil-malmaison/">Contacter le cabinet</a>
    </aside>
    <article class="adr-legal-copy"><h4>1. Généralités</h4><p>Conformément au Règlement Général de Protection des Données (RGPD), cette page de Politique de Confidentialité a pour objectif de vous informer sur notre politique en matière de sécurité, de protection et de confidentialité des traitements effectués sur les données à caractère personnel que vous nous confiez sur notre site internet : <code>assurancesderueil.fr</code>.</p><p>Les données à caractère personnel confiées par le biais de formulaires ou de cookies ne peuvent être transmises à des tiers sans votre consentement. Plus généralement, toute action entraînant la collecte de données personnelles est soumise à votre consentement préalable.</p><p>Sauf votre accord express et dans le cadre de la transmission des éléments nécessaires à la gestion et au suivi de votre dossier auprès des partenaires concernés, l’ensemble des données fournies sur notre site internet est protégé et leur confidentialité garantie.</p><p>Vous avez également la possibilité de vous inscrire gratuitement sur une liste d’opposition au démarchage téléphonique directement en ligne sur <a href="https://www.service-public.fr" target="_new" rel="noreferrer">service-public.fr</a>.</p><h4>2. Formulaires de contact</h4><p>Les informations demandées au sein des formulaires de notre site internet sont toutes obligatoires afin de répondre au mieux à vos questions et nous permettre de remplir notre mission de courtier à votre attention.</p><h4>3. Données collectées</h4><p>Toutes les données que nous vous demandons de renseigner sont utiles et dans votre intérêt pour nous permettre de vous offrir un service complet. Nous collectons sur notre site internet les données suivantes : nom, prénom, date de naissance, lieu de naissance, ville, code postal, email, téléphone, situation professionnelle, etc.</p><p>Vous devez renseigner une adresse e-mail valide vous appartenant, pas celle d’un tiers. Certaines données ne sont pas obligatoires et vous pouvez choisir de ne pas les renseigner. Néanmoins, pour bénéficier d’un traitement optimisé de votre demande et d’un service complet, nous vous invitons à remplir un maximum d’informations.</p><p>Pendant votre navigation sur notre site internet, et afin de vous garantir la meilleure expérience utilisateur possible, quelques informations peuvent être collectées par le biais de cookies.</p><p>Voici la liste des cookies que nous utilisons sur notre site internet :</p><ul><li>Google Analytics</li></ul><p>Certains cookies sont nécessaires à l’utilisation de notre site internet et d’autres peuvent être désactivés. Voici comment gérer les cookies selon votre navigateur :</p><ul><li><strong>Google Chrome</strong> (Desktop &amp; Mobile) : <a href="https://support.google.com/chrome/answer/95647?hl=fr" target="_new" rel="noreferrer">Paramètres de cookies pour Chrome</a></li><li><strong>Mozilla Firefox</strong> : <a href="https://support.mozilla.org/fr/kb/activer-desactiver-cookies-preferences" target="_new" rel="noreferrer">Paramètres de cookies pour Firefox</a></li><li><strong>Microsoft Internet Explorer</strong> : <a href="https://support.microsoft.com/en-us/help/17442/windows-internet-explorer-delete-manage-cookies" target="_new" rel="noreferrer">Paramètres de cookies pour Internet Explorer</a></li><li><strong>Microsoft Edge</strong> : <a href="https://privacy.microsoft.com/en-us/windows-10-microsoft-edge-and-privacy" target="_new" rel="noreferrer">Paramètres de cookies pour Edge</a></li><li><strong>Apple Safari</strong> : <a href="https://support.apple.com/fr-fr/guide/safari/sfri11471/15.1/mac" target="_new" rel="noreferrer">Paramètres de cookies pour Safari</a> | <a href="https://support.apple.com/en-us/HT201265" target="_new" rel="noreferrer">Mobile</a></li><li><strong>Opera</strong> : <a href="http://help.opera.com/Windows/10.20/fr/cookies.html" target="_new" rel="noreferrer">Paramètres de cookies pour Opera</a></li></ul><h4><br />4. Données non collectées</h4><p>Nous ne collectons pas de données personnelles sensibles, vos données de connexion (adresse IP, logs, etc.), vos données de localisation.</p><h4>5. Consentement</h4><p>Les données à caractère personnel collectées vous appartiennent. Aussi, aucune transmission de ces données ne sera faite à des tiers sans votre consentement exprès.</p><h4>6. Conservation</h4><p>Dans le cadre de nos obligations réglementaires, nous conservons vos données à minima 10 ans. Dans le cadre de votre consentement pour bénéficier de nos offres et/ou des offres de nos partenaires, nous ne conservons vos données qu’au maximum pendant trois (3) ans.</p><h4>7. Droit d’accès</h4><p>Vous pouvez accéder, modifier, ajouter et supprimer les données renseignées vous concernant. Vous pouvez vous opposer au traitement de vos données et à l’utilisation de celles-ci à des fins commerciales. Pour exercer vos droits, vous devez envoyer un courrier postal à l’adresse du siège : <strong>ASSURANCES DE RUEIL &#8211; 75 avenue Victor Hugo &#8211; 92500 RUEIL MALMAISON</strong>.</p><h4>8. Sécurité</h4><p>Nous mettons tout en œuvre pour protéger les données que vous nous confiez. Nos sites de stockage (numérique ou physique) de vos données sont protégés contre des actions malveillantes (intrusions, virus informatique, vol de mots de passe, etc.) mais également contre la survenance d’accidents (incendie, inondation, etc.). En cas de défaillance, vous serez avertis dans les 72 heures à partir de la connaissance de la survenance de l’événement. Les sauvegardes des données sont régulières et sur des sites de stockage différents. Les traitements statistiques sont anonymisés.</p></article>
  </section>
</div>
ADR_STAGE_PRIVACY
        ,
        'cookies' => <<<'ADR_STAGE_COOKIES'
<div class="adr-page-stage adr-legal-stage adr-cookies-stage">
  <style>    body.page-id-7672 .ekit-template-content-header,
    body.page-id-7672 .site-footer,
    body.page-id-7672 footer.footer,
    body.page-id-7672 .ekit-template-content-footer,
    body.page-id-7672 .page-banner-area,
    body.page-id-7672 .breadcrumb { display: none !important; }
    .adr-page-stage, .adr-page-stage * { box-sizing: border-box; }
    .adr-page-stage {
      --adr-blue: #003478;
      --adr-blue-soft: #6da9ff;
      --adr-ink: #07192f;
      --adr-muted: #657386;
      --adr-line: rgba(0, 52, 120, 0.18);
      --adr-paper: rgba(255, 255, 255, 0.88);
      --adr-solid: #ffffff;
      --adr-wash: #eef5fb;
      --adr-gold: #d7a53c;
      --adr-shadow: 0 24px 70px rgba(0, 28, 64, 0.16);
      --adr-soft-shadow: 0 14px 36px rgba(0, 28, 64, 0.1);
      width: 100vw;
      margin-left: calc(50% - 50vw);
      margin-right: calc(50% - 50vw);
      margin-top: -1px;
      padding: clamp(14px, 2vw, 24px) clamp(14px, 2vw, 24px) 64px;
      background: radial-gradient(circle at 10% 0%, rgba(109,169,255,.2), transparent 30%), var(--adr-wash);
      color: var(--adr-ink);
      font-family: Rubik, "Open Sans", Arial, sans-serif;
      overflow: clip;
    }
    .adr-page-stage:has(#adr-theme-toggle:checked) {
      --adr-blue: #83b7ff;
      --adr-blue-soft: #74a9f7;
      --adr-ink: #edf6ff;
      --adr-muted: #a8b8c8;
      --adr-line: rgba(169, 201, 234, 0.24);
      --adr-paper: rgba(12, 29, 49, 0.84);
      --adr-solid: #0e2135;
      --adr-wash: #06111d;
      --adr-shadow: 0 24px 70px rgba(0, 0, 0, 0.34);
      --adr-soft-shadow: 0 14px 36px rgba(0, 0, 0, 0.24);
    }
    .adr-page-stage a { color: inherit; }
    .adr-page-stage img { display: block; max-width: 100%; }
    .adr-page-stage [id] { scroll-margin-top: 140px; }
    .adr-switch-input { position: absolute; opacity: 0; pointer-events: none; }
    .adr-floating-panel {
      position: sticky;
      top: clamp(12px, 2vw, 24px);
      z-index: 9;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      width: min(100%, 1288px);
      margin: 0 auto 18px;
      border: 1px solid var(--adr-line);
      border-radius: 8px;
      padding: 7px 8px 7px 12px;
      background: var(--adr-paper);
      box-shadow: 0 18px 54px rgba(0, 0, 0, 0.18);
      backdrop-filter: blur(18px);
    }
    body.admin-bar .adr-floating-panel { top: calc(32px + clamp(12px, 2vw, 24px)); }
    .adr-brand { display: inline-flex; align-items: center; gap: 13px; min-width: 0; color: var(--adr-blue); text-decoration: none; }
    .adr-brand img { width: clamp(48px, 5vw, 78px); height: clamp(48px, 5vw, 78px); border-radius: 50%; }
    .adr-brand strong { font-size: clamp(24px, 3vw, 42px); line-height: 1; letter-spacing: 0; white-space: nowrap; }
    .adr-panel-actions { display: flex; align-items: stretch; justify-content: flex-end; gap: 8px; }
    .adr-button, .adr-switch-label {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 44px;
      height: clamp(44px, 4.6vw, 64px);
      border-radius: 8px;
      padding: 12px 18px;
      border: 1px solid var(--adr-line);
      font-weight: 900;
      line-height: 1.05;
      text-decoration: none;
      cursor: pointer;
      white-space: nowrap;
      margin: 0 !important;
    }
    .adr-button-primary, .adr-button-primary:visited, .adr-button-primary:hover, .adr-button-primary:focus {
      border-color: transparent;
      background: var(--adr-blue);
      color: #fff !important;
      box-shadow: 0 16px 34px rgba(0, 52, 120, 0.22);
    }
    .adr-page-stage:has(#adr-theme-toggle:checked) .adr-button-primary { color: #06111d !important; }
    .adr-switch-label { min-width: 58px; background: color-mix(in srgb, var(--adr-blue) 7%, var(--adr-solid)); color: var(--adr-blue); font-size: 13px; }
    .adr-page-stage:has(#adr-theme-toggle:checked) label[for="adr-theme-toggle"] { background: var(--adr-blue); color: #06111d; }
    .adr-hero, .adr-form-section, .adr-next-steps, .adr-mini-nav { width: min(100%, 1180px); margin-left: auto; margin-right: auto; }
    .adr-hero {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(320px, .75fr);
      min-height: 430px;
      overflow: hidden;
      border: 1px solid var(--adr-line);
      border-radius: 8px;
      background: var(--adr-solid);
      box-shadow: var(--adr-shadow);
    }
    .adr-hero-copy { display: grid; align-content: center; gap: 18px; padding: clamp(28px, 5vw, 70px); }
    .adr-kicker { margin: 0; color: var(--adr-gold); font-size: 14px; font-weight: 900; text-transform: uppercase; letter-spacing: .08em; }
    .adr-hero h1 { margin: 0; color: var(--adr-blue); font-size: clamp(22px, 3.02vw, 41px); line-height: 1; letter-spacing: 0; }
    .adr-hero p { max-width: 680px; margin: 0; color: var(--adr-muted); font-size: clamp(18px, 2vw, 23px); line-height: 1.55; }
    .adr-hero-media { min-height: 340px; background: #d9e7f2; }
    .adr-hero-media img { width: 100%; height: 100%; object-fit: cover; }
    .adr-mini-nav { position: sticky; top: 116px; z-index: 8; display: flex; flex-wrap: wrap; justify-content: center; gap: 6px; margin-top: 18px; margin-bottom: 18px; border: 1px solid var(--adr-line); border-radius: 8px; padding: 8px; background: var(--adr-paper); backdrop-filter: blur(18px); }
    .adr-mini-nav a { border-radius: 999px; padding: 9px 12px; color: var(--adr-blue); font-size: 13px; font-weight: 900; text-decoration: none; }
    .adr-form-section { display: grid; grid-template-columns: minmax(270px, .52fr) minmax(0, 1fr); gap: 16px; align-items: start; }
    .adr-side-card, .adr-form-card, .adr-step-card { border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); }
    .adr-side-card { position: sticky; top: 178px; display: grid; gap: 14px; padding: clamp(22px, 3vw, 34px); }
    .adr-side-card h2, .adr-form-card h2, .adr-step-card h2 { margin: 0; color: var(--adr-blue); line-height: 1.12; letter-spacing: 0; }
    .adr-side-card p, .adr-form-card p, .adr-step-card p, .adr-side-card li { margin: 0; color: var(--adr-muted); line-height: 1.58; }
    .adr-side-card ol { display: grid; gap: 12px; margin: 6px 0 0; padding-left: 22px; font-weight: 900; color: var(--adr-muted); }
    .adr-form-card { padding: clamp(18px, 3vw, 28px); }
    .adr-form-intro { display: grid; gap: 8px; margin-bottom: 16px; }
    .adr-form-card .mf-form-wrapper,
    .adr-form-card .metform-form-content,
    .adr-form-card .elementor,
    .adr-form-card .elementor-section,
    .adr-form-card .elementor-container { max-width: none !important; width: 100% !important; }
    .adr-form-card input:not([type="checkbox"]):not([type="radio"]),
    .adr-form-card select,
    .adr-form-card textarea {
      min-height: 46px !important;
      border: 1px solid var(--adr-line) !important;
      border-radius: 8px !important;
      background: color-mix(in srgb, var(--adr-solid) 88%, transparent) !important;
      color: var(--adr-ink) !important;
      box-shadow: none !important;
    }
    .adr-form-card label,
    .adr-form-card .mf-input-label { color: var(--adr-ink) !important; font-weight: 800 !important; }
    .adr-form-card .metform-btn,
    .adr-form-card .metform-submit-btn { min-height: 50px !important; border-radius: 8px !important; background: var(--adr-blue) !important; color: #fff !important; font-weight: 900 !important; }
    .adr-next-steps { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-top: 18px; }
    .adr-step-card { display: grid; gap: 8px; padding: 20px; }
    .adr-step-card img { width: 48px; height: 48px; border-radius: 50%; padding: 8px; background: color-mix(in srgb, var(--adr-blue) 8%, white); }
    @media (max-width: 980px) { .adr-form-section, .adr-hero { grid-template-columns: 1fr; } .adr-side-card, .adr-mini-nav { position: static; } .adr-hero-media { order: -1; min-height: 240px; } }
    @media (max-width: 760px) { .adr-floating-panel, .adr-panel-actions { align-items: stretch; flex-direction: column; } .adr-brand strong { white-space: normal; } .adr-panel-actions, .adr-button, .adr-switch-label { width: 100%; } }
    @media (max-width: 640px) { .adr-page-stage { padding-left: 10px; padding-right: 10px; } .adr-hero h1 { font-size: 19px; } .adr-hero-copy { padding: 22px 18px; } .adr-next-steps { grid-template-columns: 1fr; } }
    body.page-id-7672 .ekit-template-content-header,
    body.page-id-7672 .site-footer,
    body.page-id-7672 footer.footer,
    body.page-id-7672 .ekit-template-content-footer,
    body.page-id-7672 .page-banner-area,
    body.page-id-7672 .breadcrumb { display: none !important; }
    .adr-legal-stage .adr-hero { grid-template-columns: 1fr; min-height: 0; }
    .adr-legal-stage .adr-hero-copy { padding: clamp(24px, 4vw, 48px); }
    .adr-legal-stage .adr-hero h1 { font-size: clamp(20px, 2.35vw, 34px); }
    .adr-legal-stage .adr-info-card { min-height: 0; }
    .adr-legal-stage .adr-legal-layout { width: min(100%, 1180px); margin: 18px auto 0; display: grid; grid-template-columns: minmax(0, .34fr) minmax(0, 1fr); gap: 14px; align-items: start; }
    .adr-legal-stage .adr-legal-index, .adr-legal-stage .adr-legal-copy { border: 1px solid var(--adr-line); border-radius: 8px; background: var(--adr-paper); box-shadow: var(--adr-soft-shadow); backdrop-filter: blur(18px); }
    .adr-legal-stage .adr-legal-index { position: sticky; top: 176px; display: grid; gap: 10px; padding: clamp(18px, 2.6vw, 28px); }
    .adr-legal-stage .adr-legal-index h2, .adr-legal-stage .adr-legal-copy h2, .adr-legal-stage .adr-legal-copy h3, .adr-legal-stage .adr-legal-copy h4 { margin: 0; color: var(--adr-blue); line-height: 1.15; letter-spacing: 0; }
    .adr-legal-stage .adr-legal-index p, .adr-legal-stage .adr-legal-index li { margin: 0; color: var(--adr-muted); line-height: 1.55; }
    .adr-legal-stage .adr-legal-index ul { display: grid; gap: 8px; margin: 0; padding-left: 18px; }
    .adr-legal-stage .adr-legal-copy { padding: clamp(20px, 3vw, 34px); color: var(--adr-muted); line-height: 1.65; }
    .adr-legal-stage .adr-legal-copy > *:first-child { margin-top: 0 !important; }
    .adr-legal-stage .adr-legal-copy h4 { margin-top: 22px; margin-bottom: 8px; font-size: clamp(18px, 1.8vw, 24px); }
    .adr-legal-stage .adr-legal-copy p { margin: 0 0 14px; color: var(--adr-muted); }
    .adr-legal-stage .adr-legal-copy ul { margin: 0 0 16px; padding-left: 22px; color: var(--adr-muted); }
    .adr-legal-stage .adr-legal-copy li { margin: 0 0 8px; }
    .adr-legal-stage .adr-legal-copy a { color: var(--adr-blue); font-weight: 800; overflow-wrap: anywhere; }
    .adr-legal-stage .adr-legal-copy code { border: 1px solid var(--adr-line); border-radius: 6px; padding: 2px 5px; background: color-mix(in srgb, var(--adr-blue) 8%, transparent); color: var(--adr-blue); }
    .adr-legal-stage .adr-official-note { margin-top: 10px; font-size: 13px; font-weight: 800; color: var(--adr-muted); }
    @media (max-width: 900px) { .adr-legal-stage .adr-legal-layout { grid-template-columns: 1fr; } .adr-legal-stage .adr-legal-index { position: static; } }
  </style>
  <input class="adr-switch-input" id="adr-theme-toggle" type="checkbox" aria-label="Basculer le thème jour/nuit">
  <header class="adr-floating-panel" aria-label="Navigation principale">
    <a class="adr-brand" href="/"><img decoding="async" src="https://assurancesderueil.fr/wp-content/uploads/2022/08/cropped-flaticon2.png" alt=""><strong>Assurances de Rueil</strong></a>
    <div class="adr-panel-actions">
      <a class="adr-button adr-button-primary" href="/demande-de-devis-assurance-a-rueil-malmaison/">Demander un devis</a>
      <label class="adr-switch-label" for="adr-theme-toggle">Jour/Nuit</label>
    </div>
  </header>
  <section class="adr-hero" aria-label="Cookies &#038; traceurs">
    <div class="adr-hero-copy">
      <p class="adr-kicker">Cookies</p>
      <h1>Cookies &#038; traceurs</h1>
      <p>La politique détaille les cookies techniques, de préférence, de mesure d’audience et de services tiers.</p>
      <p class="adr-official-note">Texte officiel conservé en français.</p>
    </div>
  </section>
  <nav class="adr-mini-nav" aria-label="Pages principales">
    <a href="/">Accueil</a>
    <a href="/cabinet-de-courtage-en-assurances-rueil-malmaison/">Cabinet</a>
    <a href="/assurance-de-pret-a-rueil-malmaison/">Assurance de prêt</a>
    <a href="/assurance-particuliers-rueil-malmaison/">Particuliers</a>
    <a href="/assurance-entreprise-rueil-malmaison/">Professionnels</a>
    <a href="/courtier-en-assurances-de-rueil-malmaison/">Contact</a>
  </nav>
  <section class="adr-info-grid" aria-label="Repères">
    <article class="adr-info-card"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-house-gold.svg" alt=""><h2>Fonctionnement</h2><p>Cookies nécessaires à la navigation, à la session et à la sécurité.</p></article><article class="adr-info-card"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-house-gold.svg" alt=""><h2>Préférences</h2><p>Adaptation de l’affichage et mémorisation de certains choix.</p></article><article class="adr-info-card"><img decoding="async" src="https://ec92009.github.io/ADR/mock-assets/adr-icon-house-gold.svg" alt=""><h2>Tiers</h2><p>Mesure d’audience, publicité ciblée et boutons de réseaux sociaux.</p></article>
  </section>
  <section class="adr-legal-layout" aria-label="Cookies &#038; traceurs">
    <aside class="adr-legal-index">
      <h2>Repères</h2>
      <ul><li>Fonctionnement</li><li>Préférences</li><li>Tiers</li></ul>
      <a class="adr-button adr-button-primary" href="/courtier-en-assurances-de-rueil-malmaison/">Contacter le cabinet</a>
    </aside>
    <article class="adr-legal-copy"><h4>Politique de Cookies</h4><p>Vous êtes informé que, lors de vos visites sur notre site, un ou plusieurs Cookies peuvent être installés sur votre terminal.</p><h4>Qu’est-ce qu’un Cookie ?</h4><p>Un Cookie désigne l’ensemble des formes d’accès et d’inscription d’informations sur votre terminal, notamment les informations envoyées par notre site et stockées par votre navigateur sur un espace dédié du disque dur dudit terminal.</p><h4>Pourquoi et comment les Cookies sont-ils utilisés ?</h4><p>Les Cookies sont utilisés pour :</p><ul><li><strong>Assurer le fonctionnement et optimiser la performance de notre site</strong> : Par exemple, pour maintenir votre session active.</li><li><strong>Adapter notre site à vos préférences</strong> : Par exemple, en tenant compte de la langue utilisée ou de la résolution d’affichage.</li><li><strong>Faciliter votre navigation</strong> : En vous évitant d’avoir à ressaisir des informations à chaque visite.</li><li><strong>Mesurer l’audience de notre site et des communications qui vous sont adressées</strong> : Par exemple, en analysant les pages visitées et le temps passé sur le site.</li><li><strong>Proposer des publicités ciblées adaptées à vos centres d’intérêts</strong>.</li><li><strong>Développer l’interactivité de notre site</strong> : Par exemple, en vous permettant de partager des contenus via les réseaux sociaux.</li></ul><p>Vous êtes également informé que nous permettons à des tiers de déposer des Cookies via notre site. Ces Cookies sont exploités uniquement par ces tiers.</p><h4>Quels sont les types de Cookies utilisés ?</h4><ul><li><p><strong>Les Cookies Techniques</strong> : Ils facilitent la navigation, permettent et améliorent le fonctionnement de notre site ainsi que l’accès aux différentes fonctionnalités. Ils mettent également en œuvre des mesures de sécurité. Si vous choisissez de désactiver ces Cookies, l’accès aux services pourrait être altéré.</p></li><li><p><strong>Les Cookies de Mesure d’Audience</strong> : Ils mesurent la fréquentation de notre site et des communications qui vous sont adressées. Ces Cookies peuvent être déposés par des tiers pour notre compte.</p></li><li><p><strong>Les Cookies Tiers de publicité ciblée</strong> : Ils permettent de proposer des publicités adaptées à vos centres d’intérêts. Ils peuvent être déposés par nous ou par des tiers, soit pour notre compte, soit pour leur propre compte.</p></li><li><p><strong>Les Cookies Tiers liés aux boutons de partage des réseaux sociaux</strong> : Ils permettent de partager le contenu de notre site avec des tiers. Par exemple, des boutons « partager » ou « j’aime » provenant de réseaux sociaux tels que Facebook ou Twitter. Nous ne contrôlons pas ces Cookies Tiers et les Données collectées par les sociétés gestionnaires de ces réseaux sociaux.</p></li></ul><h4>Quelle est la durée de conservation des Cookies ?</h4><p>Conformément aux principes relatifs à la protection des Données, les Cookies sont conservés pendant la durée strictement nécessaire aux finalités pour lesquelles ils sont utilisés, et ce, dans les limites définies par l’autorité de protection compétente.</p><h4>Comment pouvez-vous gérer le dépôt et la lecture des Cookies ?</h4><p>Vous pouvez modifier les paramètres de votre navigateur pour désactiver tout ou une partie des cookies :</p><ul><li><p><strong>Pour Internet Explorer™</strong> : ouvrez le menu « Outils », puis sélectionnez « Options internet » ; cliquez sur l’onglet « Confidentialité » puis l’onglet « Avancé » choisissez le niveau souhaité ou suivez <a href="http://windows.microsoft.com/fr-FR/windows-vista/Block-or-allow-cookies" target="_new" rel="noreferrer">ce lien</a>.</p></li><li><p><strong>Pour Firefox™</strong> : ouvrez le menu « Outils », puis sélectionnez « Options » ; cliquez sur l’onglet « Vie privée » puis choisissez les options souhaitées ou suivez <a href="http://support.mozilla.org/fr/kb/Activer%20et%20d%C3%A9sactiver%20les%20cookies" target="_new" rel="noreferrer">ce lien</a>.</p></li><li><p><strong>Pour Chrome™</strong> : ouvrez le menu de configuration (logo clé à molette), puis sélectionnez « Options » ; cliquez sur « Options avancées » puis dans la section « Confidentialité », cliquez sur « Paramètres de contenu », et choisissez les options souhaitées ou suivez <a href="http://support.google.com/chrome/bin/answer.py?hl=fr&amp;hlrm=en&amp;answer=95647" target="_new" rel="noreferrer">ce lien</a>.</p></li><li><p><strong>Pour Opera™</strong> : ouvrez le menu « Outils » ou « Réglages », puis sélectionnez « Supprimer les données privées » ; cliquez sur l’onglet « Options détaillées », puis choisissez les options souhaitées ou suivez <a href="http://help.opera.com/Windows/10.20/fr/cookies.html" target="_new" rel="noreferrer">ce lien</a>.</p></li></ul><p>Les principales sociétés gestionnaires des réseaux sociaux disposent également de pages dédiées aux Cookies comme présenté ci-dessous :</p><ul><li><strong>Pour Facebook</strong> : <a href="https://www.facebook.com/policies/cookies" target="_new" rel="noreferrer">https://www.facebook.com/policies/cookies</a></li><li><strong>Pour Twitter</strong> : <a href="https://support.twitter.com/articles/20171379-twitter-prend-en-charge-la-desactivation-du-suivi-dnt" target="_new" rel="noreferrer">https://support.twitter.com/articles/20171379-twitter-prend-en-charge-la-desactivation-du-suivi-dnt</a></li><li><strong>Pour Google</strong> : <a href="https://support.google.com/accounts/answer/61416?hl=fr" target="_new" rel="noreferrer">https://support.google.com/accounts/answer/61416?hl=fr</a></li><li><strong>Pour LinkedIn</strong> : <a href="http://www.linkedin.com/legal/cookie-policy" target="_new" rel="noreferrer">http://www.linkedin.com/legal/cookie-policy</a></li></ul><p>Pour désactiver les cookies sur les réseaux sociaux, vous devez suivre les démarches spécifiques au réseau social concerné.</p></article>
  </section>
</div>
ADR_STAGE_COOKIES
        ,
    );
}

function adr_apply_source_truth_visual_residuals_v119_3( $html ) {
    $styles = '';

    if ( is_page( 'courtier-en-assurances-de-rueil-malmaison' ) ) {
        $styles .= '
            .adr-contact-stage .elementor-widget-mf-gdpr-consent,
            .adr-contact-stage .elementor-widget-mf-gdpr-consent * {
                font-family: Arial, sans-serif !important;
            }
        ';
    }

    if ( is_page( 'mentions-legales' ) ) {
        $styles .= '
            .adr-mentions-stage .adr-legal-copy span[style*="font-family"],
            .cmplz-cookiebanner,
            .cmplz-cookiebanner * {
                font-family: Arial, sans-serif !important;
            }
        ';
    }

    if ( is_page( 8093 ) || is_page( 'politique-de-cookies-ue' ) ) {
        $html = str_replace(
            'src="undefined"',
            'src="https://ec92009.github.io/ADR/mock-assets/adr-icon-house-gold.svg"',
            $html
        );

        $styles .= '
            .adr-cookie-eu-stage .adr-hero h1 {
                font-size: clamp(20px, 2.35vw, 34px) !important;
            }
        ';
    }

    if ( trim( $styles ) === '' || strpos( $html, 'adr-source-truth-residuals-v119-3' ) !== false ) {
        return $html;
    }

    return str_replace(
        '</head>',
        '<style id="adr-source-truth-residuals-v119-3">' . $styles . '</style>' . "\n</head>",
        $html
    );
}

function adr_apply_source_truth_dynamic_residuals_v119_3( $html ) {
    if ( strpos( $html, 'class="mf-form-wrapper"' ) === false ) {
        return $html;
    }

    $html = preg_replace(
        '/data-wp-nonce="[^"]*"/',
        'data-wp-nonce="' . esc_attr( wp_create_nonce( 'wp_rest' ) ) . '"',
        $html
    );

    $html = preg_replace(
        '/data-form-nonce="[^"]*"/',
        'data-form-nonce="' . esc_attr( wp_create_nonce( 'form_nonce' ) ) . '"',
        $html
    );

    return $html;
}

function adr_apply_source_truth_detail_sections_v119_3( $html ) {
    $sections = array(
        array(
            'labels' => array( 'Détails assurance de prêt' ),
            'html'   => <<<'HTML'
<section class="adr-detail-section" aria-label="Détails assurance de prêt">
    <div class="adr-detail-header">
      <p class="adr-detail-note">Détails repris du site original</p>
      <h2>Comprendre l'assurance de prêt</h2>
      <p>L'assurance emprunteur n'est pas imposée par la loi, mais elle est généralement exigée par les organismes prêteurs, surtout pour les crédits immobiliers. Le courtier aide à comparer les contrats et à préserver l'équivalence des garanties.</p>
    </div>
    <div class="adr-detail-grid">
      <article class="adr-detail-card" id="fonctionnement">
        <h2>Fonctionnement</h2>
        <p>Le contrat couvre l'emprunteur face à différents risques : perte totale et irréversible d'autonomie, incapacité temporaire totale de travail, invalidité permanente totale ou partielle, décès, voire perte d'emploi.</p>
        <p>Dans ces situations, l'assurance se substitue à l'emprunteur pour le paiement des mensualités, même si l'emprunteur ne subit aucune perte de revenus.</p>
      </article>
      <article class="adr-detail-card" id="souscription">
        <h2>Souscription</h2>
        <p>Lors de la souscription, plusieurs informations peuvent être demandées :</p>
        <ul>
          <li>profil : âge, fumeur ou non-fumeur, profession</li>
          <li>crédit : type de prêt, taux, durée et capital emprunté</li>
          <li>couverture exigée ou souhaitée : options, garanties, quotités</li>
          <li>habitudes de vie et état de santé, sauf en cas d'application de la Loi Lemoine.</li>
        </ul>
        <p>Les informations renseignées doivent être véridiques afin d'éviter un retard de prise d'effet ou une absence de prise en charge lors d'un sinistre. Tout savoir sur la Loi Lemoine permet aussi de vérifier si le questionnaire médical s'applique.</p>
      </article>
      <article class="adr-detail-card" id="comparer-les-offres">
        <h2>Comparer les offres</h2>
        <p>La fiche standardisée d'information précise les garanties exigées par la banque ou l'organisme prêteur. Elle permet de comparer les offres du marché sur une base claire.</p>
        <ul>
          <li>mensualités fixes ou décroissantes</li>
          <li>prestation indemnitaire ou forfaitaire</li>
          <li>durée de validité des garanties</li>
          <li>quotité couverte pour chaque emprunteur</li>
          <li>maintien des primes et garanties en cas de changement de situation.</li>
        </ul>
      </article>
      <article class="adr-detail-card" id="mensualites-prestations">
        <h2>Mensualités et prestations</h2>
        <p>Certains contrats prévoient des mensualités fixes, d'autres des mensualités décroissantes au fil du temps.</p>
        <p>Un contrat forfaitaire couvre le montant des mensualités multiplié par la quotité assurée. Un contrat indemnitaire limite la prise en charge à la perte de revenus. Les prestations forfaitaires assurent la même couverture, même en l'absence de perte de revenus.</p>
      </article>
      <article class="adr-detail-card" id="duree-garanties">
        <h2>Durée des garanties</h2>
        <p>Les garanties peuvent être limitées dans le temps, s'arrêter à un âge donné, évoluer lors d'un changement de situation ou prendre fin au moment du départ en retraite.</p>
        <p>La garantie décès proposée par le cabinet couvre l'emprunteur jusqu'à son 90e anniversaire, bien au-delà de nombreux contrats groupe proposés par les banques.</p>
      </article>
      <article class="adr-detail-card" id="quotite">
        <h2>Quotité</h2>
        <p>Lorsqu'un crédit est souscrit à deux, chaque emprunteur peut être assuré sur une partie du prêt. Une répartition à 50 % chacun laisse, en cas de décès d'un assuré, la moitié du capital restant due au survivant.</p>
        <p>La couverture peut aussi garantir jusqu'à 100 % chacun des emprunteurs selon le besoin de protection recherché.</p>
      </article>
      <article class="adr-detail-card" id="particularites-contrat">
        <h2>Particularités du contrat</h2>
        <p>Le dossier médical, les sports pratiqués, le métier ou les évolutions de vie peuvent faire varier le montant de la prime.</p>
        <p>Il faut donc vérifier la stabilité des prestations et des mensualités. La clause d'irrévocabilité permet de maintenir primes et garanties même si le style de vie change.</p>
      </article>
      <article class="adr-detail-card" id="exclusions-accompagnement">
        <h2>Exclusions et accompagnement</h2>
        <p>Les exclusions prévues par le contrat doivent être lues attentivement : elles définissent les situations dans lesquelles la prise en charge du remboursement ne serait pas assurée.</p>
        <p>Le rôle du courtier est de proposer la meilleure solution d'assurance aux emprunteurs, en fonction de leur profil de risque et des garanties demandées par la banque.</p>
      </article>
    </div>
  </section>
HTML
        ),
        array(
            'labels' => array( 'Détails assurance particuliers' ),
            'html'   => <<<'HTML'
<section class="adr-detail-section" aria-label="Détails assurance particuliers">
    <div class="adr-detail-header">
      <p class="adr-detail-note">Détails repris du site original</p>
      <h2>Solutions pour les particuliers</h2>
      <p>Les formules couvrent le patrimoine, la santé, la mobilité, les prêts et les aléas de la vie. Les garanties sont ajustées à la situation familiale, au bien assuré et au niveau de protection recherché.</p>
    </div>
    <div class="adr-detail-grid">
      <article class="adr-detail-card" id="habitation">
        <h2>Habitation</h2>
        <p>Des formules attractives sont proposées pour les propriétaires occupants, non occupants et résidences secondaires, avec de solides garanties protégeant le patrimoine en cas d'incendie, dégâts des eaux, vol, tempête et catastrophes naturelles.</p>
        <p>Le contrat est adapté aux particularités du bien : véranda, piscine, tennis, jardin et équipements spécifiques.</p>
        <ul>
          <li>indemnisation en valeur à neuf des biens</li>
          <li>garanties des équipements d'énergie renouvelable</li>
          <li>responsabilité civile étendue au monde entier</li>
          <li>assistance aux membres de la famille en France et à l'étranger.</li>
        </ul>
      </article>
      <article class="adr-detail-card" id="loyers-impayes">
        <h2>Loyers impayés</h2>
        <p>La location d'un bien peut générer de sérieux problèmes en cas d'interruption de versement des loyers. La garantie des loyers impayés permet de bénéficier d'une couverture complète.</p>
        <ul>
          <li>garantie des loyers impayés jusqu'à 30 mois</li>
          <li>garantie des frais de contentieux, huissier et avocat</li>
          <li>garantie des détériorations immobilières</li>
          <li>assistance dans la composition du dossier jusqu'à la souscription.</li>
        </ul>
        <p>La prime étant déductible des revenus fonciers, un devis personnalisé peut aider à mesurer le coût réel de cette protection.</p>
      </article>
      <article class="adr-detail-card" id="assurance-de-pret">
        <h2>Assurance de prêt</h2>
        <p>Quel que soit le type de prêt ou d'investissement, l'assurance emprunteur peut rembourser le capital restant dû en cas de décès, perte d'autonomie ou invalidité, et prendre en charge les mensualités en cas d'incapacité de travail.</p>
        <p>Choisir son assurance emprunteur en dehors de la banque peut permettre de réaliser des économies selon le profil : emprunteurs de moins de 30 ans, non-fumeurs de plus de 54 ans, fumeurs ou emprunteurs atteints de maladies graves acceptées.</p>
        <p>La loi Hamon autorise, depuis le 26 Juillet 2014, la résiliation de l'assurance de prêt groupe dans les 12 mois suivant la signature de l'offre, sous réserve de garanties au moins équivalentes.</p>
      </article>
      <article class="adr-detail-card" id="sante">
        <h2>Santé</h2>
        <p>Les dépassements d'honoraires, le forfait hospitalier et l'écart entre le remboursement de la Sécurité Sociale et la dépense réelle rendent l'assurance complémentaire santé primordiale.</p>
        <p>Les formules complètent les prestations des caisses professionnelles selon l'âge et la situation familiale : moins de 25 ans, seniors, célibataire, marié avec ou sans enfant.</p>
        <ul>
          <li>formules allant de 100 % à 400 %</li>
          <li>pas de délais d'attente</li>
          <li>option optique et dentaire pouvant être renforcée</li>
          <li>médecine douce.</li>
        </ul>
      </article>
      <article class="adr-detail-card" id="accidents-vie">
        <h2>Garantie des accidents de la vie</h2>
        <p>La GAV couvre les conséquences des accidents de la vie courante : blessure à la main empêchant de travailler, mauvaise chute d'un enfant, pratique d'une activité, erreur médicale ou invalidité perturbant toute la vie de la famille.</p>
        <p>Le cabinet propose une couverture personnalisée et efficace pour parer aux besoins du foyer.</p>
      </article>
      <article class="adr-detail-card" id="automobile-mobilite">
        <h2>Automobile et mobilité</h2>
        <p>En deux roues comme en quatre roues, les contrats couvrent le véhicule et ses occupants. Le cabinet accompagne la mise en place des solutions, les actes de gestion et la survenance d'un sinistre, sans passage par une plateforme téléphonique.</p>
        <ul>
          <li>garages agréés avec prise de rendez-vous selon votre convenance et prêt d'un véhicule lors des réparations</li>
          <li>service à domicile : prise en charge du véhicule chez vous et mise à disposition d'un véhicule de prêt le temps de l'expertise et de la réparation</li>
          <li>assistance dépannage sans franchise kilométrique en cas de panne, accident ou crevaison</li>
          <li>passagers couverts suite à un accident corporel</li>
          <li>sécurité du conducteur avec capital en cas de décès ou de blessure.</li>
        </ul>
      </article>
      <article class="adr-detail-card" id="prevoyance">
        <h2>Prévoyance</h2>
        <p>La vie peut réserver des coups durs. L'assurance prévoyance apporte une protection financière, pour vous ou pour vos proches.</p>
        <ul>
          <li>décès et obsèques</li>
          <li>hospitalisation</li>
          <li>accident de la vie et du sport</li>
          <li>dépendance.</li>
        </ul>
      </article>
    </div>
  </section>
HTML
        ),
        array(
            'labels' => array( 'Détails assurance entreprise', 'Détails assurances entreprise' ),
            'html'   => <<<'HTML'
<section class="adr-detail-section" aria-label="Détails assurances entreprise">
    <div class="adr-detail-header">
      <p class="adr-detail-note">Détails repris du site original</p>
      <h2>Garanties professionnelles détaillées</h2>
      <p>Les garanties sont présentées par situation professionnelle afin de préserver la précision commerciale de l'offre et de clarifier les protections utiles à l'activité.</p>
    </div>
    <div class="adr-detail-grid">
      <article class="adr-detail-card" id="multirisques-professionnelle">
        <h2>Multirisques professionnelle</h2>
        <p>Que vous soyez artisan, commerçant, profession libérale ou entreprise industrielle, les solutions sont adaptées à la nature et à la superficie des locaux ainsi qu'au matériel professionnel.</p>
        <p>Elles prennent en compte les machines, marchandises, matériel informatique, stocks et autres équipements nécessaires à l'activité.</p>
      </article>
      <article class="adr-detail-card" id="loyers-impayes">
        <h2>Loyers impayés</h2>
        <p>La location d'un bien peut générer de sérieux problèmes en cas d'interruption de versement des loyers. La garantie des loyers impayés permet de bénéficier d'une couverture complète.</p>
        <ul>
          <li>garantie des loyers impayés jusqu'à 30 mois</li>
          <li>garantie des frais de contentieux, huissier et avocat</li>
          <li>garantie des détériorations immobilières</li>
          <li>assistance dans la composition du dossier jusqu'à la souscription du contrat.</li>
        </ul>
        <p>La prime étant déductible des revenus fonciers, un devis personnalisé peut aider à mesurer le coût réel de cette protection.</p>
      </article>
      <article class="adr-detail-card" id="assurance-emprunteur-professionnelle">
        <h2>Assurance emprunteur professionnelle</h2>
        <p>Quel que soit le type de prêt et d'investissement, l'assurance emprunteur propose le remboursement du capital restant dû en cas de décès, perte d'autonomie ou invalidité, mais aussi la prise en charge des mensualités du prêt en cas d'incapacité de travail.</p>
        <p>Choisir son assurance emprunteur en dehors de la banque peut permettre d'importantes économies selon le profil : emprunteurs de moins de 30 ans, non-fumeurs de plus de 54 ans, fumeurs ou emprunteurs atteints de maladies graves acceptées.</p>
        <p>La loi Hamon autorise, depuis le 26 Juillet 2014, la résiliation de l'assurance de prêt groupe dans les 12 mois suivant la signature de l'offre, avec des garanties au moins équivalentes au contrat initial.</p>
      </article>
      <article class="adr-detail-card" id="responsabilite-civile-professionnelle">
        <h2>Responsabilité civile et professionnelle</h2>
        <p>Dans un environnement économique et social tendu, l'assurance responsabilité civile professionnelle peut être considérée comme une réelle assurance vie pour votre entreprise.</p>
        <p>La responsabilité peut être engagée dans de nombreuses circonstances : faute inexcusable de l'employeur, responsabilité du produit livré, activité de l'entreprise, locaux, dirigeant ou salariés.</p>
        <p>Face à ces risques, des garanties spécifiques sur mesure, adaptées au corps de métier, contribuent à préserver la pérennité de l'entreprise.</p>
      </article>
      <article class="adr-detail-card" id="prevoyance-collective">
        <h2>Prévoyance collective</h2>
        <p>En complément des prestations des régimes obligatoires de Sécurité Sociale, la prévoyance collective apporte aux salariés et à leur famille une sécurité indispensable, notamment pour des risques lourds comme le décès ou l'invalidité.</p>
        <ul>
          <li>risques de dommages corporels résultant d'une maladie ou d'un accident : complémentaire santé, indemnités journalières en cas d'arrêt de travail, rentes d'invalidité</li>
          <li>engagements liés à la durée de vie : capital décès, rentes de conjoint et d'éducation, épargne retraite, dépendance.</li>
        </ul>
      </article>
      <article class="adr-detail-card" id="flotte-automobile">
        <h2>Flotte automobile</h2>
        <p>Si le parc automobile se compose d'au moins 5 véhicules, il peut être assuré par un seul contrat. Les garanties sont évaluées selon la diversité de la flotte.</p>
        <p>L'offre peut couvrir les véhicules, camionnettes et remorques. Le véhicule personnel du dirigeant peut intégrer le contrat lorsqu'il est utilisé dans le cadre de l'activité professionnelle.</p>
        <ul>
          <li>Tiers simple</li>
          <li>Tiers avec Vol et Incendie</li>
          <li>Dommages tous accidents</li>
          <li>assistance sans franchise kilométrique et sécurité du conducteur incluses</li>
          <li>services sur mesure : domicile, garages agréés, véhicule de remplacement.</li>
        </ul>
      </article>
      <article class="adr-detail-card" id="chomage-dirigeant">
        <h2>Assurance chômage du dirigeant</h2>
        <p>Nul dirigeant n'est à l'abri d'une faillite ou d'une baisse d'activité de son entreprise. Pour affronter les coups du sort, une assurance chômage privée peut protéger ses arrières.</p>
      </article>
    </div>
  </section>
HTML
        ),
    );

    foreach ( $sections as $section ) {
        foreach ( $section['labels'] as $label ) {
            $pattern = '#<section class="adr-detail-section" aria-label="' . preg_quote( $label, '#' ) . '">.*?</section>#s';

            if ( preg_match( $pattern, $html ) ) {
                $html = preg_replace( $pattern, trim( $section['html'] ), $html, 1 );
                break;
            }
        }
    }

    return $html;
}

// adr-version-footer-v1: discreet public site version marker.
add_action( 'wp_footer', 'adr_render_version_footer', 1000 );
function adr_render_version_footer() {
    if ( is_admin() ) {
        return;
    }
    ?>
    <style id="adr-version-footer-v1">
        .adr-version-footer {
            clear: both;
            width: min(100%, 1180px);
            min-height: 0;
            margin: 12px auto 10px;
            padding: 0 clamp(18px, 3vw, 30px);
            position: relative;
            z-index: 20;
            text-align: right;
            color: rgba(106, 119, 138, 0.78);
            font: 600 11px/1.4 -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            letter-spacing: 0.04em;
            pointer-events: none;
        }
        .copy-right {
            position: relative;
            min-height: 78px;
            background: #0A4464;
        }
        .copy-right .col-lg-4,
        .copy-right .col-md-4 {
            position: static !important;
        }
        .copy-right .copyright-logo {
            position: absolute !important;
            inset: 0 !important;
            width: 100% !important;
            height: 100% !important;
        }
        .copy-right .copyright-logo .logo {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 12px;
            position: absolute;
            top: 50% !important;
            left: 50% !important;
            width: max-content;
            max-width: calc(100vw - 220px);
            min-height: 58px;
            padding: 6px 18px 6px 8px;
            border: 0;
            border-radius: 8px;
            background: #0A4464;
            color: #ffffff;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.18);
            text-decoration: none;
            line-height: 1;
            transform: translate(-50%, -50%);
        }
        .copy-right .copyright-logo .logo img {
            display: block !important;
            width: 48px !important;
            height: 48px !important;
            max-width: 48px !important;
            border-radius: 999px;
            background: transparent;
            box-shadow: none;
        }
        .copy-right .copyright-logo .adr-footer-wordmark {
            display: inline-block;
            color: #ffffff;
            font: 800 28px/1.05 -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            letter-spacing: 0;
            text-shadow: none;
            white-space: nowrap;
        }
        .copy-right .adr-version-footer {
            position: absolute;
            top: 50%;
            right: clamp(16px, 4vw, 56px);
            width: auto;
            margin: 0;
            padding: 0;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.82);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.32);
        }
        @media (max-width: 760px) {
            .copy-right .copyright-logo .logo {
                gap: 10px;
                max-width: calc(100vw - 110px);
                min-height: 50px;
                padding: 5px 12px 5px 6px;
            }
            .copy-right .copyright-logo .logo img {
                width: 40px !important;
                height: 40px !important;
                max-width: 40px !important;
            }
            .copy-right .copyright-logo .adr-footer-wordmark {
                font-size: 20px;
            }
            .copy-right .adr-version-footer {
                right: 12px;
                top: auto;
                bottom: 8px;
                transform: none;
            }
        }
    </style>
    <div class="adr-version-footer" aria-label="Version du site">v119.3</div>
    <script id="adr-version-footer-placement-v1">
    (function () {
        function placeVersionMarker() {
            var marker = document.querySelector('.adr-version-footer');
            var strip = document.querySelector('.copy-right');
            if (marker && strip && marker.parentElement !== strip) {
                strip.appendChild(marker);
            }

            var logo = document.querySelector('.copy-right .copyright-logo .logo');
            if (!logo) {
                return;
            }

            var logoImage = logo.querySelector('img');
            if (logoImage) {
                logoImage.src = 'https://assurancesderueil.fr/wp-content/uploads/2022/08/cropped-flaticon2.png';
                logoImage.alt = 'Assurances de Rueil';
                logoImage.removeAttribute('srcset');
                logoImage.removeAttribute('sizes');
            }

            if (!logo.querySelector('.adr-footer-wordmark')) {
                var wordmark = document.createElement('span');
                wordmark.className = 'adr-footer-wordmark';
                wordmark.textContent = 'Assurances de Rueil';
                logo.appendChild(wordmark);
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', placeVersionMarker, { once: true });
        } else {
            placeVersionMarker();
        }
    }());
    </script>
    <?php
}

// adr-live-quote-form-v119-3: refreshed quote form shell, preserving MetForm storage.
add_action( 'wp_footer', 'adr_render_live_quote_form', 990 );
function adr_render_live_quote_form() {
    if ( is_admin() || ! is_page( 7427 ) ) {
        return;
    }
    ?>
    <style id="adr-live-quote-form-v119-3">
        .adr-quote-stage.adr-live-quote-ready .adr-form-card .mf-form-shortcode {
            display: none !important;
        }
        .adr-quote-stage .adr-next-steps,
        .adr-quote-stage .adr-step-card {
            min-width: 0;
        }
        .adr-quote-stage .adr-step-card h2 {
            max-width: 100%;
            min-width: 0;
            font-size: clamp(22px, 2.5vw, 28px) !important;
            line-height: 1.08 !important;
            letter-spacing: 0;
            overflow-wrap: anywhere;
            word-break: normal;
            hyphens: auto;
        }
        .adr-live-quote-shell,
        .adr-live-quote-shell * {
            box-sizing: border-box;
        }
        .adr-live-quote-form {
            display: grid;
            gap: 18px;
            color: var(--adr-ink);
        }
        .adr-live-quote-form fieldset {
            display: grid;
            gap: 14px;
            min-width: 0;
            margin: 0;
            border: 0;
            border-top: 1px solid var(--adr-line);
            padding: 18px 0 0;
        }
        .adr-live-quote-form fieldset:first-of-type {
            border-top: 0;
            padding-top: 0;
        }
        .adr-live-quote-form legend {
            padding: 0 0 4px;
            color: var(--adr-blue);
            font-size: 18px;
            font-weight: 900;
            line-height: 1.2;
        }
        .adr-live-quote-extra[hidden] {
            display: none !important;
        }
        .adr-live-quote-note,
        .adr-live-quote-form small {
            margin: 0;
            color: var(--adr-muted);
            font-size: 13px;
            line-height: 1.5;
        }
        .adr-live-field {
            display: grid;
            gap: 7px;
            min-width: 0;
            margin: 0;
        }
        .adr-live-field > span,
        .adr-live-consent {
            color: var(--adr-ink);
            font-weight: 900;
            line-height: 1.35;
        }
        .adr-live-field input:not([type="radio"]):not([type="checkbox"]),
        .adr-live-field select {
            width: 100%;
            height: 64px !important;
            min-height: 64px !important;
            border: 1px solid var(--adr-line);
            border-radius: 8px;
            padding: 0 16px !important;
            background: #ffffff;
            color: #07192f;
            font: 800 15px/64px Rubik, "Open Sans", Arial, sans-serif;
            letter-spacing: 0;
            line-height: 64px !important;
            outline: none;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.1);
        }
        .adr-live-field select {
            -webkit-appearance: none;
            appearance: none;
            padding-right: 48px !important;
            background-image:
                linear-gradient(45deg, transparent 50%, #07192f 50%),
                linear-gradient(135deg, #07192f 50%, transparent 50%);
            background-position:
                calc(100% - 23px) 50%,
                calc(100% - 15px) 50%;
            background-repeat: no-repeat;
            background-size: 8px 8px, 8px 8px;
        }
        .adr-live-field select::-ms-expand {
            display: none;
        }
        .adr-live-field input:focus,
        .adr-live-field select:focus {
            border-color: var(--adr-blue);
            box-shadow: 0 0 0 3px rgba(109, 169, 255, 0.32), 0 10px 24px rgba(0, 0, 0, 0.1);
        }
        .adr-live-two,
        .adr-live-date-selects,
        .adr-live-contact-row {
            display: grid;
            gap: 12px;
        }
        .adr-live-two {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .adr-live-date-selects {
            grid-template-columns: minmax(88px, .75fr) minmax(126px, 1fr) minmax(112px, .9fr);
        }
        .adr-live-contact-row {
            grid-template-columns: 1fr;
            align-items: start;
            gap: 18px;
        }
        .adr-live-field.adr-live-phone-field input[name="telephone"]:not([type="radio"]):not([type="checkbox"]) {
            height: 46px !important;
            min-height: 46px !important;
            line-height: 46px !important;
        }
        .adr-live-phone-field {
            width: 100%;
            justify-self: stretch;
        }
        .adr-live-choice-row {
            display: flex;
            flex-wrap: wrap;
            gap: 14px 18px;
        }
        .adr-live-choice-row label,
        .adr-live-consent {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            min-width: 142px;
            border: 1px solid var(--adr-line);
            border-radius: 8px;
            padding: 11px 12px;
            background: color-mix(in srgb, var(--adr-blue) 6%, transparent);
        }
        .adr-live-choice-row label {
            align-items: center;
            color: var(--adr-blue);
            flex: 1 1 220px;
            max-width: 284px;
            min-height: 46px;
        }
        .adr-live-choice-row input,
        .adr-live-consent input {
            flex: 0 0 auto;
            width: 17px;
            height: 17px;
            min-height: 0 !important;
            margin: 2px 0 0;
            accent-color: var(--adr-blue);
            box-shadow: none !important;
        }
        .adr-live-choice-row input {
            margin-top: 0;
        }
        .adr-live-consent strong {
            color: var(--adr-blue);
        }
        .adr-live-submit {
            justify-self: start;
            min-width: 190px;
        }
        .adr-live-submit:disabled {
            cursor: not-allowed;
            filter: grayscale(.25);
            opacity: .55;
        }
        .adr-live-status {
            margin: 0;
            border: 1px solid color-mix(in srgb, var(--adr-blue) 24%, transparent);
            border-radius: 8px;
            padding: 12px;
            background: color-mix(in srgb, var(--adr-blue) 8%, transparent);
            color: var(--adr-ink);
            font-weight: 900;
            line-height: 1.45;
        }
        @media (max-width: 700px) {
            .adr-live-two,
            .adr-live-date-selects {
                grid-template-columns: 1fr;
            }
            .adr-live-choice-row label,
            .adr-live-consent,
            .adr-live-submit {
                width: 100%;
            }
        }
    </style>
    <script id="adr-live-quote-form-v119-3">
    (function () {
        var version = '119.3';
        var endpointFallback = 'https://assurancesderueil.fr/wp-json/metform/v1/entries/insert/2073';
        var consentVersion = 'adr_quote_consent_2026-06-27_v119.3';
        var quoteTypes = [
            ['pret', 'Assurance de prêt'],
            ['habitation', 'Assurance habitation'],
            ['auto', 'Assurance automobile'],
            ['sante', 'Santé / prévoyance'],
            ['professionnel', 'Assurance professionnelle'],
            ['loyers', 'Loyers impayés'],
            ['autre', 'Autre demande']
        ];
        var professions = [
            'Cadres',
            'Employés, A. de maîtrise',
            'Ouvriers',
            'Professions médicales',
            'Professions paramédicales',
            'Professions libérales',
            'Commerçants et leurs salariés',
            'Artisans hors BTP et leurs salariés',
            'Artisans du BTP',
            'Professions agricoles et péri-agricoles',
            'Professions du transport',
            'Retraités',
            'Sans profession'
        ];
        var months = [
            ['01', 'Janvier'],
            ['02', 'Février'],
            ['03', 'Mars'],
            ['04', 'Avril'],
            ['05', 'Mai'],
            ['06', 'Juin'],
            ['07', 'Juillet'],
            ['08', 'Août'],
            ['09', 'Septembre'],
            ['10', 'Octobre'],
            ['11', 'Novembre'],
            ['12', 'Décembre']
        ];

        function ready(callback) {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', callback, { once: true });
                return;
            }
            callback();
        }

        function escapeHtml(value) {
            return String(value || '').replace(/[&<>"']/g, function (char) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                }[char];
            });
        }

        function optionList(items, placeholder) {
            return '<option value="">' + escapeHtml(placeholder) + '</option>' + items.map(function (item) {
                var value = Array.isArray(item) ? item[0] : item;
                var label = Array.isArray(item) ? item[1] : item;
                return '<option value="' + escapeHtml(value) + '">' + escapeHtml(label) + '</option>';
            }).join('');
        }

        function dayOptions() {
            var html = '<option value="">Jour</option>';
            for (var day = 1; day <= 31; day += 1) {
                html += '<option value="' + day + '">' + day + '</option>';
            }
            return html;
        }

        function yearOptions() {
            var currentYear = new Date().getFullYear();
            var html = '<option value="">Année</option>';
            for (var year = currentYear - 16; year >= currentYear - 100; year -= 1) {
                html += '<option value="' + year + '">' + year + '</option>';
            }
            return html;
        }

        function setField(form, name, value) {
            var field = form.elements.namedItem(name);
            if (!field || field instanceof RadioNodeList) {
                return;
            }
            field.value = value || '';
        }

        function formValue(form, name) {
            var field = form.elements.namedItem(name);
            if (!field) {
                return '';
            }
            if (field instanceof RadioNodeList) {
                return field.value || '';
            }
            return field.value || '';
        }

        function birthdateParts(form) {
            var day = formValue(form, 'jour_naissance').padStart(2, '0');
            var month = formValue(form, 'mois_naissance');
            var year = formValue(form, 'annee_naissance');
            if (!day || day === '00' || !month || !year) {
                return { legacy: '', canonical: '' };
            }
            return {
                legacy: month + '-' + day + '-' + year,
                canonical: year + '-' + month + '-' + day
            };
        }

        function contactLabel(value) {
            if (value === 'telephone') {
                return 'téléphone';
            }
            if (value === 'whatsapp') {
                return 'WhatsApp';
            }
            return 'e-mail';
        }

        ready(function () {
            var stage = document.querySelector('.adr-quote-stage');
            if (!stage || stage.dataset.adrLiveQuoteVersion === version) {
                return;
            }
            var card = stage.querySelector('.adr-form-card');
            var oldShortcode = card ? card.querySelector('.mf-form-shortcode') : null;
            var wrapper = card ? card.querySelector('#metform-wrap-2073-2073') : null;
            if (!card || !oldShortcode || card.querySelector('[data-adr-live-quote-form]')) {
                return;
            }

            var action = wrapper && wrapper.dataset.action ? wrapper.dataset.action : endpointFallback;
            var wpNonce = wrapper && wrapper.dataset.wpNonce ? wrapper.dataset.wpNonce : '';
            var formNonce = wrapper && wrapper.dataset.formNonce ? wrapper.dataset.formNonce : '';
            var side = stage.querySelector('.adr-side-card');
            if (side) {
                var sideHeading = side.querySelector('h2');
                var sideText = side.querySelector('p');
                var sideSteps = side.querySelector('ol');
                if (sideHeading) {
                    sideHeading.innerHTML = '<span class="adr-fr">Votre demande en trois temps</span><span class="adr-en">Your request in three steps</span>';
                }
                if (sideText) {
                    sideText.innerHTML = '<span class="adr-fr">Le formulaire commence par les coordonnées indispensables. Les informations complémentaires apparaissent ensuite selon le type de devis souhaité.</span><span class="adr-en adr-block">The form starts with the essential contact details. Additional questions appear after you choose the quote type.</span>';
                }
                if (sideSteps) {
                    sideSteps.innerHTML = '<li><span class="adr-fr">Informations nécessaires</span><span class="adr-en">Required details</span></li><li><span class="adr-fr">Informations utiles</span><span class="adr-en">Useful details</span></li><li><span class="adr-fr">Consentements</span><span class="adr-en">Consents</span></li>';
                }
            }

            var introText = card.querySelector('.adr-form-intro p');
            if (introText) {
                introText.innerHTML = '<span class="adr-fr">Ces informations sont transmises au cabinet afin de préparer une réponse adaptée à votre demande.</span><span class="adr-en adr-block">These details are sent to the agency so it can prepare a tailored response to your request.</span>';
            }

            var shell = document.createElement('div');
            shell.className = 'adr-live-quote-shell';
            shell.innerHTML = `
                <form class="adr-live-quote-form" data-adr-live-quote-form data-submit-endpoint="${escapeHtml(action)}" novalidate>
                    <input type="hidden" name="form_nonce" value="${escapeHtml(formNonce)}">
                    <input type="hidden" name="schema_version" value="adr_quote_v2">
                    <input type="hidden" name="source_url" value="">
                    <input type="hidden" name="consent_version" value="${escapeHtml(consentVersion)}">
                    <input type="hidden" name="civilite" value="">
                    <input type="hidden" name="email" value="">
                    <input type="hidden" name="profession" value="">
                    <input type="hidden" name="ville" value="">
                    <input type="hidden" name="code_postal" value="">
                    <input type="hidden" name="date_naissance" value="">
                    <input type="hidden" name="mf-gdpr-consent" value="">

                    <fieldset>
                        <legend><span class="adr-fr">Informations nécessaires</span><span class="adr-en">Required details</span></legend>
                        <div class="adr-live-field" role="group" aria-labelledby="adr-live-civilite-label">
                            <span id="adr-live-civilite-label">Civilité *</span>
                            <span class="adr-live-choice-row">
                                <label><input type="radio" name="mf-checkbox" value="Madame" required> Madame</label>
                                <label><input type="radio" name="mf-checkbox" value="Monsieur"> Monsieur</label>
                            </span>
                        </div>
                        <div class="adr-live-two">
                            <label class="adr-live-field">
                                <span>Nom *</span>
                                <input type="text" name="nom" autocomplete="family-name" required>
                            </label>
                            <label class="adr-live-field">
                                <span>Prénom *</span>
                                <input type="text" name="prenom" autocomplete="given-name" required>
                            </label>
                        </div>
                        <label class="adr-live-field">
                            <span>E-mail *</span>
                            <input type="email" name="mf-email" autocomplete="email" required>
                        </label>
                        <label class="adr-live-field">
                            <span>Type de devis désiré</span>
                            <select name="type_devis" data-quote-type>
                                ${optionList(quoteTypes, 'Sélectionner')}
                            </select>
                        </label>
                    </fieldset>

                    <fieldset class="adr-live-quote-extra" data-quote-extra hidden>
                        <legend><span class="adr-fr">Informations utiles</span><span class="adr-en">Useful details</span></legend>
                        <p class="adr-live-quote-note">Sélectionnez un type de devis pour afficher les informations complémentaires utiles à la demande.</p>
                        <div class="adr-live-field adr-live-birthdate-field" role="group" aria-labelledby="adr-live-birthdate-label">
                            <span id="adr-live-birthdate-label">Date de naissance</span>
                            <span class="adr-live-date-selects">
                                <select name="jour_naissance" aria-label="Jour">${dayOptions()}</select>
                                <select name="mois_naissance" aria-label="Mois">${optionList(months, 'Mois')}</select>
                                <select name="annee_naissance" aria-label="Année">${yearOptions()}</select>
                            </span>
                        </div>
                        <div class="adr-live-contact-row">
                            <div class="adr-live-field" role="group" aria-labelledby="adr-live-contact-label">
                                <span id="adr-live-contact-label">Contact préféré</span>
                                <span class="adr-live-choice-row">
                                    <label><input type="radio" name="contact_preference" value="email" checked> E-mail</label>
                                    <label><input type="radio" name="contact_preference" value="telephone"> Téléphone</label>
                                    <label><input type="radio" name="contact_preference" value="whatsapp"> WhatsApp</label>
                                </span>
                            </div>
                            <label class="adr-live-field adr-live-phone-field">
                                <span>Téléphone</span>
                                <input type="tel" name="telephone" autocomplete="tel" placeholder="+33">
                            </label>
                            <div class="adr-live-field" role="group" aria-labelledby="adr-live-fumeur-label">
                                <span id="adr-live-fumeur-label">Êtes-vous fumeur ?</span>
                                <span class="adr-live-choice-row">
                                    <label><input type="radio" name="fumeur" value="Oui"> Oui</label>
                                    <label><input type="radio" name="fumeur" value="Non"> Non</label>
                                </span>
                                <small>Est non-fumeur toute personne certifiant qu'elle n'a fumé ni cigarette, ni cigarette électronique, ni pipe, ni cigare, ni consommé de produits contenant de la nicotine au cours des 24 derniers mois, et qu'elle n'a pas arrêté de fumer à la demande expresse du corps médical.</small>
                            </div>
                        </div>
                        <div class="adr-live-two">
                            <label class="adr-live-field">
                                <span>Votre banque</span>
                                <input type="text" name="banque" autocomplete="organization">
                            </label>
                            <label class="adr-live-field">
                                <span>Votre profession</span>
                                <select name="mf-select">
                                    ${optionList(professions, 'Sélectionner')}
                                </select>
                            </label>
                        </div>
                        <label class="adr-live-field">
                            <span>Adresse</span>
                            <input type="text" name="adresse" autocomplete="street-address">
                        </label>
                        <div class="adr-live-two">
                            <label class="adr-live-field">
                                <span>Code postal</span>
                                <input type="text" name="code-postal" autocomplete="postal-code">
                            </label>
                            <label class="adr-live-field">
                                <span>Ville</span>
                                <input type="text" name="mf-text" autocomplete="address-level2">
                            </label>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend><span class="adr-fr">Consentements</span><span class="adr-en">Consents</span></legend>
                        <label class="adr-live-consent">
                            <input type="checkbox" name="contact_consent" value="Oui" data-contact-consent required>
                            <span>En cliquant sur « Envoyer », j'accepte qu'Assurances de Rueil me contacte par <strong data-contact-channel>e-mail</strong>. *</span>
                        </label>
                        <label class="adr-live-consent">
                            <input type="checkbox" name="rgpd_consent" value="Oui" data-rgpd-consent required>
                            <span>J'accepte le traitement de mes données personnelles conformément au RGPD. <a href="https://assurancesderueil.fr/politique-de-confidentialite/">EN SAVOIR PLUS</a> *</span>
                        </label>
                    </fieldset>

                    <button class="adr-button adr-button-primary adr-live-submit" type="submit" data-submit disabled>Envoyer</button>
                    <p class="adr-live-status" hidden data-form-status aria-live="polite">Votre demande a bien été transmise au cabinet.</p>
                </form>
            `;
            card.appendChild(shell);
            stage.dataset.adrLiveQuoteVersion = version;
            stage.classList.add('adr-live-quote-ready');

            var form = shell.querySelector('[data-adr-live-quote-form]');
            var quoteType = form.querySelector('[data-quote-type]');
            var quoteExtra = form.querySelector('[data-quote-extra]');
            var contactConsent = form.querySelector('[data-contact-consent]');
            var rgpdConsent = form.querySelector('[data-rgpd-consent]');
            var contactChannel = form.querySelector('[data-contact-channel]');
            var submitButton = form.querySelector('[data-submit]');
            var submitLabel = submitButton.textContent;
            var status = form.querySelector('[data-form-status]');

            function syncPayloadFields() {
                var birthdate = birthdateParts(form);
                var contactPreference = formValue(form, 'contact_preference') || 'email';
                if (contactChannel) {
                    contactChannel.textContent = contactLabel(contactPreference);
                }
                setField(form, 'source_url', window.location.href);
                setField(form, 'civilite', formValue(form, 'mf-checkbox'));
                setField(form, 'email', formValue(form, 'mf-email'));
                setField(form, 'profession', formValue(form, 'mf-select'));
                setField(form, 'ville', formValue(form, 'mf-text'));
                setField(form, 'code_postal', formValue(form, 'code-postal'));
                setField(form, 'date_naissance', birthdate.canonical);
                setField(form, 'mf-gdpr-consent', rgpdConsent && rgpdConsent.checked ? 'Oui' : '');
            }

            function updateSubmitState() {
                submitButton.disabled = !(contactConsent.checked && rgpdConsent.checked);
                syncPayloadFields();
            }

            quoteType.addEventListener('change', function () {
                quoteExtra.hidden = quoteType.value === '';
                syncPayloadFields();
            });
            form.addEventListener('input', syncPayloadFields);
            form.addEventListener('change', updateSubmitState);
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                syncPayloadFields();
                if (!form.reportValidity()) {
                    return;
                }
                submitButton.disabled = true;
                submitButton.textContent = 'Envoi en cours...';
                status.hidden = false;
                status.textContent = 'Envoi en cours...';
                var birthdate = birthdateParts(form);
                var payload = new FormData(form);
                payload.set('mf-date', birthdate.legacy);
                payload.set('date_naissance', birthdate.canonical);
                fetch(form.dataset.submitEndpoint || endpointFallback, {
                    method: 'POST',
                    headers: wpNonce ? { 'X-WP-Nonce': wpNonce } : {},
                    body: payload,
                    credentials: 'same-origin'
                })
                    .then(function (response) {
                        return response.json().catch(function () {
                            return {};
                        }).then(function (payload) {
                            if (!response.ok || !payload.status) {
                                throw new Error((payload.error && payload.error.join ? payload.error.join(' ') : payload.message) || "L'envoi n'a pas abouti. Vous pouvez aussi écrire à contact@assurancesderueil.fr.");
                            }
                            return payload;
                        });
                    })
                    .then(function (payload) {
                        status.textContent = payload.data && payload.data.message ? payload.data.message : 'Votre demande a bien été transmise au cabinet.';
                        form.reset();
                        quoteExtra.hidden = true;
                        updateSubmitState();
                    })
                    .catch(function (error) {
                        status.textContent = error.message || "L'envoi n'a pas abouti. Vous pouvez aussi écrire à contact@assurancesderueil.fr.";
                    })
                    .finally(function () {
                        submitButton.textContent = submitLabel;
                        updateSubmitState();
                    });
            });
            updateSubmitState();
        });
    }());
    </script>
    <?php
}


