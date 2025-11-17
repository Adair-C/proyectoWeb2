// public/assets/js/auth.js
const form = document.getElementById('loginForm');
const msg = document.getElementById('loginMsg');

async function login(e) {
  e.preventDefault();
  msg.textContent = '';

  const data = new FormData(form);
  const payload = {
    username: data.get('username').trim(),
    password: data.get('password')
  };

  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

  try {
    const res = await fetch('/api/login.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrf
      },
      body: JSON.stringify(payload),
      credentials: 'same-origin'
    });

    const json = await res.json();
    if (!json.ok) {
      msg.textContent = json.error || 'Error de autenticación';
      return;
    }
    // Redirección simple al menú (puedes personalizar por rol)
    location.href = '/menu.php';
  } catch (err) {
    msg.textContent = 'Error de red / servidor';
  }
}

form?.addEventListener('submit', login);
