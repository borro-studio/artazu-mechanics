(function () {
  if (localStorage.getItem('cookie-consent')) return;

  var banner = document.createElement('div');
  banner.id = 'cookie-banner';
  banner.innerHTML = [
    '<div style="max-width:900px;display:flex;align-items:center;justify-content:space-between;gap:24px;flex-wrap:wrap;">',
    '  <p style="margin:0;font-size:14px;line-height:1.6;color:rgba(237,237,237,0.85);flex:1;min-width:200px;">',
    '    Este sitio utiliza cookies técnicas necesarias para su funcionamiento.',
    '    <a href="aviso-legal.html#cookies" style="color:#ee7419;text-decoration:underline;margin-left:4px;">Más información</a>',
    '  </p>',
    '  <div style="display:flex;gap:10px;flex-shrink:0;">',
    '    <button id="cookie-reject" style="padding:10px 20px;border:1px solid rgba(237,237,237,0.3);background:none;color:rgba(237,237,237,0.6);font-size:13px;border-radius:50px;cursor:pointer;font-family:inherit;transition:all 0.2s;">Solo necesarias</button>',
    '    <button id="cookie-accept" style="padding:10px 24px;background:#ee7419;border:none;color:#fff;font-size:13px;font-weight:600;border-radius:50px;cursor:pointer;font-family:inherit;transition:background 0.2s;">Aceptar</button>',
    '  </div>',
    '</div>'
  ].join('');

  Object.assign(banner.style, {
    position: 'fixed',
    bottom: '24px',
    left: '50%',
    transform: 'translateX(-50%)',
    width: 'calc(100% - 48px)',
    maxWidth: '980px',
    background: '#1a1a1a',
    padding: '20px 28px',
    borderRadius: '12px',
    boxShadow: '0 8px 40px rgba(0,0,0,0.35)',
    zIndex: '999999',
    fontFamily: "'Helvetica Neue', Helvetica, Arial, sans-serif",
    boxSizing: 'border-box'
  });

  document.body.appendChild(banner);

  function dismiss(value) {
    localStorage.setItem('cookie-consent', value);
    banner.style.transition = 'opacity 0.4s, transform 0.4s';
    banner.style.opacity = '0';
    banner.style.transform = 'translateX(-50%) translateY(20px)';
    setTimeout(function () { banner.remove(); }, 400);
  }

  document.getElementById('cookie-accept').addEventListener('click', function () { dismiss('accepted'); });
  document.getElementById('cookie-reject').addEventListener('click', function () { dismiss('rejected'); });
})();
