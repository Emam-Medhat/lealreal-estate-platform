// Notification Component
class NotificationManager {
    constructor() {
        this.notifications = [];
        this.init();
    }

    init() {
        // Load notifications from server
        this.loadNotifications();
        
        // Setup periodic refresh
        setInterval(() => this.loadNotifications(), 30000); // Every 30 seconds
        
        // Listen for real-time notifications
        this.setupWebSocket();
    }

    async loadNotifications() {
        try {
            const response = await fetch('/api/notifications');
            const data = await response.json();
            this.notifications = data.notifications;
            this.renderNotifications();
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }

    renderNotifications() {
        const container = document.getElementById('notificationDropdown');
        if (!container) return;

        const notificationList = container.querySelector('.notification-list');
        const badge = container.querySelector('.notification-badge');
        
        // Update badge
        const unreadCount = this.notifications.filter(n => !n.read_at).length;
        if (badge) {
            badge.textContent = unreadCount > 0 ? unreadCount : '';
            badge.style.display = unreadCount > 0 ? 'block' : 'none';
        }

        // Render notification list
        if (notificationList) {
            notificationList.innerHTML = '';
            
            if (this.notifications.length === 0) {
                notificationList.innerHTML = '<div class="dropdown-item text-muted">لا توجد إشعارات</div>';
                return;
            }

            this.notifications.forEach(notification => {
                const item = document.createElement('div');
                item.className = `dropdown-item notification-item ${notification.read_at ? 'read' : 'unread'}`;
                item.innerHTML = `
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <div class="fw-bold">${notification.data.title}</div>
                            <div class="small">${notification.data.message}</div>
                            <div class="text-muted small">${this.formatTime(notification.created_at)}</div>
                        </div>
                        ${!notification.read_at ? '<div class="notification-dot"></div>' : ''}
                    </div>
                `;
                
                item.addEventListener('click', () => this.markAsRead(notification.id));
                notificationList.appendChild(item);
            });
        }
    }

    async markAsRead(notificationId) {
        try {
            await fetch(`/api/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
            
            // Update local state
            const notification = this.notifications.find(n => n.id === notificationId);
            if (notification) {
                notification.read_at = new Date().toISOString();
                this.renderNotifications();
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    setupWebSocket() {
        if (typeof Echo !== 'undefined') {
            Echo.private('user.' + userId)
                .notification((notification) => {
                    this.notifications.unshift(notification);
                    this.renderNotifications();
                    this.showToast(notification);
                });
        }
    }

    showToast(notification) {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = 'toast-notification show';
        toast.innerHTML = `
            <div class="toast-header">
                <strong class="me-auto">${notification.data.title}</strong>
                <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
            <div class="toast-body">
                ${notification.data.message}
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }

    formatTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000); // seconds
        
        if (diff < 60) return 'الآن';
        if (diff < 3600) return Math.floor(diff / 60) + ' دقيقة';
        if (diff < 86400) return Math.floor(diff / 3600) + ' ساعة';
        return Math.floor(diff / 86400) + ' يوم';
    }
}

// Initialize notification manager
document.addEventListener('DOMContentLoaded', function() {
    window.notificationManager = new NotificationManager();
});
