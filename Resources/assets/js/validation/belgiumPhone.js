import {i18n} from "../modules/translations";

export function setBelgiumPhoneValidation(element) {
    element.addEventListener('change', function() {
        if(validateBelgiumPhone(this.value)) {
            this.setCustomValidity('');
        } else {
            this.setCustomValidity(i18n.trans('belgium_phone', {string: this.value}));
        }
    });

    function validateBelgiumPhone(value) {
        const regexLeadingPlus = /^(\+\d\d)\d\d\d\d\d\d\d\d\d?/gm;
        const regexLeadingZeros = /^(00\d\d)\d\d\d\d\d\d\d\d\d?/gm;
        let numbers = value.match(/\d+/g);

        if (numbers === null) {
            return false;
        }

        let phone = numbers.map(String).join('');
        let mz;
        let valid = false;
        while ((mz = regexLeadingZeros.exec(value)) !== null) {
            // This is necessary to avoid infinite loops with zero-width matches
            if (mz.index === regexLeadingZeros.lastIndex) {
                regexLeadingZeros.lastIndex++;
            }

            valid = valid || (phone.length === 13) || (phone.length === 12);
        }

        let mp;
        while ((mp = regexLeadingPlus.exec(value)) !== null) {
            // This is necessary to avoid infinite loops with zero-width matches
            if (mp.index === regexLeadingPlus.lastIndex) {
                regexLeadingPlus.lastIndex++;
            }

            valid = valid ||  (phone.length === 11) || (phone.length === 10);
        }

        return valid || (phone.length === 10) || (phone.length === 9);
    }
}
