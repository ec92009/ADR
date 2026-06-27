<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// adr-quote-requests-v120-0: private site-request CSV and ordered admin email.
define( 'ADR_QUOTE_REQUESTS_VERSION', '120.0' );
define( 'ADR_QUOTE_REQUESTS_FORM_ID', '2073' );
define( 'ADR_CONTACT_REQUESTS_FORM_ID', '7487' );
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

function adr_quote_request_normalized( $data, $post = null ) {
    $type_devis = adr_quote_request_value( $data, array( 'type_devis' ) );
    $date = '';
    $form_id = ADR_QUOTE_REQUESTS_FORM_ID;

    if ( $post instanceof WP_Post ) {
        $date = mysql2date( 'd/m/Y H:i', $post->post_date );
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
    );
}

function adr_render_quote_requests_csv() {
    $headers = adr_quote_requests_headers();
    $rows = adr_quote_requests_rows();

    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename="demandes-site-' . gmdate( 'Ymd-His' ) . '.csv"' );

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
        return wp_unslash( $_POST );
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
