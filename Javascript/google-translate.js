(function () {
  if (window.__gvGoogleTranslateLoaded) return;
  window.__gvGoogleTranslateLoaded = true;

  function deleteCookieEverywhere(name) {
    document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/`;

    const parts = location.hostname.split('.');
    if (parts.length >= 2) {
      const root = '.' + parts.slice(-2).join('.');
      document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; domain=${root}`;
    }
  }

  function setCookie(name, value) {
    document.cookie = `${name}=${value}; path=/`;
  }

  function getCookie(name) {
    const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return match ? decodeURIComponent(match[2]) : null;
  }

 function updateLangButtons(lang) {
  const frBtns = document.querySelectorAll('#gvLangFr, #gvMobileLangFr');
  const enBtns = document.querySelectorAll('#gvLangEn, #gvMobileLangEn');

  frBtns.forEach(btn => {
    btn.classList.toggle('is-active', lang === 'fr');

    if (lang === 'fr') {
      btn.classList.add('font-bold', 'underline');
      btn.classList.remove('font-medium');
    } else {
      btn.classList.remove('font-bold', 'underline');
      btn.classList.add('font-medium');
    }
  });

  enBtns.forEach(btn => {
    btn.classList.toggle('is-active', lang === 'en');

    if (lang === 'en') {
      btn.classList.add('font-bold', 'underline');
      btn.classList.remove('font-medium');
    } else {
      btn.classList.remove('font-bold', 'underline');
      btn.classList.add('font-medium');
    }
  });
}

  function getCurrentLangFromCookie() {
    const googtrans = getCookie('googtrans');

    if (!googtrans) return 'fr';
    if (googtrans.includes('/fr/en')) return 'en';

    return 'fr';
  }

  function setGoogleLang(lang) {
    const select = document.querySelector('select.goog-te-combo');
    if (!select) return false;

    select.value = lang;
    select.dispatchEvent(new Event('change'));
    updateLangButtons(lang);

    return true;
  }

  function resetGoogleTranslate() {
    deleteCookieEverywhere('googtrans');
    setCookie('googtrans', '/fr/fr');
    updateLangButtons('fr');
    window.location.reload();
  }

  window.setGoogleLang = setGoogleLang;
  window.resetGoogleTranslate = resetGoogleTranslate;

  window.__googleTranslateInit = function () {
    if (!window.google || !window.google.translate) return;
    if (window.__gtInited) return;

    const container = document.getElementById('google_translate_element');
    if (!container) return;

    window.__gtInited = true;

    new window.google.translate.TranslateElement(
      {
        pageLanguage: 'fr',
        includedLanguages: 'en',
        autoDisplay: false
      },
      'google_translate_element'
    );

    const currentLang = getCurrentLangFromCookie();

    if (currentLang === 'en') {
      setTimeout(function () {
        setGoogleLang('en');
        updateLangButtons('en');
      }, 300);
    } else {
      updateLangButtons('fr');
    }
  };

  document.addEventListener('DOMContentLoaded', function () {
    const frBtn = document.getElementById('gvLangFr');
    const enBtn = document.getElementById('gvLangEn');

    const mobileFrBtn = document.getElementById('gvMobileLangFr');
    const mobileEnBtn = document.getElementById('gvMobileLangEn');

    const currentLang = getCurrentLangFromCookie();
    updateLangButtons(currentLang);

    function handleFr() {
      resetGoogleTranslate();
    }

    function handleEn() {
      setCookie('googtrans', '/fr/en');
      updateLangButtons('en');

      if (!setGoogleLang('en')) {
        setTimeout(function () {
          setGoogleLang('en');
        }, 500);
      }
    }

    if (frBtn) frBtn.addEventListener('click', handleFr);
    if (enBtn) enBtn.addEventListener('click', handleEn);

    if (mobileFrBtn) mobileFrBtn.addEventListener('click', handleFr);
    if (mobileEnBtn) mobileEnBtn.addEventListener('click', handleEn);

    const existingScript = document.getElementById('google-translate-script');

    if (existingScript) {
      window.__googleTranslateInit();
      return;
    }

    const script = document.createElement('script');
    script.id = 'google-translate-script';
    script.async = true;
    script.src = 'https://translate.google.com/translate_a/element.js?cb=__googleTranslateInit';
    document.head.appendChild(script);
  });
})();