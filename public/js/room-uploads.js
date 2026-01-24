// Global function to handle room uploads - DIRECT USER ACTIVATION
window.openRoomFileInput = function (roomType) {
    console.log(' DIRECT CLICK for room:', roomType);

    const input = document.querySelector(`input[name="room_images[${roomType}][]"]`);
    console.log(' Input found:', !!input);

    if (input) {
        // Make input cover the entire card
        input.style.position = 'absolute';
        input.style.left = '0';
        input.style.top = '0';
        input.style.width = '100%';
        input.style.height = '100%';
        input.style.opacity = '0';
        input.style.cursor = 'pointer';
        input.style.zIndex = '9999';

        console.log(' Input positioned over card');

        // IMMEDIATE CLICK - NO DELAYS, NO TIMEOUTS
        try {
            input.click();
            console.log(' Click executed!');
        } catch (error) {
            console.error(' Click failed:', error);

            // Fallback: try to trigger via event
            try {
                const event = new MouseEvent('click', {
                    bubbles: true,
                    cancelable: true,
                    view: window
                });
                input.dispatchEvent(event);
                console.log(' Event dispatched');
            } catch (e2) {
                console.error(' Event dispatch failed:', e2);
            }
        }
    } else {
        console.error(' Input not found for room:', roomType);
    }
};

// Handle file selection and show success
window.handleRoomFileSelect = function (roomType, input) {
    const files = input.files;
    const card = input.closest('.room-upload-card');

    if (files && files.length > 0) {
        console.log(` ${files.length} files selected for ${roomType}`);

        // Show success indicator
        if (card) {
            // Remove existing indicators
            const existingIndicator = card.querySelector('.upload-success');
            if (existingIndicator) {
                existingIndicator.remove();
            }

            // Add success checkmark
            const successDiv = document.createElement('div');
            successDiv.className = 'upload-success';
            successDiv.innerHTML = `
                <div style="
                    position: absolute;
                    top: 10px;
                    right: 10px;
                    background: #10b981;
                    color: white;
                    width: 30px;
                    height: 30px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                    z-index: 1000;
                    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.5);
                    animation: scaleIn 0.3s ease;
                ">✓</div>
            `;
            card.appendChild(successDiv);

            // Update card appearance
            card.style.borderColor = '#10b981';
            card.style.backgroundColor = '#f0fdf4';

            // Update upload area
            const uploadArea = card.querySelector('.room-upload-area');
            if (uploadArea) {
                uploadArea.innerHTML = `
                    <i class="fas fa-check-circle" style="color: #10b981; font-size: 2rem;"></i>
                    <p style="color: #10b981; font-weight: 600; margin: 10px 0 0 0;">
                        ${files.length} photo(s) selected
                    </p>
                    <small style="color: #6b7280;">Click to add more</small>
                `;
            }
        }

        // Show file names in console
        const fileNames = Array.from(files).map(f => f.name).join(', ');
        console.log(' Files:', fileNames);

        // Show toast notification
        if (window.showToast) {
            window.showToast(`${files.length} file(s) uploaded for ${roomType.replace('_', ' ')}`, 'success');
        }
    }
};

// Simple setup - make inputs work properly
window.setupRoomUploads = function () {
    console.log(' Setting up room uploads...');

    // Find all room file inputs
    const roomInputs = document.querySelectorAll('input[name^="room_images"]');
    console.log(` Found ${roomInputs.length} room inputs`);

    roomInputs.forEach(input => {
        const roomType = input.name.match(/\[(.*?)\]/)?.[1];
        if (roomType) {
            console.log(` Setting up ${roomType} input`);

            // Add change listener to show success
            input.addEventListener('change', function () {
                handleRoomFileSelect(roomType, this);
            });
        }
    });
};

// Auto-setup when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    console.log('Room uploads script loaded');
    setupRoomUploads();
});

// Also setup when steps change
document.addEventListener('click', function (e) {
    if (e.target.closest('.step-item[data-step="4"]') ||
        e.target.closest('#nextBtn') ||
        e.target.closest('#prevBtn')) {
        setTimeout(setupRoomUploads, 100);
    }
});
