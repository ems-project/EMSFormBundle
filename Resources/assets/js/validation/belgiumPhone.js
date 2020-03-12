import {i18n} from "../modules/translations";

export function setBelgiumPhoneValidation(element)
{
    element.addEventListener('change', function() {
        let validator = new BelgiumPhoneNumberValidator(this.value);
        if(validator.validate()) {
            this.setCustomValidity('');
        } else {
            this.setCustomValidity(i18n.trans('belgium_phone', {string: this.value}));
        }
    });
}

export class BelgiumPhoneNumberValidator
{
    constructor(value) {
        this.value = value;
        this.numbers = this.value.match(/\d+/g);
    }

    validate() {
        if (this.numbers === null) {
            return false;
        }

        this.phone = this.transform();

        const typeNumber = this.getTypeNumber();

        if (this.validateNumberOfDigit(typeNumber)) {
            return true;
        }

        return false;
    }

    validateNumberOfDigit(typeNumber) {
        if (typeNumber === 'zeros') {
            return (this.phone.length === 13) || (this.phone.length === 12);
        }

        if (typeNumber === 'plus') {
            return (this.phone.length === 12) || (this.phone.length === 11);
        }

        if (typeNumber === 'local') {
            return (this.phone.length === 10) || (this.phone.length === 9);
        }

        return false;
    }

    getTypeNumber() {
        if (this.phone.startsWith('+')) {
            return 'plus';
        }

        if (this.phone.startsWith('00')) {
            return 'zeros';
        }

        return 'local';
    }

    transform() {
        let phone = this.numbers.map(String).join('');

        if (this.value.startsWith('+')) {
            phone = ('+').concat(phone);
        }

        return phone;
    }
}
