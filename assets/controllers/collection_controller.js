import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["container"]

    connect() {
        this.index = this.containerTarget.children.length;
    }

    add(event) {
        event.preventDefault();
        const prototype = this.containerTarget.dataset.prototype;
        const newForm = prototype.replace(/__name__/g, this.index);
        this.containerTarget.insertAdjacentHTML('beforeend', newForm);
        this.index++;
    }

    remove(event) {
        event.preventDefault();
        event.currentTarget.closest('.ingredient-item').remove();
    }
}
