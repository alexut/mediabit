document.addEventListener('DOMContentLoaded', function() {
    const selectElements = document.querySelectorAll('.wpcf7-form-control.wpcf7-choice-image');

    selectElements.forEach(selectElement => {
        const optionsData = JSON.parse(selectElement.getAttribute('data-options') || '[]');

        new Choices(selectElement, {
            shouldSort: false,
            itemSelectText: '',
            allowHTML: true,
            choices: optionsData.map(option => ({
                value: option.value,
                label: option.label,
                customProperties: { imgSrc: option.image }
            })),
            callbackOnCreateTemplates: function(template) {
                return {
                    item: (classNames, data) => {
                        const imgSrc = data.customProperties.imgSrc;
                        return template(`
                            <div class="choices__item choices__item--selectable" data-item data-id="${data.id}" data-value="${data.value}" ${data.active ? 'aria-selected="true"' : ''} ${data.disabled ? 'aria-disabled="true"' : ''}>
                               ${imgSrc ? ` <span class="choice-image-wrapper"><img src="${imgSrc}" style="height: 30px; margin-right: 10px;"></span>` : ''}
                                ${data.label}
                            </div>
                        `);
                    },
                    choice: (classNames, data) => {
                        const imgSrc = data.customProperties.imgSrc;
                        return template(`
                            <div class="choices__item choices__item--choice ${data.disabled ? classNames.itemDisabled : classNames.itemSelectable}" data-choice data-id="${data.id}" data-value="${data.value}" ${data.disabled ? 'data-choice-disabled aria-disabled="true"' : 'data-choice-selectable'}>
                           ${imgSrc ? ` <span class="choice-image-wrapper"><img src="${imgSrc}" style="height: 24px; margin-right: 10px;"></span>` : ''}
                                ${data.label}
                            </div>
                        `);
                    }
                };
            }
        });
    });

    document.querySelectorAll('.wpcf7-form-control.wpcf7-choice').forEach(function(el) {
        new Choices(el, {
            removeItemButton: true, // Adds a button to remove selected item(s)
            placeholder: true,
            placeholderValue: 'Selectează',
        });
    });

    var multichoiceElements = document.querySelectorAll('.wpcf7-multichoice');

    multichoiceElements.forEach(function(el) {
        new Choices(el, {
            removeItemButton: true,
            placeholder: true,
            placeholderValue: 'Selectează',
        });
    });

});
