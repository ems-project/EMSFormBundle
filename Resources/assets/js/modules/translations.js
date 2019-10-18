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
                        "niss_insz": "The social security number \"{{string}}\" has an invalid format.",
                        "max_length_count": "Remaining characters: {{count}}",
                        "belgium_phone": "The phone number \"{{string}}\" has an invalid format.",
                        "repeated": "The field \"{{string}}\" should have the same value as the previous field.",
                        "belgium_company_number": "The company registration number \"{{string}}\" has an invalid format."
                    }
                },
                "fr": {
                    translation: {
                        "niss_insz": "Le numéro de registre national \"{{string}}\" est invalide.",
                        "max_length_count": "Caractères restants: {{count}}",
                        "belgium_phone": "Le numéro téléphone \"{{string}}\" est invalide.",
                        "repeated": "Le champ \"{{string}}\" doit avoir la même valeur que le champ précédent.",
                        "belgium_company_number": "Le numéro d'entreprise \"{{string}}\" est invalide."
                    }
                },
                "nl": {
                    translation: {
                        "niss_insz": "Het rijksregisternummer \"{{string}}\" is ongeldig.",
                        "max_length_count": "Resterende tekens: {{count}}",
                        "belgium_phone": "Het telefoonnummer \"{{string}}\" is ongeldig.",
                        "repeated": "Het veld \"{{string}}\" moet dezelfde waarde hebben als het vorige veld.",
                        "belgium_company_number": "Het ondernemingsnummer \"{{string}}\" is ongeldig."	
                    }
                }
            }
        });
        translator.init()
        this.translator = translator;
    }
    trans(key, options) {
        return this.translator.t(key, options);
    }
}

export let i18n = new Translator();
