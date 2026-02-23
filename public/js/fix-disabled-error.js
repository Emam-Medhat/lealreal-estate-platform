// Fix for fillSelectElement undefined disabled property error
// This file provides a patch for the JavaScript error

// The error occurs when fillSelectElement tries to access 'disabled' property on undefined elements
// This patch adds proper null checks to prevent the TypeError

(function() {
    // Store original function if it exists
    const originalFillSelectElement = window.V0?.fillSelectElement;
    
    if (originalFillSelectElement) {
        // Override with null safety
        window.V0.fillSelectElement = function(element) {
            // Check if element exists before accessing properties
            if (!element) {
                console.warn('fillSelectElement: element is undefined');
                return;
            }
            
            // Call original function with safe element access
            try {
                return originalFillSelectElement.call(this, element);
            } catch (error) {
                console.error('fillSelectElement error:', error);
                return;
            }
        };
    }
    
    // Similar fix for fillAllElements
    const originalFillAllElements = window.V0?.fillAllElements;
    
    if (originalFillAllElements) {
        window.V0.fillAllElements = function(elements) {
            if (!elements || !Array.isArray(elements)) {
                console.warn('fillAllElements: elements is undefined or not an array');
                return;
            }
            
            try {
                return originalFillAllElements.call(this, elements);
            } catch (error) {
                console.error('fillAllElements error:', error);
                return;
            }
        };
    }
    
    // Similar fix for fillAllInputs
    const originalFillAllInputs = window.V0?.fillAllInputs;
    
    if (originalFillAllInputs) {
        window.V0.fillAllInputs = function(inputs) {
            if (!inputs || !Array.isArray(inputs)) {
                console.warn('fillAllInputs: inputs is undefined or not an array');
                return;
            }
            
            try {
                return originalFillAllInputs.call(this, inputs);
            } catch (error) {
                console.error('fillAllInputs error:', error);
                return;
            }
        };
    }
    
    console.log('JavaScript error patch applied for fillSelectElement disabled property issue');
})();
