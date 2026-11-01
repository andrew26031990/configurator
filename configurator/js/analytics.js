/**
 * Статистика посещаемости и воронка заказа — события витрины в /modules/track.php.
 *
 * pcm_vid — посетитель (cookie на год), pcm_sid — сессия (30 минут без действий).
 * Оба — случайные строки, ни IP, ни персональных данных здесь нет.
 * Без JS (боты, сканеры) cookie не появится — сервер такие запросы не считает.
 */
var Analytics = (function () {
  var SESSION_MINUTES = 30;
  var cat = window.PCM_CAT || '';
  var vid, sid, isNewVisitor = false, isNewSession = false;

  function randomId() {
    var a = new Uint8Array(8), s = '';
    (window.crypto || window.msCrypto).getRandomValues(a);
    for (var i = 0; i < a.length; i++) { s += ('0' + a[i].toString(16)).slice(-2); }
    return s;
  }

  function getCookie(name) {
    var m = document.cookie.match(new RegExp('(?:^|; )' + name + '=([a-z0-9]{16})(?:;|$)'));
    return m ? m[1] : '';
  }

  function setCookie(name, value, seconds) {
    document.cookie = name + '=' + value + '; max-age=' + seconds + '; path=/; SameSite=Lax' +
      (location.protocol === 'https:' ? '; Secure' : '');
  }

  function touchSession() {
    setCookie('pcm_sid', sid, SESSION_MINUTES * 60);
  }

  try {
    vid = getCookie('pcm_vid');
    if (!vid) { vid = randomId(); isNewVisitor = true; }
    setCookie('pcm_vid', vid, 365 * 24 * 3600);

    sid = getCookie('pcm_sid');
    if (!sid) { sid = randomId(); isNewSession = true; }
    touchSession();
  } catch (e) {
    // Нет cookie или crypto — просто не считаем.
    return { track: function () {}, once: function () {} };
  }

  function track(event, detail, extra) {
    touchSession();
    var data = new FormData();
    data.append('e', event);
    data.append('cat', cat);
    data.append('d', detail || '');
    if (isNewVisitor) { data.append('new', '1'); }
    if (extra) { for (var k in extra) { if (extra.hasOwnProperty(k)) { data.append(k, extra[k]); } } }

    if (navigator.sendBeacon && navigator.sendBeacon('/modules/track.php', data)) { return; }
    try {
      var xhr = new XMLHttpRequest();
      xhr.open('POST', '/modules/track.php', true);
      xhr.send(data);
    } catch (e) { /* статистика не должна ломать сайт */ }
  }

  // Одно событие на сессию для данного ключа (например, первый выбор в блоке).
  function once(key, event, detail) {
    var k = 'pcm_once_' + sid + '_' + key;
    try {
      if (sessionStorage.getItem(k)) { return; }
      sessionStorage.setItem(k, '1');
    } catch (e) { /* без sessionStorage — отправим, сервер переживёт */ }
    track(event, detail);
  }

  // Просмотр страницы; первая страница сессии несёт источник перехода.
  track('page_view', '', isNewSession ? { entry: '1', ref: document.referrer || '' } : null);

  return { track: track, once: once };
})();
