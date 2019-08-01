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
                        "max_length_count": "Remaining characters: {{count}}"
                    }
                },
                "fr": {
                    translation: {
                        "niss_insz": "Le numéro de registre national \"{{string}}\" est invalide.",
                        "max_length_count": "Caractères restants: {{count}}"
                    }
                },
                "nl": {
                    translation: {
                        "niss_insz": "Het rijksregisternummer \"{{string}}\" is ongeldig.",
                        "max_length_count": "Resterende tekens: {{count}}"
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