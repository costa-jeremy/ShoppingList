import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["list"]

    connect() {
        this.index = this.listTarget.children.length;
    }

    addIngredient(event) {
        event.preventDefault();
        const prototype = this.listTarget.dataset.prototype;
        const newForm = prototype.replace(/__name__/g, this.index);
        this.listTarget.insertAdjacentHTML('beforeend', newForm);
        this.index++;

        // Initialiser Select2 sur le nouvel élément si nécessaire
        const newSelect = this.listTarget.lastElementChild.querySelector('select');
        if (newSelect && typeof $ !== 'undefined') {
            $(newSelect).select2({
                theme: "bootstrap-5",
                width: '100%'
            });
        }
    }

    removeIngredient(event) {
        event.preventDefault();
        const item = event.target.closest('.ingredient-form-row');
        if (item) {
            // Détruire Select2 avant de supprimer l'élément
            const select = item.querySelector('select');
            if (select && typeof $ !== 'undefined') {
                $(select).select2('destroy');
            }
            item.remove();
        }
    }
}
