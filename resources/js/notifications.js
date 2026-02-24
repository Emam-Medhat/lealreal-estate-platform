document.addEventListener('DOMContentLoaded', function () {
    const userIdMeta = document.querySelector('meta[name="user-id"]');
    if (!userIdMeta) return;

    const userId = userIdMeta.content;

    // Dashboard selectors
    const dashCount = document.getElementById('notification-count');
    const dashList = document.getElementById('notification-list');

    // Navbar selectors
    const nbBadge = document.getElementById('nb-notif-badge');
    const nbList = document.getElementById('nb-notif-list');
    const nbCountText = document.getElementById('nb-notif-count');

    // Helper to update counts across all UI elements
    function updateUIByCount(count) {
        // Update Dashboard
        if (dashCount) {
            dashCount.innerText = count;
            if (count > 0) dashCount.classList.remove('hidden');
            else dashCount.classList.add('hidden');
        }

        // Update Navbar
        if (nbBadge) {
            nbBadge.innerText = count > 99 ? '99+' : count;
            if (count > 0) nbBadge.classList.remove('hidden');
            else nbBadge.classList.add('hidden');
        }

        if (nbCountText) {
            nbCountText.innerText = `(${count} unread)`;
        }
    }

    // Helper to add notification to UI
    function addNotification(notification, isInitial = false) {
        // Handle Dashboard List
        if (dashList) {
            const noNotifications = document.getElementById('no-notifications');
            if (noNotifications) noNotifications.style.display = 'none';

            if (!document.getElementById(`notification-${notification.id}`)) {
                const dashHtml = `
                    <div class="p-3 border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200 ${notification.read_at ? '' : 'bg-blue-50/10 border-r-4 border-r-blue-500'}" id="notification-${notification.id}">
                        <a href="${notification.url || '#'}" class="block" onclick="markAsRead('${notification.id}')">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-${notification.color || 'blue'}-100 rounded-full p-2 ml-3">
                                    <i class="fas fa-${notification.icon || 'bell'} text-${notification.color || 'blue'}-600"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <p class="text-sm font-semibold text-gray-800">${notification.title || 'تنبيه جديد'}</p>
                                        <p class="text-[10px] text-gray-400 font-normal">${notification.time || 'الآن'}</p>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-0.5 line-clamp-2">${notification.message}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                `;
                dashList.insertAdjacentHTML('afterbegin', dashHtml);
            }
        }

        // Handle Navbar List
        if (nbList) {
            const nbEmpty = nbList.querySelector('.nb-notif-empty');
            if (nbEmpty) nbEmpty.remove();

            if (!document.getElementById(`nb-notification-${notification.id}`)) {
                const nbHtml = `
                    <div class="nb-notif-item ${notification.read_at ? '' : 'unread'}" id="nb-notification-${notification.id}" onclick="markAsRead('${notification.id}')">
                        <div class="nb-notif-icon ni-${notification.color || 'blue'}">
                            <i class="fas fa-${notification.icon || 'bell'}"></i>
                        </div>
                        <div class="nb-notif-body">
                            <div class="nb-notif-title">${notification.title || 'Notification'}</div>
                            <div class="nb-notif-time">${notification.time || 'Just now'}</div>
                            <div class="nb-notif-desc" style="font-size: 11px; color: var(--nb-gray-500); margin-top: 2px;">${notification.message}</div>
                        </div>
                    </div>
                `;
                nbList.insertAdjacentHTML('afterbegin', nbHtml);
            }
        }

        if (!isInitial) {
            const currentCount = parseInt(dashCount?.innerText || nbBadge?.innerText || 0);
            updateUIByCount(currentCount + 1);

            // Highlight the bell
            const bell = document.querySelector('#notification-dropdown i, #nb-notif-btn i');
            if (bell) {
                bell.classList.add('text-red-500', 'animate-bounce');
                setTimeout(() => bell.classList.remove('text-red-500', 'animate-bounce'), 3000);
            }
        }
    }

    // Fetch existing notifications
    function fetchNotifications() {
        if (!userId) return;
        fetch(`/api/notifications/unread/${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (dashList) dashList.innerHTML = '';
                    if (nbList) nbList.innerHTML = '';

                    data.notifications.reverse().forEach(notification => {
                        addNotification(notification, true);
                    });

                    updateUIByCount(data.unread_count);
                }
            })
            .catch(error => console.error('Error fetching notifications:', error));
    }

    fetchNotifications();

    // Subscribe to Private Channel
    if (window.Echo && userId) {
        window.Echo.private(`App.Models.User.${userId}`)
            .notification((notification) => {
                console.log('Notification received:', notification);
                addNotification(notification);

                // Play sound
                const audio = document.getElementById('notification-sound') || new Audio('/sounds/notification.mp3');
                if (audio instanceof HTMLAudioElement) audio.play().catch(e => console.log('Audio play failed', e));
                else audio.play().catch(e => console.log('Audio play failed', e));

                // Set native notification if permission granted
                if (Notification.permission === "granted") {
                    new Notification(notification.title || "تنبيه جديد", {
                        body: notification.message,
                        icon: "/favicon.ico"
                    });
                }
            });

        // Request notification permission
        if (Notification.permission === "default") {
            Notification.requestPermission();
        }
    }

    // Dropdown Toggle
    window.toggleNotifications = function () {
        const menu = document.getElementById('notification-menu');
        menu.classList.toggle('hidden');
    }

    // Mark as read
    window.markAsRead = function (id) {
        fetch(`/api/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(() => {
            // Update Dashboard
            const dashEl = document.getElementById(`notification-${id}`);
            if (dashEl) dashEl.classList.remove('bg-blue-50/10', 'border-r-4', 'border-r-blue-500');

            // Update Navbar
            const nbEl = document.getElementById(`nb-notification-${id}`);
            if (nbEl) nbEl.classList.remove('unread');

            const currentCount = parseInt(dashCount?.innerText || nbBadge?.innerText || 0);
            if (currentCount > 0) {
                updateUIByCount(currentCount - 1);
            }

            // If it has a URL and we are clicking in navbar, we might want to redirect
            // But we already have the <a> tag handle it in some places.
        });
    }

    // Mark all as read
    window.markAllAsRead = function () {
        fetch(`/api/notifications/mark-all-read/${userId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateUIByCount(0);

                    document.querySelectorAll('#notification-list > div').forEach(el => {
                        el.classList.remove('bg-blue-50/10', 'border-r-4', 'border-r-blue-500');
                    });
                    document.querySelectorAll('#nb-notif-list > .nb-notif-item').forEach(el => {
                        el.classList.remove('unread');
                    });
                }
            });
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function (event) {
        const dropdown = document.getElementById('notification-dropdown');
        const menu = document.getElementById('notification-menu');
        if (dropdown && !dropdown.contains(event.target) && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
        }
    });
});
