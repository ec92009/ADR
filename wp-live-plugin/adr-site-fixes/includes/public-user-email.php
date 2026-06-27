<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class ADR_Site_Fixes_Public_User_Email {
    private const QUOTE_FORM_ID = '2073';
    private const CONTACT_FORM_ID = '7487';
    private const ADMIN_RECIPIENT = 'contact@assurancesderueil.fr';
    private const QUOTE_MARKER = 'adr-quote-user-email-v120-0';
    private const CONTACT_MARKER = 'adr-contact-user-email-v120-0';

    public static function init() {
        add_filter( 'metform_confirmation_user_email_body', array( __CLASS__, 'replace_body' ), 20, 5 );
        add_filter( 'wp_mail', array( __CLASS__, 'update_mail_args' ), 18 );
    }

    public static function replace_body( $body, $form_id, $form_data, $file_info, $form_settings ) {
        $kind = self::form_kind( $form_id );
        if ( $kind === '' || ! is_array( $form_data ) ) {
            return $body;
        }

        return self::build_message( wp_unslash( $form_data ), $kind );
    }

    public static function update_mail_args( $args ) {
        $message = isset( $args['message'] ) ? (string) $args['message'] : '';

        if ( strpos( $message, self::QUOTE_MARKER ) !== false ) {
            $args['subject'] = 'Votre demande de devis - Assurances de Rueil';
            $args['headers'] = self::headers();
            return $args;
        }

        if ( strpos( $message, self::CONTACT_MARKER ) !== false ) {
            $args['subject'] = 'Votre message - Assurances de Rueil';
            $args['headers'] = self::headers();
        }

        return $args;
    }

    private static function form_kind( $form_id ) {
        if ( (string) $form_id === self::QUOTE_FORM_ID ) {
            return 'quote';
        }

        if ( (string) $form_id === self::CONTACT_FORM_ID ) {
            return 'contact';
        }

        return '';
    }

    private static function headers() {
        return array(
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: Assurances de Rueil <' . self::ADMIN_RECIPIENT . '>',
            'Reply-To: Assurances de Rueil <' . self::ADMIN_RECIPIENT . '>',
        );
    }

    private static function flatten_value( $value ) {
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

    private static function value( $data, $keys ) {
        foreach ( $keys as $key ) {
            if ( ! isset( $data[ $key ] ) ) {
                continue;
            }

            $value = self::flatten_value( $data[ $key ] );
            if ( $value !== '' ) {
                return $value;
            }
        }

        return '';
    }

    private static function contact_label( $value ) {
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

    private static function type_label( $value ) {
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

    private static function birthdate( $data ) {
        $legacy = self::value( $data, array( 'mf-date' ) );
        if ( $legacy !== '' ) {
            return $legacy;
        }

        $canonical = self::value( $data, array( 'date_naissance' ) );
        if ( preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $canonical, $matches ) ) {
            return $matches[3] . '/' . $matches[2] . '/' . $matches[1];
        }

        return $canonical;
    }

    private static function normalized( $data ) {
        $type_devis = self::value( $data, array( 'type_devis' ) );

        return array(
            'nom'                => self::value( $data, array( 'nom', 'last_name', 'lastname' ) ),
            'prenom'             => self::value( $data, array( 'prenom' ) ),
            'email'              => self::value( $data, array( 'mf-email', 'email' ) ),
            'telephone'          => self::value( $data, array( 'telephone', 'tel', 'phone' ) ),
            'contact_preference' => self::contact_label( self::value( $data, array( 'contact_preference' ) ) ),
            'type_devis'         => self::type_label( $type_devis ),
            'date_naissance'     => self::birthdate( $data ),
            'adresse'            => self::value( $data, array( 'adresse', 'address' ) ),
            'code_postal'        => self::value( $data, array( 'code-postal', 'code_postal', 'postal_code' ) ),
            'ville'              => self::value( $data, array( 'ville', 'mf-text', 'city' ) ),
            'message'            => self::value( $data, array( 'message', 'mf-textarea' ) ),
        );
    }

    private static function summary_rows( $data, $kind ) {
        $request = self::normalized( $data );
        $rows = array();

        if ( $kind === 'contact' ) {
            if ( $request['email'] !== '' ) {
                $rows[] = array( 'E-mail', $request['email'] );
            }

            if ( $request['telephone'] !== '' ) {
                $rows[] = array( 'Téléphone', $request['telephone'] );
            }

            if ( $request['message'] !== '' ) {
                $rows[] = array( 'Message', self::excerpt( $request['message'] ) );
            }

            return $rows;
        }

        if ( $request['type_devis'] !== '' ) {
            $rows[] = array( 'Demande', $request['type_devis'] );
        }

        $rows[] = array( 'Contact préféré', $request['contact_preference'] );

        if ( $request['telephone'] !== '' ) {
            $rows[] = array( 'Téléphone', $request['telephone'] );
        }

        if ( $request['date_naissance'] !== '' ) {
            $rows[] = array( 'Date de naissance', $request['date_naissance'] );
        }

        return $rows;
    }

    private static function excerpt( $value ) {
        $value = preg_replace( '/\s+/', ' ', (string) $value );
        $value = is_string( $value ) ? trim( $value ) : '';

        if ( function_exists( 'mb_strlen' ) && function_exists( 'mb_substr' ) ) {
            if ( mb_strlen( $value ) <= 420 ) {
                return $value;
            }

            return rtrim( mb_substr( $value, 0, 417 ) ) . '...';
        }

        if ( strlen( $value ) <= 420 ) {
            return $value;
        }

        return rtrim( substr( $value, 0, 417 ) ) . '...';
    }

    private static function email_copy( $kind ) {
        if ( $kind === 'contact' ) {
            return array(
                'marker'    => self::CONTACT_MARKER,
                'preheader' => 'Votre message a bien été reçu par Assurances de Rueil.',
                'heading'   => 'Message bien reçu',
                'intro'     => 'Merci pour votre message. Il a bien été transmis au cabinet, et un conseiller reviendra vers vous dans les meilleurs délais.',
            );
        }

        return array(
            'marker'    => self::QUOTE_MARKER,
            'preheader' => 'Votre demande de devis a bien été reçue par Assurances de Rueil.',
            'heading'   => 'Demande de devis bien reçue',
            'intro'     => 'Merci pour votre demande. Elle a bien été transmise au cabinet, et un conseiller reviendra vers vous dans les meilleurs délais.',
        );
    }

    private static function build_message( $data, $kind ) {
        $request = self::normalized( $data );
        $first_name = trim( $request['prenom'] );
        $greeting = $first_name !== '' ? 'Bonjour ' . $first_name . ',' : 'Bonjour,';
        $privacy_url = home_url( '/politique-de-confidentialite/' );
        $site_url = home_url( '/' );
        $copy = self::email_copy( $kind );

        $body  = '<!doctype html><html><body style="margin:0;padding:0;background:#eef5fb;color:#07192f;font-family:Arial,Helvetica,sans-serif;">';
        $body .= '<!-- ' . esc_html( $copy['marker'] ) . ' -->';
        $body .= '<div style="display:none;max-height:0;overflow:hidden;color:#eef5fb;">' . esc_html( $copy['preheader'] ) . '</div>';
        $body .= '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;background:#eef5fb;"><tr><td align="center" style="padding:28px 14px;">';
        $body .= '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;max-width:680px;border-collapse:collapse;background:#ffffff;border:1px solid #c6d4e6;border-radius:8px;overflow:hidden;">';
        $body .= '<tr><td style="background:#07192f;padding:22px 26px;color:#ffffff;">';
        $body .= '<div style="font-size:22px;line-height:1.2;font-weight:800;">Assurances de Rueil</div>';
        $body .= '<div style="margin-top:6px;color:#d9e8f6;font-size:13px;line-height:1.5;">Courtier indépendant depuis quatre générations</div>';
        $body .= '</td></tr>';
        $body .= '<tr><td style="padding:30px 26px 8px;">';
        $body .= '<h1 style="margin:0;color:#0a3f81;font-size:28px;line-height:1.15;font-weight:800;">' . esc_html( $copy['heading'] ) . '</h1>';
        $body .= '<p style="margin:22px 0 0;color:#07192f;font-size:17px;line-height:1.55;font-weight:700;">' . esc_html( $greeting ) . '</p>';
        $body .= '<p style="margin:12px 0 0;color:#66758a;font-size:16px;line-height:1.65;">' . esc_html( $copy['intro'] ) . '</p>';
        $body .= '</td></tr>';

        $rows = self::summary_rows( $data, $kind );
        if ( ! empty( $rows ) ) {
            $body .= '<tr><td style="padding:14px 26px 2px;">';
            $body .= '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0;border:1px solid #d9e3ef;border-radius:8px;overflow:hidden;">';
            foreach ( $rows as $index => $row ) {
                $border = $index === 0 ? '' : 'border-top:1px solid #d9e3ef;';
                $body .= '<tr>';
                $body .= '<td style="' . $border . 'width:42%;padding:12px 14px;background:#f5f8fc;color:#0a3f81;font-size:13px;line-height:1.35;font-weight:800;">' . esc_html( $row[0] ) . '</td>';
                $body .= '<td style="' . $border . 'padding:12px 14px;color:#07192f;font-size:14px;line-height:1.45;">' . nl2br( esc_html( $row[1] ) ) . '</td>';
                $body .= '</tr>';
            }
            $body .= '</table></td></tr>';
        }

        $body .= '<tr><td style="padding:24px 26px 8px;">';
        $body .= '<p style="margin:0;color:#66758a;font-size:15px;line-height:1.65;">Vous pouvez répondre directement à cet e-mail ou nous joindre au <a href="tel:+33147510669" style="color:#0a3f81;font-weight:800;text-decoration:none;">+33 1 47 51 06 69</a>.</p>';
        $body .= '<p style="margin:18px 0 0;color:#07192f;font-size:15px;line-height:1.6;font-weight:800;">L’équipe Assurances de Rueil</p>';
        $body .= '</td></tr>';
        $body .= '<tr><td style="padding:18px 26px 28px;">';
        $body .= '<p style="margin:0;color:#66758a;font-size:12px;line-height:1.55;">Les informations transmises servent uniquement à traiter votre demande. Vous pouvez exercer vos droits d’accès, de rectification et de suppression en écrivant à <a href="mailto:' . esc_attr( self::ADMIN_RECIPIENT ) . '" style="color:#0a3f81;font-weight:800;">' . esc_html( self::ADMIN_RECIPIENT ) . '</a>. Plus d’informations sont disponibles dans notre <a href="' . esc_url( $privacy_url ) . '" style="color:#0a3f81;font-weight:800;">politique de confidentialité</a>.</p>';
        $body .= '</td></tr>';
        $body .= '</table>';
        $body .= '<p style="max-width:680px;margin:14px 0 0;color:#66758a;font-size:12px;line-height:1.5;text-align:center;">Assurances de Rueil - 75 avenue Victor Hugo, 92500 Rueil-Malmaison - <a href="' . esc_url( $site_url ) . '" style="color:#0a3f81;">assurancesderueil.fr</a></p>';
        $body .= '</td></tr></table></body></html>';

        return $body;
    }
}

ADR_Site_Fixes_Public_User_Email::init();
