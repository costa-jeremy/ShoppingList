// Gestion de la collection d'ingrédients
document.addEventListener('DOMContentLoaded', function() {
    initializeIngredientHandlers();
});

function initializeIngredientHandlers() {
    // Initialize existing ingredient selects
    document.querySelectorAll('.ingredient-form-row select').forEach(select => {
        select.addEventListener('change', updateUnitDisplay);
    });
}

function updateUnitDisplay(event) {
    const select = event.target;
    const option = select.options[select.selectedIndex];
    const unitDisplay = select.closest('.ingredient-form-row').querySelector('.ingredient-unit');

    if (unitDisplay && option) {
        const unit = option.getAttribute('data-unit') || '';
        unitDisplay.textContent = unit;
    }
}

function addIngredientForm() {
    const collection = document.querySelector('.ingredients-collection');
    const item = document.createElement('div');
    item.classList.add('ingredient-form-row', 'mb-3', 'p-3', 'border', 'rounded');

    const prototype = document.querySelector('[data-prototype]').getAttribute('data-prototype');
    const index = collection.getAttribute('data-index');

    item.innerHTML = `
        <div class="row align-items-end">
            <div class="col">
                ${prototype.replace(/__ingredient__/g, index)}
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeIngredient(this)">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>
    `;

    collection.appendChild(item);
    collection.setAttribute('data-index', parseInt(index) + 1);

    // Initialize the new select
    const newSelect = item.querySelector('select');
    if (newSelect) {
        newSelect.addEventListener('change', updateUnitDisplay);
    }
}

function removeIngredient(button) {
    button.closest('.ingredient-form-row').remove();
}
