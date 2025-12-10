import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["list"]
    static values = {
        prototype: String,
        index: {
            type: Number,
            default: 0
        }
    }

    connect() {
        // Initialiser l'index avec le nombre d'ingrédients existants
        this.indexValue = this.element.querySelectorAll('.ingredient-row').length;
    }

    add(event) {
        event.preventDefault();

        // Créer le nouvel élément
        const item = document.createElement('div');
        item.classList.add('ingredient-row', 'mb-3', 'p-3', 'border', 'rounded');

        // Remplacer l'index dans le prototype
        const newForm = this.prototypeValue.replace(/__name__/g, this.indexValue);

        // Créer la structure HTML
        item.innerHTML = `
            <div class="row">
                <div class="col-5">
                    ${this._extractField(newForm, 'ingredient')}
                </div>
                <div class="col-5">
                    <div class="input-group">
                        ${this._extractField(newForm, 'quantity')}
                        <span class="input-group-text ingredient-unit"></span>
                    </div>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-danger btn-sm w-100" data-action="recipe-ingredients#remove">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        `;

        // Ajouter le nouvel élément à la liste
        this.listTarget.appendChild(item);
        this.indexValue++;
    }

    remove(event) {
        event.preventDefault();
        event.currentTarget.closest('.ingredient-row').remove();
    }

    _extractField(form, fieldName) {
        const div = document.createElement('div');
        div.innerHTML = form;
        return div.querySelector(`[name$="[${fieldName}]"]`).outerHTML;
    }
}
