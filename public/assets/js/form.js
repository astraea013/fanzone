(function() {
  'use strict';

  // Form Management
  const FormManager = {
    init() {
      this.loginPanel = document.getElementById('login-panel');
      this.registerPanel = document.getElementById('register-panel');
      
      this.bindEvents();
      this.checkUrlParams();
    },

    bindEvents() {
      // Panel switching
      document.querySelectorAll('[data-switch]').forEach(link => {
        link.addEventListener('click', (e) => {
          e.preventDefault();
          const target = e.target.getAttribute('data-switch');
          this.switchPanel(target);
        });
      });

      // Password strength
      const passwordInput = document.querySelector('input[name="password"]');
      if (passwordInput) {
        passwordInput.addEventListener('input', (e) => {
          this.checkPasswordStrength(e.target.value);
        });
      }

      // Form submissions
      document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', (e) => this.handleSubmit(e));
      });
    },

    switchPanel(panel) {
      const panels = document.querySelectorAll('.auth-panel');
      panels.forEach(p => {
        p.classList.remove('active');
        p.style.display = 'none';
      });

      const target = document.getElementById(`${panel}-panel`);
      if (target) {
        target.style.display = 'flex';
        setTimeout(() => target.classList.add('active'), 10);
        
        const url = new URL(window.location);
        url.searchParams.set('action', panel === 'register' ? 'register' : 'login');
        window.history.pushState({}, '', url);
      }
    },

    checkPasswordStrength(password) {
      const strengthBar = document.querySelector('.password-strength');
      const strengthFill = document.querySelector('.strength-bar');
      const strengthText = document.querySelector('.strength-text');
      
      if (!strengthBar || !strengthFill || !strengthText) return;

      strengthBar.classList.add('active');
      strengthText.classList.add('active');

      let strength = 0;
      if (password.length >= 6) strength++;
      if (password.length >= 10) strength++;
      if (/[A-Z]/.test(password)) strength++;
      if (/[0-9]/.test(password)) strength++;
      if (/[^A-Za-z0-9]/.test(password)) strength++;

      strengthFill.className = 'strength-bar';
      
      if (password.length === 0) {
        strengthBar.classList.remove('active');
        strengthText.classList.remove('active');
        return;
      }

      if (strength <= 2) {
        strengthFill.classList.add('weak');
        strengthText.textContent = 'Weak password';
        strengthText.style.color = 'var(--error)';
      } else if (strength <= 4) {
        strengthFill.classList.add('medium');
        strengthText.textContent = 'Medium strength';
        strengthText.style.color = 'var(--warning)';
      } else {
        strengthFill.classList.add('strong');
        strengthText.textContent = 'Strong password';
        strengthText.style.color = 'var(--success)';
      }
    },

    handleSubmit(e) {
      const btn = e.target.querySelector('.btn-submit');
      const originalText = btn.textContent;
      btn.classList.add('loading');
      btn.textContent = '';
      
      setTimeout(() => {
        btn.classList.remove('loading');
        btn.textContent = originalText;
      }, 2000);
    },

    checkUrlParams() {
      const params = new URLSearchParams(window.location.search);
      const action = params.get('action');
      const panel = params.get('panel');
      
      if (action === 'register' || panel === 'register') {
        this.switchPanel('register');
      } else {
        if (this.loginPanel) {
          this.loginPanel.classList.add('active');
          this.loginPanel.style.display = 'flex';
        }
      }
    }
  };

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  function init() {
    FormManager.init();
  }

  // Expose global function for panel switching
  window.switchPanel = function(panel) {
    FormManager.switchPanel(panel);
  };
})();