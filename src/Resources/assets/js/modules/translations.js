import i18next from "i18next";

class Translator
{
    constructor() {
        let translator = i18next.createInstance({
            lng: document.documentElement.lang,
            fallbackLng: 'en',
            resources: {
                en: {
                    translation: {
                        "niss_insz": "The social security number \"{{string}}\" is invalid.",
                        "max_length_count": "Remaining characters: {{count}}",
                        "belgium_phone": "The phone number \"{{string}}\" is invalid.",
                        "international_phone_invalid_number": "The phone number \"{{string}}\" is invalid.",
                        "international_phone_invalid_country_code": "The phone number \"{{string}}\" has an invalid country code.",
                        "international_phone_too_short": "The phone number \"{{string}}\" is too short.",
                        "international_phone_too_long": "The phone number \"{{string}}\" is too long.",
                        "repeated": "The field \"{{string}}\" should have the same value as the previous field.",
                        "belgium_company_number": "The company registration number \"{{string}}\" is invalid.",
                        "belgium_company_number_multiple": "At least one company registration number \"{{string}}\" is invalid.",
                        "belgium_onss_rsz" : "NSSO number \"{{string}}\" is invalid."
                    }
                },
                "fr": {
                    translation: {
                        "niss_insz": "Le numéro de registre national \"{{string}}\" est invalide.",
                        "max_length_count": "Caractères restants: {{count}}",
                        "belgium_phone": "Le numéro téléphone \"{{string}}\" est invalide.",
                        "international_phone_invalid_number": "Le numéro téléphone \"{{string}}\" est invalide.",
                        "international_phone_invalid_country_code": "The phone number \"{{string}}\" has an invalid country code.",
                        "international_phone_too_short": "The phone number \"{{string}}\" is too short.",
                        "international_phone_too_long": "The phone number \"{{string}}\" is too long.",
                        "repeated": "Le champ \"{{string}}\" doit avoir la même valeur que le champ précédent.",
                        "belgium_company_number": "Le numéro d'entreprise \"{{string}}\" est invalide.",
                        "belgium_company_number_multiple": "Au moins un numéro d'entreprise \"{{string}}\" est invalide.",
                        "belgium_onss_rsz" : "Le numéro ONSS \"{{string}}\" est invalide."
                    }
                },
                "nl": {
                    translation: {
                        "niss_insz": "Het rijksregisternummer \"{{string}}\" is ongeldig.",
                        "max_length_count": "Resterende tekens: {{count}}",
                        "belgium_phone": "Het telefoonnummer \"{{string}}\" is ongeldig.",
                        "international_phone_invalid_number": "Het telefoonnummer \"{{string}}\" is ongeldig.",
                        "international_phone_invalid_country_code": "The phone number \"{{string}}\" has an invalid country code.",
                        "international_phone_too_short": "The phone number \"{{string}}\" is too short.",
                        "international_phone_too_long": "The phone number \"{{string}}\" is too long.",
                        "repeated": "Het veld \"{{string}}\" moet dezelfde waarde hebben als het vorige veld.",
                        "belgium_company_number": "Het ondernemingsnummer \"{{string}}\" is ongeldig.",
                        "belgium_company_number_multiple": "Minstens één ondernemingsnummer \"{{string}}\" is ongeldig.",
                        "belgium_onss_rsz" : "RSZ number \"{{string}}\" is ongeldig."
                    }
                },
                "de": {
                    translation: {
                        "niss_insz": "Die Nationalregisternummer \"{{string}}\" ist ungültig.",
                        "max_length_count": "Verbleibende Zeichen: {{count}}",
                        "belgium_phone": "Die Telefonnummer \"{{string}}\" ist ungültig.",
                        "international_phone_invalid_number": "Die Telefonnummer \"{{string}}\" ist ungültig.",
                        "international_phone_invalid_country_code": "The phone number \"{{string}}\" has an invalid country code.",
                        "international_phone_too_short": "The phone number \"{{string}}\" is too short.",
                        "international_phone_too_long": "The phone number \"{{string}}\" is too long.",
                        "repeated": "Der Feld  \"{{string}}\" sollte den gleichen Wert wie das vorherige Feld haben.",
                        "belgium_company_number": "Die Firmenregistrierungsnummer \"{{string}}\" ist ungültig.",
                        "belgium_company_number_multiple": "Mindestens eine Firmenregistrierungsnummer \"{{string}}\" ist ungültig.",
                        "belgium_onss_rsz" : "LSS-Nummer \"{{string}}\" ist ungültig."
                    }
                }
            }
        });
        translator.init();
        this.translator = translator;
    }
    trans(key, options) {
        return this.translator.t(key, options);
    }
}

export let i18n = new Translator();
