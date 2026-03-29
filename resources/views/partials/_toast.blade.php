<!-- Toast Notification System -->
<!-- Container -->
<div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 99999;"></div>

<!-- Styles -->
<style>
    .toast-card {
        background: white;
        color: #333;
        padding: 12px 20px;
        border-radius: 12px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 280px;
        border-left: 5px solid #5B2E91;
        animation: toast-in 0.3s ease-out forwards;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        pointer-events: auto;
    }
    .toast-card.success { border-left-color: #10b981; }
    .toast-card.error { border-left-color: #ef4444; }
    .toast-card.info { border-left-color: #3b82f6; }
    
    @keyframes toast-in {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes toast-out {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
</style>

<!-- Script -->
<script>
    window.showToast = function(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) {
            // Create container if it doesn't exist (failsafe)
            const newContainer = document.createElement('div');
            newContainer.id = 'toast-container';
            newContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 99999;';
            document.body.appendChild(newContainer);
            return window.showToast(message, type);
        }
        
        const toast = document.createElement('div');
        toast.className = `toast-card ${type}`;
        
        let icon = 'bi-check-circle-fill';
        if (type === 'error') icon = 'bi-exclamation-triangle-fill';
        if (type === 'info') icon = 'bi-info-circle-fill';
        
        toast.innerHTML = `
            <i class="bi ${icon}" style="font-size: 1.25rem;"></i>
            <div style="flex-grow: 1;">${message}</div>
            <button onclick="this.parentElement.remove()" style="background:none; border:none; padding:0; cursor:pointer; color:#9ca3af; margin-left:10px;">&times;</button>
        `;
        
        container.appendChild(toast);
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            if (toast.parentElement) {
                toast.style.animation = 'toast-out 0.3s ease-in forwards';
                setTimeout(() => toast.remove(), 300);
            }
        }, 4000);
    };

    // Replace native alert with toast if desired (Optional but cleaner for existing code)
    // window.alert = function(msg) { window.showToast(msg, 'info'); };
</script>
