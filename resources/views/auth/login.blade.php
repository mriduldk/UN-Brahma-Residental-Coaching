<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Candidate Login — Education Department, BTC</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Tiro+Devanagari+Hindi:ital@0;1&family=Source+Serif+4:ital,opsz,wght@0,8..60,400;0,8..60,600;0,8..60,700;1,8..60,400&family=IBM+Plex+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap');

  :root{
    --maroon:       #0284c7; /* Primary Sky Blue */
    --maroon-dark:  #0c4a6e; /* Deep Ocean Navy */
    --forest:       #0e7490; /* Cyan-700 */
    --gold:         #0ea5e9; /* Sky Blue */
    --gold-light:   #38bdf8; /* Lighter Sky Blue */
    --paper:        #f0f9ff; /* Soft ice-blue background */
    --paper-dark:   #e0f2fe; /* Light ice-blue background for tables/cards */
    --ink:          #0c4a6e; /* Dark Ocean Navy text */
    --ink-soft:     #4b6a8a; /* Slate Blue text */
    --line:         #bae6fd; /* Soft sky blue border color */
    --white:        #ffffff; /* Clean White */
    --error:        #ef4444; /* Clean Red error */
    /* Adapted Aronai palette for light blue theme */
    --ar-red:       #0ea5e9;
    --ar-black:     #0c4a6e;
    --ar-yellow:    #38bdf8;
    --ar-green:     #7dd3fc;
    --ar-cream:     #eff6ff;
  }

  *{box-sizing:border-box; margin:0; padding:0;}

  body{
    background: var(--paper);
    color: var(--ink);
    font-family: 'IBM Plex Sans', sans-serif;
    line-height: 1.5;
    background-image:
      radial-gradient(circle at 20% 10%, rgba(2, 132, 199, 0.04), transparent 40%),
      radial-gradient(circle at 80% 80%, rgba(56, 189, 248, 0.04), transparent 40%);
    min-height: 100vh;
  }

  a{ color: inherit; }
  .wrap{ max-width: 1100px; margin: 0 auto; padding: 0 24px; }

  /* ===== TOP STRIP ===== */
  .top-strip {
    background: var(--paper-dark);
    color: var(--maroon-dark);
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    letter-spacing: 0.05em;
    border-bottom: 1px solid rgba(2, 132, 199, 0.15);
  }
  .top-strip .inner {
    max-width: 1120px; margin: 0 auto; padding: 6px 24px;
    display: flex; justify-content: space-between; align-items: center;
    flex-wrap: wrap; gap: 6px;
  }
  .top-strip a { color: inherit; text-decoration: none; opacity: 0.85; }
  .top-strip a:hover { opacity: 1; color: var(--maroon); }

  /* ===== HEADER ===== */
  .site-header {
    background: var(--white);
    border-bottom: 3px solid var(--maroon);
  }
  .header-inner {
    max-width: 1120px; margin: 0 auto; padding: 20px 24px;
    display: flex; align-items: center; gap: 18px;
    position: relative;
    flex-wrap: wrap;
  }
  .emblem {
    width: 56px; height: 56px; 
    display: flex; align-items: center; justify-content: center;
  }
  .header-text h1 {
    font-family: 'Source Serif 4', serif;
    font-weight: 700;
    font-size: clamp(17px, 2.5vw, 26px);
    color: var(--maroon-dark);
    line-height: 1.2;
  }
  .header-text p.sub {
    font-size: 12.5px; color: var(--ink-soft);
    margin-top: 3px; font-style: italic;
  }
  .header-text .dept-tag {
    display: inline-block; margin-top: 6px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px; letter-spacing: 0.1em;
    text-transform: uppercase; color: var(--forest);
    font-weight: 600;
    border-left: 3px solid var(--ar-red);
    padding-left: 8px;
  }
  .back-link {
    font-size: 13px; font-weight: 600; color: var(--maroon);
    text-decoration: none; display: flex; align-items: center; gap: 6px;
    margin-left: auto;
    transition: color 0.15s ease;
  }
  .back-link:hover { color: var(--maroon-dark); text-decoration: underline; }
  .back-link{
    font-size: 13px;
    font-weight: 600;
    color: var(--maroon);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .back-link:hover{ color: var(--maroon-dark); text-decoration: underline; }

  /* ===== ARONAI BORDER STRIP ===== */
  .aronai-strip{
    height: 26px;
    background-image: url('{{ asset('assets/aronai-border.jpeg') }}');
    background-repeat: repeat-x;
    background-size: auto 100%;
    background-position: center;
    border-bottom: 1px solid var(--line);
  }

  /* ===== AUTH LAYOUT ===== */
  .auth-shell{
    padding: 48px 0 64px;
    display: flex;
    justify-content: center;
  }
  .auth-grid{
    width: 100%;
    max-width: 980px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    background: var(--white);
    border: 1px solid var(--line);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.05), 0 10px 15px -3px rgba(15, 23, 42, 0.1);
  }

  .auth-info{
    background: var(--maroon-dark);
    color: var(--paper);
    padding: 44px 38px;
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .auth-info::before{
    content:"";
    position:absolute; inset:0;
    background:
      radial-gradient(circle at 85% 15%, rgba(14, 165, 233, 0.15), transparent 45%),
      radial-gradient(circle at 10% 90%, rgba(2, 132, 199, 0.2), transparent 50%);
    pointer-events:none;
  }
  .auth-info .tag{
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--gold-light);
    font-weight: 600;
    position: relative;
  }
  .auth-info h2{
    font-family: 'Source Serif 4', serif;
    font-weight: 700;
    font-size: 26px;
    line-height: 1.3;
    margin-top: 14px;
    position: relative;
  }
  .auth-info p{
    font-size: 13.5px;
    color: rgba(240,249,255,0.78);
    margin-top: 14px;
    max-width: 320px;
    position: relative;
  }
  .auth-info ul{
    list-style: none;
    margin-top: 28px;
    position: relative;
  }
  .auth-info li{
    font-size: 13px;
    padding: 9px 0;
    border-top: 1px solid rgba(240,249,255,0.14);
    display: flex;
    align-items: flex-start;
    gap: 9px;
  }
  .auth-info li:last-child{ border-bottom: 1px solid rgba(240,249,255,0.14); }
  .auth-info li .mk{
    color: var(--gold-light);
    font-weight: 700;
    flex-shrink: 0;
  }
  .auth-info .seal{
    font-family: 'JetBrains Mono', monospace;
    font-size: 10.5px;
    color: rgba(240,249,255,0.5);
    margin-top: 30px;
    position: relative;
    letter-spacing: 0.04em;
  }

  .auth-form{
    padding: 44px 40px;
  }
  .auth-form .form-head .tag{
    display:inline-block;
    font-family: 'JetBrains Mono', monospace;
    font-size: 10.5px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--forest);
    font-weight: 700;
    background: rgba(3,105,161,0.08);
    padding: 3px 9px;
    border-radius: 2px;
  }
  .auth-form h3{
    font-family: 'Source Serif 4', serif;
    font-weight: 700;
    font-size: 24px;
    color: var(--maroon-dark);
    margin-top: 12px;
  }
  .auth-form .form-head p{
    font-size: 13px;
    color: var(--ink-soft);
    margin-top: 6px;
  }

  .field{ margin-top: 20px; }
  .field label{
    display:block;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--ink);
    margin-bottom: 6px;
    letter-spacing: 0.01em;
  }
  .field label .opt{
    font-weight: 400;
    color: var(--ink-soft);
    font-size: 11px;
    text-transform: none;
  }
  .field input, .field select{
    width: 100%;
    padding: 11px 13px;
    border: 1px solid var(--line);
    background: var(--white);
    font-family: 'IBM Plex Sans', sans-serif;
    font-size: 14px;
    color: var(--ink);
    border-radius: 6px;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
  }
  .field input:focus, .field select:focus{
    outline: none;
    border-color: var(--maroon);
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
  }
  .field .hint{
    font-size: 11px;
    color: var(--ink-soft);
    margin-top: 5px;
  }
  .field .err{
    font-size: 11.5px;
    color: var(--error);
    margin-top: 5px;
    display: none;
    font-weight: 500;
  }
  .field.invalid input{ border-color: var(--error); }
  .field.invalid .err{ display: block; }

  .pw-row{ position: relative; }
  .pw-toggle{
    position: absolute;
    right: 12px;
    top: 38px;
    background: none;
    border: none;
    font-family: 'JetBrains Mono', monospace;
    font-size: 10.5px;
    color: var(--ink-soft);
    cursor: pointer;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-weight: 600;
  }
  .pw-toggle:hover{ color: var(--maroon); }

  .row-between{
    display:flex; align-items:center; justify-content:space-between;
    margin-top: 16px;
    font-size: 12.5px;
  }
  .remember{ display:flex; align-items:center; gap: 7px; color: var(--ink-soft); }
  .remember input{ accent-color: var(--maroon); width:14px; height:14px; }
  .forgot{ color: var(--maroon); text-decoration:none; font-weight:600; }
  .forgot:hover{ text-decoration: underline; }

  .btn-gold{
    width: 100%;
    margin-top: 24px;
    background: var(--maroon);
    color: var(--white);
    border: none;
    padding: 13px 20px;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 0.02em;
    cursor: pointer;
    border-radius: 6px;
    box-shadow: 0 4px 6px -1px rgba(2, 132, 199, 0.15);
    transition: all 0.2s ease;
  }
  .btn-gold:hover{ background: #0369a1; box-shadow: 0 6px 12px rgba(2, 132, 199, 0.25); }
  .btn-gold:active{ transform: scale(0.99); }

  .btn-outline{
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    flex: 1;
    background: var(--white);
    color: var(--ink);
    border: 1px solid var(--line);
    padding: 10px 16px;
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    border-radius: 6px;
    text-decoration: none;
    transition: all 0.2s ease;
  }
  .btn-outline:hover{
    background: var(--paper-dark);
    border-color: var(--maroon);
  }

  .divider{
    display:flex; align-items:center; gap:12px;
    margin: 22px 0;
    color: var(--ink-soft);
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .divider::before, .divider::after{
    content:"";
    flex:1;
    height: 1px;
    background: var(--line);
  }

  .switch-line{
    text-align:center;
    font-size: 13px;
    color: var(--ink-soft);
  }
  .switch-line a{
    color: var(--maroon);
    font-weight: 700;
    text-decoration: none;
  }
  .switch-line a:hover{ text-decoration: underline; }

  .notice-box{
    margin-top: 22px;
    background: rgba(14,165,233,0.08);
    border-left: 3px solid var(--gold);
    padding: 10px 13px;
    font-size: 11.5px;
    color: var(--ink-soft);
    line-height: 1.5;
  }

  .form-msg{
    display:none;
    margin-top: 18px;
    padding: 10px 13px;
    font-size: 12.5px;
    border-left: 3px solid var(--forest);
    background: rgba(3,105,161,0.08);
    color: var(--forest);
    font-weight: 600;
  }

  footer.site-footer{
    text-align:center;
    padding: 22px 0 30px;
    font-size: 11.5px;
    color: var(--ink-soft);
  }

  @media (max-width: 760px){
    .auth-grid{ grid-template-columns: 1fr; max-width: 460px; }
    .auth-info{ display:none; }
    .auth-form{ padding: 34px 26px; }
  }
</style>
</head>
<body>

<x-portal-header />

  

  <main class="auth-shell">
    <div class="wrap" style="display:flex; justify-content:center; padding:0;">
      <div class="auth-grid">

        <div class="auth-info">
          <div>
            <span class="tag">Candidate Portal</span>
            <h2>Welcome back. Sign in to continue your application.</h2>
            <p>Access your saved drafts, track submitted applications, and download your acknowledgement receipts.</p>
            <ul>
              <li><span class="mk">✓</span> Apply for multiple posts in one session</li>
              <li><span class="mk">✓</span> Auto-saved application progress</li>
              <li><span class="mk">✓</span> Real-time status &amp; admit card alerts</li>
              <li><span class="mk">✓</span> Secure document upload</li>
            </ul>
          </div>
        </div>

        <div class="auth-form">
          <div class="form-head">
            <span class="tag">Sign In</span>
            <h3>Candidate Login</h3>
            <p>Enter your registered mobile number or email to continue.</p>
          </div>

          <x-auth-session-status class="mb-4" :status="session('status')" />

          <form id="loginForm" method="POST" action="{{ route('login') }}" novalidate>
            @csrf
            
            <div class="field @error('email') invalid @enderror" id="f-id">
              <label for="loginId">Registered Mobile Number or Email</label>
              <input type="email" id="loginId" name="email" value="{{ old('email') }}" placeholder="e.g. 98xxxxxxx10 or name@email.com" autocomplete="username" />
              <div class="err">@error('email') {{ $message }} @else Please enter a valid mobile number or email address. @enderror</div>
            </div>

            <div class="field pw-row @error('password') invalid @enderror" id="f-pw">
              <label for="loginPw">Password</label>
              <input type="password" id="loginPw" name="password" placeholder="Enter password" autocomplete="current-password" />
              <button type="button" class="pw-toggle" data-target="loginPw">Show</button>
              <div class="err">@error('password') {{ $message }} @else Password must be at least 6 characters. @enderror</div>
            </div>

            <div class="row-between">
              <label class="remember"><input type="checkbox" name="remember" id="remember_me" /> Keep me signed in</label>
              @if (Route::has('password.request'))
                <a class="forgot" href="{{ route('password.request') }}">Forgot password?</a>
              @endif
            </div>

            <button type="submit" class="btn-gold">Sign In →</button>

            <div class="form-msg" id="loginMsg"></div>
          </form>

          <div class="divider">Or Continue With</div>
          
          <div style="display: flex; gap: 12px; margin-top: 16px; margin-bottom: 24px;">
            <a href="{{ route('login.otp') }}" class="btn-outline">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-smartphone"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/></svg>
              Login with OTP
            </a>
            <a href="{{ route('google.redirect') }}" class="btn-outline">
              <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" style="width: 16px; height: 16px;">
              Google
            </a>
          </div>

          <div class="divider">New Candidate</div>

          <div class="switch-line">
            Don't have an account yet? <a href="{{ route('register') }}">Register here</a>
          </div>

          <div class="notice-box">
            This is the only official portal for Upendra Nath Brahma Residential Free Coaching. The Education Department, BTC never requests OTP or passwords over phone calls.
          </div>
        </div>

      </div>
    </div>
  </main>

  <footer class="site-footer">
    © 2026 Education Department, Bodoland Territorial Council. All rights reserved.
  </footer>

<script>
  document.querySelectorAll('.pw-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.target);
      const isPw = input.type === 'password';
      input.type = isPw ? 'text' : 'password';
      btn.textContent = isPw ? 'Hide' : 'Show';
    });
  });

  const form = document.getElementById('loginForm');
  const msg = document.getElementById('loginMsg');

  function isValidId(v){
    const mobile = /^[6-9]\d{9}$/;
    const email = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return mobile.test(v.trim()) || email.test(v.trim());
  }

  form.addEventListener('submit', e => {
    let valid = true;

    const idField = document.getElementById('f-id');
    const idVal = document.getElementById('loginId').value;
    if(!isValidId(idVal)){
      idField.classList.add('invalid');
      valid = false;
    } else {
      idField.classList.remove('invalid');
    }

    const pwField = document.getElementById('f-pw');
    const pwVal = document.getElementById('loginPw').value;
    if(pwVal.length < 6){
      pwField.classList.add('invalid');
      valid = false;
    } else {
      pwField.classList.remove('invalid');
    }

    if(!valid){
      e.preventDefault();
      msg.style.display = 'none';
    } else {
      msg.style.display = 'block';
      msg.textContent = 'Signing in...';
      const btn = form.querySelector('.btn-gold');
      btn.disabled = true;
      btn.textContent = 'Please wait...';
    }
  });
</script>

</body>
</html>
