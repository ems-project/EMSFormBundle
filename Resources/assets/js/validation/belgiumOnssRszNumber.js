import {i18n} from "../modules/translations";

export function setBelgiumOnssRszValidation(element) {
    element.addEventListener('change', function() {
        if(validateBelgiumOnssRsz(this.value)) {
            this.setCustomValidity('');
        } else {
            this.setCustomValidity(i18n.trans('belgium_onss_rsz', {string: this.value}));
        }
    });

    function validateBelgiumOnssRsz(value) {
        let numbers = value.match(/\d+/g);

        if (numbers === null) {
            return false;
        }

        let number = numbers.map(String).join('');
        
        if (number.length >= 9 && number.length <= 10) {
            return true;
        }
        
        return false;
    }
}
