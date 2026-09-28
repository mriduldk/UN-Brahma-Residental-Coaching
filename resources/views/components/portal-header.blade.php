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
  max-width: 1120px; margin: 0 auto; padding: 6px 24px;
  display: flex; justify-content: space-between; align-items: center;
  flex-wrap: wrap; gap: 6px;
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
  max-width: 1120px; margin: 0 auto; padding: 20px 24px;
  display: flex; align-items: center; gap: 18px;
  position: relative;
  flex-wrap: wrap;
  box-sizing: border-box;
}
.emblem {
  width: 48px; height: 56px; 
  display: flex; align-items: center; justify-content: center;
}
.header-text h1 {
  font-family: 'Source Serif 4', serif;
  font-weight: 700;
  font-size: clamp(17px, 2.5vw, 26px);
  color: var(--maroon-dark, #0c4a6e);
  line-height: 1.2;
  margin: 0;
}
.header-text p.sub {
  font-size: 12.5px; color: var(--ink-soft, #4b6a8a);
  margin-top: 3px; font-style: italic;
  margin-bottom: 0;
}
.header-text .dept-tag {
  display: inline-block; margin-top: 6px;
  font-family: 'JetBrains Mono', monospace;
  font-size: 10px; letter-spacing: 0.1em;
  text-transform: uppercase; color: var(--forest, #0e7490);
  font-weight: 600;
  border-left: 3px solid var(--ar-red, #0ea5e9);
  padding-left: 8px;
}

nav.primary {
  background: rgba(224, 242, 254, 0.95);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid rgba(2, 132, 199, 0.15);
  position: sticky; top: 0; z-index: 100;
  box-shadow: 0 4px 12px rgba(12, 74, 110, 0.05);
  box-sizing: border-box;
}
nav.primary .inner {
  max-width: 1120px; margin: 0 auto;
  display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;
  box-sizing: border-box;
}
.nav-container { display: flex; flex: 1; justify-content: space-between; align-items: center; box-sizing: border-box; }
.mobile-menu-toggle {
  display: none; background: transparent; border: none; color: var(--maroon-dark, #0c4a6e);
  cursor: pointer; padding: 14px 24px; font-size: 14px; font-weight: 600;
  font-family: inherit; align-items: center; gap: 8px; width: 100%; justify-content: center;
  border-bottom: 1px solid rgba(2,132,199,0.1); transition: color 0.15s;
  box-sizing: border-box;
}
.mobile-menu-toggle:hover { color: var(--maroon, #0284c7); }
.nav-links { display: flex; flex-wrap: wrap; box-sizing: border-box; }
.nav-actions { display: flex; align-items: center; padding-right: 20px; gap: 12px; box-sizing: border-box; }
.nav-actions a {
  border: none !important;
  padding: 8px 18px !important;
  border-radius: 4px;
  box-sizing: border-box;
  text-decoration: none;
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
  display: block; padding: 13px 20px;
  color: var(--maroon-dark, #0c4a6e);
  text-decoration: none; font-size: 13px; font-weight: 600;
  letter-spacing: 0.02em;
  border-right: 1px solid rgba(2, 132, 199, 0.15);
  transition: background 0.15s, color 0.15s;
  box-sizing: border-box;
}
nav.primary a:hover { background: rgba(2, 132, 199, 0.08); color: var(--maroon, #0284c7); }
nav.primary a.active { background: var(--gold, #0ea5e9); color: var(--maroon-dark, #0c4a6e); font-weight: 700; }

@media (max-width: 900px) {
  nav.primary .inner { flex-direction: column; padding: 0; align-items: stretch; }
  .mobile-menu-toggle { display: flex; }
  .nav-container { flex-direction: column; display: none; }
  .nav-container.active { display: flex; }
  .nav-links { flex-direction: column; width: 100%; text-align: center; }
  nav.primary a { border-right: none; border-bottom: 1px solid rgba(255,255,255,0.05); }
  .nav-actions { width: 100%; justify-content: center; padding: 16px; gap: 12px; }
  .btn-nav-login, .btn-nav-register { flex: 1; justify-content: center; }
  .header-inner { flex-direction: column; text-align: center; padding: 24px 20px; }
  .site-header h1 { font-size: 22px; }
}

@media (max-width: 600px) {
  .top-strip .inner { flex-direction: column; text-align: center; justify-content: center; }
}
</style>

<!-- TOP STRIP -->
<div class="top-strip">
  <div class="inner">
    <span>GOVERNMENT OF BTC &nbsp;|&nbsp; BODOLAND TERRITORIAL COUNCIL &nbsp;|&nbsp; DEPARTMENT OF EDUCATION</span>
    <span id="liveClock" style="letter-spacing:0.04em;"></span>
  </div>
</div>

<!-- HEADER -->
<header class="site-header">
  <div class="header-inner">
    <a href="{{ url('/') }}" style="display:flex; align-items:center; gap:18px; text-decoration:none; color:inherit;">
      <img src="/image/logo/logo-em.png" alt="BTC Emblem" class="emblem">
      <div class="header-text">
        <h1>Upendra Nath Brahma Residential Free Coaching</h1>
        <p class="sub">Department of Education, Bodoland Territorial Council · Kokrajhar, Assam</p>
        <span class="dept-tag">Engineering · Medical · APSC · UPSC · 2026</span>
      </div>
    </a>
  </div>
</header>

<!-- NAV -->
<nav class="primary" role="navigation" aria-label="Main navigation">
  <div class="inner">
    <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle navigation">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
      <span>Menu</span>
    </button>
    <div class="nav-container" id="navContainer">
      <div class="nav-links">
        <a href="{{ request()->is('/') ? '#' : url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a>
        <a href="{{ request()->is('/') ? '#courses' : url('/#courses') }}">Disciplines</a>
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

    // Mobile Menu Toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const navContainer = document.getElementById('navContainer');
    if (mobileMenuToggle && navContainer) {
        mobileMenuToggle.addEventListener('click', () => {
            navContainer.classList.toggle('active');
        });
    }
});
</script>
