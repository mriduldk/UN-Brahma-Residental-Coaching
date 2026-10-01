<style>
/* Portal Header & Navigation Styles */
.top-strip {
  background: var(--paper-dark, #e0f2fe);
  color: var(--maroon-dark, #0c4a6e);
  font-family: 'JetBrains Mono', monospace;
  font-size: 11px;
  letter-spacing: 0.05em;
  border-bottom: 1px solid rgba(2, 132, 199, 0.15);
  box-sizing: border-box;
}
.top-strip .inner {
  max-width: 1120px; margin: 0 auto; padding: 6px 20px;
  display: flex; justify-content: space-between; align-items: center;
  flex-wrap: wrap; gap: 8px;
  box-sizing: border-box;
}
.top-strip a { color: inherit; text-decoration: none; opacity: 0.85; }
.top-strip a:hover { opacity: 1; color: var(--maroon, #0284c7); }

.site-header {
  background: var(--white, #ffffff);
  border-bottom: 3px solid var(--maroon, #0284c7);
  box-sizing: border-box;
}
.header-inner {
  max-width: 1120px; margin: 0 auto; padding: 18px 20px;
  display: flex; align-items: center; gap: 16px;
  position: relative;
  box-sizing: border-box;
}
.emblem {
  width: 46px; height: 54px; 
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.header-text h1 {
  font-family: 'Source Serif 4', serif;
  font-weight: 700;
  font-size: clamp(17px, 2.5vw, 24px);
  color: var(--maroon-dark, #0c4a6e);
  line-height: 1.2;
  margin: 0;
}
.header-text p.sub {
  font-size: 12px; color: var(--ink-soft, #4b6a8a);
  margin-top: 3px; font-style: italic;
  margin-bottom: 0;
}

nav.primary {
  background: rgba(224, 242, 254, 0.96);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(2, 132, 199, 0.15);
  position: sticky; top: 0; z-index: 100;
  box-shadow: 0 4px 12px rgba(12, 74, 110, 0.05);
  box-sizing: border-box;
}
nav.primary .inner {
  max-width: 1120px; margin: 0 auto;
  display: flex; justify-content: space-between; align-items: center;
  box-sizing: border-box;
  position: relative;
}
.nav-container { 
  display: flex; flex: 1; justify-content: space-between; align-items: center; 
  box-sizing: border-box; 
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.mobile-menu-toggle {
  display: none; background: transparent; border: none; color: var(--maroon-dark, #0c4a6e);
  cursor: pointer; padding: 12px 20px; font-size: 13.5px; font-weight: 600;
  font-family: inherit; align-items: center; gap: 8px; width: 100%; justify-content: space-between;
  border-bottom: 1px solid rgba(2,132,199,0.1); transition: color 0.15s, background 0.15s;
  box-sizing: border-box;
}
.mobile-menu-toggle:hover { color: var(--maroon, #0284c7); background: rgba(2, 132, 199, 0.05); }

/* Hamburger animated icon */
.hamburger-icon {
  width: 20px; height: 14px;
  position: relative;
  display: inline-flex;
  flex-direction: column;
  justify-content: space-between;
}
.hamburger-icon span {
  display: block; width: 100%; height: 2px;
  background: var(--maroon-dark, #0c4a6e);
  border-radius: 2px;
  transition: transform 0.25s ease, opacity 0.25s ease, background 0.2s ease;
}
.mobile-menu-toggle.active .hamburger-icon span:nth-child(1) {
  transform: translateY(6px) rotate(45deg);
  background: var(--maroon, #0284c7);
}
.mobile-menu-toggle.active .hamburger-icon span:nth-child(2) {
  opacity: 0;
}
.mobile-menu-toggle.active .hamburger-icon span:nth-child(3) {
  transform: translateY(-6px) rotate(-45deg);
  background: var(--maroon, #0284c7);
}

.nav-links { display: flex; flex-wrap: wrap; box-sizing: border-box; }
.nav-actions { display: flex; align-items: center; padding-right: 20px; gap: 10px; box-sizing: border-box; }
.nav-actions a {
  border: none !important;
  padding: 8px 16px !important;
  border-radius: 4px;
  box-sizing: border-box;
  text-decoration: none;
  font-size: 12.5px;
}
.btn-nav-login {
  background: transparent; border: 1px solid rgba(2, 132, 199, 0.4) !important;
  color: var(--maroon, #0284c7) !important;
  transition: all 0.2s ease;
}
.btn-nav-login:hover { border-color: var(--maroon-dark, #0c4a6e) !important; color: var(--maroon-dark, #0c4a6e) !important; background: rgba(2, 132, 199, 0.05); }
.btn-nav-register {
  background: var(--maroon, #0284c7) !important; color: var(--white, #ffffff) !important; font-weight: 700 !important;
  transition: all 0.2s ease;
}
.btn-nav-register:hover { background: var(--maroon-dark, #0c4a6e) !important; }
nav.primary a {
  display: block; padding: 12px 18px;
  color: var(--maroon-dark, #0c4a6e);
  text-decoration: none; font-size: 13px; font-weight: 600;
  letter-spacing: 0.02em;
  border-right: 1px solid rgba(2, 132, 199, 0.15);
  transition: background 0.15s, color 0.15s;
  box-sizing: border-box;
}
nav.primary a:hover { background: rgba(2, 132, 199, 0.08); color: var(--maroon, #0284c7); }
nav.primary a.active { background: var(--gold, #0ea5e9); color: var(--maroon-dark, #0c4a6e); font-weight: 700; }

/* Medium screens (960px - 1120px) */
@media (max-width: 1120px) and (min-width: 961px) {
  nav.primary a { padding: 12px 13px; font-size: 12.5px; }
  .nav-actions { padding-right: 14px; gap: 8px; }
  .nav-actions a { padding: 7px 12px !important; font-size: 12px; }
}

/* Tablet & Mobile Screens (<= 960px) */
@media (max-width: 960px) {
  nav.primary .inner { flex-direction: column; padding: 0; align-items: stretch; }
  .mobile-menu-toggle { display: flex; }
  .nav-container { 
    flex-direction: column; 
    max-height: 0; 
    opacity: 0; 
    overflow: hidden; 
    visibility: hidden;
    background: rgba(240, 249, 255, 0.98);
    border-top: 1px solid rgba(2, 132, 199, 0.12);
  }
  .nav-container.active { 
    max-height: 480px; 
    opacity: 1; 
    visibility: visible;
  }
  .nav-links { flex-direction: column; width: 100%; text-align: left; }
  nav.primary a { 
    border-right: none; 
    border-bottom: 1px solid rgba(2, 132, 199, 0.1); 
    padding: 12px 20px;
    font-size: 13.5px;
  }
  .nav-actions { 
    width: 100%; 
    justify-content: stretch; 
    padding: 14px 20px 18px; 
    gap: 10px; 
  }
  .btn-nav-login, .btn-nav-register { 
    flex: 1; 
    text-align: center;
    padding: 10px 14px !important;
  }
  .header-inner { padding: 14px 20px; gap: 12px; }
  .header-text h1 { font-size: 19px; }
  .header-text p.sub { font-size: 11.5px; }
}

@media (max-width: 640px) {
  .top-strip .inner { 
    padding: 5px 16px;
    font-size: 10px;
  }
  .header-inner { padding: 12px 16px; }
  .emblem { width: 38px; height: 46px; }
  .header-text h1 { font-size: 16.5px; }
  .header-text p.sub { font-size: 11px; }
}
</style>

<!-- TOP STRIP -->
<div class="top-strip">
  <div class="inner">
    <span>GOVERNMENT OF BTC &nbsp;|&nbsp; DEPARTMENT OF EDUCATION</span>
    <span id="liveClock" style="letter-spacing:0.04em;"></span>
  </div>
</div>

<!-- HEADER -->
<header class="site-header">
  <div class="header-inner">
    <a href="{{ url('/') }}" style="display:flex; align-items:center; gap:14px; text-decoration:none; color:inherit;">
      <img src="{{ asset('image/logo/logo-em.png') }}" alt="BTC Emblem" class="emblem">
      <div class="header-text">
        <h1>Upendra Nath Brahma Residential Free Coaching</h1>
        <p class="sub">Department of Education, Bodoland Territorial Council · Kokrajhar, Assam</p>
      </div>
    </a>
  </div>
</header>

<!-- NAV -->
<nav class="primary" role="navigation" aria-label="Main navigation">
  <div class="inner">
    <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle navigation menu" aria-expanded="false">
      <span style="display:flex; align-items:center; gap:8px;">
        <span class="hamburger-icon">
          <span></span>
          <span></span>
          <span></span>
        </span>
        <span>Navigation Menu</span>
      </span>
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
    </button>
    <div class="nav-container" id="navContainer">
      <div class="nav-links">
        <a href="{{ request()->is('/') ? '#' : url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a>
        <a href="{{ request()->is('/') ? '#institutes' : url('/#institutes') }}">Institutes</a>
        <a href="{{ request()->is('/') ? '#programmes' : url('/#programmes') }}">Programmes</a>
        <a href="{{ request()->is('/') ? '#documents' : url('/#documents') }}">Notices</a>
        <a href="{{ request()->is('/') ? '#instructions' : url('/#instructions') }}">How to Apply</a>
        <a href="{{ route('help') }}" class="{{ request()->routeIs('help') ? 'active' : '' }}">Helpdesk</a>
      </div>
      <div class="nav-actions">
        @auth
          <a href="{{ url('/dashboard') }}" class="btn-nav-register">Dashboard</a>
        @else
          <a href="{{ route('login') }}" class="btn-nav-login">Login</a>
          <a href="{{ route('register') }}" class="btn-nav-register">Register</a>
        @endauth
      </div>
    </div>
  </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Clock
    function updateClock() {
        const liveClockEl = document.getElementById('liveClock');
        if (!liveClockEl) return;
        const d = new Date();
        const s = d.toLocaleDateString('en-IN',{weekday:'short',day:'2-digit',month:'short',year:'numeric'}) +
                  '  ' + d.toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit',second:'2-digit',hour12:true});
        liveClockEl.textContent = s;
    }
    updateClock(); 
    setInterval(updateClock, 1000);

    // Responsive Mobile Menu Toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const navContainer = document.getElementById('navContainer');
    if (mobileMenuToggle && navContainer) {
        function toggleMenu() {
            const isOpen = navContainer.classList.toggle('active');
            mobileMenuToggle.classList.toggle('active', isOpen);
            mobileMenuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        }

        function closeMenu() {
            navContainer.classList.remove('active');
            mobileMenuToggle.classList.remove('active');
            mobileMenuToggle.setAttribute('aria-expanded', 'false');
        }

        mobileMenuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMenu();
        });

        // Close menu when clicking on any nav link
        navContainer.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                closeMenu();
            });
        });

        // Close on click outside
        document.addEventListener('click', (e) => {
            if (!mobileMenuToggle.contains(e.target) && !navContainer.contains(e.target)) {
                closeMenu();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeMenu();
            }
        });
    }
});
</script>
