/**
 * Appearance Settings - Color Picker Sync
 */
(function() {
    // Update text input when color picker changes
    document.querySelectorAll('input[type="color"]').forEach(colorPicker => {
        colorPicker.addEventListener('input', function() {
            const textInput = this.nextElementSibling;
            if (textInput && textInput.tagName === 'INPUT' && textInput.type === 'text') {
                textInput.value = this.value;
            }
        });
    });
})();
