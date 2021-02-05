import {i18n} from "../modules/translations";

export function addMaxLengthCounter(element) {
    let parent = element.parentElement;
    let max = parseInt(element.getAttribute('maxlength'));

    let spanCounter = document.createElement("small");
    spanCounter.innerText = i18n.trans('max_length_count', {'count': max});
    parent.appendChild(spanCounter);

    element.addEventListener('keyup', function (){
        let diff = max - parseInt(this.value.length);
        spanCounter.innerText = i18n.trans('max_length_count', {'count': diff});
    });
}