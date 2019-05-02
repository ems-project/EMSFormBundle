export default function setNissInszValidation(nissId) {
    let niss = document.getElementById(nissId);

    niss.addEventListener('change', function() {
        if(validateNissInsz(niss.value))
            this.setCustomValidity('');
        else
            this.setCustomValidity('NISS-INSZ format error');
    });

    function validateNissInsz(value) {
        const regex = /(\d\d\d\d\d\d\d\d\d)(\d\d)/gm;
        let numbers = value.match(/\d+/g);
        if (numbers === null) {
            return false;
        }
        let niss = numbers.map(String).join('');
        let m;
        let valid;
        while ((m = regex.exec(niss)) !== null) {
            // This is necessary to avoid infinite loops with zero-width matches
            if (m.index === regex.lastIndex) {
                regex.lastIndex++;
            }

            let base = m[1];
            let control = m[2];
            valid = control == (97 - (base % 97));
            if (!valid) {
                valid = control == (97 - ('2'.concat(base) % 97));
            }
        }
        return valid;
    }
}