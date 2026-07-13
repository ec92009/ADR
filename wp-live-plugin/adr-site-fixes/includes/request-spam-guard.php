<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'ADR_SITE_REQUEST_GUARD_VERSION', '135.1' );
define( 'ADR_SITE_REQUEST_GUARD_TOKEN_FIELD', 'adr_form_guard' );
define( 'ADR_SITE_REQUEST_GUARD_HONEYPOT_FIELD', 'adr_website' );
define( 'ADR_SITE_REQUEST_GUARD_MIN_AGE_SECONDS', 3 );
define( 'ADR_SITE_REQUEST_GUARD_MAX_AGE_SECONDS', 36 * HOUR_IN_SECONDS );
define( 'ADR_SITE_REQUEST_RATE_LIMIT_MAX_REQUESTS', 3 );
define( 'ADR_SITE_REQUEST_RATE_LIMIT_WINDOW_SECONDS', HOUR_IN_SECONDS );

add_filter( 'mf_after_validation_check', 'adr_site_request_validate_spam_guard', 20 );
add_filter( 'metform_filter_before_store_form_data', 'adr_site_request_strip_spam_guard_fields', 1, 4 );
add_action( 'wp_footer', 'adr_site_request_render_spam_guard_fields', 995 );

function adr_site_request_guarded_form_ids() {
    return array( ADR_QUOTE_REQUESTS_FORM_ID, ADR_CONTACT_REQUESTS_FORM_ID );
}

function adr_site_request_guard_token( $form_id, $issued_at = null ) {
    $issued_at = $issued_at === null ? time() : (int) $issued_at;
    $payload = (string) $form_id . '|' . $issued_at;
    $signature = hash_hmac( 'sha256', $payload, wp_salt( 'nonce' ) );

    return $issued_at . '.' . $signature;
}

function adr_site_request_verify_guard_token( $token, $form_id, $now = null ) {
    $parts = explode( '.', (string) $token, 2 );
    if ( count( $parts ) !== 2 || ! ctype_digit( $parts[0] ) || ! preg_match( '/^[a-f0-9]{64}$/', $parts[1] ) ) {
        return false;
    }

    $issued_at = (int) $parts[0];
    $now = $now === null ? time() : (int) $now;
    $age = $now - $issued_at;
    if ( $age < ADR_SITE_REQUEST_GUARD_MIN_AGE_SECONDS || $age > ADR_SITE_REQUEST_GUARD_MAX_AGE_SECONDS ) {
        return false;
    }

    $expected = adr_site_request_guard_token( $form_id, $issued_at );

    return hash_equals( $expected, (string) $token );
}

function adr_site_request_guard_form_value( $form_data, $key ) {
    if ( ! is_array( $form_data ) || ! isset( $form_data[ $key ] ) || ! is_scalar( $form_data[ $key ] ) ) {
        return '';
    }

    return sanitize_text_field( (string) $form_data[ $key ] );
}

function adr_site_request_rate_limit_ip() {
    $remote_ip = adr_site_request_first_valid_ip( adr_site_request_server_value( 'REMOTE_ADDR' ) );
    if ( $remote_ip !== '' ) {
        return $remote_ip;
    }

    return adr_site_request_requester_ip();
}

function adr_site_request_rate_limit_key( $ip ) {
    $digest = hash_hmac( 'sha256', (string) $ip, wp_salt( 'auth' ) );

    return 'adr_req_rate_' . $digest;
}

function adr_site_request_rate_limit_allows( $ip, $now = null ) {
    if ( $ip === '' ) {
        return true;
    }

    $now = $now === null ? time() : (int) $now;
    $key = adr_site_request_rate_limit_key( $ip );
    $state = get_transient( $key );
    $started_at = is_array( $state ) && isset( $state['started_at'] ) ? (int) $state['started_at'] : 0;
    $count = is_array( $state ) && isset( $state['count'] ) ? (int) $state['count'] : 0;
    $elapsed = $now - $started_at;

    if ( $started_at <= 0 || $elapsed < 0 || $elapsed >= ADR_SITE_REQUEST_RATE_LIMIT_WINDOW_SECONDS ) {
        $started_at = $now;
        $count = 0;
        $elapsed = 0;
    }

    if ( $count >= ADR_SITE_REQUEST_RATE_LIMIT_MAX_REQUESTS ) {
        return false;
    }

    $remaining = max( 1, ADR_SITE_REQUEST_RATE_LIMIT_WINDOW_SECONDS - $elapsed );
    set_transient(
        $key,
        array(
            'started_at' => $started_at,
            'count'      => $count + 1,
        ),
        $remaining
    );

    return true;
}

function adr_site_request_validate_spam_guard( $validation ) {
    if ( ! is_array( $validation ) || empty( $validation['is_valid'] ) ) {
        return $validation;
    }

    $form_id = adr_site_request_form_id_from_request();
    if ( ! in_array( (string) $form_id, adr_site_request_guarded_form_ids(), true ) ) {
        return $validation;
    }

    $form_data = isset( $validation['form_data'] ) && is_array( $validation['form_data'] )
        ? $validation['form_data']
        : array();
    $honeypot = adr_site_request_guard_form_value( $form_data, ADR_SITE_REQUEST_GUARD_HONEYPOT_FIELD );
    $token = adr_site_request_guard_form_value( $form_data, ADR_SITE_REQUEST_GUARD_TOKEN_FIELD );

    if ( $honeypot !== '' || ! adr_site_request_verify_guard_token( $token, $form_id ) ) {
        $validation['is_valid'] = false;
        $validation['message'] = __( 'Votre envoi n\'a pas pu être vérifié. Rechargez la page puis réessayez.', 'adr-site-fixes' );

        return $validation;
    }

    if ( ! adr_site_request_rate_limit_allows( adr_site_request_rate_limit_ip() ) ) {
        $validation['is_valid'] = false;
        $validation['message'] = __( 'Trop de demandes ont été envoyées récemment. Réessayez dans une heure ou contactez le cabinet par téléphone.', 'adr-site-fixes' );
    }

    return $validation;
}

function adr_site_request_strip_spam_guard_fields( $form_data, $form_id, $form_settings, $attributes ) {
    if ( ! is_array( $form_data ) || ! in_array( (string) $form_id, adr_site_request_guarded_form_ids(), true ) ) {
        return $form_data;
    }

    unset( $form_data[ ADR_SITE_REQUEST_GUARD_TOKEN_FIELD ] );
    unset( $form_data[ ADR_SITE_REQUEST_GUARD_HONEYPOT_FIELD ] );

    return $form_data;
}

function adr_site_request_render_spam_guard_fields() {
    if ( is_admin() ) {
        return;
    }

    $tokens = array();
    foreach ( adr_site_request_guarded_form_ids() as $form_id ) {
        $tokens[ (string) $form_id ] = adr_site_request_guard_token( $form_id );
    }
    ?>
    <style id="adr-site-request-guard-v135-1">
        .adr-form-guard-trap {
            position: absolute !important;
            left: -10000px !important;
            width: 1px !important;
            height: 1px !important;
            overflow: hidden !important;
        }
    </style>
    <script id="adr-site-request-guard-v135-1">
    (function () {
        var nativeFetch = window.fetch;
        var tokens = <?php echo wp_json_encode( $tokens ); ?>;
        var selectors = {
            '2073': '[data-adr-live-quote-form]',
            '7487': '#metform-wrap-7487-7487 form'
        };

        function formIdFromRequest(input) {
            var url = typeof input === 'string' ? input : (input && input.url ? input.url : '');
            var match = String(url).match(/\/metform\/v1\/entries\/insert\/(2073|7487)(?:[/?#]|$)/);
            return match ? match[1] : '';
        }

        function setFormDataValue(body, name, value) {
            if (typeof body.set === 'function') {
                body.set(name, value);
                return;
            }
            if (typeof body.delete === 'function') {
                body.delete(name);
            }
            body.append(name, value);
        }

        function guardRequest(input, options) {
            var formId = formIdFromRequest(input);
            var body = options && options.body;
            if (!formId || !tokens[formId] || !(body instanceof FormData)) {
                return;
            }

            var form = document.querySelector(selectors[formId]);
            var trap = form ? form.querySelector('input[name="<?php echo esc_js( ADR_SITE_REQUEST_GUARD_HONEYPOT_FIELD ); ?>"]') : null;
            setFormDataValue(body, '<?php echo esc_js( ADR_SITE_REQUEST_GUARD_TOKEN_FIELD ); ?>', tokens[formId]);
            setFormDataValue(body, '<?php echo esc_js( ADR_SITE_REQUEST_GUARD_HONEYPOT_FIELD ); ?>', trap ? trap.value : '');
        }

        function addFields(form, formId) {
            if (!form || !tokens[formId]) {
                return;
            }

            var token = form.querySelector('input[name="<?php echo esc_js( ADR_SITE_REQUEST_GUARD_TOKEN_FIELD ); ?>"]');
            if (!token) {
                token = document.createElement('input');
                token.type = 'hidden';
                token.name = '<?php echo esc_js( ADR_SITE_REQUEST_GUARD_TOKEN_FIELD ); ?>';
                form.appendChild(token);
            }
            token.value = tokens[formId];

            if (!form.querySelector('input[name="<?php echo esc_js( ADR_SITE_REQUEST_GUARD_HONEYPOT_FIELD ); ?>"]')) {
                var trap = document.createElement('label');
                trap.className = 'adr-form-guard-trap';
                trap.setAttribute('aria-hidden', 'true');
                trap.innerHTML = 'Site web<input type="text" name="<?php echo esc_js( ADR_SITE_REQUEST_GUARD_HONEYPOT_FIELD ); ?>" tabindex="-1" autocomplete="off">';
                form.appendChild(trap);
            }
        }

        function primeForms() {
            Object.keys(selectors).forEach(function (formId) {
                document.querySelectorAll(selectors[formId]).forEach(function (form) {
                    addFields(form, formId);
                });
            });
        }

        function ready(callback) {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', callback, { once: true });
                return;
            }
            callback();
        }

        document.addEventListener('submit', function (event) {
            Object.keys(selectors).some(function (formId) {
                if (event.target.matches(selectors[formId])) {
                    addFields(event.target, formId);
                    return true;
                }
                return false;
            });
        }, true);

        window.fetch = function (input, options) {
            guardRequest(input, options);
            return nativeFetch.apply(this, arguments);
        };

        ready(function () {
            primeForms();
            var observer = new MutationObserver(primeForms);
            observer.observe(document.body, { childList: true, subtree: true });
            window.setTimeout(function () {
                observer.disconnect();
            }, 15000);
        });
    }());
    </script>
    <?php
}
