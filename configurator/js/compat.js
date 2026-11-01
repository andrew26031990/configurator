/**
 * Совместимость материнской платы, процессора и ОЗУ на витрине.
 *
 * Роль блока — data-compat-role у table.product-table (motherboard | cpu | ram),
 * данные товара — data-socket / data-mem у чекбокса (mem — через запятую).
 * Пустое значение = «неизвестно», такой товар ничем не ограничивается.
 *
 * Правила:
 *   процессор ↔ плата — одинаковый сокет;
 *   ОЗУ ↔ плата, ОЗУ ↔ процессор — тип памяти ОЗУ среди поддерживаемых.
 *
 * Несовместимые с текущим выбором товары тускнеют, под названием — причина.
 * Клик по такому товару предлагает выбрать его и снять конфликтующие.
 */
var Compat = (function ($) {
  var ROLE_NAMES = { motherboard: 'материнской платой', cpu: 'процессором', ram: 'ОЗУ' };
  var cache = {};   // id товара -> {id, role, socket, mem, name, type} — переживает перерисовку списков фильтром
  var bypass = false;

  function memList(s) {
    return s ? String(s).split(',').map(function (x) { return x.trim(); }).filter(Boolean) : [];
  }

  function roleOfTable($table) {
    return $table.attr('data-compat-role') || '';
  }

  function info(input) {
    var $in = $(input), $table = $in.closest('table.product-table');
    var role = roleOfTable($table);
    if (!role) { return null; }
    var d = {
      id: $in.attr('id'),
      role: role,
      type: $in.attr('name'),
      socket: $in.attr('data-socket') || '',
      mem: memList($in.attr('data-mem')),
      name: $('label[for="' + $in.attr('id') + '"]').attr('data-name') || ''
    };
    cache[d.id] = d;
    return d;
  }

  // Выбранное в блоках совместимости: роль -> данные товара.
  function selected() {
    var out = {};
    $('table.product-table[data-compat-role]').each(function () {
      var $t = $(this), role = roleOfTable($t);
      if (!role) { return; }
      var $checked = $t.find('input[type=checkbox]:checked');
      if ($checked.length) {
        out[role] = info($checked[0]);
        return;
      }
      // Выбранный товар может быть скрыт фильтром (список перерисован) — берём из кэша.
      var type = $t.attr('id');
      var selId = (typeof sel_bd_id !== 'undefined' && sel_bd_id.has(type)) ? sel_bd_id.get(type) : null;
      if (selId && cache[selId] && !$t.find('input#' + selId).length) {
        out[role] = cache[selId];
      }
    });
    return out;
  }

  function intersects(a, b) {
    for (var i = 0; i < a.length; i++) { if (b.indexOf(a[i]) !== -1) { return true; } }
    return false;
  }

  // Причина несовместимости кандидата c с выбранным товаром s, или '' если совместимы.
  function conflict(c, s) {
    if (!c || !s || c.role === s.role || c.id === s.id) { return ''; }
    var pair = [c.role, s.role].sort().join('+');
    if (pair === 'cpu+motherboard') {
      if (c.socket && s.socket && c.socket !== s.socket) {
        return 'сокет ' + c.socket + ', а у ' + (s.role === 'cpu' ? 'процессора' : 'платы') + ' — ' + s.socket;
      }
      return '';
    }
    // ОЗУ с платой или процессором
    if (c.mem.length && s.mem.length && !intersects(c.mem, s.mem)) {
      if (c.role === 'ram') {
        return c.mem.join('/') + ', а ' + (s.role === 'cpu' ? 'процессор' : 'плата') + ' поддерживает ' + s.mem.join('/');
      }
      return 'поддерживает ' + c.mem.join('/') + ', а выбрана память ' + s.mem.join('/');
    }
    return '';
  }

  function conflictsOf(c, sel) {
    var out = [];
    $.each(sel, function (role, s) {
      var why = conflict(c, s);
      if (why) { out.push({ with: s, why: why }); }
    });
    return out;
  }

  function noteText(list) {
    return 'Несовместимо с выбранной ' + list.map(function (x) {
      return ROLE_NAMES[x.with.role] + ': ' + x.why;
    }).join('; ');
  }

  function ensureAlert() {
    var $a = $('.compat-alert');
    if (!$a.length) {
      $a = $('<div class="compat-alert" style="display:none"></div>');
      $('.center-block').prepend($a);
    }
    return $a;
  }

  function refresh() {
    var sel = selected();

    $('table.product-table[data-compat-role] input[type=checkbox]').each(function () {
      var c = info(this);
      var $li = $(this).closest('li');
      var list = this.checked ? [] : conflictsOf(c, sel);
      $li.toggleClass('compat-off', list.length > 0);
      var $note = $li.find('.compat-note');
      if (list.length) {
        if (!$note.length) { $note = $('<span class="compat-note"></span>').appendTo($li.find('.product-block')); }
        $note.text('Несовместимо: ' + list.map(function (x) { return x.why; }).join('; '));
        $li.attr('title', noteText(list));
      } else {
        $note.remove();
        $li.removeAttr('title');
      }
    });

    // Конфликты внутри уже собранного (ссылка «Поделиться» со старой сборкой, неполные данные)
    var bad = [];
    var roles = Object.keys(sel);
    for (var i = 0; i < roles.length; i++) {
      for (var j = i + 1; j < roles.length; j++) {
        var why = conflict(sel[roles[i]], sel[roles[j]]);
        if (why) { bad.push('«' + sel[roles[i]].name + '» и «' + sel[roles[j]].name + '»: ' + why); }
      }
    }
    var $a = ensureAlert();
    if (bad.length) {
      $a.html('<b>В сборке есть несовместимые комплектующие.</b> ').append(document.createTextNode(bad.join('. ')));
      $a.show();
    } else {
      $a.hide().empty();
    }
  }

  // Снять выбор так же, как кнопка «Убрать из сборки».
  function deselect(d) {
    var type = d.type;
    $('input[type=checkbox][name="' + type + '"]').prop('checked', false);
    if (typeof sel_price !== 'undefined') { sel_price.delete(type); }
    if (typeof sel_bd_id !== 'undefined') { sel_bd_id.delete(type); }
    if (typeof sel_quantity !== 'undefined') { sel_quantity.delete(d.id); }
    $('.' + type + ' img').attr('src', 'configurator/images/products/noimage.png');
  }

  // Оставляет ли товар хоть один совместимый вариант в каждом другом блоке совместимости,
  // где ещё ничего не выбрано. Иначе автовыбор, например, ОЗУ DDR4 сделал бы
  // тусклыми все платы AM5.
  function leavesOptions(c, sel) {
    var ok = true;
    $('table.product-table[data-compat-role]').each(function () {
      var role = roleOfTable($(this));
      if (!ok || role === c.role || sel[role]) { return; }
      var $all = $(this).find('input[type=checkbox]');
      if (!$all.length) { return; }
      var any = false;
      $all.each(function () {
        if (!conflict(info(this), c)) { any = true; return false; }
      });
      ok = any;
    });
    return ok;
  }

  // Заполнены ли у товара данные совместимости (ОЗУ — тип памяти, остальные — сокет и память).
  function isKnown(c) {
    return c.role === 'ram' ? c.mem.length > 0 : (c.socket !== '' && c.mem.length > 0);
  }

  // Товар блока для автовыбора при загрузке: первый совместимый с уже выбранным
  // и не отрезающий остальные блоки, с заполненными данными (а не, скажем, SSD,
  // по ошибке лежащий в блоке ОЗУ). Дальше по убыванию — просто совместимый, просто первый.
  function firstCompatible($inputs) {
    var sel = selected(), viableUnknown = null, fallback = null;
    for (var i = 0; i < $inputs.length; i++) {
      var c = info($inputs[i]);
      if (!c) { return $inputs.eq(i); }
      if (conflictsOf(c, sel).length) { continue; }
      if (leavesOptions(c, sel)) {
        if (isKnown(c)) { return $inputs.eq(i); }
        if (viableUnknown === null) { viableUnknown = i; }
      }
      if (fallback === null) { fallback = i; }
    }
    if (viableUnknown !== null) { return $inputs.eq(viableUnknown); }
    return $inputs.eq(fallback === null ? 0 : fallback);
  }

  // Клик по тусклому товару: перехватываем в фазе захвата, до обработчиков main.js.
  document.addEventListener('click', function (e) {
    if (bypass) { return; }
    var input = e.target;
    // К моменту события галочка уже переключена: checked === false — это снятие выбора, его не трогаем.
    if (!input || input.tagName !== 'INPUT' || input.type !== 'checkbox' || !input.checked) { return; }
    var $li = $(input).closest('li');
    if (!$li.hasClass('compat-off')) { return; }

    e.preventDefault();
    e.stopPropagation();

    var c = info(input), list = conflictsOf(c, selected());
    if (!list.length) { return; }
    var msg = '«' + c.name + '» несовместим:\n' + list.map(function (x) {
      return '— с «' + x.with.name + '» (' + x.why + ')';
    }).join('\n') + '\n\nВыбрать этот товар и убрать из сборки несовместимые комплектующие?';
    if (!window.confirm(msg)) { return; }

    list.forEach(function (x) { deselect(x.with); });
    // Повторный click() внутри обработки этого же клика браузер игнорирует — следующим тиком.
    setTimeout(function () {
      bypass = true;
      try { input.click(); } finally { bypass = false; }
    }, 0);
  }, true);

  // Любое изменение выбора — пересчитать (обработчики main.js отработают раньше).
  $(document).on('change', 'table.product-table input[type=checkbox]', function () { setTimeout(refresh, 0); });
  $(document).on('click', '.uncheck-btn, .refresh', function () { setTimeout(refresh, 0); });

  return { refresh: refresh, firstCompatible: firstCompatible };
})(jQuery);
