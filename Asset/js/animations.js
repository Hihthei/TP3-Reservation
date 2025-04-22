document.addEventListener('DOMContentLoaded', function() {
    animatePageLoad();
    
    setupCalendarAnimations();
    
    setupFormAnimations();
    
    setupNotifications();
});

function animatePageLoad() {
    document.body.classList.add('loaded');
    
    const fadeElements = document.querySelectorAll('.fade-in');
    fadeElements.forEach((element, index) => {
        setTimeout(() => {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, 100 * index);
    });
    
    const mainTitle = document.querySelector('h2.text-center');
    if (mainTitle) {
        mainTitle.classList.add('animated-title');
    }
}

function setupCalendarAnimations() {
    const calendarCells = document.querySelectorAll('.calendar-cell');
    calendarCells.forEach(cell => {
        cell.addEventListener('mouseenter', function() {
            this.classList.add('cell-hover');
        });
        
        cell.addEventListener('mouseleave', function() {
            this.classList.remove('cell-hover');
        });
        
        cell.addEventListener('click', function() {
            if (event.target.classList.contains('event')) return;
            
            this.classList.add('pulse');
            setTimeout(() => {
                this.classList.remove('pulse');
            }, 300);
        });
    });
    
    const events = document.querySelectorAll('.event');
    events.forEach(event => {
        event.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.add('event-click');
            
            setTimeout(() => {
                this.classList.remove('event-click');
            }, 300);
        });
    });
}

function setupFormAnimations() {
    const formInputs = document.querySelectorAll('.form-control');
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('input-focus');
        });
        
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.parentElement.classList.remove('input-focus');
            }
        });
        
        if (input.value.trim() !== '') {
            input.parentElement.classList.add('input-focus');
        }
    });
    
    const submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            if (form && form.checkValidity()) {
                this.innerHTML = '<span class="spinner"></span> ' + this.textContent;
                this.disabled = true;
            }
        });
    });
    
    setupFormValidation();
}

function setupFormValidation() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                validateInput(this);
            });
            
            input.addEventListener('blur', function() {
                validateInput(this);
            });
        });
    });
}

function validateInput(input) {
    input.classList.remove('is-valid', 'is-invalid');
    
    if (input.value.trim() !== '') {
        if (input.checkValidity()) {
            input.classList.add('is-valid');
            input.classList.add('valid-pulse');
            setTimeout(() => {
                input.classList.remove('valid-pulse');
            }, 500);
        } else {
            input.classList.add('is-invalid');
            input.classList.add('invalid-shake');
            setTimeout(() => {
                input.classList.remove('invalid-shake');
            }, 500);
        }
    }
}

function setupNotifications() {
    const errorMessage = document.getElementById('error-message');
    
    if (errorMessage && errorMessage.textContent.trim() !== '') {
        errorMessage.style.opacity = '0';
        errorMessage.style.transform = 'translateY(-10px)';
        
        setTimeout(() => {
            errorMessage.style.opacity = '1';
            errorMessage.style.transform = 'translateY(0)';
        }, 300);
    }
    
    window.showNotification = function(message, type = 'info') {
        let notificationContainer = document.getElementById('notification-container');
        
        if (!notificationContainer) {
            notificationContainer = document.createElement('div');
            notificationContainer.id = 'notification-container';
            notificationContainer.style.position = 'fixed';
            notificationContainer.style.top = '20px';
            notificationContainer.style.right = '20px';
            notificationContainer.style.zIndex = '9999';
            document.body.appendChild(notificationContainer);
        }
        
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} notification`;
        notification.innerHTML = message;
        
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(50px)';
        notification.style.transition = 'all 0.3s ease';
        
        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'btn-close';
        closeButton.setAttribute('aria-label', 'Close');
        closeButton.style.float = 'right';
        closeButton.addEventListener('click', function() {
            hideNotification(notification);
        });
        
        notification.appendChild(closeButton);
        notificationContainer.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        setTimeout(() => {
            hideNotification(notification);
        }, 5000);
        
        return notification;
    };
    
    function hideNotification(notification) {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(50px)';
        
        setTimeout(() => {
            notification.remove();
        }, 300);
    }
}

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('btn')) {
        const button = e.target;
        const ripple = document.createElement('span');
        const rect = button.getBoundingClientRect();
        
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.className = 'ripple';
        ripple.style.width = ripple.style.height = `${size}px`;
        ripple.style.left = `${x}px`;
        ripple.style.top = `${y}px`;
        
        button.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    }
});

const style = document.createElement('style');
style.textContent = `
    .animated-title {
        animation: titleSlideIn 0.6s ease forwards;
    }
    
    @keyframes titleSlideIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .input-focus label {
        color: #3498db;
        transform: translateY(-5px) scale(0.9);
        transform-origin: top left;
        transition: all 0.3s ease;
    }
    
    .event-click {
        transform: scale(0.95) !important;
        opacity: 0.8 !important;
    }
    
    .valid-pulse {
        animation: validPulse 0.5s ease;
    }
    
    @keyframes validPulse {
        0% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.5); }
        70% { box-shadow: 0 0 0 10px rgba(46, 204, 113, 0); }
        100% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0); }
    }
    
    .invalid-shake {
        animation: invalidShake 0.5s ease;
    }
    
    @keyframes invalidShake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-3px); }
        20%, 40%, 60%, 80% { transform: translateX(3px); }
    }
    
    .btn {
        position: relative;
        overflow: hidden;
    }
    
    .ripple {
        position: absolute;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.4);
        transform: scale(0);
        animation: rippleEffect 0.6s linear;
        pointer-events: none;
    }
    
    @keyframes rippleEffect {
        to {
            transform: scale(2.5);
            opacity: 0;
        }
    }
    
    .cell-hover {
        background-color: rgba(52, 152, 219, 0.05);
    }
    
    .pulse {
        animation: pulse 0.3s ease-in-out;
    }
`;

document.head.appendChild(style);