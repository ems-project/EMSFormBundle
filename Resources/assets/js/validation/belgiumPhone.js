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
        while ((mz = regexLeadingZeros.exec(phone)) !== null) {
            // This is necessary to avoid infinite loops with zero-width matches
            if (mz.index === regexLeadingZeros.lastIndex) {
                regexLeadingZeros.lastIndex++;
            }
            valid = valid || (phone.length === 13) || (phone.length === 12);
        }
        if (value.startsWith("+")) {
            // in this case we add a character
            phone = ("+").concat(phone);
            let mp;
            while ((mp = regexLeadingPlus.exec(phone)) !== null) {
                // This is necessary to avoid infinite loops with zero-width matches
                if (mp.index === regexLeadingPlus.lastIndex) {
                    regexLeadingPlus.lastIndex++;
                }
                // Phone with a + ( => a character has been added)
                valid = valid || (phone.length === 12) || (phone.length === 11);
            }
            return valid;
        }
        return valid || (phone.length === 10) || (phone.length === 9);
    }
}
