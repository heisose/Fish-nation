<?php
include('config/db.php');
$current_page = 'tools';

// Handle form submissions
$form_success = '';
$form_error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type'])) {
    $form_type = $_POST['form_type'];

    // Sanitize helper
    function s($v) { return htmlspecialchars(trim($v ?? ''), ENT_QUOTES); }

    if ($form_type === 'order') {
        $stmt = $conn->prepare("INSERT INTO order_requests (full_name, phone, state, lga, fish_type, size, quantity, delivery_method, delivery_date, budget, payment_method, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW())");
        $stmt->bind_param("ssssssissss",
            $_POST['full_name'], $_POST['phone'], $_POST['state'], $_POST['lga'],
            $_POST['fish_type'], $_POST['size'], $_POST['quantity'],
            $_POST['delivery_method'], $_POST['delivery_date'],
            $_POST['budget'], $_POST['payment_method']
        );
        $form_success = $stmt->execute() ? 'order' : '';

    } elseif ($form_type === 'bulk') {
        $stmt = $conn->prepare("INSERT INTO bulk_buyers (business_name, contact_name, phone, location, monthly_demand, delivery_frequency, budget_range, created_at) VALUES (?,?,?,?,?,?,?,NOW())");
        $stmt->bind_param("sssssss",
            $_POST['business_name'], $_POST['contact_name'], $_POST['phone'],
            $_POST['location'], $_POST['monthly_demand'],
            $_POST['delivery_frequency'], $_POST['budget_range']
        );
        $form_success = $stmt->execute() ? 'bulk' : '';

    } elseif ($form_type === 'mortality') {
        $stmt = $conn->prepare("INSERT INTO mortality_reports (farmer_name, phone, fish_age, symptoms, water_source, last_feed_type, notes, created_at) VALUES (?,?,?,?,?,?,?,NOW())");
        $stmt->bind_param("sssssss",
            $_POST['farmer_name'], $_POST['phone'], $_POST['fish_age'],
            implode(', ', $_POST['symptoms'] ?? []),
            $_POST['water_source'], $_POST['last_feed_type'], $_POST['notes']
        );
        $form_success = $stmt->execute() ? 'mortality' : '';

    } elseif ($form_type === 'education') {
        $stmt = $conn->prepare("INSERT INTO education_signups (full_name, phone, experience_level, biggest_challenge, created_at) VALUES (?,?,?,?,NOW())");
        $stmt->bind_param("ssss",
            $_POST['full_name'], $_POST['phone'],
            $_POST['experience_level'], $_POST['biggest_challenge']
        );
        $form_success = $stmt->execute() ? 'education' : '';

    } elseif ($form_type === 'logistics') {
        $stmt = $conn->prepare("INSERT INTO logistics_requests (contact_name, phone, pickup_location, delivery_location, quantity, urgency, transport_method, created_at) VALUES (?,?,?,?,?,?,?,NOW())");
        $stmt->bind_param("sssssss",
            $_POST['contact_name'], $_POST['phone'],
            $_POST['pickup_location'], $_POST['delivery_location'],
            $_POST['quantity'], $_POST['urgency'], $_POST['transport_method']
        );
        $form_success = $stmt->execute() ? 'logistics' : '';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Farm Tools & Calculators — FishNation</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Raleway:wght@600;700;800&display=swap" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
  <link href="assets/css/main.css" rel="stylesheet">

  <style>
    :root {
      --fn-dark:    #07150f;
      --fn-green:   #45A80D;
      --fn-lime:    #7ef29d;
      --fn-card:    rgba(7,21,15,0.82);
      --fn-border:  rgba(69,168,13,0.22);
      --fn-muted:   rgba(216,243,220,0.45);
      --fn-text:    #d8f3dc;
      --fn-gold:    #f0c040;
      --fn-red:     #e85555;
      --fn-orange:  #f07830;
    }

    *, *::before, *::after { box-sizing: border-box; }

    body {
      font-family: 'Inter', sans-serif;
      background: #07150f;
      color: var(--fn-text);
      min-height: 100vh;
    }

    /* ── Hero ── */
    .tools-hero {
      background:
        radial-gradient(ellipse at 20% 50%, rgba(69,168,13,0.18) 0%, transparent 60%),
        radial-gradient(ellipse at 80% 20%, rgba(126,242,157,0.08) 0%, transparent 50%),
        linear-gradient(180deg, #0a2e14 0%, #07150f 100%);
      padding: 80px 0 50px;
      text-align: center;
      border-bottom: 1px solid var(--fn-border);
    }
    .tools-hero .eyebrow {
      display: inline-block;
      background: rgba(69,168,13,0.15);
      border: 1px solid rgba(69,168,13,0.3);
      color: var(--fn-lime);
      font-size: 0.78rem;
      font-weight: 600;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      padding: 6px 16px;
      border-radius: 999px;
      margin-bottom: 20px;
    }
    .tools-hero h1 {
      font-family: 'Raleway', sans-serif;
      font-size: clamp(1.8rem, 3.5vw, 2.6rem);
      font-weight: 800;
      color: #fff;
      line-height: 1.2;
      margin-bottom: 16px;
      max-width: 700px;
      margin-left: auto;
      margin-right: auto;
    }
    .tools-hero h1 span { color: var(--fn-lime); }
    .tools-hero p {
      color: var(--fn-muted);
      font-size: 1.05rem;
      max-width: 580px;
      margin: 0 auto;
    }

    /* ── Tab nav ── */
    .tab-strip {
      display: flex;
      gap: 8px;
      overflow-x: auto;
      padding: 24px 0 0;
      scrollbar-width: none;
      flex-wrap: nowrap;
      justify-content: center;
    }
    .tab-strip::-webkit-scrollbar { display: none; }
    .tab-btn {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      padding: 9px 18px;
      border-radius: 999px;
      border: 1px solid var(--fn-border);
      background: transparent;
      color: var(--fn-muted);
      font-family: 'Inter', sans-serif;
      font-size: 0.85rem;
      font-weight: 500;
      cursor: pointer;
      white-space: nowrap;
      transition: all 0.2s;
    }
    .tab-btn:hover { border-color: var(--fn-green); color: var(--fn-lime); }
    .tab-btn.active {
      background: var(--fn-green);
      border-color: var(--fn-green);
      color: #fff;
      font-weight: 600;
    }
    .tab-btn i { font-size: 14px; }

    /* ── Main layout ── */
    .tools-body { padding: 40px 0 80px; }

    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

    /* ── Cards ── */
    .fn-card {
      background: var(--fn-card);
      border: 1px solid var(--fn-border);
      border-radius: 20px;
      padding: 28px;
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
    }
    .fn-card h3 {
      font-family: 'Raleway', sans-serif;
      font-size: 1.15rem;
      font-weight: 700;
      color: #fff;
      margin-bottom: 6px;
    }
    .fn-card .card-sub {
      color: var(--fn-muted);
      font-size: 0.85rem;
      margin-bottom: 24px;
      line-height: 1.5;
    }

    /* ── Form elements ── */
    .fn-label {
      display: block;
      font-size: 0.82rem;
      font-weight: 600;
      color: var(--fn-lime);
      letter-spacing: 0.5px;
      margin-bottom: 6px;
    }
    .fn-input, .fn-select, .fn-textarea {
      width: 100%;
      padding: 11px 14px;
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(69,168,13,0.2);
      border-radius: 10px;
      color: var(--fn-text);
      font-family: 'Inter', sans-serif;
      font-size: 0.92rem;
      outline: none;
      transition: border-color 0.2s, background 0.2s;
      margin-bottom: 16px;
    }
    .fn-input::placeholder, .fn-textarea::placeholder { color: rgba(216,243,220,0.25); }
    .fn-input:focus, .fn-select:focus, .fn-textarea:focus {
      border-color: var(--fn-green);
      background: rgba(255,255,255,0.08);
    }
    .fn-select option { background: #0d2e18; color: #d8f3dc; }
    .fn-textarea { resize: vertical; min-height: 80px; }

    .fn-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    @media (max-width: 560px) { .fn-row { grid-template-columns: 1fr; } }

    /* Checkbox group */
    .check-group { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
    .check-item input { display: none; }
    .check-item label {
      display: inline-block;
      padding: 6px 13px;
      border: 1px solid var(--fn-border);
      border-radius: 999px;
      font-size: 0.82rem;
      color: var(--fn-muted);
      cursor: pointer;
      transition: all 0.2s;
    }
    .check-item input:checked + label {
      background: rgba(69,168,13,0.2);
      border-color: var(--fn-green);
      color: var(--fn-lime);
    }

    /* ── Buttons ── */
    .btn-calc {
      width: 100%;
      padding: 13px;
      background: var(--fn-green);
      color: #fff;
      font-family: 'Raleway', sans-serif;
      font-size: 0.95rem;
      font-weight: 700;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      transition: background 0.2s, transform 0.15s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      margin-top: 6px;
    }
    .btn-calc:hover  { background: #3a9008; }
    .btn-calc:active { transform: scale(0.98); }
    .btn-calc.danger { background: var(--fn-red); }
    .btn-calc.gold   { background: #c8960a; }

    .btn-submit {
      width: 100%;
      padding: 13px;
      background: var(--fn-green);
      color: #fff;
      font-family: 'Raleway', sans-serif;
      font-size: 0.95rem;
      font-weight: 700;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      transition: background 0.2s;
      margin-top: 8px;
    }
    .btn-submit:hover { background: #3a9008; }

    /* ── Result boxes ── */
    .result-box {
      display: none;
      margin-top: 20px;
      border-radius: 16px;
      overflow: hidden;
      border: 1px solid var(--fn-border);
    }
    .result-box.show { display: block; }
    .result-header {
      background: rgba(69,168,13,0.18);
      padding: 14px 20px;
      font-family: 'Raleway', sans-serif;
      font-weight: 700;
      color: var(--fn-lime);
      font-size: 0.95rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .result-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1px;
      background: var(--fn-border);
    }
    @media (max-width: 480px) { .result-grid { grid-template-columns: 1fr; } }
    .result-item {
      background: rgba(7,21,15,0.9);
      padding: 16px 18px;
    }
    .result-item .r-label {
      font-size: 0.75rem;
      color: var(--fn-muted);
      letter-spacing: 0.5px;
      text-transform: uppercase;
      margin-bottom: 4px;
    }
    .result-item .r-value {
      font-family: 'Raleway', sans-serif;
      font-size: 1.25rem;
      font-weight: 700;
      color: #fff;
    }
    .result-item.highlight .r-value { color: var(--fn-lime); }
    .result-item.danger   .r-value { color: var(--fn-red); }
    .result-item.gold     .r-value { color: var(--fn-gold); }

    .result-alert {
      padding: 14px 18px;
      background: rgba(7,21,15,0.9);
      border-top: 1px solid var(--fn-border);
      font-size: 0.85rem;
      color: var(--fn-muted);
      line-height: 1.6;
    }
    .result-alert.warn { border-left: 3px solid var(--fn-orange); color: #ffc87a; }
    .result-alert.good { border-left: 3px solid var(--fn-green);  color: var(--fn-lime); }
    .result-alert.bad  { border-left: 3px solid var(--fn-red);    color: #ffaaaa; }

    /* Success toast */
    .form-success-msg {
      background: rgba(69,168,13,0.15);
      border: 1px solid rgba(69,168,13,0.35);
      border-radius: 12px;
      padding: 14px 18px;
      color: var(--fn-lime);
      font-size: 0.9rem;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    /* Section label */
    .section-eyebrow {
      font-size: 0.75rem;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: var(--fn-green);
      margin-bottom: 8px;
    }
    .section-title {
      font-family: 'Raleway', sans-serif;
      font-size: 1.5rem;
      font-weight: 800;
      color: #fff;
      margin-bottom: 6px;
    }
    .section-sub {
      color: var(--fn-muted);
      font-size: 0.9rem;
      margin-bottom: 28px;
    }

    /* Divider */
    .fn-divider {
      border: none;
      border-top: 1px solid var(--fn-border);
      margin: 28px 0;
    }

    /* Comparison table */
    .compare-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
    .compare-table th {
      background: rgba(69,168,13,0.15);
      color: var(--fn-lime);
      padding: 10px 14px;
      text-align: left;
      font-weight: 600;
      font-size: 0.8rem;
      letter-spacing: 0.5px;
    }
    .compare-table td {
      padding: 10px 14px;
      border-bottom: 1px solid rgba(69,168,13,0.1);
      color: var(--fn-text);
    }
    .compare-table tr:last-child td { border-bottom: none; }
    .compare-table .winner { color: var(--fn-lime); font-weight: 700; }

    /* Feed rate badge */
    .rate-badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 999px;
      font-size: 0.75rem;
      font-weight: 700;
    }
    .rate-high   { background: rgba(69,168,13,0.2);  color: var(--fn-lime); }
    .rate-med    { background: rgba(240,120,48,0.2);  color: var(--fn-orange); }
    .rate-low    { background: rgba(232,85,85,0.15);  color: #ff9999; }
  </style>
</head>

<body>

<?php include 'navbar.php'; ?>

<!-- Hero -->
<section class="tools-hero">
  <div class="container">
    <span class="eyebrow"><i class="bi bi-calculator"></i> Smart Farm Tools</span>
    <h1>Fish Farming<br><span>Command Center</span></h1>
    <p>Calculators, forms, and planning tools built for Nigerian fish farmers. Stop guessing — start profiting.</p>

    <!-- Tab nav -->
    <div class="tab-strip" role="tablist">
      <button class="tab-btn active" onclick="switchTab('feeding')"     role="tab"><i class="bi bi-droplet-fill"></i> Feed Calculator</button>
      <button class="tab-btn"        onclick="switchTab('profit')"      role="tab"><i class="bi bi-graph-up-arrow"></i> Profit Loss</button>
      <button class="tab-btn"        onclick="switchTab('smoking')"     role="tab"><i class="bi bi-fire"></i> Fresh vs Smoked</button>
      <button class="tab-btn"        onclick="switchTab('planning')"    role="tab"><i class="bi bi-layout-text-window"></i> Farm Planner</button>
      <button class="tab-btn"        onclick="switchTab('fcr')"         role="tab"><i class="bi bi-bar-chart-fill"></i> FCR Calculator</button>
      <button class="tab-btn"        onclick="switchTab('middleman')"   role="tab"><i class="bi bi-people-fill"></i> Middleman Loss</button>
      <button class="tab-btn"        onclick="switchTab('order')"       role="tab"><i class="bi bi-bag-plus-fill"></i> Order Now</button>
      <button class="tab-btn"        onclick="switchTab('mortality')"   role="tab"><i class="bi bi-exclamation-triangle-fill"></i> Report Issue</button>
      <button class="tab-btn"        onclick="switchTab('bulk')"        role="tab"><i class="bi bi-building"></i> Bulk Buyer</button>
      <button class="tab-btn"        onclick="switchTab('logistics')"   role="tab"><i class="bi bi-truck"></i> Delivery</button>
      <button class="tab-btn"        onclick="switchTab('education')"   role="tab"><i class="bi bi-mortarboard-fill"></i> Learn</button>
    </div>
  </div>
</section>

<section class="tools-body">
<div class="container">

<!-- ════════════════════════════════════════════
     1. FEED CALCULATOR
     ════════════════════════════════════════════ -->
<div id="tab-feeding" class="tab-panel active">
  <div class="row g-4">
    <div class="col-lg-5">
      <div class="fn-card">
        <div class="section-eyebrow">Calculator</div>
        <h3>Fish Feeding Calculator</h3>
        <p class="card-sub">Enter your fish details to get precise daily and weekly feed recommendations.</p>

        <label class="fn-label">Number of Fish</label>
        <input type="number" id="f-count" class="fn-input" placeholder="e.g. 1000" min="1">

        <label class="fn-label">Average Fish Weight (grams)</label>
        <input type="number" id="f-weight" class="fn-input" placeholder="e.g. 50" min="0.5">

        <label class="fn-label">Feeding Frequency (times per day)</label>
        <select id="f-freq" class="fn-select">
          <option value="2">2× per day</option>
          <option value="3" selected>3× per day</option>
          <option value="4">4× per day</option>
          <option value="5">5× per day</option>
        </select>

        <label class="fn-label">Feed Cost (₦ per kg) — optional</label>
        <input type="number" id="f-cost" class="fn-input" placeholder="e.g. 1800">

        <button class="btn-calc" onclick="calcFeeding()">
          <i class="bi bi-calculator"></i> Calculate Feeding
        </button>

        <div id="feed-result" class="result-box">
          <div class="result-header"><i class="bi bi-droplet-fill"></i> Feeding Plan</div>
          <div class="result-grid">
            <div class="result-item highlight">
              <div class="r-label">Daily Feed</div>
              <div class="r-value" id="r-daily">—</div>
            </div>
            <div class="result-item highlight">
              <div class="r-label">Per Feeding Session</div>
              <div class="r-value" id="r-session">—</div>
            </div>
            <div class="result-item">
              <div class="r-label">Weekly Feed</div>
              <div class="r-value" id="r-weekly">—</div>
            </div>
            <div class="result-item">
              <div class="r-label">Monthly Feed</div>
              <div class="r-value" id="r-monthly">—</div>
            </div>
            <div class="result-item gold">
              <div class="r-label">Weekly Feed Cost</div>
              <div class="r-value" id="r-wcost">—</div>
            </div>
            <div class="result-item gold">
              <div class="r-label">Monthly Feed Cost</div>
              <div class="r-value" id="r-mcost">—</div>
            </div>
          </div>
          <div class="result-alert" id="r-feed-tip"></div>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="fn-card">
        <div class="section-eyebrow">Reference Guide</div>
        <h3>Feeding Rate by Fish Size</h3>
        <p class="card-sub">Based on body weight percentage — the industry standard for catfish farming.</p>

        <table class="compare-table">
          <thead>
            <tr>
              <th>Stage</th>
              <th>Weight Range</th>
              <th>Approx. Age</th>
              <th>Feed Rate</th>
              <th>Strategy</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>Fingerlings</strong></td>
              <td>1g – 20g</td>
              <td>0 – 1 month</td>
              <td><span class="rate-badge rate-high">5% BW</span></td>
              <td>Fast growth stage — feed aggressively</td>
            </tr>
            <tr>
              <td><strong>Juveniles</strong></td>
              <td>20g – 200g</td>
              <td>1 – 3 months</td>
              <td><span class="rate-badge rate-med">3–4% BW</span></td>
              <td>Strong growth, monitor conversion</td>
            </tr>
            <tr>
              <td><strong>Grow-out</strong></td>
              <td>200g+</td>
              <td>3+ months</td>
              <td><span class="rate-badge rate-low">2–3% BW</span></td>
              <td>Efficiency stage — watch FCR</td>
            </tr>
          </tbody>
        </table>

        <hr class="fn-divider">

        <div class="section-eyebrow">Formula Used</div>
        <div style="background:rgba(255,255,255,0.04);border-radius:12px;padding:18px;font-family:monospace;font-size:1rem;color:var(--fn-lime);text-align:center;line-height:2;">
          Daily Feed (g) = Weight (g) × Fish Count × Feed Rate (%)<br>
          <span style="color:var(--fn-muted);font-size:0.85rem;">e.g. 50g × 1,000 fish × 3% = 1,500g = <strong style="color:#fff">1.5 kg/day</strong></span>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- ════════════════════════════════════════════
     2. PROFIT / LOSS CALCULATOR
     ════════════════════════════════════════════ -->
<div id="tab-profit" class="tab-panel">
  <div class="row g-4">
    <div class="col-lg-6">
      <div class="fn-card">
        <div class="section-eyebrow">Calculator</div>
        <h3>How Much Money Am I Losing?</h3>
        <p class="card-sub">The most honest calculator in fish farming. Find out your real profit — or real loss.</p>

        <div class="fn-row">
          <div>
            <label class="fn-label">Fish Stocked</label>
            <input type="number" id="p-stocked" class="fn-input" placeholder="e.g. 2000">
          </div>
          <div>
            <label class="fn-label">Mortality Rate (%)</label>
            <input type="number" id="p-mortality" class="fn-input" placeholder="e.g. 15" max="100">
          </div>
        </div>
        <div class="fn-row">
          <div>
            <label class="fn-label">Fingerling Cost (₦ each)</label>
            <input type="number" id="p-fling-cost" class="fn-input" placeholder="e.g. 120">
          </div>
          <div>
            <label class="fn-label">Total Feed Cost (₦)</label>
            <input type="number" id="p-feed-cost" class="fn-input" placeholder="e.g. 280000">
          </div>
        </div>
        <div class="fn-row">
          <div>
            <label class="fn-label">Avg Harvest Weight (kg)</label>
            <input type="number" id="p-weight" class="fn-input" placeholder="e.g. 1.2">
          </div>
          <div>
            <label class="fn-label">Selling Price (₦/kg)</label>
            <input type="number" id="p-price" class="fn-input" placeholder="e.g. 2800">
          </div>
        </div>
        <label class="fn-label">Other Costs (₦) — pond rent, labour, meds</label>
        <input type="number" id="p-other" class="fn-input" placeholder="e.g. 50000">

        <button class="btn-calc danger" onclick="calcProfit()">
          <i class="bi bi-graph-up-arrow"></i> Show My Real Numbers
        </button>

        <div id="profit-result" class="result-box">
          <div class="result-header"><i class="bi bi-bar-chart-fill"></i> Profit & Loss Report</div>
          <div class="result-grid">
            <div class="result-item">
              <div class="r-label">Fish Survived</div>
              <div class="r-value" id="pr-survived">—</div>
            </div>
            <div class="result-item danger">
              <div class="r-label">Fish Lost</div>
              <div class="r-value" id="pr-lost">—</div>
            </div>
            <div class="result-item">
              <div class="r-label">Total Revenue</div>
              <div class="r-value" id="pr-revenue">—</div>
            </div>
            <div class="result-item">
              <div class="r-label">Total Cost</div>
              <div class="r-value" id="pr-cost">—</div>
            </div>
            <div class="result-item highlight" id="pr-profit-box">
              <div class="r-label">Net Profit / Loss</div>
              <div class="r-value" id="pr-profit">—</div>
            </div>
            <div class="result-item gold">
              <div class="r-label">Mortality Loss Value</div>
              <div class="r-value" id="pr-mortality-loss">—</div>
            </div>
          </div>
          <div class="result-alert" id="pr-tip"></div>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="fn-card">
        <div class="section-eyebrow">Calculator</div>
        <h3>Feed Conversion Ratio (FCR)</h3>
        <p class="card-sub">Discover if your feed is converting efficiently into fish weight — or being wasted.</p>

        <label class="fn-label">Total Feed Used (kg)</label>
        <input type="number" id="fcr-feed" class="fn-input" placeholder="e.g. 500">

        <label class="fn-label">Total Fish Weight Gained (kg)</label>
        <input type="number" id="fcr-gain" class="fn-input" placeholder="e.g. 350">

        <label class="fn-label">Feed Cost per kg (₦)</label>
        <input type="number" id="fcr-feedcost" class="fn-input" placeholder="e.g. 1800">

        <label class="fn-label">Selling Price per kg (₦)</label>
        <input type="number" id="fcr-sell" class="fn-input" placeholder="e.g. 2800">

        <button class="btn-calc" onclick="calcFCR()">
          <i class="bi bi-calculator"></i> Calculate FCR
        </button>

        <div id="fcr-result" class="result-box">
          <div class="result-header"><i class="bi bi-activity"></i> FCR Analysis</div>
          <div class="result-grid">
            <div class="result-item highlight">
              <div class="r-label">Your FCR</div>
              <div class="r-value" id="fcr-val">—</div>
            </div>
            <div class="result-item">
              <div class="r-label">Industry Target</div>
              <div class="r-value">1.2 – 1.8</div>
            </div>
            <div class="result-item gold">
              <div class="r-label">Cost per kg Produced</div>
              <div class="r-value" id="fcr-cpkg">—</div>
            </div>
            <div class="result-item highlight">
              <div class="r-label">Profit per kg</div>
              <div class="r-value" id="fcr-ppkg">—</div>
            </div>
          </div>
          <div class="result-alert" id="fcr-tip"></div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- ════════════════════════════════════════════
     3. FRESH vs SMOKED CALCULATOR
     ════════════════════════════════════════════ -->
<div id="tab-smoking" class="tab-panel">
  <div class="row g-4">
    <div class="col-lg-5">
      <div class="fn-card">
        <div class="section-eyebrow">Calculator</div>
        <h3>Fresh Fish vs Smoked Profit</h3>
        <p class="card-sub">Find out whether selling fresh or smoking your catfish makes you more money.</p>
        <div style="background:rgba(240,120,48,0.1);border:1px solid rgba(240,120,48,0.25);border-radius:10px;padding:12px 14px;font-size:0.83rem;color:#ffc07a;margin-bottom:20px;">
          <i class="bi bi-info-circle"></i> <strong>Industry fact:</strong> 22 kg fresh catfish → ~6 kg smoked (72.7% weight loss from moisture)
        </div>

        <label class="fn-label">Total Fresh Fish Weight (kg)</label>
        <input type="number" id="sm-weight" class="fn-input" placeholder="e.g. 22">

        <label class="fn-label">Fresh Price (₦ per kg)</label>
        <input type="number" id="sm-fresh-price" class="fn-input" placeholder="e.g. 2800">

        <label class="fn-label">Smoked Price (₦ per kg)</label>
        <input type="number" id="sm-smoked-price" class="fn-input" placeholder="e.g. 17000">

        <label class="fn-label">Total Smoking Cost (₦) — fuel, labour</label>
        <input type="number" id="sm-cost" class="fn-input" placeholder="e.g. 5000">

        <button class="btn-calc" style="background:var(--fn-orange)" onclick="calcSmoking()">
          <i class="bi bi-fire"></i> Compare Options
        </button>

        <div id="smoke-result" class="result-box">
          <div class="result-header"><i class="bi bi-fire"></i> Fresh vs Smoked Comparison</div>
          <div class="result-grid">
            <div class="result-item">
              <div class="r-label">🐟 Fresh Revenue</div>
              <div class="r-value" id="sm-r-fresh">—</div>
            </div>
            <div class="result-item">
              <div class="r-label">🔥 Smoked Weight</div>
              <div class="r-value" id="sm-r-sweight">—</div>
            </div>
            <div class="result-item">
              <div class="r-label">🔥 Smoked Revenue</div>
              <div class="r-value" id="sm-r-srev">—</div>
            </div>
            <div class="result-item danger">
              <div class="r-label">Smoking Cost</div>
              <div class="r-value" id="sm-r-scost">—</div>
            </div>
            <div class="result-item highlight">
              <div class="r-label">🔥 Net Smoked Profit</div>
              <div class="r-value" id="sm-r-sprofit">—</div>
            </div>
            <div class="result-item gold">
              <div class="r-label">Extra Profit from Smoking</div>
              <div class="r-value" id="sm-r-extra">—</div>
            </div>
          </div>
          <div class="result-alert" id="sm-tip"></div>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="fn-card">
        <div class="section-eyebrow">Reference</div>
        <h3>Smoking Yield Facts</h3>
        <p class="card-sub">Understanding moisture loss and yield ratios is key to pricing smoked fish correctly.</p>
        <table class="compare-table">
          <thead><tr><th>Fresh Weight</th><th>Smoked Yield (~27%)</th><th>Weight Lost</th></tr></thead>
          <tbody>
            <tr><td>10 kg</td><td class="winner">2.7 kg</td><td>7.3 kg</td></tr>
            <tr><td>22 kg</td><td class="winner">6 kg</td><td>16 kg</td></tr>
            <tr><td>50 kg</td><td class="winner">13.6 kg</td><td>36.4 kg</td></tr>
            <tr><td>100 kg</td><td class="winner">27 kg</td><td>73 kg</td></tr>
          </tbody>
        </table>
        <hr class="fn-divider">
        <div style="font-size:0.85rem;color:var(--fn-muted);line-height:1.8;">
          <p><strong style="color:var(--fn-lime)">Why is smoked fish more profitable?</strong></p>
          <p>Despite losing ~73% of weight, smoked catfish sells for 5–8× more per kg than fresh fish. The moisture removal concentrates value. The shelf life also increases from 1–2 days (fresh) to 3–6 months (smoked), reducing wastage and expanding your market reach beyond local buyers.</p>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- ════════════════════════════════════════════
     4. FARM PLANNING CALCULATOR
     ════════════════════════════════════════════ -->
<div id="tab-planning" class="tab-panel">
  <div class="row g-4">
    <div class="col-lg-5">
      <div class="fn-card">
        <div class="section-eyebrow">Calculator</div>
        <h3>Farm Planning Calculator</h3>
        <p class="card-sub">Tell us your pond size and budget — we'll tell you exactly how many fish to stock and what to expect.</p>

        <label class="fn-label">Pond Size (m²)</label>
        <input type="number" id="pl-pond" class="fn-input" placeholder="e.g. 200">

        <label class="fn-label">Number of Ponds</label>
        <input type="number" id="pl-ponds" class="fn-input" placeholder="e.g. 2" min="1" value="1">

        <label class="fn-label">Total Budget (₦)</label>
        <input type="number" id="pl-budget" class="fn-input" placeholder="e.g. 500000">

        <label class="fn-label">Target Harvest Weight (kg per fish)</label>
        <select id="pl-target" class="fn-select">
          <option value="0.5">0.5 kg (small table size)</option>
          <option value="1" selected>1.0 kg (standard)</option>
          <option value="1.5">1.5 kg (premium)</option>
          <option value="2">2.0 kg (large)</option>
        </select>

        <label class="fn-label">Production Duration (months)</label>
        <select id="pl-duration" class="fn-select">
          <option value="4">4 months</option>
          <option value="5">5 months</option>
          <option value="6" selected>6 months</option>
          <option value="8">8 months</option>
        </select>

        <button class="btn-calc" onclick="calcPlanning()">
          <i class="bi bi-layout-text-window"></i> Plan My Farm
        </button>

        <div id="plan-result" class="result-box">
          <div class="result-header"><i class="bi bi-layout-text-window"></i> Farm Plan</div>
          <div class="result-grid">
            <div class="result-item highlight">
              <div class="r-label">Recommended Stocking</div>
              <div class="r-value" id="pl-r-stock">—</div>
            </div>
            <div class="result-item">
              <div class="r-label">Expected Harvest</div>
              <div class="r-value" id="pl-r-harvest">—</div>
            </div>
            <div class="result-item gold">
              <div class="r-label">Est. Feed Required</div>
              <div class="r-value" id="pl-r-feed">—</div>
            </div>
            <div class="result-item gold">
              <div class="r-label">Est. Feed Cost</div>
              <div class="r-value" id="pl-r-feedcost">—</div>
            </div>
            <div class="result-item">
              <div class="r-label">Fingerling Cost</div>
              <div class="r-value" id="pl-r-fingcost">—</div>
            </div>
            <div class="result-item highlight">
              <div class="r-label">Estimated Revenue</div>
              <div class="r-value" id="pl-r-rev">—</div>
            </div>
          </div>
          <div class="result-alert" id="pl-tip"></div>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="fn-card">
        <div class="section-eyebrow">Calculator</div>
        <h3>Middleman Loss Calculator</h3>
        <p class="card-sub">See exactly how much money middlemen are taking from your pocket every cycle.</p>

        <div class="fn-row">
          <div>
            <label class="fn-label">Your Farm Gate Price (₦/kg)</label>
            <input type="number" id="mm-farm" class="fn-input" placeholder="e.g. 2000">
          </div>
          <div>
            <label class="fn-label">Market Price (₦/kg)</label>
            <input type="number" id="mm-market" class="fn-input" placeholder="e.g. 3200">
          </div>
        </div>
        <label class="fn-label">Total Quantity Sold (kg)</label>
        <input type="number" id="mm-qty" class="fn-input" placeholder="e.g. 500">

        <button class="btn-calc danger" onclick="calcMiddleman()">
          <i class="bi bi-people-fill"></i> Show What I'm Losing
        </button>

        <div id="mm-result" class="result-box">
          <div class="result-header"><i class="bi bi-people-fill"></i> Middleman Loss Report</div>
          <div class="result-grid">
            <div class="result-item">
              <div class="r-label">Your Revenue</div>
              <div class="r-value" id="mm-r-yours">—</div>
            </div>
            <div class="result-item danger">
              <div class="r-label">Middleman Earned</div>
              <div class="r-value" id="mm-r-middle">—</div>
            </div>
            <div class="result-item danger">
              <div class="r-label">You Lost Per kg</div>
              <div class="r-value" id="mm-r-perkg">—</div>
            </div>
            <div class="result-item danger">
              <div class="r-label">Total Loss This Cycle</div>
              <div class="r-value" id="mm-r-total">—</div>
            </div>
          </div>
          <div class="result-alert bad" id="mm-tip"></div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- ════════════════════════════════════════════
     5. FCR TAB (redirects to profit tab content)
     ════════════════════════════════════════════ -->
<div id="tab-fcr" class="tab-panel">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="fn-card">
        <div class="section-eyebrow">Calculator</div>
        <h3>Feed Conversion Ratio (FCR)</h3>
        <p class="card-sub">FCR tells you how many kg of feed it takes to produce 1 kg of fish. Lower is better. Industry target: 1.2–1.8.</p>

        <label class="fn-label">Total Feed Used (kg)</label>
        <input type="number" id="fcr2-feed" class="fn-input" placeholder="e.g. 500">

        <label class="fn-label">Total Fish Weight Gained (kg)</label>
        <input type="number" id="fcr2-gain" class="fn-input" placeholder="e.g. 350">

        <label class="fn-label">Feed Cost per kg (₦)</label>
        <input type="number" id="fcr2-feedcost" class="fn-input" placeholder="e.g. 1800">

        <label class="fn-label">Market Price per kg (₦)</label>
        <input type="number" id="fcr2-sell" class="fn-input" placeholder="e.g. 2800">

        <button class="btn-calc" onclick="calcFCR2()">
          <i class="bi bi-calculator"></i> Analyse My FCR
        </button>

        <div id="fcr2-result" class="result-box">
          <div class="result-header"><i class="bi bi-activity"></i> FCR Analysis</div>
          <div class="result-grid">
            <div class="result-item highlight">
              <div class="r-label">Your FCR</div>
              <div class="r-value" id="fcr2-val">—</div>
            </div>
            <div class="result-item">
              <div class="r-label">Industry Target</div>
              <div class="r-value">1.2 – 1.8</div>
            </div>
            <div class="result-item gold">
              <div class="r-label">Cost per kg Produced</div>
              <div class="r-value" id="fcr2-cpkg">—</div>
            </div>
            <div class="result-item highlight">
              <div class="r-label">Profit per kg</div>
              <div class="r-value" id="fcr2-ppkg">—</div>
            </div>
          </div>
          <div class="result-alert" id="fcr2-tip"></div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- ════════════════════════════════════════════
     6. MIDDLEMAN TAB
     ════════════════════════════════════════════ -->
<div id="tab-middleman" class="tab-panel">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="fn-card">
        <div class="section-eyebrow">Calculator</div>
        <h3>Middleman Loss Calculator</h3>
        <p class="card-sub">Most farmers are unknowingly funding a middleman's lifestyle. Find out exactly how much you're losing.</p>

        <div class="fn-row">
          <div>
            <label class="fn-label">Your Farm Gate Price (₦/kg)</label>
            <input type="number" id="mm2-farm" class="fn-input" placeholder="e.g. 2000">
          </div>
          <div>
            <label class="fn-label">Current Market Price (₦/kg)</label>
            <input type="number" id="mm2-market" class="fn-input" placeholder="e.g. 3200">
          </div>
        </div>
        <label class="fn-label">Total kg Sold This Cycle</label>
        <input type="number" id="mm2-qty" class="fn-input" placeholder="e.g. 500">
        <label class="fn-label">Number of Cycles per Year</label>
        <input type="number" id="mm2-cycles" class="fn-input" placeholder="e.g. 2" value="2">

        <button class="btn-calc danger" onclick="calcMiddleman2()">
          <i class="bi bi-people-fill"></i> Reveal My Losses
        </button>

        <div id="mm2-result" class="result-box">
          <div class="result-header"><i class="bi bi-people-fill"></i> Middleman Cost Report</div>
          <div class="result-grid">
            <div class="result-item">
              <div class="r-label">Your Revenue (cycle)</div>
              <div class="r-value" id="mm2-r-yours">—</div>
            </div>
            <div class="result-item danger">
              <div class="r-label">Middleman Earned</div>
              <div class="r-value" id="mm2-r-middle">—</div>
            </div>
            <div class="result-item danger">
              <div class="r-label">Annual Loss</div>
              <div class="r-value" id="mm2-r-annual">—</div>
            </div>
            <div class="result-item gold">
              <div class="r-label">Your Potential Revenue</div>
              <div class="r-value" id="mm2-r-potential">—</div>
            </div>
          </div>
          <div class="result-alert bad">
            Sell directly on FishNation and keep 100% of market price. <a href="register.php" style="color:var(--fn-lime);font-weight:600;">Create your seller account →</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- ════════════════════════════════════════════
     7. ORDER FORM
     ════════════════════════════════════════════ -->
<div id="tab-order" class="tab-panel">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="fn-card">
        <div class="section-eyebrow">Order Form</div>
        <h3>Order Fingerlings Now</h3>
        <p class="card-sub">Fill in your details and we'll contact you within 24 hours to confirm your order.</p>

        <?php if($form_success === 'order'): ?>
          <div class="form-success-msg"><i class="bi bi-check-circle-fill"></i> Your order request has been received! We'll call you shortly.</div>
        <?php endif; ?>

        <form method="POST">
          <input type="hidden" name="form_type" value="order">
          <div class="fn-row">
            <div>
              <label class="fn-label">Full Name</label>
              <input type="text" name="full_name" class="fn-input" placeholder="Your full name" required>
            </div>
            <div>
              <label class="fn-label">Phone Number *</label>
              <input type="tel" name="phone" class="fn-input" placeholder="08012345678" required>
            </div>
          </div>
          <div class="fn-row">
            <div>
              <label class="fn-label">State</label>
              <input type="text" name="state" class="fn-input" placeholder="e.g. Abuja (FCT)">
            </div>
            <div>
              <label class="fn-label">LGA</label>
              <input type="text" name="lga" class="fn-input" placeholder="e.g. Bwari">
            </div>
          </div>
          <div class="fn-row">
            <div>
              <label class="fn-label">Fish Type</label>
              <select name="fish_type" class="fn-select">
                <option>Catfish</option>
                <option>Tilapia</option>
                <option>Carp</option>
                <option>Other</option>
              </select>
            </div>
            <div>
              <label class="fn-label">Size</label>
              <select name="size" class="fn-select">
                <option>Fingerlings (1–5g)</option>
                <option>Juveniles (20–100g)</option>
                <option>Post-Juveniles (100g+)</option>
              </select>
            </div>
          </div>
          <div class="fn-row">
            <div>
              <label class="fn-label">Quantity</label>
              <input type="number" name="quantity" class="fn-input" placeholder="e.g. 1000" required>
            </div>
            <div>
              <label class="fn-label">Budget (₦)</label>
              <input type="number" name="budget" class="fn-input" placeholder="e.g. 50000">
            </div>
          </div>
          <div class="fn-row">
            <div>
              <label class="fn-label">Delivery or Pickup?</label>
              <select name="delivery_method" class="fn-select">
                <option>Delivery</option>
                <option>Pickup from farm</option>
              </select>
            </div>
            <div>
              <label class="fn-label">Preferred Delivery Date</label>
              <input type="date" name="delivery_date" class="fn-input">
            </div>
          </div>
          <label class="fn-label">Payment Method</label>
          <select name="payment_method" class="fn-select">
            <option>Bank Transfer</option>
            <option>Cash on Delivery</option>
            <option>Pay on Pickup</option>
          </select>
          <button type="submit" class="btn-submit"><i class="bi bi-bag-check-fill"></i> Submit Order Request</button>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- ════════════════════════════════════════════
     8. MORTALITY / PROBLEM REPORT FORM
     ════════════════════════════════════════════ -->
<div id="tab-mortality" class="tab-panel">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="fn-card">
        <div class="section-eyebrow">Problem Report</div>
        <h3>Mortality & Disease Report</h3>
        <p class="card-sub">Report fish health issues so our team can help you quickly and we can track disease patterns across farms.</p>

        <?php if($form_success === 'mortality'): ?>
          <div class="form-success-msg"><i class="bi bi-check-circle-fill"></i> Report received. Our team will reach out to you within 12 hours.</div>
        <?php endif; ?>

        <form method="POST">
          <input type="hidden" name="form_type" value="mortality">
          <div class="fn-row">
            <div>
              <label class="fn-label">Your Name</label>
              <input type="text" name="farmer_name" class="fn-input" placeholder="Full name" required>
            </div>
            <div>
              <label class="fn-label">Phone Number</label>
              <input type="tel" name="phone" class="fn-input" placeholder="08012345678" required>
            </div>
          </div>
          <div class="fn-row">
            <div>
              <label class="fn-label">Fish Age</label>
              <select name="fish_age" class="fn-select">
                <option>Less than 1 month</option>
                <option>1–2 months</option>
                <option>2–4 months</option>
                <option>4–6 months</option>
                <option>Over 6 months</option>
              </select>
            </div>
            <div>
              <label class="fn-label">Water Source</label>
              <select name="water_source" class="fn-select">
                <option>Borehole</option>
                <option>Stream / River</option>
                <option>Waterboard (tap)</option>
                <option>Rainwater</option>
                <option>Mixed</option>
              </select>
            </div>
          </div>

          <label class="fn-label">Symptoms Observed (select all that apply)</label>
          <div class="check-group">
            <?php foreach(['Slow growth','Fish floating','Death/Mortality','Fin rot','Slow eating','Pale skin','Jaundice/Yellow','Jumping out','Gasping at surface','Swollen belly'] as $s): ?>
              <div class="check-item">
                <input type="checkbox" name="symptoms[]" id="sym-<?= md5($s) ?>" value="<?= $s ?>">
                <label for="sym-<?= md5($s) ?>"><?= $s ?></label>
              </div>
            <?php endforeach; ?>
          </div>

          <label class="fn-label">Last Feed Type Used</label>
          <input type="text" name="last_feed_type" class="fn-input" placeholder="e.g. Coppens 2mm, local feed...">

          <label class="fn-label">Additional Notes</label>
          <textarea name="notes" class="fn-textarea" placeholder="Describe what you've observed in detail..."></textarea>

          <button type="submit" class="btn-submit" style="background:var(--fn-orange)">
            <i class="bi bi-exclamation-triangle-fill"></i> Submit Report
          </button>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- ════════════════════════════════════════════
     9. BULK BUYER FORM
     ════════════════════════════════════════════ -->
<div id="tab-bulk" class="tab-panel">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="fn-card">
        <div class="section-eyebrow">Business Form</div>
        <h3>Bulk Buyer / Distributor Enquiry</h3>
        <p class="card-sub">For restaurants, markets, distributors, and processors buying in volume. We offer special pricing for serious buyers.</p>

        <?php if($form_success === 'bulk'): ?>
          <div class="form-success-msg"><i class="bi bi-check-circle-fill"></i> Enquiry received. Our sales team will contact you within 24 hours.</div>
        <?php endif; ?>

        <form method="POST">
          <input type="hidden" name="form_type" value="bulk">
          <div class="fn-row">
            <div>
              <label class="fn-label">Business Name</label>
              <input type="text" name="business_name" class="fn-input" placeholder="Your company name" required>
            </div>
            <div>
              <label class="fn-label">Contact Person</label>
              <input type="text" name="contact_name" class="fn-input" placeholder="Full name" required>
            </div>
          </div>
          <div class="fn-row">
            <div>
              <label class="fn-label">Phone Number</label>
              <input type="tel" name="phone" class="fn-input" placeholder="08012345678" required>
            </div>
            <div>
              <label class="fn-label">Business Location</label>
              <input type="text" name="location" class="fn-input" placeholder="City, State">
            </div>
          </div>
          <div class="fn-row">
            <div>
              <label class="fn-label">Monthly Demand (kg)</label>
              <input type="text" name="monthly_demand" class="fn-input" placeholder="e.g. 500kg/month">
            </div>
            <div>
              <label class="fn-label">Delivery Frequency</label>
              <select name="delivery_frequency" class="fn-select">
                <option>Weekly</option>
                <option>Bi-weekly</option>
                <option>Monthly</option>
                <option>On demand</option>
              </select>
            </div>
          </div>
          <label class="fn-label">Budget Range (₦ per month)</label>
          <select name="budget_range" class="fn-select">
            <option>Under ₦500,000</option>
            <option>₦500,000 – ₦1,000,000</option>
            <option>₦1,000,000 – ₦5,000,000</option>
            <option>Over ₦5,000,000</option>
          </select>
          <button type="submit" class="btn-submit"><i class="bi bi-building"></i> Submit Enquiry</button>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- ════════════════════════════════════════════
     10. LOGISTICS FORM
     ════════════════════════════════════════════ -->
<div id="tab-logistics" class="tab-panel">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="fn-card">
        <div class="section-eyebrow">Logistics Form</div>
        <h3>Delivery & Logistics Request</h3>
        <p class="card-sub">Transport is a major bottleneck in Nigerian fish farming. We connect you with reliable fish transport services.</p>

        <?php if($form_success === 'logistics'): ?>
          <div class="form-success-msg"><i class="bi bi-check-circle-fill"></i> Logistics request received! We'll confirm available transport within 6 hours.</div>
        <?php endif; ?>

        <form method="POST">
          <input type="hidden" name="form_type" value="logistics">
          <div class="fn-row">
            <div>
              <label class="fn-label">Your Name</label>
              <input type="text" name="contact_name" class="fn-input" placeholder="Full name" required>
            </div>
            <div>
              <label class="fn-label">Phone</label>
              <input type="tel" name="phone" class="fn-input" placeholder="08012345678" required>
            </div>
          </div>
          <label class="fn-label">Pickup Location</label>
          <input type="text" name="pickup_location" class="fn-input" placeholder="Farm address / state / LGA" required>
          <label class="fn-label">Delivery Location</label>
          <input type="text" name="delivery_location" class="fn-input" placeholder="Destination address" required>
          <div class="fn-row">
            <div>
              <label class="fn-label">Quantity (kg or count)</label>
              <input type="text" name="quantity" class="fn-input" placeholder="e.g. 200kg or 5,000 fish">
            </div>
            <div>
              <label class="fn-label">Urgency</label>
              <select name="urgency" class="fn-select">
                <option>Same day</option>
                <option>Next day</option>
                <option>Within 3 days</option>
                <option>Flexible</option>
              </select>
            </div>
          </div>
          <label class="fn-label">Preferred Transport Method</label>
          <select name="transport_method" class="fn-select">
            <option>Oxygenated live fish truck</option>
            <option>Cooler box (dead/iced)</option>
            <option>Motorbike (small quantities)</option>
            <option>Any available</option>
          </select>
          <button type="submit" class="btn-submit"><i class="bi bi-truck"></i> Request Transport</button>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- ════════════════════════════════════════════
     11. EDUCATION SIGNUP
     ════════════════════════════════════════════ -->
<div id="tab-education" class="tab-panel">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="fn-card">
        <div class="section-eyebrow">Training & Education</div>
        <h3>Farmer Education Signup</h3>
        <p class="card-sub">Join our growing community of fish farmers. Get free training content, feeding guides, and expert advice sent to your phone.</p>

        <?php if($form_success === 'education'): ?>
          <div class="form-success-msg"><i class="bi bi-check-circle-fill"></i> You're signed up! Check your WhatsApp for your first lesson.</div>
        <?php endif; ?>

        <form method="POST">
          <input type="hidden" name="form_type" value="education">
          <div class="fn-row">
            <div>
              <label class="fn-label">Full Name</label>
              <input type="text" name="full_name" class="fn-input" placeholder="Your name" required>
            </div>
            <div>
              <label class="fn-label">WhatsApp Number</label>
              <input type="tel" name="phone" class="fn-input" placeholder="08012345678" required>
            </div>
          </div>
          <label class="fn-label">Your Experience Level</label>
          <select name="experience_level" class="fn-select">
            <option>Beginner (planning to start)</option>
            <option>Beginner (started less than 1 year)</option>
            <option>Intermediate (1–3 years)</option>
            <option>Advanced (3+ years)</option>
          </select>
          <label class="fn-label">Your Biggest Challenge Right Now</label>
          <textarea name="biggest_challenge" class="fn-textarea" placeholder="e.g. High mortality, poor feed conversion, no market access, funding issues..."></textarea>
          <button type="submit" class="btn-submit"><i class="bi bi-mortarboard-fill"></i> Join Free Training</button>
        </form>
      </div>
    </div>
  </div>
</div>


</div><!-- /container -->
</section>



<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
// ── Toast helper ──────────────────────────────────────────────
    function toast(msg, ok) {
      Toastify({
        text: msg,
        duration: 4000,
        close: true,
        gravity: 'bottom',
        position: 'right',
        stopOnFocus: true,
        style: {
          background: ok
            ? 'linear-gradient(135deg,#1f7a3d,#45A80D)'
            : 'linear-gradient(135deg,#b91c1c,#e85555)',
          borderRadius: '12px',
          fontFamily: "'DM Sans', sans-serif",
          fontSize: '14px',
          fontWeight: '500',
          padding: '13px 20px',
          boxShadow: '0 8px 24px rgba(0,0,0,0.3)',
          minWidth: '260px'
        }
      }).showToast();
    }
function switchTab(id) {
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-' + id).classList.add('active');
  event.currentTarget.classList.add('active');
  window.scrollTo({ top: document.querySelector('.tools-body').offsetTop - 80, behavior: 'smooth' });
}

// ── Helpers ───────────────────────────────────────────────────
function naira(n) { return '₦' + Math.round(n).toLocaleString('en-NG'); }
function kg(n)    { return (Math.round(n * 100) / 100).toLocaleString() + ' kg'; }
function show(id) { document.getElementById(id).classList.add('show'); }

// ── 1. Feed Calculator ────────────────────────────────────────
function calcFeeding() {
  const count  = parseFloat(document.getElementById('f-count').value)  || 0;
  const weight = parseFloat(document.getElementById('f-weight').value) || 0;
  const freq   = parseFloat(document.getElementById('f-freq').value)   || 3;
  const cost   = parseFloat(document.getElementById('f-cost').value)   || 0;

  if (!count || !weight) { toast('⚠️ Please enter number of fish and average weight.', false); return; }

  // Determine feeding rate
  let rate = 0.03, stageLabel = 'Juveniles (3% body weight)';
  if (weight <= 20)       { rate = 0.05; stageLabel = 'Fingerlings (5% body weight)'; }
  else if (weight <= 200) { rate = 0.035; stageLabel = 'Juveniles (3.5% body weight)'; }
  else                    { rate = 0.025; stageLabel = 'Grow-out (2.5% body weight)'; }

  const dailyG   = weight * count * rate;
  const dailyKg  = dailyG / 1000;
  const session  = dailyKg / freq;
  const weekly   = dailyKg * 7;
  const monthly  = dailyKg * 30;
  const wcost    = cost > 0 ? weekly  * cost : null;
  const mcost    = cost > 0 ? monthly * cost : null;

  document.getElementById('r-daily').textContent   = kg(dailyKg);
  document.getElementById('r-session').textContent = kg(session);
  document.getElementById('r-weekly').textContent  = kg(weekly);
  document.getElementById('r-monthly').textContent = kg(monthly);
  document.getElementById('r-wcost').textContent   = wcost  ? naira(wcost)  : 'Enter cost above';
  document.getElementById('r-mcost').textContent   = mcost  ? naira(mcost)  : 'Enter cost above';

  const tip = document.getElementById('r-feed-tip');
  tip.className = 'result-alert good';
  tip.innerHTML = `<strong>Stage:</strong> ${stageLabel} &nbsp;|&nbsp; <strong>Rate used:</strong> ${(rate*100).toFixed(1)}% of body weight per day.<br>
  Feed ${freq}× daily. Adjust if fish stop eating within 10–15 mins.`;
  show('feed-result');
  toast('✓ Feed plan ready for ' + count.toLocaleString() + ' fish!', true);
}

// ── 2. Profit / Loss ──────────────────────────────────────────
function calcProfit() {
  const stocked   = parseFloat(document.getElementById('p-stocked').value)    || 0;
  const mortality = parseFloat(document.getElementById('p-mortality').value)  || 0;
  const flingCost = parseFloat(document.getElementById('p-fling-cost').value) || 0;
  const feedCost  = parseFloat(document.getElementById('p-feed-cost').value)  || 0;
  const weight    = parseFloat(document.getElementById('p-weight').value)     || 0;
  const price     = parseFloat(document.getElementById('p-price').value)      || 0;
  const other     = parseFloat(document.getElementById('p-other').value)      || 0;

  if (!stocked || !price || !weight) { toast('⚠️ Please fill in all required fields.', false); return; }

  const survived      = Math.round(stocked * (1 - mortality / 100));
  const lost          = stocked - survived;
  const revenue       = survived * weight * price;
  const fingerlingCost = stocked * flingCost;
  const totalCost     = fingerlingCost + feedCost + other;
  const profit        = revenue - totalCost;
  const mortalityLoss = lost * weight * price;

  document.getElementById('pr-survived').textContent      = survived.toLocaleString() + ' fish';
  document.getElementById('pr-lost').textContent          = lost.toLocaleString() + ' fish';
  document.getElementById('pr-revenue').textContent       = naira(revenue);
  document.getElementById('pr-cost').textContent          = naira(totalCost);
  document.getElementById('pr-profit').textContent        = naira(Math.abs(profit));
  document.getElementById('pr-mortality-loss').textContent = naira(mortalityLoss);

  const box = document.getElementById('pr-profit-box');
  const tip = document.getElementById('pr-tip');
  if (profit >= 0) {
    box.className = 'result-item highlight';
    document.getElementById('pr-profit').textContent = '+ ' + naira(profit);
    tip.className = 'result-alert good';
    tip.innerHTML = `<strong>Profitable cycle!</strong> Your ${(100 - mortality)}% survival rate generated ${naira(profit)} net profit. Aim to reduce mortality below 10% next cycle.`;
  } else {
    box.className = 'result-item danger';
    document.getElementById('pr-profit').textContent = '- ' + naira(Math.abs(profit));
    tip.className = 'result-alert bad';
    tip.innerHTML = `<strong>Loss cycle.</strong> You lost ${naira(Math.abs(profit))}. Your ${mortality}% mortality cost you ${naira(mortalityLoss)} in lost revenue. Focus on water quality, feeding regularity, and sourcing better-quality fingerlings.`;
  }
  show('profit-result');
  toast(profit >= 0 ? '✓ Profitable cycle calculated!' : '⚠️ Loss cycle detected — see breakdown below.', profit >= 0);
}

// ── 3. FCR (profit tab) ───────────────────────────────────────
function calcFCR() {
  const feed = parseFloat(document.getElementById('fcr-feed').value)     || 0;
  const gain = parseFloat(document.getElementById('fcr-gain').value)     || 0;
  const fc   = parseFloat(document.getElementById('fcr-feedcost').value) || 0;
  const sell = parseFloat(document.getElementById('fcr-sell').value)     || 0;
  if (!feed || !gain) { toast('⚠️ Please enter feed used and weight gained.', false); return; }
  const fcr    = feed / gain;
  const cpkg   = fc  > 0 ? fc * fcr : null;
  const ppkg   = sell > 0 && cpkg ? sell - cpkg : null;
  document.getElementById('fcr-val').textContent  = fcr.toFixed(2);
  document.getElementById('fcr-cpkg').textContent = cpkg ? naira(cpkg) : '—';
  document.getElementById('fcr-ppkg').textContent = ppkg !== null ? naira(ppkg) : '—';
  const tip = document.getElementById('fcr-tip');
  tip.className = fcr <= 1.8 ? 'result-alert good' : 'result-alert bad';
  tip.innerHTML = fcr <= 1.8
    ? `<strong>Excellent FCR!</strong> You're within industry target. Every kg of feed produced ${(1/fcr).toFixed(2)} kg of fish.`
    : `<strong>FCR is high.</strong> You're using ${fcr.toFixed(2)} kg feed per 1 kg of fish — above the 1.8 target. Check feed quality, feeding schedule, and water temperature.`;
  show('fcr-result');
  toast(fcr <= 1.8 ? '✓ Good FCR — within industry target.' : '⚠️ FCR is above target — see tips below.', fcr <= 1.8);
}

// ── 4. FCR tab 2 ────────────────────────────────────────────
function calcFCR2() {
  const feed = parseFloat(document.getElementById('fcr2-feed').value)     || 0;
  const gain = parseFloat(document.getElementById('fcr2-gain').value)     || 0;
  const fc   = parseFloat(document.getElementById('fcr2-feedcost').value) || 0;
  const sell = parseFloat(document.getElementById('fcr2-sell').value)     || 0;
  if (!feed || !gain) { toast('⚠️ Please enter feed used and weight gained.', false); return; }
  const fcr  = feed / gain;
  const cpkg = fc   > 0 ? fc * fcr : null;
  const ppkg = sell > 0 && cpkg ? sell - cpkg : null;
  document.getElementById('fcr2-val').textContent  = fcr.toFixed(2);
  document.getElementById('fcr2-cpkg').textContent = cpkg ? naira(cpkg) : '—';
  document.getElementById('fcr2-ppkg').textContent = ppkg !== null ? naira(ppkg) : '—';
  const tip = document.getElementById('fcr2-tip');
  tip.className = fcr <= 1.8 ? 'result-alert good' : 'result-alert bad';
  tip.innerHTML = fcr <= 1.8
    ? `<strong>Good FCR!</strong> ${fcr.toFixed(2)} — within industry target of 1.2–1.8.`
    : `<strong>FCR needs work.</strong> ${fcr.toFixed(2)} is above target. Reduce feed waste, check water temp, and feed at correct intervals.`;
  show('fcr2-result');
  toast(fcr <= 1.8 ? '✓ Good FCR — within target range.' : '⚠️ FCR needs improvement — check tips.', fcr <= 1.8);
}

// ── 5. Smoking calculator ────────────────────────────────────
function calcSmoking() {
  const freshW  = parseFloat(document.getElementById('sm-weight').value)       || 0;
  const freshP  = parseFloat(document.getElementById('sm-fresh-price').value)  || 0;
  const smokedP = parseFloat(document.getElementById('sm-smoked-price').value) || 0;
  const smokeCost = parseFloat(document.getElementById('sm-cost').value)       || 0;
  if (!freshW || !freshP || !smokedP) { toast('⚠️ Please fill in weight and both price fields.', false); return; }
  const freshRev   = freshW * freshP;
  const smokedW    = freshW * 0.2727; // 22kg → 6kg ratio
  const smokedRev  = smokedW * smokedP;
  const smokedNet  = smokedRev - smokeCost;
  const extra      = smokedNet - freshRev;

  document.getElementById('sm-r-fresh').textContent   = naira(freshRev);
  document.getElementById('sm-r-sweight').textContent = kg(smokedW);
  document.getElementById('sm-r-srev').textContent    = naira(smokedRev);
  document.getElementById('sm-r-scost').textContent   = naira(smokeCost);
  document.getElementById('sm-r-sprofit').textContent = naira(smokedNet);
  document.getElementById('sm-r-extra').textContent   = naira(Math.abs(extra));

  const tip = document.getElementById('sm-tip');
  if (extra > 0) {
    tip.className = 'result-alert good';
    tip.innerHTML = `<i class="bi bi-fire"></i> <strong>Smoking wins!</strong> You earn ${naira(extra)} MORE by smoking your fish. That's a ${Math.round(extra/freshRev*100)}% revenue increase.`;
  } else {
    tip.className = 'result-alert warn';
    tip.innerHTML = `<i class="bi bi-info-circle"></i> Fresh selling is better at your current smoked price. Try negotiating better smoked prices or reducing smoking costs.`;
  }
  show('smoke-result');
  toast(extra > 0 ? '🔥 Smoking is more profitable by ' + naira(extra) + '!' : '🐟 Fresh selling wins at current prices.', extra > 0);
}

// ── 6. Farm planning ─────────────────────────────────────────
function calcPlanning() {
  const pond     = parseFloat(document.getElementById('pl-pond').value)     || 0;
  const ponds    = parseFloat(document.getElementById('pl-ponds').value)    || 1;
  const budget   = parseFloat(document.getElementById('pl-budget').value)   || 0;
  const target   = parseFloat(document.getElementById('pl-target').value)   || 1;
  const duration = parseFloat(document.getElementById('pl-duration').value) || 6;
  if (!pond || !budget) { toast('⚠️ Please enter pond size and budget.', false); return; }

  const totalArea   = pond * ponds;
  const stockDensity = 10; // 10 fish per m²
  const recommended = Math.floor(totalArea * stockDensity);
  const survival    = 0.85; // 85% assumed
  const harvest     = Math.round(recommended * survival);
  const harvestKg   = harvest * target;
  // Feed: FCR 1.5 × harvest weight
  const feedKg      = harvestKg * 1.5;
  const feedCost    = feedKg * 1800; // ₦1800/kg
  const fingCost    = recommended * 120; // ₦120 per fingerling
  const revenue     = harvestKg * 2800; // ₦2800/kg selling price

  document.getElementById('pl-r-stock').textContent    = recommended.toLocaleString() + ' fish';
  document.getElementById('pl-r-harvest').textContent  = harvestKg.toLocaleString() + ' kg';
  document.getElementById('pl-r-feed').textContent     = kg(feedKg);
  document.getElementById('pl-r-feedcost').textContent = naira(feedCost);
  document.getElementById('pl-r-fingcost').textContent = naira(fingCost);
  document.getElementById('pl-r-rev').textContent      = naira(revenue);

  const tip = document.getElementById('pl-tip');
  const totalEst = feedCost + fingCost;
  tip.className = budget >= totalEst ? 'result-alert good' : 'result-alert warn';
  tip.innerHTML = budget >= totalEst
    ? `<strong>Budget looks sufficient.</strong> Estimated fingerling + feed cost is ${naira(totalEst)}. You have ${naira(budget - totalEst)} buffer for other costs.`
    : `<strong>Budget may be tight.</strong> Estimated costs are ${naira(totalEst)} but your budget is ${naira(budget)}. Consider starting with ${Math.floor(budget / (fingCost/recommended + feedCost/recommended))} fish instead.`;
  show('plan-result');
  toast('✓ Farm plan ready — stock ' + recommended.toLocaleString() + ' fish.', true);
}

// ── 7. Middleman (planning tab) ───────────────────────────────
function calcMiddleman() {
  const farm   = parseFloat(document.getElementById('mm-farm').value)   || 0;
  const market = parseFloat(document.getElementById('mm-market').value) || 0;
  const qty    = parseFloat(document.getElementById('mm-qty').value)    || 0;
  if (!farm || !market || !qty) { toast('⚠️ Please fill in all three fields.', false); return; }
  const yourRev  = farm * qty;
  const middleRev = (market - farm) * qty;
  document.getElementById('mm-r-yours').textContent  = naira(yourRev);
  document.getElementById('mm-r-middle').textContent = naira(middleRev);
  document.getElementById('mm-r-perkg').textContent  = naira(market - farm);
  document.getElementById('mm-r-total').textContent  = naira(middleRev);
  const tip = document.getElementById('mm-tip');
  tip.innerHTML = `Middlemen earned ${naira(middleRev)} on YOUR fish. Sell directly and keep 100% of market value. <a href="register.php" style="color:var(--fn-lime);font-weight:600;">Open a seller account →</a>`;
  show('mm-result');
  toast('⚠️ Middlemen earned ' + naira(middleRev) + ' on your fish!', false);
}

// ── 8. Middleman tab 2 ────────────────────────────────────────
function calcMiddleman2() {
  const farm    = parseFloat(document.getElementById('mm2-farm').value)   || 0;
  const market  = parseFloat(document.getElementById('mm2-market').value) || 0;
  const qty     = parseFloat(document.getElementById('mm2-qty').value)    || 0;
  const cycles  = parseFloat(document.getElementById('mm2-cycles').value) || 2;
  if (!farm || !market || !qty) { toast('⚠️ Please fill in all three fields.', false); return; }
  const yourRev   = farm * qty;
  const middleRev = (market - farm) * qty;
  const annual    = middleRev * cycles;
  const potential = market * qty;
  document.getElementById('mm2-r-yours').textContent    = naira(yourRev);
  document.getElementById('mm2-r-middle').textContent   = naira(middleRev);
  document.getElementById('mm2-r-annual').textContent   = naira(annual);
  document.getElementById('mm2-r-potential').textContent = naira(potential * cycles);
  show('mm2-result');
  toast('⚠️ You\'re losing ' + naira(annual) + ' per year to middlemen!', false);
}
</script>

</body>
</html>
