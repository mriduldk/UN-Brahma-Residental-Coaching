<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password — Education Department, BTC</title>
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
    background: var(--maroon-dark);
    color: rgba(245,240,228,0.8);
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    letter-spacing: 0.05em;
  }
  .top-strip .inner {
    max-width: 1120px; margin: 0 auto; padding: 6px 24px;
    display: flex; justify-content: space-between; align-items: center;
    flex-wrap: wrap; gap: 6px;
  }
  .top-strip a { color: inherit; text-decoration: none; opacity: 0.75; }
  .top-strip a:hover { opacity: 1; }

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
  }
  .back-link:hover { color: var(--maroon-dark); text-decoration: underline; }

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
      radial-gradient(circle at 10% 90%, rgba(245,240,228,0.03), transparent 30%),
      radial-gradient(circle at 80% 20%, rgba(184,134,46,0.04), transparent 40%);
    pointer-events: none;
  }
  .auth-info > div{ position:relative; z-index:1; }
  .auth-info .tag{
    display:inline-block; font-family: 'JetBrains Mono', monospace;
    font-size: 11px; text-transform:uppercase; letter-spacing:0.05em;
    color: var(--gold-light); margin-bottom: 12px;
  }
  .auth-info h2{
    font-family: 'Source Serif 4', serif;
    font-size: 28px; line-height: 1.2; margin-bottom: 16px;
    font-weight: 600;
  }
  .auth-info p{
    font-size: 14.5px; opacity: 0.85; margin-bottom: 24px;
  }
  .auth-info ul{ list-style:none; }
  .auth-info li{
    margin-bottom: 12px; font-size: 13.5px;
    display:flex; align-items:flex-start; gap:8px; opacity: 0.9;
  }
  .auth-info .mk{ color: var(--gold-light); font-weight:700; }
  .seal{
    font-family: 'JetBrains Mono', monospace; font-size:10px;
    opacity: 0.5; margin-top: 40px; letter-spacing:0.05em;
  }

  .auth-form{
    padding: 44px 38px;
    background: var(--white);
  }
  .form-head{ margin-bottom: 28px; }
  .form-head .tag{
    display:inline-block; font-family: 'JetBrains Mono', monospace;
    font-size: 10px; text-transform:uppercase; letter-spacing:0.05em;
    color: var(--forest); margin-bottom: 6px;
  }
  .form-head h3{
    font-family: 'Source Serif 4', serif; font-size: 24px;
    color: var(--maroon-dark); margin-bottom: 4px;
  }
  .form-head p{ font-size: 13px; color: var(--ink-soft); }

  .field{ margin-bottom: 20px; position: relative; }
  .field label{
    display:block; font-size: 12.5px; font-weight: 600;
    margin-bottom: 6px; color: var(--ink);
  }
  .field input{
    width: 100%; padding: 10px 14px; font-size: 14px;
    font-family: inherit;
    border: 1px solid var(--line); background: var(--white);
    border-radius: 2px; color: var(--ink);
    transition: all 0.2s;
  }
  .field input:focus{
    outline:none; border-color: var(--maroon);
    box-shadow: 0 0 0 3px rgba(107,31,42,0.1);
  }
  .field.invalid input{ border-color: var(--error); background: #fdf5f5; }
  .err{
    display:none; color: var(--error); font-size: 11.5px;
    margin-top: 6px; font-weight: 500;
  }
  .field.invalid .err{ display:block; }
  .has-error .err{ display:block; }

  .btn-gold{
    width: 100%; background: var(--maroon);
    color: var(--white); border:none; padding: 12px;
    font-size: 14px; font-weight: 600; font-family: inherit;
    border-radius: 6px; cursor: pointer; transition: all 0.2s ease;
    display: flex; align-items:center; justify-content:center; gap:8px;
    margin-top: 8px;
    box-shadow: 0 4px 6px -1px rgba(2, 132, 199, 0.15);
  }
  .btn-gold:hover{ background: #0369a1; box-shadow: 0 6px 12px rgba(2, 132, 199, 0.25); }
  .btn-gold:disabled{ opacity: 0.7; cursor: not-allowed; }

  .divider{
    text-align:center; position:relative; margin: 32px 0 20px;
    font-size: 11.5px; font-weight: 600; color: var(--ink-soft);
    text-transform: uppercase; letter-spacing: 0.05em;
  }
  .divider::before{
    content:""; position:absolute; top:50%; left:0; right:0;
    height: 1px; background: var(--line); z-index: 1;
  }
  .divider span{
    position:relative; z-index: 2; background: var(--white); padding: 0 12px;
  }

  .switch-line{
    text-align: center; font-size: 13px; color: var(--ink-soft);
  }
  .switch-line a{
    color: var(--maroon); font-weight: 600;
    text-decoration: none;
  }
  .switch-line a:hover{ text-decoration: underline; }

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
          <span class="tag">Account Recovery</span>
          <h2>Forgot your password?</h2>
          <p>No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.</p>
          <ul>
            <li><span class="mk">✓</span> Secure reset link sent instantly</li>
            <li><span class="mk">✓</span> Ensure email matches your registration</li>
            <li><span class="mk">✓</span> Check your spam folder if delayed</li>
          </ul>
        </div>
        <div class="seal">REF: ED/BTC/04/2026 &middot; KOKRAJHAR</div>
      </div>

      <div class="auth-form">
        <div class="form-head">
          <span class="tag">Recover Password</span>
          <h3>Reset Password</h3>
          <p>Enter your registered email address.</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form id="forgotForm" method="POST" action="{{ route('password.email') }}" novalidate>
          @csrf
          
          <div class="field @error('email') has-error @enderror" id="f-email">
            <label for="email">Registered Email Address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" autocomplete="username" autofocus required />
            <div class="err">@error('email') {{ $message }} @else Please enter a valid email address. @enderror</div>
          </div>

          <button type="submit" class="btn-gold">Email Password Reset Link &rarr;</button>

          <div class="form-msg" id="forgotMsg" style="display:none; color: var(--forest); margin-top: 12px; font-weight: 600; font-size: 13px;"></div>
        </form>

        <div class="divider"><span>Or</span></div>
        
        <div class="switch-line">
          Remember your password? <a href="{{ route('login') }}">Back to Login</a>
        </div>

      </div>

    </div>
  </div>
</main>

<footer class="site-footer">
  © 2026 Education Department, Bodoland Territorial Council. All rights reserved.
</footer>

<script>
  const form = document.getElementById('forgotForm');
  const msg = document.getElementById('forgotMsg');

  function isValidEmail(v){
    const email = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return email.test(v.trim());
  }

  form.addEventListener('submit', e => {
    let valid = true;

    const emailField = document.getElementById('f-email');
    const emailVal = document.getElementById('email').value;
    if(!isValidEmail(emailVal)){
      emailField.classList.add('invalid');
      valid = false;
    } else {
      emailField.classList.remove('invalid');
    }

    if(!valid){
      e.preventDefault();
      msg.style.display = 'none';
    } else {
      msg.style.display = 'block';
      msg.textContent = 'Sending...';
      const btn = form.querySelector('.btn-gold');
      btn.disabled = true;
      btn.textContent = 'Please wait...';
    }
  });
</script>

</body>
</html>
