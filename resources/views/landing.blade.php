<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'OrcaTech CRM') }}</title>
    <style>
        :root { color-scheme: dark; font-family: Inter, ui-sans-serif, system-ui, sans-serif; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: #07111f; color: #e5eef8; }
        main { width: min(1100px, calc(100% - 40px)); margin: 0 auto; padding: 72px 0; }
        .eyebrow { color: #5eead4; font-size: .78rem; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; }
        h1 { max-width: 760px; margin: 18px 0; font-size: clamp(2.5rem, 7vw, 5.5rem); line-height: .98; letter-spacing: -.05em; }
        p { max-width: 650px; color: #a8b8ca; font-size: 1.1rem; line-height: 1.7; }
        .actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 32px; }
        a { display: inline-block; border-radius: 10px; padding: 13px 20px; font-weight: 700; text-decoration: none; }
        .primary { background: #5eead4; color: #07111f; }
        .secondary { border: 1px solid #40536a; color: #e5eef8; }
        .panel { margin-top: 72px; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
        .card { border: 1px solid #1f344a; border-radius: 16px; padding: 22px; background: #0d1b2b; }
        .card strong { display: block; margin-bottom: 8px; color: #f8fafc; }
        .card span { color: #91a4b8; line-height: 1.5; }
    </style>
</head>
<body>
    <main>
        <div class="eyebrow">OrcaTech CRM</div>
        <h1>A foundation you can build on.</h1>
        <p>Customer relationships, sales activity, and team operations together in one Laravel CRM application.</p>
        <div class="actions">
            <a class="primary" href="/login">Sign in</a>
            <a class="secondary" href="/register">Get started free</a>
        </div>
        <section class="panel" aria-label="Product capabilities">
            <div class="card"><strong>Customer relationships</strong><span>Keep contacts, leads, and account activity organized.</span></div>
            <div class="card"><strong>Team operations</strong><span>Coordinate work across teams with one shared workspace.</span></div>
            <div class="card"><strong>Railway ready</strong><span>Production deployment with a dedicated liveness endpoint.</span></div>
        </section>
    </main>
</body>
</html>
