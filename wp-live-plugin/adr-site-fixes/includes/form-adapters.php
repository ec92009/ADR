<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// adr-live-quote-form-v125-1: refreshed quote form shell, preserving MetForm storage.
add_action( 'wp_footer', 'adr_render_live_quote_form', 990 );
function adr_render_live_quote_form() {
    if ( is_admin() || ! is_page( 7427 ) ) {
        return;
    }
    ?>
    <style id="adr-live-quote-form-v125-1">
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
    <script id="adr-live-quote-form-v125-1">
    (function () {
        var version = '125.1';
        var endpointFallback = 'https://assurancesderueil.fr/wp-json/metform/v1/entries/insert/2073';
        var consentVersion = 'adr_quote_consent_2026-06-28_v120.2';
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
        var monthCodes = {
            '01': 'JAN',
            '02': 'FEV',
            '03': 'MAR',
            '04': 'AVR',
            '05': 'MAI',
            '06': 'JUIN',
            '07': 'JUIL',
            '08': 'AOUT',
            '09': 'SEP',
            '10': 'OCT',
            '11': 'NOV',
            '12': 'DEC'
        };

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
            var formatted = day + '-' + (monthCodes[month] || month) + '-' + year;
            return {
                legacy: formatted,
                canonical: formatted
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
                                <input type="text" name="telephone" autocomplete="tel" inputmode="tel" placeholder="+33">
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
