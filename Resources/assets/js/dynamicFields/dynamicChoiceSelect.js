import * as $ from 'jquery';

export function addDynamicChoiceSelect(element, ajaxUrl) {
    element.addEventListener('change', function() {
        //let form = $(this).closest('form');
        let data = {};
        data[this.getAttribute('name')] = this.value;
        $.ajax({
            url : ajaxUrl,
            type: 'GET',
            data : data,
            success: function(html) {
                console.log(html);
            }
        });
    });
}
