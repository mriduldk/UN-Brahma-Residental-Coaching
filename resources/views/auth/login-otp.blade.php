<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>OTP Login — Education Department, BTC</title>
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
      radial-gradient(circle at 85% 15%, rgba(184,134,46,0.18), transparent 45%),
      radial-gradient(circle at 10% 90%, rgba(31,77,58,0.35), transparent 50%);
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
    color: rgba(245,240,228,0.78);
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
    border-top: 1px solid rgba(245,240,228,0.14);
    display: flex;
    align-items: flex-start;
    gap: 9px;
  }
  .auth-info li:last-child{ border-bottom: 1px solid rgba(245,240,228,0.14); }
  .auth-info li .mk{
    color: var(--gold-light);
    font-weight: 700;
    flex-shrink: 0;
  }
  .auth-info .seal{
    font-family: 'JetBrains Mono', monospace;
    font-size: 10.5px;
    color: rgba(245,240,228,0.5);
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
    background: rgba(31,77,58,0.08);
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
  .field input{
    width: 100%;
    padding: 11px 13px;
    border: 1px solid var(--line);
    background: var(--paper);
    font-family: 'IBM Plex Sans', sans-serif;
    font-size: 14px;
    color: var(--ink);
    border-radius: 2px;
    transition: border-color 0.15s ease, background 0.15s ease;
  }
  .field input:focus{
    outline: none;
    border-color: var(--maroon);
    background: var(--white);
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
  
  .input-group { position: relative; }
  .input-prefix {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 14px;
    font-weight: 600;
    color: var(--ink-soft);
  }
  .input-group input { padding-left: 45px; }

  .resend-btn {
    background: none;
    border: none;
    color: var(--maroon);
    font-weight: 600;
    font-size: 12.5px;
    cursor: pointer;
  }
  .resend-btn:hover { text-decoration: underline; }
  
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
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }
  .btn-gold:hover:not(:disabled){ background: #0369a1; box-shadow: 0 6px 12px rgba(2, 132, 199, 0.25); }
  .btn-gold:active:not(:disabled){ transform: scale(0.99); }
  .btn-gold:disabled { opacity: 0.7; cursor: not-allowed; }

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
    background: rgba(184,134,46,0.09);
    border-left: 3px solid var(--gold);
    padding: 10px 13px;
    font-size: 11.5px;
    color: var(--ink-soft);
    line-height: 1.5;
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
  
  .spin { animation: spin 1s linear infinite; }
  @keyframes spin { 100% { transform: rotate(360deg); } }
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
          <div class="seal">REF: ED/BTC/04/2026 &middot; KOKRAJHAR</div>
        </div>

        <div class="auth-form">
          <div class="form-head">
            <span class="tag">OTP Login</span>
            <h3>Login with OTP</h3>
            <p>Enter your registered mobile number to receive an OTP.</p>
          </div>

          <x-auth-session-status class="mb-4" :status="session('status')" />

          <form method="POST" action="{{ route('login.otp.verify.post') }}" id="loginOtpForm">
            @csrf
            <input type="hidden" name="access_token" id="access_token">

            <div class="field @error('mobile_number') invalid @enderror" id="f-mobile">
              <label for="mobile_number">Mobile Number</label>
              <div class="input-group">
                <span class="input-prefix">+91</span>
                <input type="text" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" placeholder="XXXXXXXXXX" maxlength="10" autofocus />
              </div>
              <div class="err">@error('mobile_number') {{ $message }} @enderror</div>
            </div>

            <div class="field" id="otpSection" style="display: none;">
              <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px;">
                <label for="otp_code" style="margin:0;">Enter OTP</label>
                <button type="button" id="resendOtpBtn" class="resend-btn">Resend OTP</button>
              </div>
              <input type="text" id="otp_code" placeholder="Enter 4-digit OTP" maxlength="6" />
              <div class="err" id="otpError" style="display:none;"></div>
            </div>

            <button type="button" id="actionBtn" class="btn-gold">
              <span id="btnText">Send OTP</span>
              <svg id="btnIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" x2="11" y1="2" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            </button>

          </form>

          <div class="divider">Or Continue With</div>
          
          <div style="display: flex; gap: 12px; margin-top: 16px; margin-bottom: 24px;">
            <a href="{{ route('login') }}" class="btn-outline">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              Password
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
            This is the only official portal for ED/BTC/04/2026 recruitment. The Education Department, BTC never requests OTP or passwords over phone calls.
          </div>
        </div>

      </div>
    </div>
  </main>

  <footer class="site-footer">
    © 2026 Education Department, Bodoland Territorial Council. All rights reserved.
  </footer>

  <script>
    const configuration = {
        widgetId: "{{ config('services.msg91.widget_id') }}",
        tokenAuth: "{{ config('services.msg91.auth_key') }}",
        exposeMethods: true,
        success: () => {}, 
        failure: () => {}
    };

    (function loadOtpScript(urls) {
        let i = 0;
        function attempt() {
            const s = document.createElement('script');
            s.src = urls[i];
            s.async = true;
            s.onload = () => {
                console.log('MSG91 script loaded');
                if (typeof window.initSendOTP === 'function') {
                    window.initSendOTP(configuration);
                }
            };
            s.onerror = () => {
                i++;
                if (i < urls.length) {
                    attempt();
                } else {
                    console.error('Failed to load MSG91 script');
                }
            };
            document.head.appendChild(s);
        }
        attempt();
    })([
        'https://verify.msg91.com/otp-provider.js',
        'https://verify.phone91.com/otp-provider.js'
    ]);

    document.addEventListener('DOMContentLoaded', function() {
        let otpSent = false;
        const actionBtn = document.getElementById('actionBtn');
        let btnText = document.getElementById('btnText');
        const btnIcon = document.getElementById('btnIcon');
        const otpSection = document.getElementById('otpSection');
        const resendBtn = document.getElementById('resendOtpBtn');
        const otpError = document.getElementById('otpError');
        const mobileInput = document.getElementById('mobile_number');
        const fMobile = document.getElementById('f-mobile');

        // SVG icons
        const iconSend = '<line x1="22" x2="11" y1="2" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>';
        const iconCheck = '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>';
        const iconLoader = '<path d="M21 12a9 9 0 1 1-6.219-8.56"/>';

        function setLoading(isLoading) {
            actionBtn.disabled = isLoading;
            if (isLoading) {
                btnIcon.classList.add('spin');
                btnIcon.innerHTML = iconLoader;
            } else {
                btnIcon.classList.remove('spin');
                btnIcon.innerHTML = otpSent ? iconCheck : iconSend;
            }
        }

        // restrict non-numeric input
        mobileInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        actionBtn.addEventListener('click', function() {
            const mobileRaw = mobileInput.value;
            if (!mobileRaw || mobileRaw.length !== 10) {
                fMobile.classList.add('invalid');
                let errDiv = fMobile.querySelector('.err');
                errDiv.textContent = 'Please enter a valid 10-digit mobile number.';
                return;
            }
            fMobile.classList.remove('invalid');
            const mobileWithCode = '91' + mobileRaw;

            if (!otpSent) {
                if (typeof window.sendOtp !== 'function') {
                    alert('OTP Service is not ready. Please wait or refresh.');
                    return;
                }
                
                setLoading(true);
                window.sendOtp(
                    mobileWithCode, 
                    (data) => {
                        otpSent = true;
                        btnText.innerText = 'Verify & Login';
                        setLoading(false);
                        console.log('OTP sent successfully', data);
                        otpSection.style.display = 'block';
                        otpError.style.display = 'none';
                    },
                    (error) => {
                        setLoading(false);
                        console.log('Error sending OTP', error);
                        alert(error.message || 'Failed to send OTP. Please try again.');
                    }
                );
            } else {
                const otpValue = document.getElementById('otp_code').value;
                if (!otpValue) {
                    otpError.innerText = 'Please enter the OTP';
                    otpError.style.display = 'block';
                    document.getElementById('otpSection').classList.add('invalid');
                    return;
                }

                if (typeof window.verifyOtp !== 'function') {
                    alert('OTP Service error.');
                    return;
                }

                setLoading(true);
                window.verifyOtp(
                    otpValue,
                    (data) => {
                        setLoading(false);
                        console.log('OTP verified', data);
                        document.getElementById('access_token').value = data.message || data;
                        document.getElementById('loginOtpForm').submit();
                    },
                    (error) => {
                        setLoading(false);
                        console.log('Error verifying OTP', error);
                        otpError.innerText = error.message || 'Invalid OTP. Please try again.';
                        otpError.style.display = 'block';
                        document.getElementById('otpSection').classList.add('invalid');
                    }
                );
            }
        });

        resendBtn.addEventListener('click', function() {
           if (typeof window.retryOtp === 'function') {
               resendBtn.innerText = 'Sending...';
               resendBtn.disabled = true;
               window.retryOtp(
                   null, 
                   (data) => {
                       console.log('Resend success', data);
                       resendBtn.innerText = 'OTP Sent';
                       setTimeout(() => {
                           resendBtn.innerText = 'Resend OTP';
                           resendBtn.disabled = false;
                       }, 3000);
                   },
                   (error) => {
                       console.log('Resend error', error);
                       alert(error.message || 'Failed to resend OTP.');
                       resendBtn.innerText = 'Resend OTP';
                       resendBtn.disabled = false;
                   }
               );
           } 
        });
    });
  </script>

</body>
</html>
