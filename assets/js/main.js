/* =========================================================
   main.js  -  all JavaScript for the website
   1. Mobile menu   2. Scroll animation   3. Date rules
   4. Live price calculator   5. Form validation
   6. Counter animation   7. Confirm dialogs
   ========================================================= */
document.addEventListener('DOMContentLoaded', function () {

  // 1. Mobile menu toggle
  var toggle = document.getElementById('menuToggle');
  var nav = document.getElementById('mainNav');
  if (toggle) toggle.addEventListener('click', function () { nav.classList.toggle('open'); });

  // 2. Reveal elements when they scroll into view
  var items = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { en.target.classList.add('visible'); io.unobserve(en.target); }
      });
    }, { threshold: 0.12 });
    items.forEach(function (el) { io.observe(el); });
  } else { items.forEach(function (el) { el.classList.add('visible'); }); }

  // 3. Date rules: check-in >= today, check-out > check-in
  var inDate = document.getElementById('check_in');
  var outDate = document.getElementById('check_out');
  function isoDate(d) { return d.toISOString().split('T')[0]; }
  if (inDate && outDate) {
    var today = new Date();
    inDate.min = isoDate(today);
    function syncOut() {
      if (!inDate.value) return;
      var next = new Date(inDate.value); next.setDate(next.getDate() + 1);
      outDate.min = isoDate(next);
      if (!outDate.value || outDate.value < outDate.min) outDate.value = isoDate(next);
      updatePrice();
    }
    inDate.addEventListener('change', syncOut);
    outDate.addEventListener('change', updatePrice);
    if (inDate.value) syncOut();
  }

  // 4. Live price calculator on booking page
  function updatePrice() {
    var box = document.getElementById('priceBox');
    if (!box || !inDate.value || !outDate.value) return;
    var nights = Math.round((new Date(outDate.value) - new Date(inDate.value)) / 86400000);
    if (nights < 1) return;
    var price = parseFloat(box.dataset.price);
    var total = nights * price;
    document.getElementById('sumNights').textContent = nights;
    document.getElementById('sumTotal').textContent = '₹' + total.toLocaleString('en-IN');
  }
  if (inDate && outDate && inDate.value && outDate.value) updatePrice();

  // 5. Generic form validation (forms with class "validate")
  document.querySelectorAll('form.validate').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var ok = true;
      form.querySelectorAll('[data-rule]').forEach(function (field) {
        var rule = field.dataset.rule, v = field.value.trim(), msg = '';
        if (rule.indexOf('required') > -1 && v === '') msg = 'This field is required.';
        else if (rule.indexOf('email') > -1 && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) msg = 'Enter a valid email address.';
        else if (rule.indexOf('phone') > -1 && v !== '' && !/^[0-9+\-\s]{10,15}$/.test(v)) msg = 'Enter a valid phone number.';
        else if (rule.indexOf('min6') > -1 && v.length < 6) msg = 'Minimum 6 characters.';
        else if (rule.indexOf('match') > -1) {
          var other = form.querySelector('[name="' + field.dataset.match + '"]');
          if (other && other.value !== v) msg = 'Passwords do not match.';
        }
        var err = field.parentElement.querySelector('.error-text');
        if (err) { err.textContent = msg; err.style.display = msg ? 'block' : 'none'; }
        if (msg) ok = false;
      });
      if (!ok) e.preventDefault();
    });
  });

  // 6. Counter animation (home page statistics)
  document.querySelectorAll('[data-count]').forEach(function (el) {
    var target = parseInt(el.dataset.count, 10), cur = 0, step = Math.max(1, Math.floor(target / 60));
    var t = setInterval(function () {
      cur += step; if (cur >= target) { cur = target; clearInterval(t); }
      el.textContent = cur.toLocaleString('en-IN') + (el.dataset.suffix || '');
    }, 25);
  });

  // 7. Confirm before dangerous actions (links/buttons with data-confirm)
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (!confirm(el.dataset.confirm)) e.preventDefault();
    });
  });
});
