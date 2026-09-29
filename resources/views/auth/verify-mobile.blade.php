<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify Mobile Number — Education Department, BTC</title>
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

  .field{ margin-top: 20px; position: relative; }
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
    background: var(--white);
    font-family: 'IBM Plex Sans', sans-serif;
    font-size: 14px;
    color: var(--ink);
    border-radius: 6px;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
  }
  .field input:focus{
    outline: none;
    border-color: var(--maroon);
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
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
  .btn-gold:hover{ background: #0369a1; box-shadow: 0 6px 12px rgba(2, 132, 199, 0.25); }
  .btn-gold:active{ transform: scale(0.99); }
  .btn-gold:disabled{ background: var(--line); color: var(--ink-soft); cursor: not-allowed; box-shadow: none; transform: none; }

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
  .switch-line a, .switch-line button{
    color: var(--maroon);
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    background: none;
    border: none;
    font-size: 13px;
    font-family: inherit;
  }
  .switch-line a:hover, .switch-line button:hover{ text-decoration: underline; }

  .notice-box{
    margin-top: 22px;
    background: rgba(14,165,233,0.08);
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

  .lucide {
    width: 1em;
    height: 1em;
    vertical-align: middle;
  }
  
  @keyframes spin {
    to { transform: rotate(360deg); }
  }
  .animate-spin {
    animation: spin 1s linear infinite;
  }
</style>
<script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

<x-portal-header />

  <main class="auth-shell">
    <div class="wrap" style="display:flex; justify-content:center; padding:0;">
      <div class="auth-grid">

        <div class="auth-info">
          <div>
            <span class="tag">Security Step</span>
            <h2>Secure your account with Mobile Verification.</h2>
            <p>We need to verify your phone number to ensure you can receive critical updates regarding your application status.</p>
            <ul>
              <li><span class="mk">✓</span> Instant admit card alerts via SMS</li>
              <li><span class="mk">✓</span> Verification for password resets</li>
              <li><span class="mk">✓</span> Protects against unauthorized access</li>
              <li><span class="mk">✓</span> Required for final submission</li>
            </ul>
          </div>
        </div>

        <div class="auth-form">
          <div class="form-head">
            <span class="tag">Verification</span>
            <h3>Verify Mobile Number</h3>
            <p>Please enter your 10-digit mobile number to receive an OTP.</p>
          </div>
          
          @if ($errors->any())
              <div class="notice-box" style="border-left-color: var(--error); background: rgba(239, 68, 68, 0.08); color: var(--error); font-weight: 500;">
                  {{ $errors->first() }}
              </div>
          @endif

          <form id="verifyForm" method="POST" action="{{ route('mobile.verify.post') }}">
            @csrf
            
            <div class="field" id="f-mobile">
              <label for="mobile_number">Mobile Number</label>
              <div style="position: relative;">
                <span style="position: absolute; left: 13px; top: 8px; font-weight: 600; color: var(--ink-soft);">+91</span>
                <input type="text" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" placeholder="XXXXXXXXXX" maxlength="10" style="padding-left: 46px; font-weight: 600; color: var(--ink-soft); top: 11px; " />
              </div>
              <div class="err">Please enter a valid 10-digit mobile number.</div>
            </div>

            <div class="field" id="f-otp" style="display: none;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                <label for="otp_code" style="margin-bottom: 0;">Enter OTP</label>
                <button type="button" id="resendOtpBtn" style="background: none; border: none; font-size: 11.5px; font-weight: 600; color: var(--maroon); cursor: pointer;">Resend OTP</button>
              </div>
              <input type="text" id="otp_code" placeholder="4-digit OTP" maxlength="6" />
              <div class="err" id="otpError"></div>
            </div>

            <input type="hidden" name="access_token" id="access_token">

            <button type="button" id="actionBtn" class="btn-gold">
              <span id="btnText">Send OTP</span> 
              <i data-lucide="send" id="btnIcon" style="margin-left: 6px; width: 18px; height: 18px;"></i>
            </button>

          </form>

          <div class="divider">Or</div>

          <div class="switch-line">
            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit">Logout and try a different account</button>
            </form>
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

  <script type="text/javascript">
    lucide.createIcons();

    const configuration = {
        widgetId: "{{ config('services.msg91.widget_id') }}",
        tokenAuth: "{{ config('services.msg91.auth_key') }}",
        exposeMethods: true,
        success: () => { },
        failure: () => { }
    };

    (function loadOtpScript(urls) {
        let i = 0;
        function attempt() {
            const s = document.createElement('script');
            s.src = urls[i];
            s.async = true;
            s.onload = () => {
                if (typeof window.initSendOTP === 'function') {
                    window.initSendOTP(configuration);
                }
            };
            s.onerror = () => {
                i++;
                if (i < urls.length) attempt();
            };
            document.head.appendChild(s);
        }
        attempt();
    })([
        'https://verify.msg91.com/otp-provider.js',
        'https://verify.phone91.com/otp-provider.js'
    ]);

    document.addEventListener('DOMContentLoaded', function () {
        let otpSent = false;
        const actionBtn = document.getElementById('actionBtn');
        let btnText = document.getElementById('btnText');
        const btnIcon = document.getElementById('btnIcon');
        const otpSection = document.getElementById('f-otp');
        const resendBtn = document.getElementById('resendOtpBtn');
        const otpError = document.getElementById('otpError');
        const mobileInput = document.getElementById('mobile_number');
        const mobileField = document.getElementById('f-mobile');

        function setLoading(isLoading) {
            actionBtn.disabled = isLoading;
            if (isLoading) {
                btnText.innerText = btnText.innerText.replace('...', '') + '...';
                actionBtn.innerHTML = `<span id="btnText">${btnText.innerText}</span> <i data-lucide="loader-2" id="btnIcon" class="animate-spin" style="margin-left: 6px; width: 18px; height: 18px;"></i>`;
            } else {
                const iconName = otpSent ? 'check-circle' : 'send';
                btnText.innerText = otpSent ? 'Verify & Proceed' : 'Send OTP';
                actionBtn.innerHTML = `<span id="btnText">${btnText.innerText}</span> <i data-lucide="${iconName}" id="btnIcon" style="margin-left: 6px; width: 18px; height: 18px;"></i>`;
            }
            btnText = document.getElementById('btnText');
            lucide.createIcons();
        }

        mobileInput.addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            if(this.value.length === 10) {
                mobileField.classList.remove('invalid');
            }
        });

        actionBtn.addEventListener('click', function () {
            const mobileRaw = mobileInput.value;
            if (!mobileRaw || mobileRaw.length !== 10) {
                mobileField.classList.add('invalid');
                return;
            }
            mobileField.classList.remove('invalid');
            
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
                        setLoading(false);
                        otpSection.style.display = 'block';
                        mobileInput.readOnly = true;
                        mobileInput.style.backgroundColor = 'var(--paper)';
                        mobileInput.style.color = 'var(--ink-soft)';
                    },
                    (error) => {
                        setLoading(false);
                        alert(error.message || 'Failed to send OTP. Please try again.');
                    }
                );
            } else {
                const otpValue = document.getElementById('otp_code').value;
                if (!otpValue) {
                    otpSection.classList.add('invalid');
                    otpError.innerText = 'Please enter the OTP';
                    otpError.style.display = 'block';
                    return;
                }
                otpSection.classList.remove('invalid');
                otpError.style.display = 'none';

                if (typeof window.verifyOtp !== 'function') {
                    alert('OTP Service error.');
                    return;
                }

                setLoading(true);
                window.verifyOtp(
                    otpValue,
                    (data) => {
                        document.getElementById('access_token').value = data.message || data;
                        document.getElementById('verifyForm').submit();
                    },
                    (error) => {
                        setLoading(false);
                        otpSection.classList.add('invalid');
                        otpError.innerText = error.message || 'Invalid OTP. Please try again.';
                        otpError.style.display = 'block';
                    }
                );
            }
        });

        resendBtn.addEventListener('click', function () {
            if (typeof window.retryOtp === 'function') {
                resendBtn.innerText = 'Sending...';
                resendBtn.disabled = true;
                window.retryOtp(
                    null,
                    (data) => {
                        resendBtn.innerText = 'OTP Sent';
                        resendBtn.style.color = 'var(--forest)';
                        setTimeout(() => {
                            resendBtn.innerText = 'Resend OTP';
                            resendBtn.style.color = 'var(--maroon)';
                            resendBtn.disabled = false;
                        }, 3000);
                    },
                    (error) => {
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