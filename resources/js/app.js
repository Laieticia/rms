import './bootstrap';
import './echo';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();



// Écouter les notifications admin
if (document.getElementById('notificationBell')) {
    window.Echo.private('admin.notifications')
        .listen('.new.notification', (e) => {
            updateNotificationBell(e);
            addNotificationToList(e);
            showToastNotification(e);
        })
        .listen('.new.order', (e) => {
            updateNotificationBell({
                title: 'Nouvelle commande',
                message: `#${e.order_number} - ${e.customer_name} - ${e.total}`,
                type: 'new_order',
                created_at: 'À l\'instant',
            });
            addNotificationToList(e);
            showToastNotification(e);
        });
}

// Écouter les notifications utilisateur
if (window.userId) {
    window.Echo.private(`user.${window.userId}`)
        .listen('.new.notification', (e) => {
            updateNotificationBell(e);
            showToastNotification(e);
        });
}

// Fonctions
function updateNotificationBell(data) {
    const badge = document.querySelector('#notificationBadge');
    const bell = document.querySelector('#notificationBell');
    
    if (badge) {
        let count = parseInt(badge.textContent || '0');
        badge.textContent = count + 1;
        badge.classList.remove('d-none');
    }
    
    if (bell) {
        bell.classList.add('animate__animated', 'animate__swing');
        setTimeout(() => bell.classList.remove('animate__animated', 'animate__swing'), 1000);
    }
}

function addNotificationToList(data) {
    const list = document.querySelector('#notificationList');
    if (!list) return;
    
    const item = document.createElement('a');
    item.href = '#';
    item.className = 'iq-sub-card';
    item.innerHTML = `
        <div class="media align-items-center cust-card py-3 border-bottom">
            <div class="">
                <img class="avatar-50 rounded-small" 
                     src="${data.avatar || '{{ url("admin/assets/images/user/01.jpg") }}'}" 
                     alt="notif">
            </div>
            <div class="media-body ml-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="mb-0">${data.title || 'Notification'}</h6>
                    <small class="text-dark"><b>${data.created_at || 'À l\'instant'}</b></small>
                </div>
                <small class="mb-0">${data.message || ''}</small>
            </div>
        </div>
    `;
    
    list.prepend(item);
    
    // Limiter à 5 notifications
    if (list.children.length > 5) {
        list.lastElementChild.remove();
    }
}

function showToastNotification(data) {
    // Créer un toast Bootstrap
    const toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) return;
    
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-bg-info border-0 show';
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-bell me-2"></i>
                <strong>${data.title || 'Notification'}</strong><br>
                <small>${data.message || ''}</small>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast, { delay: 5000 });
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => toast.remove());
}
