document.addEventListener('DOMContentLoaded', function() {
    const userIdMeta = document.querySelector('meta[name="user-id"]');
    if (!userIdMeta) return;

    const userId = userIdMeta.content;
    const notificationCount = document.getElementById('notification-count');
    const notificationList = document.getElementById('notification-list');
    const noNotifications = document.getElementById('no-notifications');

    // Helper to add notification to UI
    function addNotification(notification) {
        if (noNotifications) {
            noNotifications.style.display = 'none';
        }

        const currentCount = parseInt(notificationCount.innerText) || 0;
        notificationCount.innerText = currentCount + 1;
        notificationCount.classList.remove('hidden');

        const html = `
            <div class="p-3 border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200">
                <a href="${notification.url || '#'}" class="block">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-blue-100 rounded-full p-2 ml-3">
                            <i class="fas fa-home text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">${notification.title}</p>
                            <p class="text-xs text-gray-500 mt-1">${notification.message}</p>
                            <p class="text-xs text-gray-400 mt-1">${notification.time || 'الآن'}</p>
                        </div>
                    </div>
                </a>
            </div>
        `;

        notificationList.insertAdjacentHTML('afterbegin', html);
    }

    // Subscribe to Private Channel
    // Note: This requires Laravel Sanctum or Session auth to be configured for broadcasting
    if (window.Echo) {
        window.Echo.private(`App.Models.User.${userId}`)
            .notification((notification) => {
                console.log('Notification received:', notification);
                addNotification(notification);
                
                // Play sound
                const audio = new Audio('/sounds/notification.mp3');
                audio.play().catch(e => console.log('Audio play failed', e));
            });
    }

    // Dropdown Toggle
    window.toggleNotifications = function() {
        const menu = document.getElementById('notification-menu');
        menu.classList.toggle('hidden');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('notification-dropdown');
        const menu = document.getElementById('notification-menu');
        if (dropdown && !dropdown.contains(event.target) && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
        }
    });
});
