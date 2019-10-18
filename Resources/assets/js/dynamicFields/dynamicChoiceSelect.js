import * as $ from 'jquery';

export function addDynamicChoiceSelect(element) {
    element.addEventListener('change', function() {
        let form = $(this).closest('form');
        let data = {};
        data[this.getAttribute('name')] = this.value;
        $.ajax({
            url : form.attr('action'),
            type: form.attr('method'),
            data : data,
            success: function(html) {
                console.log(html);
            }
        });
    });
}
