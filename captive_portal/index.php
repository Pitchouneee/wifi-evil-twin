<?php
// Counter: an access attempt occurs.
require_once __DIR__ . '/notify.php';
notify_dashboard('attempt');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Sign in to the captive portal</title>

  <meta charset="utf-8">
  <meta name="robots" content="noindex, nofollow">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
    @import url("https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;700&display=swap");

    :root {
      --golden: #C29570;
      --golden-dark: #9c744f;
      --bg: #f4f1ee;
      --card-bg: #ffffff;
      --ink: #2b2b2b;
      --muted: #8a8177;
      --border: #e4ddd4;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Source Sans Pro', system-ui, -apple-system, sans-serif;
      background: var(--bg);
      color: var(--ink);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
    }

    .card {
      background: var(--card-bg);
      width: 100%;
      max-width: 420px;
      border-radius: 24px;
      padding: 2.5rem 2rem;
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
      border-top: 4px solid var(--golden);
    }

    h1 {
      text-align: center;
      font-size: 1.6rem;
      font-weight: 700;
      letter-spacing: -0.3px;
    }

    .subtitle {
      text-align: center;
      color: var(--muted);
      margin-top: 0.4rem;
      margin-bottom: 2rem;
      font-size: 0.98rem;
    }

    .field { margin-bottom: 1.1rem; }

    label {
      display: block;
      font-size: 0.85rem;
      font-weight: 700;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: 0.4px;
      margin-bottom: 0.4rem;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 0.8rem 0.9rem;
      font-size: 1rem;
      font-family: inherit;
      color: var(--ink);
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 12px;
      transition: border-color 0.15s, box-shadow 0.15s;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
      outline: none;
      border-color: var(--golden);
      box-shadow: 0 0 0 3px rgba(194, 149, 112, 0.2);
    }

    .password-wrap { position: relative; }
    .toggle-pass {
      position: absolute;
      right: 0.6rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      font-size: 1.1rem;
      color: var(--muted);
      padding: 0.25rem;
      line-height: 1;
    }

    button.submit {
      width: 100%;
      margin-top: 0.8rem;
      padding: 0.9rem;
      font-size: 1rem;
      font-weight: 700;
      font-family: inherit;
      color: #fff;
      background: var(--golden);
      border: none;
      border-radius: 24px;
      cursor: pointer;
      transition: background 0.15s, transform 0.05s;
    }

    button.submit:hover { background: var(--golden-dark); }
    button.submit:active { transform: translateY(1px); }
    button.submit:disabled { opacity: 0.7; cursor: default; }
  </style>
</head>

<body>
  <div class="card">
    <h1>Captive portal</h1>
    <p class="subtitle">Please sign in to access the wifi.</p>

    <form action="connect.php" method="post">
      <input name="credentialId" type="hidden" value="">

      <div class="field">
        <label for="username">AD username</label>
        <input autofocus required id="username" name="username" type="text"
          placeholder="AD username" autocomplete="username">
      </div>

      <div class="field">
        <label for="password">Password</label>
        <div class="password-wrap">
          <input required id="password" name="password" type="password" placeholder="Password" autocomplete="current-password">
          <button type="button" class="toggle-pass" id="togglePass" aria-label="Show password">👁️</button>
        </div>
      </div>

      <button class="submit" name="login" type="submit">Sign in</button>
    </form>
  </div>

  <script>
    // Show / hide the password.
    var toggle = document.getElementById('togglePass');
    var pass = document.getElementById('password');
    toggle.addEventListener('click', function () {
      var show = pass.type === 'password';
      pass.type = show ? 'text' : 'password';
      toggle.textContent = show ? '🙈' : '👁️';
    });
  </script>
</body>

</html>
