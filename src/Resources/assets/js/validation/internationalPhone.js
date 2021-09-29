'use strict'

import intlTelInput from 'intl-tel-input'
import './internationalPhone.css'
import {i18n} from '../modules/translations'

export function initializeInternationalPhoneFieldAndSetValidation(element)
{
    const internationalPhoneField = initializeInternationalPhoneField(element);

    element.addEventListener('change', validateInternationalPhoneField.bind(internationalPhoneField))
    element.addEventListener('keyup', validateInternationalPhoneField.bind(internationalPhoneField))
}

const errorMap = [
    'international_phone_invalid_number', // IS_POSSIBLE
    'international_phone_invalid_country_code', // INVALID_COUNTRY_CODE
    'international_phone_too_short', // TOO_SHORT
    'international_phone_too_long', // TOO_LONG
    'international_phone_invalid_number', // IS_POSSIBLE_LOCAL_ONLY
    'international_phone_invalid_number' // INVALID_LENGTH
]

function initializeInternationalPhoneField(element) {
    let options = {
        allowDropdown: true,
        autoHideDialCode: true,
        autoPlaceholder: 'polite',
        formatOnDisplay: false,
        nationalMode: false,
        placeholderNumberType: 'MOBILE',
        separateDialCode: false,
        utilsScript: '/bundles/emsform/vendor/intl-tel-input/utils.js?1613236686837'
    }

    const allowedCountries = (element.dataset.allowedCountries) ? element.dataset.allowedCountries.split(',') : []
    if (allowedCountries.length > 0) {
        options.onlyCountries = allowedCountries
    }

    return intlTelInput(element, options)
}

function validateInternationalPhoneField(event) {
    const HTMLInputElement = event.target
    const internationalPhoneField = this

    if (HTMLInputElement.value === '') {
        HTMLInputElement.setCustomValidity('')
        return undefined
    }

    if (internationalPhoneField.isValidNumber()) {
        HTMLInputElement.setCustomValidity('')
    } else {
        const errorCode = internationalPhoneField.getValidationError()
        const errorMessage = (errorMap[errorCode]) ? errorMap[errorCode] : 'international_phone_invalid_number'
        HTMLInputElement.setCustomValidity(i18n.trans(errorMessage, {string: HTMLInputElement.value}))
    }
}