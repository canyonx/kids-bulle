import { Controller } from '@hotwired/stimulus';

/*
* This is an example Stimulus controller!
*
* Any element with a data-controller="hello" attribute will cause
* this controller to be executed. The name "hello" comes from the filename:
* hello_controller.js -> "hello"
*
* Delete this file or adapt it for your use!
*/
export default class extends Controller {
    connect() {
        let selectAll = document.getElementById('select-all');
        selectAll.addEventListener('change', function(e) {
            // console.log(this.checked);
            toggle(this.checked);      
        })

        function toggle(value) {
            let checkboxes = document.getElementById('select_childs_form_childrens').getElementsByClassName('form-check-input');
            // console.log(checkboxes);
            for (var i = 0; i < checkboxes.length; i++) {
                // console.log(checkboxes[i].checked);
                if (value == true) {
                    checkboxes[i].checked = true;
                } else {
                    checkboxes[i].checked = false;
                }
            };
        }
    }

}

