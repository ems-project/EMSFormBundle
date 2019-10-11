import {i18n} from "../modules/translations";

export function setRepeatedValidation(element) {
    element.addEventListener('change', function() {
        if(validateRepetition(this.value, this.id)) {
            this.setCustomValidity('');
        } else {
            this.setCustomValidity(i18n.trans('repeated', {string: getLabel(this.id)}));
        }
    });

    function getLabel(id) {
        return document.getElementById(id + '_label').textContent;
    }

    function validateRepetition(value, id) {
        let original = document.getElementById(id.replace('_second', '_first'));
        return value === original.value;
    }
}
