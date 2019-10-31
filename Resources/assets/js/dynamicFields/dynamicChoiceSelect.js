export function addDynamicChoiceSelect(element, emsForm) {
    element.addEventListener('change', function() {
        let token = document.getElementById('form__token');
        let elementId = this.getAttribute('id');
        let idPrefix = elementId.substr(0, elementId.lastIndexOf('_'));
        let ids = document.querySelectorAll('*[id^="' + idPrefix + '"]');

        let data = {};
        data[token.getAttribute('name')] = token.value;
        Array.from(ids).forEach(function(element) {
            data[element.getAttribute('name')] = element.value;
        });

        emsForm.onDynamicFieldChange(data);
    });
}
