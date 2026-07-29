document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-mockup-editor]').forEach((editor) => {
        const colorInput = editor.querySelector('[data-selected-color]');
        const frontDesign = editor.querySelector('[data-design-input="front"]');
        const backDesign = editor.querySelector('[data-design-input="back"]');

        const setPreviewImage = (input, targetSelector) => {
            const target = editor.querySelector(targetSelector);
            if (!input || !target) {
                return;
            }

            input.addEventListener('change', () => {
                const file = input.files && input.files[0];
                target.src = file ? URL.createObjectURL(file) : '';
                target.classList.toggle('hidden', !file);
            });
        };

        setPreviewImage(frontDesign, '[data-design-preview="front"]');
        setPreviewImage(backDesign, '[data-design-preview="back"]');

        editor.querySelectorAll('[data-color-swatch]').forEach((button) => {
            button.addEventListener('click', () => {
                const color = button.dataset.colorSwatch;
                colorInput.value = color;
                editor.querySelectorAll('[data-template-preview]').forEach((preview) => {
                    preview.style.backgroundColor = button.dataset.colorValue || color;
                });
                editor.querySelectorAll('[data-color-swatch]').forEach((swatch) => {
                    swatch.classList.toggle('ring-2', swatch === button);
                    swatch.classList.toggle('ring-indigo-400', swatch === button);
                });
            });
        });
    });
});
