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
    connnect() {
        let containers = document.getElementsByClassName('form-check');
        // console.log(containers);
        for (var item of containers) {
            // console.log(item.firstChild);   
            if (item.firstChild.tagName == 'INPUT') {
                console.log(item.firstChild);
                item.firstChild.setAttribute('checked', false);
            }
        }
    }


    toggle() {
        
        /**
        * Simple snippet to check/uncheck checkboxes of a specific group
        * The check trigger needs a data-checkall-trigger="groupname"
        * The checkable checkboxes need a data-checkall-group="groupname"
        */

        // Get master
        const checkAll = document.getElementById('select_all_childs');
        
        // Get master state and change childs
        checkAll.addEventListener('change', function(){
            let checked = self.checked ? true : false;
            toggleCheckboxes(checked); 
        });
        
        
       
        // Check / Uncheck all matching checkboxes
        function toggleCheckboxes (checked) {
            let containers = document.getElementsByClassName('form-check');
            // console.log(containers);
            for (var item of containers) {
                // console.log(item.firstChild);   
                if (item.firstChild.tagName == 'INPUT') {
                    // console.log(item.firstChild);
                    if (checked == true) {
                        item.firstChild.setAttribute('checked', true);
                    } else {
                        item.firstChild.setAttribute('checked', false);
                    }
                }
           
            };
        }
    }
}
