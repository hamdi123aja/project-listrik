<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Monitoring Listrik')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Space+Mono:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* =============================================
           ELECTRIC INDUSTRIAL DARK — CSS VARIABLES
        ============================================= */
        :root {
            --bg:        #0a0c0f;
            --bg-2:      #0e1117;
            --bg-3:      #141820;
            --card:      #111520;
            --card-2:    #181d2a;
            --line:      #1e2535;
            --line-2:    #252d40;
            --text:      #e8eaf0;
            --muted:     #6b7592;
            --muted-2:   #8891aa;
            --accent:    #f0c000;      /* electric yellow */
            --accent-2:  #00d4ff;      /* cyan spark */
            --accent-3:  #ff4d4d;      /* danger red */
            --good:      #00e087;
            --warn:      #f0a000;
            --glow-y:    rgba(240,192,0,0.18);
            --glow-c:    rgba(0,212,255,0.12);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Animated grid background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(240,192,0,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(240,192,0,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
            z-index: 0;
        }

        body::after {
            content: '';
            position: fixed;
            top: -200px; right: -200px;
            width: 600px; height: 600px;
            background: radial-gradient(circle, var(--glow-y) 0%, transparent 65%);
            pointer-events: none;
            z-index: 0;
            animation: pulse-glow 6s ease-in-out infinite alternate;
        }

        @keyframes pulse-glow {
            from { opacity: 0.6; transform: scale(1); }
            to   { opacity: 1;   transform: scale(1.1); }
        }

        a { text-decoration: none; color: inherit; }

        /* =============================================
           AUTH PAGES
        ============================================= */
        .auth-wrap {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            position: relative;
            z-index: 1;
        }

        .auth-card {
            width: min(480px, 100%);
            background: var(--card);
            border: 1px solid var(--line-2);
            border-radius: 4px;
            padding: 40px;
            position: relative;
            box-shadow: 0 0 0 1px rgba(240,192,0,0.08), 0 32px 80px rgba(0,0,0,0.6);
        }

        /* Corner accents */
        .auth-card::before,
        .auth-card::after {
            content: '';
            position: absolute;
            width: 24px; height: 24px;
            border-color: var(--accent);
            border-style: solid;
        }
        .auth-card::before { top: -1px; left: -1px; border-width: 2px 0 0 2px; }
        .auth-card::after  { bottom: -1px; right: -1px; border-width: 0 2px 2px 0; }

        .brand { margin-bottom: 32px; }
        .brand h1 {
            font-family: 'Rajdhani', sans-serif;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 0.04em;
            color: var(--text);
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .brand h1 span { color: var(--accent); }
        .muted { color: var(--muted); font-size: 13px; }

        .error {
            background: rgba(255,77,77,0.1);
            border: 1px solid rgba(255,77,77,0.3);
            border-left: 3px solid var(--accent-3);
            color: #ff8080;
            padding: 12px 14px;
            border-radius: 2px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .field { margin-bottom: 18px; }
        .field label {
            display: block;
            font-family: 'Space Mono', monospace;
            font-size: 10px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--muted-2);
            margin-bottom: 8px;
        }
        .field input,
        .field select {
            width: 100%;
            background: var(--bg-2);
            border: 1px solid var(--line-2);
            border-radius: 2px;
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            padding: 12px 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .field input:focus,
        .field select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--glow-y);
        }
        .field select option { background: var(--bg-2); }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            background: var(--accent);
            color: #0a0c0f;
            font-family: 'Rajdhani', sans-serif;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 12px 20px;
            border-radius: 2px;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }
        .btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, transparent 60%);
        }
        .btn:hover {
            background: #ffd020;
            box-shadow: 0 0 20px var(--glow-y), 0 4px 12px rgba(0,0,0,0.4);
            transform: translateY(-1px);
        }
        .btn:active { transform: translateY(0); }

        .btn-secondary {
            background: transparent;
            color: var(--muted-2);
            border: 1px solid var(--line-2);
        }
        .btn-secondary::after { display: none; }
        .btn-secondary:hover {
            background: var(--bg-3);
            color: var(--text);
            border-color: var(--muted);
            box-shadow: none;
        }

        /* =============================================
           MAIN LAYOUT
        ============================================= */
        .main {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: 100vh;
            position: relative;
        }

        /* =============================================
           SIDEBAR
        ============================================= */
        .sidebar {
            background: var(--card);
            border-right: 1px solid var(--line);
            padding: 28px 20px;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .sidebar-brand {
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--line);
        }
        .sidebar-brand .logo-mark {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
        }
        .sidebar-brand .bolt {
            font-size: 22px;
            line-height: 1;
        }
        .sidebar-brand h2 {
            font-family: 'Rajdhani', sans-serif;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--text);
            margin: 0;
        }
        .sidebar-brand h2 span { color: var(--accent); }
        .sidebar-brand .sub {
            font-family: 'Space Mono', monospace;
            font-size: 9px;
            letter-spacing: 0.14em;
            color: var(--muted);
            text-transform: uppercase;
            margin-left: 32px;
        }

        .menu { flex: 1; }
        .menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 2px;
            margin-bottom: 2px;
            color: var(--muted-2);
            font-size: 13px;
            font-weight: 500;
            letter-spacing: 0.02em;
            transition: all 0.15s;
            border-left: 2px solid transparent;
        }
        .menu a:hover {
            background: var(--bg-3);
            color: var(--text);
            border-left-color: var(--line-2);
        }
        .menu a.active {
            background: rgba(240,192,0,0.08);
            color: var(--accent);
            border-left-color: var(--accent);
            font-weight: 600;
        }
        .menu a .icon {
            font-size: 15px;
            width: 18px;
            text-align: center;
        }

        .sidebar-footer {
            padding-top: 16px;
            border-top: 1px solid var(--line);
        }
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }
        .user-avatar {
            width: 34px; height: 34px;
            background: rgba(240,192,0,0.12);
            border: 1px solid rgba(240,192,0,0.25);
            border-radius: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Rajdhani', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: var(--accent);
            flex-shrink: 0;
        }
        .user-info .name {
            font-size: 13px;
            font-weight: 500;
            color: var(--text);
            line-height: 1.2;
        }
        .user-info .role {
            font-family: 'Space Mono', monospace;
            font-size: 9px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        /* =============================================
           CONTENT AREA
        ============================================= */
        .content {
            padding: 28px 32px;
            overflow-x: hidden;
        }

        /* =============================================
           TOPBAR
        ============================================= */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }
        .topbar h1 {
            font-family: 'Rajdhani', sans-serif;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--text);
            line-height: 1;
            margin-bottom: 4px;
        }
        .topbar .muted {
            font-family: 'Space Mono', monospace;
            font-size: 11px;
            letter-spacing: 0.06em;
        }

        /* =============================================
           STATUS PILL
        ============================================= */
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 2px;
            font-family: 'Space Mono', monospace;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }
        .pill::before {
            content: '';
            width: 6px; height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .pill.normal {
            background: rgba(0,224,135,0.1);
            border: 1px solid rgba(0,224,135,0.25);
            color: var(--good);
        }
        .pill.normal::before { background: var(--good); box-shadow: 0 0 6px var(--good); animation: blink 2s ease-in-out infinite; }
        .pill.warning {
            background: rgba(240,160,0,0.1);
            border: 1px solid rgba(240,160,0,0.25);
            color: var(--warn);
        }
        .pill.warning::before { background: var(--warn); }
        .pill.offline {
            background: rgba(255,77,77,0.1);
            border: 1px solid rgba(255,77,77,0.25);
            color: var(--accent-3);
        }
        .pill.offline::before { background: var(--accent-3); }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.4; }
        }

        /* =============================================
           METRIC CARDS
        ============================================= */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 3px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            border-color: var(--line-2);
            box-shadow: 0 4px 24px rgba(0,0,0,0.3);
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .card:hover::before { opacity: 0.6; }

        .metric-card {
            min-height: 148px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: visible;
            z-index: 1;
        }
        .metric-card:hover,
        .metric-card:focus-within {
            z-index: 20;
        }
        .metric-card::after {
            content: '';
            position: absolute;
            right: -32px;
            bottom: -38px;
            width: 128px;
            height: 128px;
            border: 1px solid rgba(0,212,255,0.09);
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0,212,255,0.08), transparent 66%);
            pointer-events: none;
        }
        .metric-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
        }
        .metric-icon {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border: 1px solid var(--line-2);
            border-radius: 4px;
            background: rgba(0,212,255,0.06);
            color: var(--accent-2);
            font-family: 'Rajdhani', sans-serif;
            font-size: 16px;
            font-weight: 700;
            line-height: 1;
        }
        .metric-strip {
            height: 5px;
            margin-top: 16px;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--accent), var(--accent-2), rgba(255,255,255,0.08));
            opacity: 0.72;
        }
        .metric-foot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
            font-family: 'Space Mono', monospace;
            font-size: 10px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .info-label {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: help;
            outline: none;
        }
        .info-label::after {
            content: '?';
            width: 15px;
            height: 15px;
            display: inline-grid;
            place-items: center;
            border: 1px solid rgba(0,212,255,0.35);
            border-radius: 50%;
            color: var(--accent-2);
            font-family: 'Space Mono', monospace;
            font-size: 9px;
            line-height: 1;
        }
        .info-label .tooltip {
            position: absolute;
            left: 0;
            top: calc(100% + 12px);
            z-index: 60;
            width: min(300px, 78vw);
            padding: 12px 14px;
            border: 1px solid rgba(240,192,0,0.55);
            border-left: 4px solid var(--accent);
            border-radius: 5px;
            background: #f5f7fb;
            color: #10131a;
            box-shadow: 0 18px 46px rgba(0,0,0,0.58), 0 0 0 1px rgba(255,255,255,0.08);
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.5;
            letter-spacing: 0;
            text-transform: none;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-4px);
            transition: opacity 0.16s, transform 0.16s, visibility 0.16s;
            pointer-events: none;
        }
        .info-label .tooltip::before {
            content: '';
            position: absolute;
            left: 18px;
            top: -8px;
            width: 14px;
            height: 14px;
            background: #f5f7fb;
            border-left: 1px solid rgba(240,192,0,0.55);
            border-top: 1px solid rgba(240,192,0,0.55);
            transform: rotate(45deg);
        }
        .info-label:hover .tooltip,
        .info-label:focus .tooltip,
        .info-label:focus-within .tooltip {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .card .muted {
            font-family: 'Space Mono', monospace;
            font-size: 10px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .value {
            font-family: 'Rajdhani', sans-serif;
            font-size: 34px;
            font-weight: 700;
            color: var(--text);
            line-height: 1;
        }
        .value small {
            font-size: 16px;
            color: var(--muted-2);
            font-weight: 400;
        }

        /* =============================================
           GRID 2-COL
        ============================================= */
        .grid-2 {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        /* =============================================
           CHART
        ============================================= */
        .chart {
            display: flex;
            align-items: flex-end;
            gap: 5px;
            height: 200px;
            padding-top: 18px;
            padding-bottom: 28px;
            overflow-x: auto;
            overflow-y: hidden;
        }
        .bar {
            flex: 0 0 42px;
            min-width: 42px;
            background: rgba(240,192,0,0.12);
            border: 1px solid rgba(240,192,0,0.15);
            border-radius: 2px 2px 0 0;
            position: relative;
            transition: background 0.2s, box-shadow 0.2s;
            cursor: default;
        }
        .bar strong {
            position: absolute;
            left: 50%;
            top: -17px;
            transform: translateX(-50%);
            font-family: 'Space Mono', monospace;
            font-size: 9px;
            font-weight: 600;
            color: var(--text);
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.2s;
        }
        .bar:hover {
            background: rgba(240,192,0,0.22);
            box-shadow: 0 0 12px var(--glow-y);
        }
        .bar.active {
            background: var(--accent);
            border-color: var(--accent);
            box-shadow: 0 0 16px var(--glow-y);
        }
        .bar:hover strong,
        .bar.active strong {
            opacity: 1;
        }
        .bar span {
            position: absolute;
            bottom: -22px;
            left: 50%;
            transform: translateX(-50%);
            font-family: 'Space Mono', monospace;
            font-size: 9px;
            color: var(--muted);
            white-space: nowrap;
        }

        /* Cost card accent */
        .cost-value {
            font-family: 'Rajdhani', sans-serif;
            font-size: 40px;
            font-weight: 700;
            color: var(--accent);
            line-height: 1;
            margin: 12px 0;
        }

        .cost-grid {
            display: grid;
            grid-template-columns: minmax(220px, 0.8fr) minmax(320px, 1.2fr);
            gap: 16px;
            align-items: stretch;
        }
        .summary-stack {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            background: var(--bg-2);
            border: 1px solid var(--line);
            border-radius: 3px;
        }
        .summary-row .label {
            font-family: 'Space Mono', monospace;
            font-size: 10px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .summary-row .number {
            font-family: 'Rajdhani',sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
            white-space: nowrap;
        }
        .realtime-chart-panel {
            min-height: 260px;
            border: 1px solid var(--line);
            border-radius: 4px;
            background: linear-gradient(180deg, rgba(0,212,255,0.04), rgba(10,12,15,0.24));
            padding: 14px;
        }
        .chart-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            font-family: 'Space Mono', monospace;
            font-size: 10px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
        }
        .line-chart {
            width: 100%;
            height: 210px;
            display: block;
        }
        .line-chart .grid-line { stroke: rgba(136,145,170,0.12); stroke-width: 1; }
        .line-chart .area-fill { fill: rgba(0,212,255,0.10); }
        .line-chart .series-line {
            fill: none;
            stroke: var(--accent-2);
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
            filter: drop-shadow(0 0 7px rgba(0,212,255,0.35));
        }
        .line-chart .series-point {
            fill: var(--accent);
            stroke: #10131a;
            stroke-width: 2;
        }
        .chart-empty {
            height: 210px;
            display: grid;
            place-items: center;
            color: var(--muted);
            font-family: 'Space Mono', monospace;
            font-size: 11px;
            text-align: center;
        }

        .alert {
            border-radius: 4px;
            padding: 14px 16px;
            margin-bottom: 16px;
            font-size: 13px;
            line-height: 1.5;
        }
        .alert.warning {
            background: rgba(240,160,0,0.12);
            border: 1px solid rgba(240,160,0,0.32);
            border-left: 3px solid var(--warn);
            color: #ffd27a;
            box-shadow: 0 0 24px rgba(240,160,0,0.08);
        }

        /* =============================================
           TABLE
        ============================================= */
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th {
            font-family: 'Space Mono', monospace;
            font-size: 10px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--muted);
            padding: 12px 14px;
            border-bottom: 1px solid var(--line-2);
            text-align: left;
            font-weight: 400;
            background: var(--bg-2);
        }
        .table th:first-child { border-radius: 2px 0 0 0; }
        .table th:last-child { border-radius: 0 2px 0 0; }
        .table td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--line);
            font-size: 13px;
            color: var(--muted-2);
            transition: background 0.15s;
        }
        .table tr:hover td { background: var(--bg-3); color: var(--text); }
        .table tr:last-child td { border-bottom: none; }
        .table td:first-child { color: var(--text); font-size: 12px; font-family: 'Space Mono', monospace; }

        /* =============================================
           CARD HEADER
        ============================================= */
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        .card-header h3 {
            font-family: 'Rajdhani', sans-serif;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--text);
        }
        .card-header .label-tag {
            font-family: 'Space Mono', monospace;
            font-size: 9px;
            letter-spacing: 0.12em;
            color: var(--accent);
            text-transform: uppercase;
            padding: 3px 8px;
            border: 1px solid rgba(240,192,0,0.2);
            border-radius: 2px;
        }

        /* =============================================
           MOBILE TOGGLE
        ============================================= */
        .mobile-nav-toggle {
            display: none;
            position: fixed;
            top: 14px; left: 14px;
            z-index: 1200;
            background: var(--card);
            border: 1px solid var(--line-2);
            color: var(--text);
            border-radius: 2px;
            width: 42px; height: 42px;
            cursor: pointer;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        .mobile-nav-toggle .line {
            display: block;
            width: 18px; height: 1.5px;
            background: var(--text);
            transition: all 0.2s;
        }
        .mobile-overlay { display: none; }

        /* =============================================
           PAGINATION (Laravel default override)
        ============================================= */
        .pagination { display: flex; gap: 4px; flex-wrap: wrap; margin-top: 16px; }
        nav[aria-label="Pagination"] span,
        nav[aria-label="Pagination"] a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            border: 1px solid var(--line-2);
            border-radius: 2px;
            font-size: 12px;
            font-family: 'Space Mono', monospace;
            color: var(--muted-2);
            background: var(--bg-2);
            text-decoration: none;
            transition: all 0.15s;
        }
        nav[aria-label="Pagination"] a:hover { border-color: var(--accent); color: var(--accent); }
        nav[aria-label="Pagination"] span[aria-current="page"] {
            background: var(--accent);
            border-color: var(--accent);
            color: #0a0c0f;
            font-weight: 700;
        }

        /* =============================================
           RESPONSIVE
        ============================================= */
        @media (max-width: 1024px) {
            .grid-2 { grid-template-columns: 1fr; }
            .cost-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 900px) {
            .main { grid-template-columns: 1fr; }

            .mobile-nav-toggle {
                display: flex;
                background: #f4f4f4;
                border-color: #cfcfcf;
                color: #111;
            }
            .mobile-nav-toggle .line { background: #111; }

            .sidebar {
                position: fixed;
                top: 0; left: 0; bottom: 0;
                width: 280px;
                max-width: 85vw;
                z-index: 1300;
                transform: translateX(-110%);
                transition: transform 0.25s cubic-bezier(0.4,0,0.2,1);
                overflow-y: auto;
                height: 100vh;
                background: #fafafa;
                color: #1b1b1b;
                border-right: 1px solid #d5d5d5;
                padding-top: 72px;
            }
            .sidebar .menu { position: relative; z-index: 1301; }
            .sidebar .sidebar-brand h2,
            .sidebar .sidebar-user .name { color: #1b1b1b; }
            .sidebar .sidebar-brand .sub,
            .sidebar .menu a,
            .sidebar .user-info .role { color: #555; }
            .sidebar .menu a:hover { background: #ececec; color: #111; border-left-color: #c8c8c8; }
            .sidebar .menu a.active { background: #f5e6e6; color: #7f0f0f; border-left-color: #7f0f0f; }
            .sidebar > h2,
            .sidebar > .muted { display: none; }

            .content { padding: 72px 16px 24px; }

            .mobile-overlay {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.18);
                z-index: 1250;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.2s;
            }

            body.sidebar-open .sidebar { transform: translateX(0); }
            body.sidebar-open .mobile-overlay { opacity: 1; pointer-events: auto; }
            body.sidebar-open .mobile-nav-toggle { opacity: 0; pointer-events: none; }

            .cards { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 600px) {
            .cards { grid-template-columns: 1fr 1fr; }
            .topbar h1 { font-size: 24px; }
            .cost-value { font-size: 30px; }
        }
    </style>
</head>
<body>
@yield('body')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var toggleBtn = document.querySelector('[data-mobile-nav-toggle]');
    var overlay   = document.querySelector('[data-mobile-overlay]');
    var closeNav  = function() { document.body.classList.remove('sidebar-open'); };
    var openNav   = function() { document.body.classList.add('sidebar-open'); };
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            document.body.classList.contains('sidebar-open') ? closeNav() : openNav();
        });
    }
    if (overlay) overlay.addEventListener('click', closeNav);
    window.addEventListener('resize', function() {
        if (window.innerWidth > 900) closeNav();
    });
});
</script>
</body>
</html>
