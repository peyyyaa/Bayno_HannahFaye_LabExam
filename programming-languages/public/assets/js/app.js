// Small UI touches. The PHP still checks everything on the server;
// this only gives instant feedback.

// Show / hide password (the eye icon)
document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
  btn.addEventListener('click', () => {
    const input = btn.parentElement.querySelector('input');
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.classList.toggle('is-visible', show);
    btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
  });
});

// Clear a field's error state as soon as the person edits it
document.querySelectorAll('.input input').forEach((input) => {
  input.addEventListener('input', () => {
    const box = input.closest('.input');
    if (!box.classList.contains('is-error')) return;
    box.classList.remove('is-error');
    input.removeAttribute('aria-invalid');
    const err = box.parentElement.querySelector('.field-error');
    if (err) err.hidden = true;
  });
});

// "Coming soon" toast for buttons that aren't wired up yet
const toast = document.getElementById('toast');
let toastTimer;
document.querySelectorAll('[data-soon]').forEach((el) => {
  el.addEventListener('click', (event) => {
    event.preventDefault();
    toast.querySelector('.toast-text').textContent = el.dataset.soon;
    toast.hidden = false;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => { toast.hidden = true; }, 2600);
  });
});

// Sign-up page: live password rules + confirm-password check
const form = document.querySelector('[data-register]');
if (form) {
  const password = form.querySelector('#f-password');
  const confirm = form.querySelector('#f-confirm_password');
  const pop = document.getElementById('pw-rules');

  const rules = {
    length: (v) => v.length >= 8,
    upper: (v) => /[A-Z]/.test(v),
    number: (v) => /[0-9]/.test(v),
    special: (v) => /[^A-Za-z0-9]/.test(v),
  };

  const checkPassword = () => {
    const value = password.value;
    let allOk = true;
    pop.querySelectorAll('li').forEach((li) => {
      const ok = rules[li.dataset.rule](value);
      li.classList.toggle('ok', ok);
      allOk = allOk && ok;
    });
    password.closest('.input').classList.toggle('is-ok', value !== '' && allOk);
    checkConfirm();
  };

  const checkConfirm = () => {
    const box = confirm.closest('.input');
    if (confirm.value === '') {
      box.classList.remove('is-ok');
      return;
    }
    const match = confirm.value === password.value;
    box.classList.toggle('is-ok', match);
    box.classList.toggle('is-error', !match);
  };

  password.addEventListener('focus', () => { pop.hidden = false; checkPassword(); });
  password.addEventListener('blur', () => { pop.hidden = true; });
  password.addEventListener('input', checkPassword);
  confirm.addEventListener('input', checkConfirm);
}
