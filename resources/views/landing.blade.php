<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CurrLens — Deteksi & Konversi Mata Uang</title>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --bg: #04060f;
      --surface: #0c0f1e;
      --surface2: #111627;
      --border: rgba(255,255,255,0.07);
      --accent: #f5c842;
      --accent2: #3b82f6;
      --text: #eef0f8;
      --muted: #6b7280;
      --muted2: #9ca3af;
      --card: #0e1220;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body {
      background: var(--bg); color: var(--text);
      font-family: 'DM Sans', sans-serif; font-size: 16px;
      line-height: 1.6; overflow-x: hidden;
    }
    body::before {
      content: ''; position: fixed; inset: 0;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.03'/%3E%3C/svg%3E");
      pointer-events: none; z-index: 999; opacity: .5;
    }
    h1,h2,h3,h4,h5 { font-family: 'Syne', sans-serif; line-height: 1.1; }

    /* ── NAV ── */
    nav {
      position: fixed; top: 0; left: 0; right: 0; z-index: 100;
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 5%; height: 68px;
      background: rgba(4,6,15,0.80); backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--border);
    }
    .nav-logo {
      display: flex; align-items: center; gap: 10px;
      font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.25rem;
      color: var(--text); text-decoration: none; letter-spacing: -.5px;
    }
    .nav-logo .dot {
      width: 30px; height: 30px; border-radius: 8px;
      background: linear-gradient(135deg, var(--accent), #f59e0b);
      display: grid; place-items: center; font-size: .85rem; color: #04060f;
    }
    .nav-links { display: flex; gap: 32px; }
    .nav-links a { text-decoration: none; color: var(--muted2); font-size: .92rem; transition: color .2s; }
    .nav-links a:hover { color: var(--text); }
    .nav-cta {
      background: var(--accent); color: #04060f; border: none;
      padding: 9px 22px; border-radius: 8px;
      font-family: 'Syne', sans-serif; font-weight: 700; font-size: .88rem;
      cursor: pointer; transition: opacity .2s, transform .15s;
    }
    .nav-cta:hover { opacity: .88; transform: translateY(-1px); }

    /* ── HERO ── */
    .hero {
      min-height: 100vh; display: flex; flex-direction: column;
      align-items: center; justify-content: center; text-align: center;
      padding: 120px 5% 100px; position: relative; overflow: hidden;
    }

    /* background glow */
    .hero-glow {
      position: absolute; top: 0; left: 50%; transform: translateX(-50%);
      width: 900px; height: 600px;
      background: radial-gradient(ellipse at center, rgba(245,200,66,.1) 0%, transparent 65%);
      pointer-events: none;
    }

    /* ── FLOATING BANKNOTES ── */
    .bill {
      position: absolute;
      border-radius: 14px;
      display: flex; align-items: center; justify-content: center;
      font-family: 'Syne', sans-serif; font-weight: 800;
      pointer-events: none; user-select: none;
      border: 1px solid rgba(255,255,255,.06);
      backdrop-filter: blur(2px);
      box-shadow: 0 8px 32px rgba(0,0,0,.4);
    }

    .bill-usd {
      width: 200px; height: 90px;
      background: linear-gradient(135deg, #0a2e1a 0%, #0d3d22 50%, #0a2e1a 100%);
      border-color: rgba(79,255,120,.12);
      top: 14%; left: 4%;
      transform: rotate(-12deg);
      animation: float1 7s ease-in-out infinite;
    }
    .bill-eur {
      width: 220px; height: 96px;
      background: linear-gradient(135deg, #0d1a3a 0%, #0f2255 50%, #0d1a3a 100%);
      border-color: rgba(59,130,246,.15);
      top: 18%; right: 3%;
      transform: rotate(10deg);
      animation: float2 8s ease-in-out infinite;
    }
    .bill-jpy {
      width: 190px; height: 84px;
      background: linear-gradient(135deg, #2a1a0a 0%, #3d2810 50%, #2a1a0a 100%);
      border-color: rgba(245,200,66,.12);
      bottom: 22%; left: 6%;
      transform: rotate(8deg);
      animation: float3 6.5s ease-in-out infinite;
    }
    .bill-gbp {
      width: 195px; height: 88px;
      background: linear-gradient(135deg, #1a0a2e 0%, #280f42 50%, #1a0a2e 100%);
      border-color: rgba(168,85,247,.15);
      bottom: 18%; right: 5%;
      transform: rotate(-9deg);
      animation: float4 9s ease-in-out infinite;
    }
    .bill-sgd {
      width: 175px; height: 78px;
      background: linear-gradient(135deg, #1a0d0d 0%, #2e1515 50%, #1a0d0d 100%);
      border-color: rgba(239,68,68,.12);
      top: 55%; left: 50%; transform: translateX(-50%) rotate(5deg);
      animation: float5 7.5s ease-in-out infinite;
    }

    /* bill inner layout */
    .bill-inner {
      width: 100%; height: 100%; padding: 10px 14px;
      display: flex; flex-direction: column; justify-content: space-between;
      position: relative; overflow: hidden;
    }
    .bill-inner::before {
      content: ''; position: absolute; inset: 0;
      background: repeating-linear-gradient(45deg, transparent, transparent 8px, rgba(255,255,255,.015) 8px, rgba(255,255,255,.015) 9px);
    }
    .bill-top { display: flex; justify-content: space-between; align-items: flex-start; }
    .bill-flag { font-size: 1rem; }
    .bill-country { font-size: .55rem; color: rgba(255,255,255,.35); letter-spacing: .08em; text-transform: uppercase; }
    .bill-amount { font-size: 1.55rem; letter-spacing: -1px; }
    .bill-code { font-size: .6rem; letter-spacing: .12em; opacity: .5; }

    /* bill colours per currency */
    .bill-usd .bill-amount { color: #4dff91; }
    .bill-eur .bill-amount { color: #60a5fa; }
    .bill-jpy .bill-amount { color: var(--accent); }
    .bill-gbp .bill-amount { color: #c084fc; }
    .bill-sgd .bill-amount { color: #f87171; }

    /* coin floaters */
    .coin {
      position: absolute; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.1rem;
      pointer-events: none;
      box-shadow: 0 4px 16px rgba(0,0,0,.5);
      border: 1px solid rgba(255,255,255,.08);
    }
    .coin1 { width:52px;height:52px; background:linear-gradient(135deg,#b8860b,#daa520); top:38%;left:14%; animation:float2 5s ease-in-out infinite; }
    .coin2 { width:44px;height:44px; background:linear-gradient(135deg,#1c3d5a,#2563eb); top:28%;right:14%; animation:float3 6s ease-in-out infinite; }
    .coin3 { width:40px;height:40px; background:linear-gradient(135deg,#5b2333,#b91c1c); bottom:32%;left:22%; animation:float1 7s ease-in-out infinite; }
    .coin4 { width:48px;height:48px; background:linear-gradient(135deg,#1a3a1a,#16a34a); bottom:28%;right:18%; animation:float4 5.5s ease-in-out infinite; }

    /* scan line on hero bg */
    .scan-line {
      position: absolute; left: 0; right: 0; height: 2px;
      background: linear-gradient(90deg, transparent 0%, rgba(245,200,66,.4) 40%, rgba(245,200,66,.6) 50%, rgba(245,200,66,.4) 60%, transparent 100%);
      animation: scanMove 4s linear infinite;
      pointer-events: none;
    }
    @keyframes scanMove { from{top:20%} to{top:80%} }

    @keyframes float1 { 0%,100%{transform:rotate(-12deg) translateY(0)} 50%{transform:rotate(-10deg) translateY(-14px)} }
    @keyframes float2 { 0%,100%{transform:rotate(10deg) translateY(0)} 50%{transform:rotate(12deg) translateY(-18px)} }
    @keyframes float3 { 0%,100%{transform:rotate(8deg) translateY(0)} 50%{transform:rotate(6deg) translateY(-12px)} }
    @keyframes float4 { 0%,100%{transform:rotate(-9deg) translateY(0)} 50%{transform:rotate(-11deg) translateY(-16px)} }
    @keyframes float5 { 0%,100%{transform:translateX(-50%) rotate(5deg) translateY(0)} 50%{transform:translateX(-50%) rotate(3deg) translateY(-10px)} }

    /* hero content (sits above floating bills) */
    .hero-content { position: relative; z-index: 2; display: flex; flex-direction: column; align-items: center; text-align: center; width: 100%; }

    .hero-badge {
      display: inline-flex; align-items: center; gap: 8px;
      background: rgba(245,200,66,.08); border: 1px solid rgba(245,200,66,.25);
      color: var(--accent); padding: 6px 16px; border-radius: 100px;
      font-size: .8rem; font-weight: 600; letter-spacing: .05em; text-transform: uppercase;
      margin-bottom: 28px; animation: fadeUp .7s ease both;
    }
    .hero-badge span { width: 6px; height: 6px; background: var(--accent); border-radius: 50%; animation: blink 1.5s infinite; }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

    .hero h1 {
      font-size: clamp(2rem, 4vw, 3.4rem); font-weight: 800;
      letter-spacing: -1.5px; max-width: 640px;
      text-align: center; margin-left: auto; margin-right: auto;
      background: linear-gradient(160deg, #fff 30%, rgba(255,255,255,.45));
      -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
      animation: fadeUp .8s .1s ease both;
    }
    .hero h1 em {
      font-style: normal;
      background: linear-gradient(90deg, var(--accent), #fde68a);
      -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    }
    .hero p {
      max-width: 500px; color: var(--muted2); font-size: 1.08rem;
      margin: 24px auto 0; font-weight: 300; line-height: 1.75;
      animation: fadeUp .8s .2s ease both;
    }
    .hero-actions {
      display: flex; gap: 14px; margin-top: 40px; flex-wrap: wrap; justify-content: center;
      animation: fadeUp .8s .3s ease both;
    }
    .btn-primary {
      background: var(--accent); color: #04060f; padding: 14px 32px; border-radius: 10px; border: none;
      font-family: 'Syne', sans-serif; font-weight: 700; font-size: .95rem;
      cursor: pointer; transition: all .2s; box-shadow: 0 0 32px rgba(245,200,66,.3);
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 0 50px rgba(245,200,66,.5); }
    .btn-outline {
      background: transparent; color: var(--text); padding: 14px 32px; border-radius: 10px;
      border: 1px solid var(--border); font-family: 'Syne', sans-serif; font-weight: 600; font-size: .95rem;
      cursor: pointer; transition: all .2s;
    }
    .btn-outline:hover { border-color: rgba(255,255,255,.2); background: rgba(255,255,255,.04); }

    /* hero currency ticker */
    .hero-ticker {
      margin-top: 56px; position: relative; z-index: 2;
      background: rgba(255,255,255,.03); border: 1px solid var(--border);
      border-radius: 14px; padding: 16px 28px;
      display: flex; gap: 32px; flex-wrap: wrap; justify-content: center;
      animation: fadeUp .9s .5s ease both;
      backdrop-filter: blur(12px);
    }
    .ticker-item { display: flex; align-items: center; gap: 10px; }
    .ticker-flag { font-size: 1.2rem; }
    .ticker-info { }
    .ticker-code { font-family: 'Syne', sans-serif; font-weight: 700; font-size: .92rem; }
    .ticker-rate { font-size: .75rem; color: var(--muted); }
    .ticker-change { font-size: .72rem; font-weight: 600; }
    .up { color: #34d399; }
    .dn { color: #f87171; }
    .ticker-sep { width: 1px; height: 32px; background: var(--border); }

    /* ── COMMON ── */
    section { padding: 100px 5%; }
    .section-tag { display: inline-block; color: var(--accent); font-size: .78rem; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; margin-bottom: 14px; }
    .section-title {
      font-size: clamp(1.9rem, 4vw, 3rem); font-weight: 800; letter-spacing: -1px; max-width: 560px;
      background: linear-gradient(150deg, #fff 40%, rgba(255,255,255,.5));
      -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    }
    .section-sub { color: var(--muted2); max-width: 480px; font-size:.97rem; margin-top:16px; font-weight:300; }
    .divider { width: 48px; height: 2px; background: var(--accent); opacity: .3; margin: 48px auto; border-radius:2px; }

    /* chips */
    .chip { display:inline-flex;align-items:center;gap:6px; background:rgba(245,200,66,.07);border:1px solid rgba(245,200,66,.2); color:var(--accent);font-size:.72rem;font-weight:600;letter-spacing:.04em;padding:4px 12px;border-radius:99px; }
    .chip-blue { background:rgba(59,130,246,.07);border-color:rgba(59,130,246,.2);color:#60a5fa; }
    .chip-green { background:rgba(52,211,153,.07);border-color:rgba(52,211,153,.2);color:#34d399; }

    /* ── STATS ── */
    .stats-section { text-align: center; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-top: 56px; max-width: 960px; margin-left:auto; margin-right:auto; }
    .stat-card {
      background: var(--surface); border: 1px solid var(--border); border-radius: 18px; padding: 32px;
      transition: border-color .3s, transform .3s; position: relative; overflow: hidden;
    }
    .stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background: linear-gradient(90deg, var(--accent), #f59e0b); opacity:0; transition:opacity .3s; }
    .stat-card:hover { border-color: rgba(245,200,66,.25); transform: translateY(-4px); }
    .stat-card:hover::before { opacity:1; }
    .stat-img { width:100%; height:110px; background:var(--surface2); border-radius:10px; margin-bottom:20px; display:flex; align-items:center; justify-content:center; font-size:2.8rem; }
    .stat-num { font-family:'Syne',sans-serif; font-size:2.6rem; font-weight:800; color:var(--accent); }
    .stat-card h4 { font-size:1rem; font-weight:600; margin-bottom:8px; margin-top:4px; }
    .stat-card p { font-size:.85rem; color:var(--muted); font-weight:300; }

    /* ── FEATURES ── */
    .features-section { background: var(--surface); }
    .features-header { text-align:center; margin-bottom:60px; }
    .features-header .section-title, .features-header .section-sub { margin-left:auto; margin-right:auto; }
    .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; max-width: 1100px; margin: 0 auto; }
    .feature-card { background: var(--card); border: 1px solid var(--border); border-radius: 18px; padding: 28px; transition: all .3s; }
    .feature-card:hover { border-color: rgba(245,200,66,.2); transform:translateY(-3px); }
    .feat-icon { width:48px; height:48px; border-radius:12px; background:rgba(245,200,66,.08); display:grid; place-items:center; margin-bottom:18px; font-size:1.5rem; }
    .feat-img { width:100%; height:110px; background:var(--surface2); border-radius:10px; margin:16px 0; display:flex; align-items:center; justify-content:center; font-size:1.8rem; letter-spacing:6px; font-family:'Syne',sans-serif; font-weight:800; color:rgba(255,255,255,.15); }
    .feature-card h4 { font-size:1.05rem; font-weight:700; margin-bottom:10px; }
    .feature-card p { font-size:.85rem; color:var(--muted); font-weight:300; line-height:1.65; }

    /* ── ACCURACY ── */
    .accuracy-layout { display:grid; grid-template-columns:1fr 1fr; gap:24px; max-width:900px; margin:56px auto 0; }
    .acc-card { background: var(--surface); border: 1px solid var(--border); border-radius: 18px; padding: 28px; transition: all .3s; }
    .acc-card:hover { border-color: rgba(245,200,66,.2); transform:translateY(-3px); }
    .acc-label { font-size:.8rem; color:var(--muted2); font-weight:500; margin-bottom:12px; letter-spacing:.04em; }
    .acc-img { width:100%; height:140px; background:var(--surface2); border-radius:10px; margin-bottom:16px; display:flex; align-items:center; justify-content:center; position:relative; overflow:hidden; }
    .acc-caption { font-size:.82rem; color:var(--muted); text-align:center; font-weight:300; }
    .acc-bar-row { display:flex; align-items:center; justify-content:space-between; margin-bottom:6px; }
    .acc-bar-row span:first-child { font-size:.8rem; color:var(--muted2); }
    .acc-bar-row span:last-child { font-size:.8rem; color:var(--accent); font-weight:600; }
    .mini-bar { height:4px;background:rgba(255,255,255,.06);border-radius:99px; margin-bottom:10px; overflow:hidden; }
    .mini-fill { height:100%; background: linear-gradient(90deg, #f59e0b, var(--accent)); border-radius:99px; }

    /* ── DEMO ── */
    .demo-section { text-align:center; }
    .demo-card { max-width:700px; margin:56px auto 0; background:var(--surface); border:1px solid var(--border); border-radius:24px; padding:40px; position:relative; overflow:hidden; }
    .demo-card::before { content:''; position:absolute; top:-60px;left:50%;transform:translateX(-50%); width:300px;height:200px; background:radial-gradient(ellipse,rgba(245,200,66,.1),transparent 70%); pointer-events:none; }
    .demo-upload { width:100%; min-height:180px; border:2px dashed rgba(245,200,66,.25); border-radius:16px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:14px; cursor:pointer; transition:border-color .3s,background .3s; background:rgba(245,200,66,.02); }
    .demo-upload:hover { border-color:var(--accent); background:rgba(245,200,66,.05); }
    .upload-icon { width:52px;height:52px;border-radius:14px;background:rgba(245,200,66,.1);display:grid;place-items:center;font-size:1.6rem; }
    .demo-upload p { font-size:.88rem;color:var(--muted);font-weight:300; }
    .demo-output-label { text-align:left; font-size:.78rem; color:var(--muted); letter-spacing:.07em; text-transform:uppercase; font-weight:500; margin:24px 0 10px; }
    .output-row { background:var(--card); border-radius:10px; padding:14px 18px; margin-bottom:10px; }
    .output-row.active { border:1px solid rgba(245,200,66,.2); }
    .output-row input { background:transparent; border:none; outline:none; color:var(--muted2); font-size:.88rem; width:100%; font-family:'DM Sans',sans-serif; }
    .demo-convert-box { background:var(--card); border:1px solid rgba(245,200,66,.15); border-radius:12px; padding:20px; margin-top:14px; }
    .demo-convert-row { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
    .demo-convert-row select { flex:1; background:var(--surface2); border:1px solid var(--border); color:var(--text); padding:10px 14px; border-radius:8px; font-size:.88rem; font-family:'DM Sans',sans-serif; outline:none; cursor:pointer; }
    .swap-btn { background:rgba(245,200,66,.1); border:1px solid rgba(245,200,66,.2); color:var(--accent); width:36px;height:36px;border-radius:8px; display:grid;place-items:center; cursor:pointer; font-size:1rem; transition:background .2s; flex-shrink:0; }
    .swap-btn:hover { background:rgba(245,200,66,.2); }
    .demo-result-num { font-family:'Syne',sans-serif; font-size:2rem; font-weight:800; color:var(--accent); text-align:center; padding:10px 0 4px; }
    .demo-result-sub { font-size:.78rem;color:var(--muted);text-align:center; }
    .demo-stats { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:14px; }
    .demo-stat { background:var(--card); border-radius:10px; padding:14px 16px; }
    .demo-stat label { font-size:.72rem;color:var(--muted);letter-spacing:.06em;text-transform:uppercase; }
    .demo-stat .dval { font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;color:var(--accent);margin-top:2px; }
    .demo-buttons { display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:16px; }
    .btn-upload-d { background:var(--accent);color:#04060f;padding:13px;border-radius:10px;border:none; font-family:'Syne',sans-serif;font-weight:700;font-size:.88rem;cursor:pointer; display:flex;align-items:center;justify-content:center;gap:8px; transition:opacity .2s; }
    .btn-upload-d:hover { opacity:.85; }
    .btn-cam { background:transparent;color:var(--text);padding:13px;border-radius:10px; border:1px solid var(--border);font-family:'Syne',sans-serif;font-weight:600;font-size:.88rem; cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px; transition:border-color .2s; }
    .btn-cam:hover { border-color:rgba(255,255,255,.2); }

    /* ── FOOTER ── */
    footer { background:var(--surface2); border-top:1px solid var(--border); padding:60px 5% 32px; }
    .footer-grid { display:grid;grid-template-columns:2fr 1fr 1fr;gap:48px;max-width:1000px; }
    .footer-logo { display:flex;align-items:center;gap:10px;margin-bottom:16px; font-family:'Syne',sans-serif;font-weight:800;font-size:1.1rem; }
    .footer-logo .dot { width:26px;height:26px;border-radius:7px;background:linear-gradient(135deg,var(--accent),#f59e0b);display:grid;place-items:center;font-size:.75rem;color:#04060f; }
    footer p { font-size:.85rem;color:var(--muted);font-weight:300;max-width:260px;line-height:1.7; }
    .footer-col h5 { font-family:'Syne',sans-serif;font-size:.85rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--muted2);margin-bottom:18px; }
    .footer-col a { display:block;color:var(--muted);font-size:.85rem;text-decoration:none;margin-bottom:12px;transition:color .2s; }
    .footer-col a:hover { color:var(--text); }
    .footer-bottom { margin-top:40px;padding-top:24px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px; }
    .footer-bottom p { font-size:.78rem;color:var(--muted); }
    .footer-bottom a { color:var(--muted);font-size:.78rem;text-decoration:none;margin-left:20px; }
    .footer-bottom a:hover { color:var(--accent); }

    /* ── ANIMATIONS ── */
    @keyframes fadeUp { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:translateY(0)} }
    .reveal { opacity:0; transform:translateY(30px); transition:opacity .7s ease,transform .7s ease; }
    .reveal.visible { opacity:1; transform:translateY(0); }

    @media(max-width:900px){
      .bill,.coin,.scan-line{display:none;}
      .accuracy-layout{grid-template-columns:1fr;}
      .footer-grid{grid-template-columns:1fr;}
      .demo-buttons,.demo-stats{grid-template-columns:1fr;}
      .stats-grid{grid-template-columns:1fr;}
      .nav-links{display:none;}
      .hero-ticker{gap:16px;}
      .ticker-sep{display:none;}
    }
  </style>
</head>
<body>

<!-- NAV -->
<nav>
  <a href="#" class="nav-logo">
    <span class="dot">₿</span> CurrLens
  </a>
  <div class="nav-links">
    <a href="#fitur">Fitur</a>
    <a href="#akurasi">Akurasi</a>
    <a href="#demo">Demo</a>
  </div>
  <button class="nav-cta">Coba Sekarang</button>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-glow"></div>
  <div class="scan-line"></div>

  <!-- Floating banknotes -->
  <div class="bill bill-usd">
    <div class="bill-inner">
      <div class="bill-top">
        <span class="bill-flag">🇺🇸</span>
        <span class="bill-country">United States</span>
      </div>
      <div class="bill-amount">$100</div>
      <div class="bill-code">US DOLLAR · USD</div>
    </div>
  </div>

  <div class="bill bill-eur">
    <div class="bill-inner">
      <div class="bill-top">
        <span class="bill-flag">🇪🇺</span>
        <span class="bill-country">European Union</span>
      </div>
      <div class="bill-amount">€50</div>
      <div class="bill-code">EURO · EUR</div>
    </div>
  </div>

  <div class="bill bill-jpy">
    <div class="bill-inner">
      <div class="bill-top">
        <span class="bill-flag">🇯🇵</span>
        <span class="bill-country">Japan</span>
      </div>
      <div class="bill-amount">¥1000</div>
      <div class="bill-code">JAPANESE YEN · JPY</div>
    </div>
  </div>

  <div class="bill bill-gbp">
    <div class="bill-inner">
      <div class="bill-top">
        <span class="bill-flag">🇬🇧</span>
        <span class="bill-country">United Kingdom</span>
      </div>
      <div class="bill-amount">£50</div>
      <div class="bill-code">BRITISH POUND · GBP</div>
    </div>
  </div>

  <div class="bill bill-sgd">
    <div class="bill-inner">
      <div class="bill-top">
        <span class="bill-flag">🇸🇬</span>
        <span class="bill-country">Singapore</span>
      </div>
      <div class="bill-amount">S$50</div>
      <div class="bill-code">SINGAPORE DOLLAR · SGD</div>
    </div>
  </div>

  <!-- Floating coins -->
  <div class="coin coin1">🪙</div>
  <div class="coin coin2">💶</div>
  <div class="coin coin3">💷</div>
  <div class="coin coin4">💵</div>

  <!-- Hero content -->
  <div class="hero-content">
    <div class="hero-badge"><span></span> AI Deteksi Mata Uang #1</div>
    <h1>Foto Uangmu,<br>Kami <em>Deteksi & Konversi</em></h1>
    <p>Cukup foto uang kertas dari kamera — CurrLens mengenali mata uang dari seluruh dunia dan langsung mengkonversinya ke mata uang pilihanmu dalam hitungan detik.</p>
    <div class="hero-actions">
      <button class="btn-primary">Coba Gratis Sekarang</button>
      <button class="btn-outline">Lihat Demo →</button>
    </div>

    <!-- Live ticker -->
    <div class="hero-ticker">
      <div class="ticker-item">
        <div class="ticker-flag">🇺🇸</div>
        <div class="ticker-info">
          <div class="ticker-code">USD / IDR</div>
          <div class="ticker-rate">Rp 15.980 <span class="ticker-change up">▲ 0.12%</span></div>
        </div>
      </div>
      <div class="ticker-sep"></div>
      <div class="ticker-item">
        <div class="ticker-flag">🇪🇺</div>
        <div class="ticker-info">
          <div class="ticker-code">EUR / IDR</div>
          <div class="ticker-rate">Rp 17.350 <span class="ticker-change up">▲ 0.08%</span></div>
        </div>
      </div>
      <div class="ticker-sep"></div>
      <div class="ticker-item">
        <div class="ticker-flag">🇯🇵</div>
        <div class="ticker-info">
          <div class="ticker-code">JPY / IDR</div>
          <div class="ticker-rate">Rp 106.8 <span class="ticker-change dn">▼ 0.05%</span></div>
        </div>
      </div>
      <div class="ticker-sep"></div>
      <div class="ticker-item">
        <div class="ticker-flag">🇬🇧</div>
        <div class="ticker-info">
          <div class="ticker-code">GBP / IDR</div>
          <div class="ticker-rate">Rp 20.180 <span class="ticker-change up">▲ 0.21%</span></div>
        </div>
      </div>
      <div class="ticker-sep"></div>
      <div class="ticker-item">
        <div class="ticker-flag">🇸🇬</div>
        <div class="ticker-info">
          <div class="ticker-code">SGD / IDR</div>
          <div class="ticker-rate">Rp 11.920 <span class="ticker-change dn">▼ 0.03%</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- STATS -->
<section class="stats-section" id="stats">
  <div class="section-tag">Dipercaya Pengguna Global</div>
  <h2 class="section-title" style="margin:0 auto;">Telah Membantu</h2>
  <p class="section-sub" style="margin:16px auto 0;">Dari traveler, pedagang, hingga money changer — CurrLens adalah teman setia setiap transaksi lintas negara.</p>
  <div class="stats-grid">
    <div class="stat-card reveal">
      <div class="stat-img">🌍</div>
      <div class="stat-num">150+</div>
      <h4>Mata Uang Didukung</h4>
      <p>Dollar, Euro, Yen, Rupiah, hingga mata uang langka dari seluruh penjuru dunia.</p>
    </div>
    <div class="stat-card reveal">
      <div class="stat-img">👥</div>
      <div class="stat-num">50K+</div>
      <h4>Pengguna Aktif</h4>
      <p>Traveler, pedagang, money changer, dan pebisnis internasional menggunakan CurrLens.</p>
    </div>
    <div class="stat-card reveal">
      <div class="stat-img">⚡</div>
      <div class="stat-num">0.5s</div>
      <h4>Rata-rata Deteksi</h4>
      <p>Deteksi dan konversi selesai kurang dari 1 detik. Secepat kamu membalik uangnya.</p>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- FEATURES -->
<section class="features-section" id="fitur">
  <div class="features-header">
    <div class="section-tag">Kemampuan Platform</div>
    <h2 class="section-title">Fitur Kami</h2>
    <p class="section-sub">Teknologi computer vision terdepan dikombinasi data kurs live untuk pengalaman konversi yang sempurna.</p>
  </div>
  <div class="features-grid">
    <div class="feature-card reveal">
      <div class="feat-icon">📸</div>
      <h4>Deteksi Visual AI</h4>
      <div class="feat-img">$ € ¥ £</div>
      <p>Model AI kami dilatih dengan jutaan foto uang kertas dari seluruh dunia. Cukup foto — sisanya kami yang urus.</p>
    </div>
    <div class="feature-card reveal">
      <div class="feat-icon">🔄</div>
      <h4>Konversi Real-time</h4>
      <div class="feat-img" style="font-size:1rem;letter-spacing:3px;">Rp → $ → ¥</div>
      <p>Kurs diperbarui setiap menit dari sumber terpercaya. Konversi ke 150+ mata uang secara instan.</p>
    </div>
    <div class="feature-card reveal">
      <div class="feat-icon">📷</div>
      <h4>Kamera & Upload</h4>
      <div class="feat-img">📱 → 💵</div>
      <p>Gunakan kamera langsung atau upload dari galeri. Mendukung foto buram, pencahayaan rendah, dan sudut miring.</p>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- ACCURACY -->
<section id="akurasi" style="text-align:center;">
  <div class="section-tag">Benchmark & Metrik</div>
  <h2 class="section-title" style="margin:0 auto;">Tingkat Akurasi<br>dan Validasi</h2>
  <p class="section-sub" style="margin:16px auto 0;">Model kami diuji terhadap ribuan sampel uang kertas dari berbagai kondisi dan pencahayaan.</p>
  <div class="accuracy-layout">
    <div class="acc-card reveal">
      <div class="acc-label">Tingkat Akurasi Deteksi</div>
      <div class="acc-img">
        <div style="position:absolute;bottom:12px;left:12px;right:12px;display:flex;align-items:flex-end;gap:5px;height:90px;">
          <div style="flex:1;background:rgba(245,200,66,.2);border-radius:4px 4px 0 0;height:50%;"></div>
          <div style="flex:1;background:rgba(245,200,66,.35);border-radius:4px 4px 0 0;height:68%;"></div>
          <div style="flex:1;background:rgba(245,200,66,.5);border-radius:4px 4px 0 0;height:82%;"></div>
          <div style="flex:1;background:var(--accent);border-radius:4px 4px 0 0;height:97%;"></div>
          <div style="flex:1;background:rgba(245,200,66,.4);border-radius:4px 4px 0 0;height:75%;"></div>
          <div style="flex:1;background:rgba(245,200,66,.25);border-radius:4px 4px 0 0;height:58%;"></div>
        </div>
        <div style="position:absolute;top:12px;right:14px;font-family:'Syne',sans-serif;font-size:1.6rem;font-weight:800;color:var(--accent);">97.1%</div>
      </div>
      <div class="acc-caption" style="color:var(--accent);font-size:1.1rem;font-weight:700;font-family:'Syne',sans-serif;">Mean Detection Accuracy</div>
    </div>
    <div class="acc-card reveal">
      <div class="acc-label">Dataset Pelatihan</div>
      <div class="acc-img">
        <div style="position:absolute;inset:14px;display:grid;grid-template-columns:1fr 1fr 1fr;grid-template-rows:1fr 1fr;gap:6px;">
          <div style="background:rgba(245,200,66,.15);border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">💵</div>
          <div style="background:rgba(59,130,246,.12);border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">💶</div>
          <div style="background:rgba(245,200,66,.1);border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">💴</div>
          <div style="background:rgba(59,130,246,.1);border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">💷</div>
          <div style="background:rgba(245,200,66,.12);border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">🪙</div>
          <div style="background:rgba(59,130,246,.15);border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;">💸</div>
        </div>
      </div>
      <div class="acc-caption">2 juta+ gambar uang dari 150 negara</div>
    </div>
    <div class="acc-card reveal">
      <div class="acc-label">Hasil Pengujian</div>
      <div class="acc-img" style="align-items:flex-start;padding:16px;">
        <div style="width:100%">
          <div class="acc-bar-row"><span>Deteksi Nominal</span><span>97.1%</span></div>
          <div class="mini-bar"><div class="mini-fill" style="width:97.1%"></div></div>
          <div class="acc-bar-row"><span>Deteksi Negara Asal</span><span>98.4%</span></div>
          <div class="mini-bar"><div class="mini-fill" style="width:98.4%"></div></div>
          <div class="acc-bar-row"><span>Kondisi Foto Buram</span><span>89.2%</span></div>
          <div class="mini-bar"><div class="mini-fill" style="width:89.2%"></div></div>
          <div class="acc-bar-row"><span>Pencahayaan Rendah</span><span>85.7%</span></div>
          <div class="mini-bar"><div class="mini-fill" style="width:85.7%"></div></div>
        </div>
      </div>
      <div class="acc-caption">Diuji pada 50.000+ sampel independen</div>
    </div>
    <div class="acc-card reveal">
      <div class="acc-label">Standar Referensi Kurs</div>
      <div class="acc-img">
        <div style="display:flex;flex-direction:column;gap:10px;width:100%;padding:14px;">
          <div style="display:flex;align-items:center;justify-content:space-between;background:rgba(245,200,66,.07);border-radius:8px;padding:10px 14px;">
            <span style="font-size:.82rem;color:var(--muted2);">🏦 Bank Indonesia</span>
            <span class="chip" style="font-size:.65rem;padding:3px 8px;">Live</span>
          </div>
          <div style="display:flex;align-items:center;justify-content:space-between;background:rgba(59,130,246,.07);border-radius:8px;padding:10px 14px;">
            <span style="font-size:.82rem;color:var(--muted2);">🌐 Open Exchange Rates</span>
            <span class="chip-blue chip" style="font-size:.65rem;padding:3px 8px;">API</span>
          </div>
          <div style="display:flex;align-items:center;justify-content:space-between;background:rgba(245,200,66,.05);border-radius:8px;padding:10px 14px;">
            <span style="font-size:.82rem;color:var(--muted2);">📊 Fixer.io</span>
            <span class="chip" style="font-size:.65rem;padding:3px 8px;">Realtime</span>
          </div>
        </div>
      </div>
      <div class="acc-caption">Kurs diperbarui setiap 60 detik</div>
    </div>
  </div>
</section>

<div class="divider"></div>

<!-- DEMO -->
<section class="demo-section" id="demo">
  <div class="section-tag">Coba Langsung</div>
  <h2 class="section-title" style="margin:0 auto;">Coba Sekarang</h2>
  <p class="section-sub" style="margin:16px auto 0;">Upload foto uang kertas kamu dan lihat hasilnya — deteksi + konversi dalam hitungan detik.</p>
  <div class="demo-card reveal">
    <div class="demo-upload" id="demo-drop">
      <div class="upload-icon">📷</div>
      <p>Upload foto uang kertas untuk dideteksi</p>
      <span class="chip">Drag &amp; Drop atau Klik</span>
    </div>
    <div class="demo-output-label">Hasil Deteksi</div>
    <div class="output-row active">
      <input type="text" id="detect-output" placeholder="Mata uang yang terdeteksi akan muncul di sini…" readonly />
    </div>
    <div class="demo-convert-box">
      <div style="font-size:.78rem;color:var(--muted);letter-spacing:.06em;text-transform:uppercase;font-weight:500;margin-bottom:14px;text-align:left;">Konversi ke</div>
      <div class="demo-convert-row">
        <select id="from-cur">
          <option value="USD">🇺🇸 USD — US Dollar</option>
          <option value="EUR">🇪🇺 EUR — Euro</option>
          <option value="JPY">🇯🇵 JPY — Japanese Yen</option>
          <option value="GBP">🇬🇧 GBP — British Pound</option>
          <option value="SGD">🇸🇬 SGD — Singapore Dollar</option>
        </select>
        <div class="swap-btn" onclick="swapCurrency()">⇄</div>
        <select id="to-cur">
          <option value="IDR" selected>🇮🇩 IDR — Indonesian Rupiah</option>
          <option value="USD">🇺🇸 USD — US Dollar</option>
          <option value="EUR">🇪🇺 EUR — Euro</option>
          <option value="MYR">🇲🇾 MYR — Malaysian Ringgit</option>
          <option value="SGD">🇸🇬 SGD — Singapore Dollar</option>
        </select>
      </div>
      <div class="demo-result-num" id="convert-result">—</div>
      <div class="demo-result-sub" id="convert-sub">Upload foto uang untuk melihat hasil konversi</div>
    </div>
    <div class="demo-stats">
      <div class="demo-stat">
        <label>Tingkat Akurasi</label>
        <div class="dval" id="acc-val">—</div>
      </div>
      <div class="demo-stat">
        <label>Waktu Proses</label>
        <div class="dval" style="color:var(--accent2)" id="time-val">—</div>
      </div>
    </div>
    <div style="margin-top:16px;">
      <button class="btn-upload-d" style="width:100%;" onclick="document.getElementById('demo-drop').click()">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
        Upload Foto
      </button>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-grid">
    <div class="footer-brand">
      <div class="footer-logo"><span class="dot">₿</span> CurrLens</div>
      <p>Deteksi dan konversi mata uang dari foto — cepat, akurat, dan selalu update dengan kurs terkini.</p>
    </div>
    <div class="footer-col">
      <h5>Produk</h5>
      <a href="#">Fitur</a><a href="#">Mata Uang</a><a href="#">API</a><a href="#">Dokumentasi</a>
    </div>
    <div class="footer-col">
      <h5>Perusahaan</h5>
      <a href="#">Tentang Kami</a><a href="#">Blog</a><a href="#">Karir</a><a href="#">Kontak</a>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© 2025 CurrLens. All rights reserved.</p>
    <div><a href="#">Terms &amp; Conditions</a><a href="#">Privacy Policy</a></div>
  </div>
</footer>

<script>
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('visible'); });
  }, { threshold: 0.1 });
  document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

  const mockCurrencies = [
    { name:'US Dollar',         code:'USD', symbol:'$',  nominal:100,  flag:'🇺🇸' },
    { name:'Euro',              code:'EUR', symbol:'€',  nominal:50,   flag:'🇪🇺' },
    { name:'Japanese Yen',      code:'JPY', symbol:'¥',  nominal:1000, flag:'🇯🇵' },
    { name:'British Pound',     code:'GBP', symbol:'£',  nominal:50,   flag:'🇬🇧' },
    { name:'Singapore Dollar',  code:'SGD', symbol:'S$', nominal:50,   flag:'🇸🇬' },
  ];
  const rates = {
    USD:{ IDR:15980, EUR:0.92, JPY:149.5, GBP:0.79, SGD:1.34, MYR:4.71, USD:1 },
    EUR:{ IDR:17350, USD:1.09, JPY:162.3, GBP:0.86, SGD:1.46, MYR:5.12, EUR:1 },
    JPY:{ IDR:106.8, USD:0.0067, EUR:0.0062, GBP:0.0053, SGD:0.009, MYR:0.031, JPY:1 },
    GBP:{ IDR:20180, USD:1.27, EUR:1.16, JPY:189.2, SGD:1.69, MYR:5.96, GBP:1 },
    SGD:{ IDR:11920, USD:0.75, EUR:0.68, JPY:111.6, GBP:0.59, MYR:3.52, SGD:1 },
  };
  let detectedCurrency = null;
  const symMap = {IDR:'Rp ',EUR:'€',GBP:'£',JPY:'¥',SGD:'S$',MYR:'RM ',USD:'$'};

  function formatNumber(n) {
    if(n>=1000) return n.toLocaleString('id-ID');
    if(n<0.01)  return n.toFixed(6);
    if(n<1)     return n.toFixed(4);
    return n.toLocaleString('id-ID',{maximumFractionDigits:2});
  }
  function doConvert() {
    if(!detectedCurrency) return;
    const from=document.getElementById('from-cur').value, to=document.getElementById('to-cur').value;
    const rate=(rates[from]&&rates[from][to])||1;
    const result=detectedCurrency.nominal*rate;
    const sym=symMap[to]||'';
    document.getElementById('convert-result').textContent=sym+formatNumber(result);
    document.getElementById('convert-sub').textContent=`1 ${from} = ${sym}${formatNumber(rate)} ${to} · Kurs diperbarui baru saja`;
  }
  function swapCurrency() {
    const f=document.getElementById('from-cur'),t=document.getElementById('to-cur');
    const tmp=f.value; f.value=t.value; t.value=tmp; doConvert();
  }
  document.getElementById('from-cur').addEventListener('change',doConvert);
  document.getElementById('to-cur').addEventListener('change',doConvert);

  const drop=document.getElementById('demo-drop');
  drop.addEventListener('click',()=>{
    const inp=document.createElement('input'); inp.type='file'; inp.accept='image/*';
    inp.onchange=(e)=>{
      if(!e.target.files[0]) return;
      drop.style.borderColor='var(--accent)';
      drop.style.background='rgba(245,200,66,.05)';
      drop.querySelector('p').textContent='⏳ Mendeteksi mata uang...';
      document.getElementById('detect-output').value='Menganalisis gambar...';
      document.getElementById('convert-result').textContent='...';
      document.getElementById('acc-val').textContent='...';
      document.getElementById('time-val').textContent='...';
      const start=Date.now();
      setTimeout(()=>{
        const picked=mockCurrencies[Math.floor(Math.random()*mockCurrencies.length)];
        detectedCurrency=picked;
        const elapsed=((Date.now()-start)/1000).toFixed(2);
        const conf=(94+Math.random()*5).toFixed(1);
        drop.querySelector('p').textContent='✓ '+e.target.files[0].name;
        document.getElementById('detect-output').value=`${picked.flag} Terdeteksi: ${picked.name} (${picked.code}) · Nominal: ${picked.symbol}${picked.nominal} · Confidence ${conf}%`;
        document.getElementById('acc-val').textContent=conf+'%';
        document.getElementById('time-val').textContent=elapsed+'s';
        const fs=document.getElementById('from-cur');
        for(let o of fs.options){ if(o.value===picked.code){fs.value=picked.code;break;} }
        doConvert();
      },1600);
    };
    inp.click();
  });
  drop.addEventListener('dragover',e=>{e.preventDefault();drop.style.borderColor='var(--accent)';});
  drop.addEventListener('dragleave',()=>{drop.style.borderColor='';});
  drop.addEventListener('drop',e=>{e.preventDefault();drop.style.borderColor='';});
</script>
</body>
</html>