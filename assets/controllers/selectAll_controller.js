import { Controller } from '@hotwired/stimulus';

/*
* This make alive a select all checkbox and checkboxes kids
*/
export default class extends Controller {
    static selectAll;
    static checkboxes;
    static btnMove;
    static btnRemove;


    connect() {
        this.selectAll = document.getElementById('select-all');
        this.checkboxes = document.getElementById('select_childs_form_childrens').getElementsByClassName('form-check-input')
        this.btnMove = document.getElementById('select_childs_form_move');
        this.btnRemove = document.getElementById('select_childs_form_remove');
    }

    /**
     * Un.Check all checkboxes when action on select all
     */
    toggle() {
        for (let i = 0; i < this.checkboxes.length; i++) {
            if (this.selectAll.checked == true) {
                this.checkboxes[i].checked = true;
            } else {
                this.checkboxes[i].checked = false;
            }
        }

        this.toggleButton();
    }

    /**
     * Uncheck Select All if some checkboxes are not checked
     * Check Select All if all checkboxes are checked
     */
    toggleParent() {
        this.selectAll.checked = true;

        for (let i = 0; i < this.checkboxes.length; i++) {
            // if one unchecked, uncheck select all
            if (this.checkboxes[i].checked == false) {
                this.selectAll.checked = false;
            }
        }

        this.toggleButton();
    }

    /**
     * De.Activate buttons when some checkboxes are checked or not
     */
    toggleButton() {
        for (let i = 0; i < this.checkboxes.length; i++) {
            // if one check, activate buttons
            if (this.checkboxes[i].checked == true) {
                this.btnMove.removeAttribute('disabled');
                this.btnRemove.removeAttribute('disabled');
                return;
            }
        }

        this.btnMove.setAttribute('disabled', true);
        this.btnRemove.setAttribute('disabled', true);
    }
}

