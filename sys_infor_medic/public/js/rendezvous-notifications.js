/**
 * Script de gestion des notifications temps réel pour les rendez-vous
 * Compatible avec Pusher et notifications WebSocket 
 */

class RendezVousNotifications {
    constructor() {
        this.pusher = null;
        this.channel = null;
        this.isSecretaire = window.userRole === 'secretaire';
        this.isPatient = window.userRole === 'patient';
        this.userId = window.userId;
        
        this.init();
    }

    init() {
        // Initialiser Pusher si disponible
        if (typeof Pusher !== 'undefined') {
            this.initPusher();
        } else {
            // Fallback sur polling
            this.initPolling();
        }
        
        // Écouter les événements pour les secrétaires
        if (this.isSecretaire) {
            this.listenForNewAppointments();
            this.listenForAppointmentUpdates();
        }
        
        // Écouter les événements pour les patients
        if (this.isPatient) {
            this.listenForPatientUpdates();
        }
    }

    initPusher() {
        try {
            this.pusher = new Pusher(window.pusherKey || 'fake-key', {
                cluster: window.pusherCluster || 'eu',
                encrypted: true
            });
            
            // Canal public pour toutes les notifications de rendez-vous
            this.channel = this.pusher.subscribe('rendezvous-updates');
            
            // Canal privé pour les notifications spécifiques au patient
            if (this.isPatient && this.userId) {
                this.privateChannel = this.pusher.subscribe(`private-patient.${this.userId}`);
            }
            
            console.log('✅ Pusher initialisé pour les notifications rendez-vous');
        } catch (error) {
            console.warn('⚠️ Erreur Pusher, basculement vers polling:', error);
            this.initPolling();
        }
    }

    initPolling() {
        // Système de polling en cas d'absence de Pusher
        if (this.isSecretaire) {
            setInterval(() => {
                this.pollNewAppointments();
            }, 30000); // Toutes les 30 secondes
        }
    }

    listenForNewAppointments() {
        if (this.channel) {
            this.channel.bind('rendezvous.created', (data) => {
                this.handleNewAppointment(data);
            });
        }
    }

    listenForAppointmentUpdates() {
        if (this.channel) {
            this.channel.bind('rendezvous.status.updated', (data) => {
                this.handleAppointmentUpdate(data);
            });
        }
    }

    listenForPatientUpdates() {
        if (this.privateChannel) {
            this.privateChannel.bind('rendezvous.status.updated', (data) => {
                this.handlePatientAppointmentUpdate(data);
            });
        }
    }

    handleNewAppointment(data) {
        console.log('📅 Nouvelle demande de rendez-vous:', data);
        
        // Notification toast
        this.showToast({
            type: 'info',
            title: 'Nouvelle demande de rendez-vous',
            message: `${data.patient_name} souhaite voir ${data.medecin_name}`,
            actions: [
                {
                    text: 'Voir',
                    action: () => this.goToAppointments()
                }
            ]
        });
        
        // Mettre à jour le badge de notification
        this.updateNotificationBadge();
        
        // Mettre à jour la liste si on est sur la page des rendez-vous
        if (window.location.pathname.includes('rendezvous')) {
            this.refreshAppointmentsList();
        }
        
        // Son de notification
        this.playNotificationSound();
    }

    handleAppointmentUpdate(data) {
        console.log('🔄 Mise à jour de rendez-vous:', data);
        
        let message = '';
        let type = 'info';
        
        if (data.new_status === 'confirmé') {
            message = `RDV de ${data.patient_name} confirmé`;
            type = 'success';
        } else if (data.new_status === 'annulé') {
            message = `RDV de ${data.patient_name} annulé`;
            type = 'warning';
        }
        
        if (message) {
            this.showToast({
                type: type,
                title: 'Rendez-vous mis à jour',
                message: message
            });
        }
        
        // Rafraîchir la liste
        if (window.location.pathname.includes('rendezvous')) {
            this.refreshAppointmentsList();
        }
    }

    handlePatientAppointmentUpdate(data) {
        console.log('👤 Mise à jour pour patient:', data);
        
        let message = '';
        let type = 'info';
        
        if (data.new_status === 'confirmé') {
            message = `Votre rendez-vous du ${data.date} à ${data.heure} a été confirmé`;
            type = 'success';
        } else if (data.new_status === 'annulé') {
            message = `Votre rendez-vous du ${data.date} à ${data.heure} a été annulé`;
            type = 'error';
        }
        
        if (message) {
            this.showToast({
                type: type,
                title: 'Votre rendez-vous',
                message: message,
                duration: 10000, // 10 secondes pour les patients
                actions: data.new_status === 'confirmé' ? [
                    {
                        text: 'Payer maintenant',
                        action: () => this.goToPayments()
                    }
                ] : []
            });
        }
        
        // Rafraîchir le dashboard patient
        if (window.location.pathname.includes('patient/dashboard')) {
            this.refreshPatientDashboard();
        }
    }

    async pollNewAppointments() {
        try {
            const response = await fetch('/api/rendezvous/pending-count', {
                headers: {
                    'Authorization': `Bearer ${window.csrfToken}`,
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.new_appointments > 0) {
                    this.updateNotificationBadge(data.new_appointments);
                }
            }
        } catch (error) {
            console.error('Erreur lors du polling:', error);
        }
    }

    showToast({ type, title, message, duration = 5000, actions = [] }) {
        // Créer le toast
        const toastId = 'toast-' + Date.now();
        const actionsHtml = actions.map(action => 
            `<button class="toast-action-btn" onclick="window.rdvNotifications.handleToastAction('${toastId}', ${action.action})">${action.text}</button>`
        ).join('');
        
        const toastHtml = `
            <div id="${toastId}" class="modern-toast ${type}">
                <div class="toast-icon">
                    <i class="bi bi-${this.getToastIcon(type)}"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                    ${actionsHtml ? `<div class="toast-actions">${actionsHtml}</div>` : ''}
                </div>
                <button class="toast-close" onclick="window.rdvNotifications.closeToast('${toastId}')">
                    <i class="bi bi-x"></i>
                </button>
                <div class="toast-progress-bar"></div>
            </div>
        `;
        
        // Injecter dans le container
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        
        container.insertAdjacentHTML('afterbegin', toastHtml);
        
        // Animation et suppression automatique
        const toastElement = document.getElementById(toastId);
        setTimeout(() => {
            if (toastElement) {
                toastElement.classList.add('show');
            }
        }, 100);
        
        setTimeout(() => {
            this.closeToast(toastId);
        }, duration);
    }

    getToastIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-triangle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'bell';
    }

    closeToast(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.classList.add('hide');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }
    }

    handleToastAction(toastId, actionFn) {
        if (typeof actionFn === 'function') {
            actionFn();
        }
        this.closeToast(toastId);
    }

    updateNotificationBadge(count = null) {
        const badge = document.getElementById('notif-badge');
        if (badge) {
            if (count !== null && count > 0) {
                badge.textContent = count;
                badge.classList.remove('d-none');
            } else {
                // Incrémenter le badge existant
                const current = parseInt(badge.textContent) || 0;
                badge.textContent = current + 1;
                badge.classList.remove('d-none');
            }
        }
    }

    playNotificationSound() {
        try {
            // Créer un son de notification simple
            const context = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = context.createOscillator();
            const gainNode = context.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(context.destination);
            
            oscillator.frequency.setValueAtTime(800, context.currentTime);
            oscillator.frequency.setValueAtTime(600, context.currentTime + 0.1);
            
            gainNode.gain.setValueAtTime(0.3, context.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, context.currentTime + 0.5);
            
            oscillator.start(context.currentTime);
            oscillator.stop(context.currentTime + 0.5);
        } catch (error) {
            // Son non supporté, ignorer silencieusement
        }
    }

    goToAppointments() {
        if (this.isSecretaire) {
            window.location.href = '/secretaire/rendezvous';
        }
    }

    goToPayments() {
        if (this.isPatient) {
            window.location.href = '/patient/paiements';
        }
    }

    async refreshAppointmentsList() {
        // Recharger uniquement la section des rendez-vous sans rafraîchir toute la page
        if (typeof window.refreshAppointments === 'function') {
            await window.refreshAppointments();
        }
    }

    async refreshPatientDashboard() {
        // Recharger les données du dashboard patient
        if (typeof window.refreshPatientData === 'function') {
            await window.refreshPatientData();
        }
    }
}

// Initialisation globale
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si on est connecté
    if (window.userId && window.userRole) {
        window.rdvNotifications = new RendezVousNotifications();
    }
});

// Styles CSS intégrés pour les toasts si pas déjà présents
if (!document.querySelector('#rdv-toast-styles')) {
    const styles = `
        <style id="rdv-toast-styles">
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        }
        
        .modern-toast {
            display: flex;
            align-items: flex-start;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            border: 1px solid #e2e8f0;
            margin-bottom: 16px;
            padding: 16px;
            position: relative;
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            overflow: hidden;
        }
        
        .modern-toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        
        .modern-toast.hide {
            transform: translateX(100%);
            opacity: 0;
        }
        
        .toast-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
        }
        
        .modern-toast.success .toast-icon {
            background: #dcfce7;
            color: #16a34a;
        }
        
        .modern-toast.error .toast-icon {
            background: #fef2f2;
            color: #dc2626;
        }
        
        .modern-toast.warning .toast-icon {
            background: #fef3c7;
            color: #d97706;
        }
        
        .modern-toast.info .toast-icon {
            background: #dbeafe;
            color: #2563eb;
        }
        
        .toast-content {
            flex: 1;
        }
        
        .toast-title {
            font-weight: 600;
            font-size: 14px;
            color: #1f2937;
            margin-bottom: 4px;
        }
        
        .toast-message {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.4;
        }
        
        .toast-actions {
            margin-top: 8px;
            display: flex;
            gap: 8px;
        }
        
        .toast-action-btn {
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 4px 12px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .toast-action-btn:hover {
            background: #2563eb;
        }
        
        .toast-close {
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 0;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 8px;
        }
        
        .toast-close:hover {
            color: #6b7280;
        }
        
        .toast-progress-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: currentColor;
            width: 100%;
            opacity: 0.3;
            animation: progressBar 5s linear;
        }
        
        @keyframes progressBar {
            from { width: 100%; }
            to { width: 0%; }
        }
        
        @media (max-width: 768px) {
            .toast-container {
                left: 10px;
                right: 10px;
                max-width: none;
            }
        }
        </style>
    `;
    document.head.insertAdjacentHTML('beforeend', styles);
}