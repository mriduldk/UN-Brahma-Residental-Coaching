<style>
/* Portal Footer Styles */
.portal-footer {
  background: var(--paper, #f0f9ff);
  color: var(--ink-soft, #4b6a8a);
  border-top: 1px solid var(--line, #bae6fd);
  margin-top: 50px;
  padding: 32px 0 20px;
  font-size: 12.5px;
  box-sizing: border-box;
}
.portal-footer .wrap {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 20px;
  max-width: 1120px;
  margin: 0 auto;
  padding: 0 24px;
  box-sizing: border-box;
}
.portal-footer h5 {
  color: var(--maroon-dark, #0c4a6e);
  font-family: 'Source Serif 4', serif;
  font-size: 14px;
  margin-bottom: 8px;
  font-weight: 600;
  margin-top: 0;
}
.portal-footer .col {
  max-width: 320px;
  box-sizing: border-box;
}
.portal-footer .col p {
  margin-bottom: 8px;
  line-height: 1.5;
}
.portal-footer .emblem {
  width: 48px;
  height: 56px;
  display: block;
  margin-bottom: 12px;
}
.portal-footer .copyline {
  text-align: center;
  margin-top: 26px;
  padding-top: 16px;
  border-top: 1px solid var(--line, #bae6fd);
  font-size: 11.5px;
  color: var(--ink-soft, #4b6a8a);
  opacity: 0.8;
  max-width: 1120px;
  margin-left: auto;
  margin-right: auto;
  box-sizing: border-box;
}
.portal-footer a {
  color: var(--maroon, #0284c7);
  text-decoration: none;
  font-weight: 600;
  transition: color 0.15s ease;
}
.portal-footer a:hover {
  color: var(--maroon-dark, #0c4a6e);
  text-decoration: underline;
}
</style>

<footer class="portal-footer" id="footer">
  <div class="wrap">
    <div class="col">
      <img src="/image/logo/logo-em.png" alt="BTC Emblem" class="emblem">
      <h5>UN Brahma Residential Free Coaching</h5>
      <p>Department of Education, Bodoland Territorial Council<br>Kokrajhar, BTR, Assam — 783370</p>
    </div>
    <div class="col">
      <h5>Programmes &amp; Disciplines</h5>
      <p>• Engineering (JEE Main &amp; Advanced)<br>• Medical (NEET-UG)<br>• APSC (Assam Civil Services - CCE)<br>• UPSC (Civil Services - CSE)</p>
    </div>
    <div class="col">
      <h5>Candidate Support</h5>
      <p>education@bodoland.gov.in<br>Helpline: 03661-270XXX (10 AM – 5 PM, Mon–Fri)<br><span style="font-size: 11px; color: var(--forest);">100% Free Residential Coaching by BTC</span></p>
    </div>
  </div>
  <div class="copyline">
    © 2026 Upendra Nath Brahma Residential Free Coaching, Department of Education, Bodoland Territorial Council. All rights reserved. 
    &nbsp;|&nbsp; 
    <a href="{{ route('terms') }}">Terms &amp; Conditions</a> 
    &nbsp;|&nbsp; 
    <a href="{{ route('privacy') }}">Privacy Policy</a>
    - Design and Developed by <a href="https://jypko.com" target="_blank">JYPKO</a>
  </div>
</footer>
