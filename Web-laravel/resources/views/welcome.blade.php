<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Konsumsi Listrik — Pahami & Kendalikan Energimu</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Space+Mono:ital,wght@0,400;0,700;1,400&family=Crimson+Pro:ital,wght@0,300;0,400;0,600;1,300;1,400&display=swap" rel="stylesheet">
<style>
/* ============================================================
   ROOT & RESET
============================================================ */
:root {
  --bg:       #080a0d;
  --bg2:      #0c0f14;
  --bg3:      #111620;
  --card:     #0f1319;
  --card2:    #141b27;
  --line:     #1a2133;
  --line2:    #21293d;
  --text:     #dde2ec;
  --text2:    #a8b4cc;
  --muted:    #5a6a88;
  --accent:   #f0c000;
  --accent2:  #00d4ff;
  --accent3:  #ff5c3a;
  --good:     #00e59a;
  --gy:       rgba(240,192,0,0.15);
  --gc:       rgba(0,212,255,0.12);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html { scroll-behavior: smooth; }

body {
  font-family: 'Crimson Pro', Georgia, serif;
  background: var(--bg);
  color: var(--text);
  overflow-x: hidden;
  cursor: none;
}

/* ============================================================
   CUSTOM CURSOR
============================================================ */
.cursor {
  position: fixed;
  pointer-events: none;
  z-index: 9999;
  mix-blend-mode: difference;
}
.cursor-dot {
  width: 8px; height: 8px;
  background: var(--accent);
  border-radius: 50%;
  transform: translate(-50%, -50%);
  transition: transform 0.1s;
}
.cursor-ring {
  width: 36px; height: 36px;
  border: 1px solid rgba(240,192,0,0.5);
  border-radius: 50%;
  transform: translate(-50%, -50%);
  transition: all 0.18s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}
body:has(a:hover) .cursor-ring,
body:has(button:hover) .cursor-ring {
  transform: translate(-50%, -50%) scale(1.6);
  border-color: var(--accent);
  background: rgba(240,192,0,0.06);
}

/* ============================================================
   ANIMATED GRID BACKGROUND
============================================================ */
.grid-bg {
  position: fixed;
  inset: 0;
  background-image:
    linear-gradient(rgba(240,192,0,0.028) 1px, transparent 1px),
    linear-gradient(90deg, rgba(240,192,0,0.028) 1px, transparent 1px);
  background-size: 48px 48px;
  pointer-events: none;
  z-index: 0;
}

.orb {
  position: fixed;
  border-radius: 50%;
  pointer-events: none;
  z-index: 0;
  filter: blur(80px);
  animation: orb-drift 12s ease-in-out infinite alternate;
}
.orb-1 { width: 500px; height: 500px; top: -100px; right: -100px; background: radial-gradient(circle, rgba(240,192,0,0.1) 0%, transparent 70%); }
.orb-2 { width: 400px; height: 400px; bottom: 20%; left: -80px; background: radial-gradient(circle, rgba(0,212,255,0.08) 0%, transparent 70%); animation-delay: -6s; }
.orb-3 { width: 300px; height: 300px; top: 50%; right: 10%; background: radial-gradient(circle, rgba(255,92,58,0.06) 0%, transparent 70%); animation-delay: -3s; }

@keyframes orb-drift {
  from { transform: translate(0, 0) scale(1); }
  to   { transform: translate(30px, 20px) scale(1.05); }
}

/* ============================================================
   NAVBAR
============================================================ */
nav {
  position: fixed;
  top: 0; left: 0; right: 0;
  z-index: 100;
  padding: 0 48px;
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid var(--line);
  background: rgba(8,10,13,0.8);
  backdrop-filter: blur(16px);
}
.nav-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
}
.nav-logo .bolt { font-size: 22px; animation: bolt-flash 3s ease-in-out infinite; }
@keyframes bolt-flash {
  0%,90%,100% { opacity: 1; filter: drop-shadow(0 0 4px var(--accent)); }
  92% { opacity: 0.3; }
  94% { opacity: 1; }
}
.nav-logo h1 {
  font-family: 'Rajdhani', sans-serif;
  font-size: 18px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--text);
}
.nav-logo h1 span { color: var(--accent); }

.nav-links {
  display: flex;
  align-items: center;
  gap: 32px;
  list-style: none;
}
.nav-links a {
  font-family: 'Rajdhani', sans-serif;
  font-size: 13px;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--muted);
  text-decoration: none;
  transition: color 0.2s;
  position: relative;
}
.nav-links a::after {
  content: '';
  position: absolute;
  bottom: -4px; left: 0; right: 0;
  height: 1px;
  background: var(--accent);
  transform: scaleX(0);
  transition: transform 0.25s;
}
.nav-links a:hover { color: var(--accent); }
.nav-links a:hover::after { transform: scaleX(1); }

.nav-cta {
  font-family: 'Rajdhani', sans-serif;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  background: var(--accent);
  color: #080a0d;
  border: none;
  padding: 9px 20px;
  border-radius: 2px;
  cursor: none;
  text-decoration: none;
  transition: all 0.2s;
}
.nav-cta:hover {
  background: #ffd020;
  box-shadow: 0 0 24px var(--gy);
}

.nav-ham { display: none; flex-direction: column; gap: 5px; cursor: none; background: none; border: none; padding: 4px; }
.nav-ham span { display: block; width: 22px; height: 1.5px; background: var(--text2); border-radius: 2px; transition: all 0.25s; }

/* ============================================================
   HERO SECTION
============================================================ */
.hero {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 120px 24px 80px;
  position: relative;
  z-index: 1;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 16px;
  border: 1px solid rgba(240,192,0,0.3);
  border-radius: 2px;
  background: rgba(240,192,0,0.06);
  font-family: 'Space Mono', monospace;
  font-size: 11px;
  letter-spacing: 0.12em;
  color: var(--accent);
  text-transform: uppercase;
  margin-bottom: 32px;
  animation: fade-up 0.8s cubic-bezier(0.16,1,0.3,1) both;
}
.hero-badge::before {
  content: '';
  width: 6px; height: 6px;
  background: var(--accent);
  border-radius: 50%;
  animation: blink 2s ease-in-out infinite;
}

.hero h2 {
  font-family: 'Rajdhani', sans-serif;
  font-size: clamp(52px, 10vw, 110px);
  font-weight: 700;
  letter-spacing: -0.01em;
  text-transform: uppercase;
  line-height: 0.9;
  margin-bottom: 8px;
  animation: fade-up 0.9s 0.1s cubic-bezier(0.16,1,0.3,1) both;
}
.hero h2 .line1 { color: var(--text); }
.hero h2 .line2 {
  color: transparent;
  -webkit-text-stroke: 1.5px var(--accent);
  display: block;
}
.hero h2 .line3 { color: var(--accent); display: block; }

.hero-sub {
  font-size: clamp(17px, 2.5vw, 22px);
  font-weight: 300;
  font-style: italic;
  color: var(--text2);
  max-width: 620px;
  line-height: 1.6;
  margin: 24px auto 48px;
  animation: fade-up 0.9s 0.2s cubic-bezier(0.16,1,0.3,1) both;
}

.hero-stats {
  display: flex;
  gap: 0;
  border: 1px solid var(--line2);
  border-radius: 4px;
  overflow: hidden;
  animation: fade-up 0.9s 0.35s cubic-bezier(0.16,1,0.3,1) both;
}
.hstat {
  padding: 18px 32px;
  border-right: 1px solid var(--line2);
  text-align: center;
  background: var(--card);
  transition: background 0.2s;
}
.hstat:hover { background: var(--card2); }
.hstat:last-child { border-right: none; }
.hstat .num {
  font-family: 'Rajdhani', sans-serif;
  font-size: 32px;
  font-weight: 700;
  color: var(--accent);
  line-height: 1;
  margin-bottom: 4px;
}
.hstat .desc {
  font-family: 'Space Mono', monospace;
  font-size: 9px;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--muted);
}

/* Scroll indicator */
.scroll-hint {
  position: absolute;
  bottom: 32px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  animation: fade-up 1s 0.6s both;
}
.scroll-hint span {
  font-family: 'Space Mono', monospace;
  font-size: 9px;
  letter-spacing: 0.14em;
  color: var(--muted);
  text-transform: uppercase;
}
.scroll-line {
  width: 1px;
  height: 48px;
  background: linear-gradient(to bottom, var(--accent), transparent);
  animation: scroll-drop 2s ease-in-out infinite;
}
@keyframes scroll-drop {
  0%   { transform: scaleY(0); transform-origin: top; opacity: 1; }
  50%  { transform: scaleY(1); transform-origin: top; opacity: 1; }
  100% { transform: scaleY(1); transform-origin: bottom; opacity: 0; }
}

/* ============================================================
   DIVIDER
============================================================ */
.section-divider {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 0 48px;
  margin: 0 auto;
  max-width: 1200px;
}
.section-divider .tag {
  font-family: 'Space Mono', monospace;
  font-size: 10px;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: var(--accent);
  white-space: nowrap;
}
.section-divider .line { flex: 1; height: 1px; background: var(--line2); }

/* ============================================================
   SECTION BASE
============================================================ */
section {
  position: relative;
  z-index: 1;
  padding: 80px 48px;
  max-width: 1200px;
  margin: 0 auto;
}

.section-label {
  font-family: 'Space Mono', monospace;
  font-size: 10px;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: var(--accent);
  margin-bottom: 14px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.section-label::before {
  content: '';
  width: 28px; height: 1px;
  background: var(--accent);
}

.section-title {
  font-family: 'Rajdhani', sans-serif;
  font-size: clamp(32px, 5vw, 56px);
  font-weight: 700;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  line-height: 1;
  margin-bottom: 16px;
}
.section-title span { color: var(--accent); }

.section-body {
  font-size: 18px;
  font-weight: 300;
  color: var(--text2);
  line-height: 1.75;
  max-width: 640px;
}

/* ============================================================
   WHY SECTION — IMPORTANCE
============================================================ */
.why-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1px;
  background: var(--line);
  border: 1px solid var(--line);
  border-radius: 4px;
  overflow: hidden;
  margin-top: 56px;
}
.why-card {
  background: var(--card);
  padding: 36px 32px;
  position: relative;
  overflow: hidden;
  transition: background 0.25s;
}
.why-card:hover { background: var(--card2); }
.why-card::after {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 2px;
  background: linear-gradient(90deg, var(--accent), transparent);
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 0.4s cubic-bezier(0.16,1,0.3,1);
}
.why-card:hover::after { transform: scaleX(1); }

.why-icon {
  font-size: 36px;
  margin-bottom: 16px;
  display: block;
  filter: drop-shadow(0 0 8px rgba(240,192,0,0.4));
}
.why-num {
  font-family: 'Rajdhani', sans-serif;
  font-size: 72px;
  font-weight: 700;
  color: var(--line2);
  position: absolute;
  top: 16px; right: 24px;
  line-height: 1;
  transition: color 0.3s;
}
.why-card:hover .why-num { color: rgba(240,192,0,0.08); }
.why-card h3 {
  font-family: 'Rajdhani', sans-serif;
  font-size: 22px;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  color: var(--text);
  margin-bottom: 10px;
}
.why-card p {
  font-size: 16px;
  font-weight: 300;
  color: var(--text2);
  line-height: 1.7;
}

/* ============================================================
   HOW IT WORKS — FLOW SECTION
============================================================ */
.flow-section { padding-top: 0; }

.flow-container {
  margin-top: 56px;
  position: relative;
}
.flow-track {
  position: absolute;
  top: 40px; left: 40px; right: 40px;
  height: 1px;
  background: var(--line2);
  display: none;
}
.flow-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 2px;
}
.flow-step {
  padding: 32px 24px;
  background: var(--card);
  position: relative;
  border: 1px solid var(--line);
  border-radius: 3px;
  transition: all 0.3s;
  cursor: default;
}
.flow-step:hover {
  background: var(--card2);
  border-color: rgba(240,192,0,0.25);
  transform: translateY(-4px);
  box-shadow: 0 16px 40px rgba(0,0,0,0.4), 0 0 0 1px rgba(240,192,0,0.1);
}
.flow-step-num {
  font-family: 'Space Mono', monospace;
  font-size: 10px;
  letter-spacing: 0.12em;
  color: var(--accent);
  margin-bottom: 14px;
}
.flow-step-icon {
  font-size: 28px;
  margin-bottom: 14px;
  display: block;
}
.flow-step h3 {
  font-family: 'Rajdhani', sans-serif;
  font-size: 18px;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  color: var(--text);
  margin-bottom: 8px;
}
.flow-step p {
  font-size: 14px;
  font-weight: 300;
  color: var(--muted);
  line-height: 1.65;
}
.flow-arrow {
  position: absolute;
  top: 36px; right: -14px;
  z-index: 2;
  font-family: 'Space Mono', monospace;
  font-size: 16px;
  color: var(--accent);
  display: none;
}

/* ============================================================
   FEATURES SECTION
============================================================ */
.features-layout {
  display: grid;
  grid-template-columns: 1fr 1.2fr;
  gap: 64px;
  align-items: start;
  margin-top: 56px;
}

.feat-list { display: flex; flex-direction: column; gap: 4px; }
.feat-item {
  display: flex;
  align-items: flex-start;
  gap: 20px;
  padding: 20px;
  border: 1px solid transparent;
  border-radius: 3px;
  transition: all 0.2s;
  cursor: default;
}
.feat-item:hover {
  background: var(--card);
  border-color: var(--line);
}
.feat-item.active-feat {
  background: var(--card2);
  border-color: rgba(240,192,0,0.2);
}
.feat-icon-wrap {
  width: 44px; height: 44px;
  background: rgba(240,192,0,0.08);
  border: 1px solid rgba(240,192,0,0.2);
  border-radius: 2px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
  transition: all 0.2s;
}
.feat-item:hover .feat-icon-wrap,
.feat-item.active-feat .feat-icon-wrap {
  background: rgba(240,192,0,0.14);
  box-shadow: 0 0 16px rgba(240,192,0,0.15);
}
.feat-text h4 {
  font-family: 'Rajdhani', sans-serif;
  font-size: 18px;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  color: var(--text);
  margin-bottom: 6px;
}
.feat-text p {
  font-size: 15px;
  font-weight: 300;
  color: var(--text2);
  line-height: 1.65;
}

/* Fake monitor display */
.monitor-display {
  background: var(--card);
  border: 1px solid var(--line2);
  border-radius: 4px;
  padding: 24px;
  position: sticky;
  top: 100px;
}
.mon-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 14px;
  border-bottom: 1px solid var(--line);
}
.mon-title {
  font-family: 'Space Mono', monospace;
  font-size: 10px;
  letter-spacing: 0.12em;
  color: var(--accent);
  text-transform: uppercase;
}
.mon-badge {
  display: flex;
  align-items: center;
  gap: 5px;
  font-family: 'Space Mono', monospace;
  font-size: 9px;
  color: var(--good);
  letter-spacing: 0.1em;
}
.mon-badge::before { content: ''; width: 5px; height: 5px; background: var(--good); border-radius: 50%; animation: blink 2s ease-in-out infinite; }

.mon-metrics { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 16px; }
.mon-metric {
  background: var(--bg2);
  border: 1px solid var(--line);
  border-radius: 2px;
  padding: 12px;
}
.mon-metric .mk { font-family: 'Space Mono', monospace; font-size: 9px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 6px; }
.mon-metric .mv { font-family: 'Rajdhani', sans-serif; font-size: 26px; font-weight: 700; color: var(--text); line-height: 1; }
.mon-metric .mv small { font-size: 13px; color: var(--muted); }

.mon-chart { height: 80px; display: flex; align-items: flex-end; gap: 3px; margin-top: 16px; }
.mon-bar { flex: 1; border-radius: 1px 1px 0 0; background: rgba(240,192,0,0.15); border: 1px solid rgba(240,192,0,0.12); transition: background 0.3s; }
.mon-bar.hi { background: var(--accent); border-color: var(--accent); }
.mon-cost {
  margin-top: 16px;
  padding: 12px 14px;
  background: rgba(240,192,0,0.06);
  border: 1px solid rgba(240,192,0,0.15);
  border-radius: 2px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.mon-cost .ck { font-family: 'Space Mono', monospace; font-size: 9px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em; }
.mon-cost .cv { font-family: 'Rajdhani', sans-serif; font-size: 22px; font-weight: 700; color: var(--accent); }

/* ============================================================
   FACTS / DATA SECTION
============================================================ */
.facts-section { padding-top: 0; }

.facts-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  margin-top: 56px;
}
.fact-card {
  padding: 32px 28px;
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 3px;
  position: relative;
  overflow: hidden;
  transition: all 0.25s;
}
.fact-card::before {
  content: '';
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 2px;
  background: linear-gradient(90deg, var(--accent2), transparent);
  opacity: 0;
  transition: opacity 0.3s;
}
.fact-card:hover { border-color: rgba(0,212,255,0.2); }
.fact-card:hover::before { opacity: 1; }

.fact-num {
  font-family: 'Rajdhani', sans-serif;
  font-size: 52px;
  font-weight: 700;
  color: var(--accent2);
  line-height: 1;
  margin-bottom: 8px;
  counter-reset: none;
}
.fact-num .unit { font-size: 24px; color: var(--muted); }
.fact-label {
  font-family: 'Space Mono', monospace;
  font-size: 10px;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--muted);
  margin-bottom: 12px;
}
.fact-card p {
  font-size: 15px;
  font-weight: 300;
  color: var(--text2);
  line-height: 1.6;
}

/* ============================================================
   TIPS SECTION
============================================================ */
.tips-section { padding-top: 0; }

.tips-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  margin-top: 56px;
}
.tip-card {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 3px;
  padding: 28px 24px;
  position: relative;
  overflow: hidden;
  transition: all 0.25s;
}
.tip-card:hover {
  transform: translateY(-6px);
  border-color: rgba(240,192,0,0.2);
  box-shadow: 0 20px 48px rgba(0,0,0,0.4);
}
.tip-num {
  font-family: 'Rajdhani', sans-serif;
  font-size: 64px;
  font-weight: 700;
  color: var(--line2);
  position: absolute;
  top: 8px; right: 16px;
  line-height: 1;
  transition: color 0.3s;
}
.tip-card:hover .tip-num { color: rgba(240,192,0,0.06); }
.tip-icon { font-size: 28px; margin-bottom: 14px; display: block; }
.tip-card h4 {
  font-family: 'Rajdhani', sans-serif;
  font-size: 18px;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  color: var(--text);
  margin-bottom: 8px;
}
.tip-card p {
  font-size: 14px;
  font-weight: 300;
  color: var(--text2);
  line-height: 1.6;
}
.tip-save {
  margin-top: 12px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-family: 'Space Mono', monospace;
  font-size: 9px;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--good);
  padding: 4px 10px;
  border: 1px solid rgba(0,229,154,0.2);
  border-radius: 2px;
  background: rgba(0,229,154,0.06);
}

/* ============================================================
   CTA SECTION
============================================================ */
.cta-section {
  text-align: center;
  padding: 100px 48px;
  max-width: 100%;
  position: relative;
  z-index: 1;
  overflow: hidden;
  background: linear-gradient(to bottom, transparent, rgba(240,192,0,0.03), transparent);
  border-top: 1px solid var(--line);
  border-bottom: 1px solid var(--line);
}
.cta-section::before {
  content: '';
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  width: 600px; height: 300px;
  background: radial-gradient(ellipse, rgba(240,192,0,0.08) 0%, transparent 70%);
  pointer-events: none;
}
.cta-section h2 {
  font-family: 'Rajdhani', sans-serif;
  font-size: clamp(36px, 6vw, 72px);
  font-weight: 700;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  line-height: 1;
  margin-bottom: 20px;
}
.cta-section h2 span { color: var(--accent); }
.cta-section p {
  font-size: 19px;
  font-weight: 300;
  font-style: italic;
  color: var(--text2);
  max-width: 520px;
  margin: 0 auto 40px;
  line-height: 1.6;
}
.cta-buttons { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
.btn-primary {
  font-family: 'Rajdhani', sans-serif;
  font-size: 14px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  background: var(--accent);
  color: #080a0d;
  border: none;
  padding: 14px 32px;
  border-radius: 2px;
  cursor: none;
  text-decoration: none;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.btn-primary:hover { background: #ffd020; box-shadow: 0 0 32px var(--gy); transform: translateY(-2px); }
.btn-outline {
  font-family: 'Rajdhani', sans-serif;
  font-size: 14px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  background: transparent;
  color: var(--text);
  border: 1px solid var(--line2);
  padding: 14px 32px;
  border-radius: 2px;
  cursor: none;
  text-decoration: none;
  transition: all 0.2s;
}
.btn-outline:hover { border-color: var(--accent); color: var(--accent); }

/* ============================================================
   FOOTER
============================================================ */
footer {
  position: relative;
  z-index: 1;
  padding: 32px 48px;
  border-top: 1px solid var(--line);
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 16px;
}
.footer-logo {
  font-family: 'Rajdhani', sans-serif;
  font-size: 15px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--muted);
  display: flex;
  align-items: center;
  gap: 8px;
}
.footer-logo span { color: var(--accent); }
footer p {
  font-family: 'Space Mono', monospace;
  font-size: 10px;
  letter-spacing: 0.1em;
  color: var(--muted);
  text-align: center;
}
.footer-links { display: flex; gap: 20px; }
.footer-links a {
  font-family: 'Space Mono', monospace;
  font-size: 10px;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--muted);
  text-decoration: none;
  transition: color 0.2s;
}
.footer-links a:hover { color: var(--accent); }

/* ============================================================
   ANIMATIONS
============================================================ */
@keyframes fade-up {
  from { opacity: 0; transform: translateY(32px); }
  to   { opacity: 1; transform: translateY(0); }
}
@keyframes blink {
  0%,100% { opacity: 1; }
  50%      { opacity: 0.3; }
}

/* Scroll reveal */
.reveal {
  opacity: 0;
  transform: translateY(40px);
  transition: opacity 0.8s cubic-bezier(0.16,1,0.3,1), transform 0.8s cubic-bezier(0.16,1,0.3,1);
}
.reveal.visible { opacity: 1; transform: translateY(0); }
.reveal-delay-1 { transition-delay: 0.1s; }
.reveal-delay-2 { transition-delay: 0.2s; }
.reveal-delay-3 { transition-delay: 0.3s; }
.reveal-delay-4 { transition-delay: 0.4s; }

/* Counter animation */
.count-up { display: inline-block; }

/* ============================================================
   MOBILE NAV
============================================================ */
.mobile-menu {
  display: none;
  position: fixed;
  inset: 0;
  background: var(--bg);
  z-index: 99;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 28px;
  border-top: 1px solid var(--line);
}
.mobile-menu.open { display: flex; }
.mobile-menu a {
  font-family: 'Rajdhani', sans-serif;
  font-size: 28px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--text2);
  text-decoration: none;
  transition: color 0.2s;
}
.mobile-menu a:hover { color: var(--accent); }

/* ============================================================
   RESPONSIVE
============================================================ */
@media (max-width: 1024px) {
  .features-layout { grid-template-columns: 1fr; gap: 48px; }
  .monitor-display { position: static; }
}

@media (max-width: 768px) {
  nav { padding: 0 20px; }
  .nav-links, .nav-cta { display: none; }
  .nav-ham { display: flex; }
  section { padding: 60px 20px; }
  .section-divider { padding: 0 20px; }
  .why-grid { grid-template-columns: 1fr; }
  .flow-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
  .facts-row { grid-template-columns: 1fr; }
  .tips-grid { grid-template-columns: 1fr 1fr; }
  .cta-section { padding: 72px 20px; }
  footer { padding: 24px 20px; flex-direction: column; text-align: center; }
  .hero-stats { flex-direction: column; }
  .hstat { border-right: none; border-bottom: 1px solid var(--line2); }
  .hstat:last-child { border-bottom: none; }
  body { cursor: auto; }
  .cursor { display: none; }
}

@media (max-width: 480px) {
  .flow-grid { grid-template-columns: 1fr; }
  .tips-grid { grid-template-columns: 1fr; }
  .hero h2 { font-size: 48px; }
  .mon-metrics { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<!-- CURSOR -->
<div class="cursor cursor-dot" id="cursorDot"></div>
<div class="cursor cursor-ring" id="cursorRing"></div>

<!-- BACKGROUND -->
<div class="grid-bg"></div>
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>

<!-- MOBILE MENU -->
<div class="mobile-menu" id="mobileMenu">
  <a href="#pentingnya" onclick="closeMobile()">Pentingnya</a>
  <a href="#cara-kerja" onclick="closeMobile()">Cara Kerja</a>
  <a href="#manfaat" onclick="closeMobile()">Manfaat</a>
  <a href="#tips" onclick="closeMobile()">Tips Hemat</a>
  <a href="{{ route('login') }}" class="nav-cta" style="font-size:16px;padding:12px 28px" onclick="closeMobile()">Mulai Monitor</a>
</div>

<!-- NAVBAR -->
<nav>
  <a href="#" class="nav-logo">
    <span class="bolt">⚡</span>
    <h1>Watt<span>Monitor</span></h1>
  </a>
  <ul class="nav-links">
    <li><a href="#pentingnya">Pentingnya</a></li>
    <li><a href="#cara-kerja">Cara Kerja</a></li>
    <li><a href="#manfaat">Manfaat</a></li>
    <li><a href="#tips">Tips Hemat</a></li>
  </ul>
  <a href="{{ route('login') }}" class="nav-cta">Mulai Monitor</a>
  <button class="nav-ham" id="hamburger" aria-label="Menu">
    <span></span><span></span><span></span>
  </button>
</nav>

<!-- ============================================================
     HERO
============================================================ -->
<div class="hero">
  <div class="hero-badge">Sistem Monitoring IoT Real-Time</div>
  <h2>
    <span class="line1">Kendalikan</span>
    <span class="line2">Konsumsi</span>
    <span class="line3">Listrikmu</span>
  </h2>
  <p class="hero-sub">
    Pahami bagaimana energi mengalir di rumahmu — pantau, analisis,
    dan kurangi pemborosan secara cerdas dengan teknologi sensor modern.
  </p>
  <div class="hero-stats">
    <div class="hstat">
      <div class="num"><span class="count-up" data-target="67">0</span>%</div>
      <div class="desc">Potensi Penghematan</div>
    </div>
    <div class="hstat">
      <div class="num">Rp <span class="count-up" data-target="2">0</span>Jt+</div>
      <div class="desc">Hemat per Tahun</div>
    </div>
    <div class="hstat">
      <div class="num"><span class="count-up" data-target="24">0</span>/7</div>
      <div class="desc">Monitoring Real-Time</div>
    </div>
    <div class="hstat">
      <div class="num"><span class="count-up" data-target="99">0</span>%</div>
      <div class="desc">Akurasi Sensor</div>
    </div>
  </div>
  <div class="scroll-hint">
    <span>Scroll</span>
    <div class="scroll-line"></div>
  </div>
</div>

<!-- DIVIDER -->
<div class="section-divider">
  <span class="tag">01 — Pentingnya</span>
  <div class="line"></div>
</div>

<!-- ============================================================
     PENTINGNYA SECTION
============================================================ -->
<section id="pentingnya">
  <div class="section-label reveal">Mengapa Penting?</div>
  <h2 class="section-title reveal">
    Listrik Bukan <span>Sekadar</span> Tombol
  </h2>
  <p class="section-body reveal">
    Setiap watt yang kamu gunakan berdampak pada tagihan, lingkungan, dan kestabilan jaringan listrik nasional.
    Monitoring konsumsi listrik adalah langkah pertama menuju rumah yang efisien dan cerdas.
  </p>

  <div class="why-grid">
    <div class="why-card reveal">
      <span class="why-num">01</span>
      <span class="why-icon">💰</span>
      <h3>Efisiensi Finansial</h3>
      <p>Rata-rata rumah tangga Indonesia membuang <strong style="color:var(--accent)">30–40%</strong> energi secara sia-sia. Dengan mengetahui perangkat mana yang paling boros, kamu bisa memotong tagihan listrik secara signifikan setiap bulannya.</p>
    </div>
    <div class="why-card reveal reveal-delay-1">
      <span class="why-num">02</span>
      <span class="why-icon">🌍</span>
      <h3>Tanggung Jawab Lingkungan</h3>
      <p>Setiap 1 kWh listrik yang dihemat berarti pengurangan emisi CO₂ sekitar <strong style="color:var(--accent)">0,7 kg</strong>. Monitoring konsumsi adalah kontribusi nyata terhadap keberlanjutan bumi untuk generasi mendatang.</p>
    </div>
    <div class="why-card reveal reveal-delay-2">
      <span class="why-num">03</span>
      <span class="why-icon">⚠️</span>
      <h3>Deteksi Dini Bahaya</h3>
      <p>Lonjakan arus yang tidak wajar bisa menjadi tanda korsleting atau kerusakan perangkat sebelum berujung kebakaran. Monitoring real-time memberikan <strong style="color:var(--accent)">peringatan dini</strong> yang bisa menyelamatkan nyawa.</p>
    </div>
    <div class="why-card reveal reveal-delay-3">
      <span class="why-num">04</span>
      <span class="why-icon">🏠</span>
      <h3>Rumah Lebih Cerdas</h3>
      <p>Data konsumsi listrik adalah fondasi <strong style="color:var(--accent)">smart home</strong>. Dengan memahami pola penggunaan, sistem otomasi dapat bekerja lebih efisien dan tepat sasaran sesuai kebutuhan penghuninya.</p>
    </div>
  </div>
</section>

<!-- DIVIDER -->
<div class="section-divider">
  <span class="tag">02 — Cara Kerja</span>
  <div class="line"></div>
</div>

<!-- ============================================================
     CARA KERJA SECTION
============================================================ -->
<section id="cara-kerja" class="flow-section">
  <div class="section-label reveal">Bagaimana Sistemnya?</div>
  <h2 class="section-title reveal">
    Dari Sensor ke <span>Dashboard</span>
  </h2>
  <p class="section-body reveal">
    Sistem monitoring listrik menggunakan rantai teknologi yang ringkas namun powerful — mulai dari pembacaan fisik hingga tampilan data yang bisa langsung kamu pahami.
  </p>

  <div class="flow-container">
    <div class="flow-grid">
      <div class="flow-step reveal">
        <div class="flow-step-num">// STEP 01</div>
        <span class="flow-step-icon">🔌</span>
        <h3>Sensor PZEM-004T</h3>
        <p>Sensor klem mengukur tegangan (V), arus (A), daya (W), energi (kWh), frekuensi (Hz), dan faktor daya secara bersamaan tanpa memutus aliran listrik.</p>
      </div>
      <div class="flow-step reveal reveal-delay-1">
        <div class="flow-step-num">// STEP 02</div>
        <span class="flow-step-icon">📡</span>
        <h3>Mikrokontroler ESP32</h3>
        <p>Data dari sensor dibaca oleh ESP32 setiap beberapa detik, lalu dikirim melalui WiFi ke server menggunakan protokol HTTP/MQTT secara otomatis.</p>
      </div>
      <div class="flow-step reveal reveal-delay-2">
        <div class="flow-step-num">// STEP 03</div>
        <span class="flow-step-icon">🖥️</span>
        <h3>Server Laravel API</h3>
        <p>Backend Laravel menerima, memvalidasi, dan menyimpan data ke database MySQL. API endpoint tersedia untuk pengambilan data historis dan real-time.</p>
      </div>
      <div class="flow-step reveal reveal-delay-3">
        <div class="flow-step-num">// STEP 04</div>
        <span class="flow-step-icon">📊</span>
        <h3>Dashboard & Analisis</h3>
        <p>Data divisualisasikan dalam grafik, tabel, dan kartu metrik. Sistem menghitung estimasi biaya, mendeteksi anomali, dan menyajikan riwayat lengkap.</p>
      </div>
    </div>
  </div>
</section>

<!-- DIVIDER -->
<div class="section-divider">
  <span class="tag">03 — Manfaat</span>
  <div class="line"></div>
</div>

<!-- ============================================================
     MANFAAT SECTION
============================================================ -->
<section id="manfaat">
  <div class="section-label reveal">Apa yang Kamu Dapatkan?</div>
  <h2 class="section-title reveal">
    Fitur & <span>Kegunaan</span>
  </h2>

  <div class="features-layout">
    <div class="feat-list">
      <div class="feat-item active-feat reveal">
        <div class="feat-icon-wrap">⚡</div>
        <div class="feat-text">
          <h4>Monitoring Real-Time</h4>
          <p>Pantau tegangan, arus, daya, dan energi secara langsung. Data diperbarui setiap beberapa detik sehingga kamu selalu tahu kondisi listrik saat ini.</p>
        </div>
      </div>
      <div class="feat-item reveal reveal-delay-1">
        <div class="feat-icon-wrap">📈</div>
        <div class="feat-text">
          <h4>Analisis Historis</h4>
          <p>Lihat tren konsumsi harian, mingguan, dan bulanan. Identifikasi jam-jam dengan konsumsi tertinggi dan sesuaikan kebiasaan penggunaan perangkat.</p>
        </div>
      </div>
      <div class="feat-item reveal reveal-delay-2">
        <div class="feat-icon-wrap">💸</div>
        <div class="feat-text">
          <h4>Estimasi Biaya Akurat</h4>
          <p>Sistem secara otomatis menghitung estimasi tagihan listrik berdasarkan konsumsi aktual dan tarif PLN, sehingga tidak ada kejutan di akhir bulan.</p>
        </div>
      </div>
      <div class="feat-item reveal reveal-delay-3">
        <div class="feat-icon-wrap">🚨</div>
        <div class="feat-text">
          <h4>Deteksi Anomali</h4>
          <p>Sistem memberi peringatan otomatis saat terdeteksi lonjakan daya yang tidak normal, membantu mencegah kerusakan perangkat dan risiko kebakaran.</p>
        </div>
      </div>
      <div class="feat-item reveal reveal-delay-4">
        <div class="feat-icon-wrap">📁</div>
        <div class="feat-text">
          <h4>Ekspor Data CSV</h4>
          <p>Unduh seluruh riwayat data dalam format CSV untuk keperluan analisis lanjutan, laporan, atau arsip penggunaan energi jangka panjang.</p>
        </div>
      </div>
    </div>

    <div class="monitor-display reveal">
      <div class="mon-header">
        <span class="mon-title">// Live Monitor</span>
        <span class="mon-badge">NORMAL</span>
      </div>
      <div class="mon-metrics">
        <div class="mon-metric">
          <div class="mk">Tegangan</div>
          <div class="mv" id="monV">220.4 <small>V</small></div>
        </div>
        <div class="mon-metric">
          <div class="mk">Arus</div>
          <div class="mv" id="monA">3.82 <small>A</small></div>
        </div>
        <div class="mon-metric">
          <div class="mk">Daya</div>
          <div class="mv" id="monW">840 <small>W</small></div>
        </div>
        <div class="mon-metric">
          <div class="mk">Energi</div>
          <div class="mv" id="monE">2.46 <small>kWh</small></div>
        </div>
      </div>
      <div class="mon-chart" id="monChart"></div>
      <div class="mon-cost">
        <span class="ck">Estimasi Hari Ini</span>
        <span class="cv" id="monCost">Rp 3.720</span>
      </div>
    </div>
  </div>
</section>

<!-- DIVIDER -->
<div class="section-divider">
  <span class="tag">04 — Fakta</span>
  <div class="line"></div>
</div>

<!-- ============================================================
     FAKTA SECTION
============================================================ -->
<section class="facts-section">
  <div class="section-label reveal">Tahukah Kamu?</div>
  <h2 class="section-title reveal">
    Data & <span>Fakta</span> Energi
  </h2>

  <div class="facts-row">
    <div class="fact-card reveal">
      <div class="fact-num"><span class="count-up" data-target="40">0</span><span class="unit">%</span></div>
      <div class="fact-label">Pemborosan Rata-Rata Rumah Tangga</div>
      <p>Penelitian menunjukkan hampir separuh konsumsi listrik rumah tangga bisa dihindari dengan kebiasaan yang lebih bijak dan pemantauan yang tepat.</p>
    </div>
    <div class="fact-card reveal reveal-delay-1">
      <div class="fact-num"><span class="count-up" data-target="270">0</span><span class="unit">kWh</span></div>
      <div class="fact-label">Rata-Rata Konsumsi Bulanan Indonesia</div>
      <p>Rata-rata rumah tangga di Indonesia mengonsumsi sekitar 270 kWh per bulan — setara dengan menyalakan AC 1 PK selama lebih dari 300 jam.</p>
    </div>
    <div class="fact-card reveal reveal-delay-2">
      <div class="fact-num"><span class="count-up" data-target="700">0</span><span class="unit">gr</span></div>
      <div class="fact-label">CO₂ per kWh dari PLN</div>
      <p>Setiap kilowatt-hour listrik yang kamu gunakan menghasilkan sekitar 700 gram emisi CO₂ — hemat listrik berarti langsung berkontribusi pada iklim yang lebih baik.</p>
    </div>
  </div>
</section>

<!-- DIVIDER -->
<div class="section-divider">
  <span class="tag">05 — Tips</span>
  <div class="line"></div>
</div>

<!-- ============================================================
     TIPS HEMAT
============================================================ -->
<section id="tips" class="tips-section">
  <div class="section-label reveal">Hemat Mulai Sekarang</div>
  <h2 class="section-title reveal">
    Tips <span>Cerdas</span> Hemat Listrik
  </h2>
  <p class="section-body reveal">
    Penghematan nyata dimulai dari kebiasaan kecil yang konsisten. Dengan monitoring yang tepat, kamu tahu persis di mana harus fokus.
  </p>

  <div class="tips-grid" style="margin-top:48px">
    <div class="tip-card reveal">
      <span class="tip-num">1</span>
      <span class="tip-icon">🌡️</span>
      <h4>Atur Suhu AC Optimal</h4>
      <p>Setiap kenaikan 1°C pada thermostat AC menghemat sekitar 6% konsumsi energi. Suhu 24–26°C adalah titik ideal antara kenyamanan dan efisiensi.</p>
      <span class="tip-save">⬇ Hemat ~15%</span>
    </div>
    <div class="tip-card reveal reveal-delay-1">
      <span class="tip-num">2</span>
      <span class="tip-icon">🔌</span>
      <h4>Cabut Charger Saat Tidak Dipakai</h4>
      <p>Charger dan adaptor yang tetap terpasang terus menyedot listrik meski tidak digunakan (standby power). Kebiasaan ini bisa membuang hingga 10% tagihan.</p>
      <span class="tip-save">⬇ Hemat ~10%</span>
    </div>
    <div class="tip-card reveal reveal-delay-2">
      <span class="tip-num">3</span>
      <span class="tip-icon">💡</span>
      <h4>Ganti ke Lampu LED</h4>
      <p>Lampu LED mengonsumsi 75% lebih sedikit energi dibanding lampu pijar biasa, dengan umur 25 kali lebih panjang. Investasi sekali, hemat bertahun-tahun.</p>
      <span class="tip-save">⬇ Hemat ~20%</span>
    </div>
    <div class="tip-card reveal reveal-delay-3">
      <span class="tip-num">4</span>
      <span class="tip-icon">🕐</span>
      <h4>Manfaatkan Tarif Off-Peak</h4>
      <p>Gunakan peralatan berdaya tinggi (mesin cuci, setrika, oven) pada jam di luar beban puncak (22:00–06:00) untuk mendapatkan tarif listrik yang lebih murah.</p>
      <span class="tip-save">⬇ Hemat ~12%</span>
    </div>
    <div class="tip-card reveal reveal-delay-4">
      <span class="tip-num">5</span>
      <span class="tip-icon">❄️</span>
      <h4>Rawat Kulkas Secara Rutin</h4>
      <p>Karet pintu kulkas yang rusak bisa membuat kompresor bekerja ekstra keras. Bersihkan kondensor dan pastikan tidak ada makanan panas yang dimasukkan langsung.</p>
      <span class="tip-save">⬇ Hemat ~8%</span>
    </div>
    <div class="tip-card reveal reveal-delay-4">
      <span class="tip-num">6</span>
      <span class="tip-icon">📊</span>
      <h4>Monitor & Evaluasi Rutin</h4>
      <p>Periksa dashboard monitoring setiap hari. Identifikasi anomali dan bandingkan konsumsi harian. Pengetahuan adalah kunci penghematan yang konsisten dan terukur.</p>
      <span class="tip-save">⬇ Hemat ~30%</span>
    </div>
  </div>
</section>

<!-- ============================================================
     CTA SECTION
============================================================ -->
<div class="cta-section" id="dashboard">
  <h2>Siap Mulai <span>Monitor</span><br>Listrikmu?</h2>
  <p>Pantau konsumsi energi rumahmu secara real-time. Data yang kamu butuhkan, kapan pun kamu mau.</p>
  <div class="cta-buttons">
    <a href="{{ route('login') }}" class="btn-primary">⚡ Buka Dashboard</a>
    <a href="{{ route('login') }}" class="btn-outline">Lihat Riwayat Data</a>
  </div>
</div>

<!-- FOOTER -->
<footer>
  <div class="footer-logo">⚡ Watt<span>Monitor</span></div>
  <p>Sistem Monitoring Konsumsi Listrik Berbasis IoT © 2026</p>
  <div class="footer-links">
    <a href="{{ route('login') }}">Dashboard</a>
    <a href="{{ route('login') }}">History</a>
    <a href="{{ route('api.sensor-readings.index') }}">API</a>
  </div>
</footer>

<!-- ============================================================
     JAVASCRIPT
============================================================ -->
<script>
const dot  = document.getElementById('cursorDot');
const ring = document.getElementById('cursorRing');
let mx = 0, my = 0, rx = 0, ry = 0;
document.addEventListener('mousemove', e => { mx = e.clientX; my = e.clientY; });
function animCursor() {
  if (dot)  { dot.style.left  = mx + 'px'; dot.style.top  = my + 'px'; }
  rx += (mx - rx) * 0.12;
  ry += (my - ry) * 0.12;
  if (ring) { ring.style.left = rx + 'px'; ring.style.top = ry + 'px'; }
  requestAnimationFrame(animCursor);
}
animCursor();

const ham   = document.getElementById('hamburger');
const mMenu = document.getElementById('mobileMenu');
ham.addEventListener('click', () => {
  mMenu.classList.toggle('open');
  const spans = ham.querySelectorAll('span');
  if (mMenu.classList.contains('open')) {
    spans[0].style.transform = 'translateY(6.5px) rotate(45deg)';
    spans[1].style.opacity = '0';
    spans[2].style.transform = 'translateY(-6.5px) rotate(-45deg)';
  } else {
    spans[0].style.transform = '';
    spans[1].style.opacity = '';
    spans[2].style.transform = '';
  }
});
function closeMobile() { mMenu.classList.remove('open'); }

const revealEls = document.querySelectorAll('.reveal');
const observer  = new IntersectionObserver((entries) => {
  entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); } });
}, { threshold: 0.12 });
revealEls.forEach(el => observer.observe(el));

function animateCounter(el) {
  const target = parseInt(el.dataset.target);
  const dur    = 1600;
  const start  = performance.now();
  function update(now) {
    const elapsed = now - start;
    const progress = Math.min(elapsed / dur, 1);
    const ease = 1 - Math.pow(1 - progress, 3);
    el.textContent = Math.floor(ease * target);
    if (progress < 1) requestAnimationFrame(update);
    else el.textContent = target;
  }
  requestAnimationFrame(update);
}
const countEls = document.querySelectorAll('.count-up');
const countObs = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting && !e.target.dataset.done) {
      e.target.dataset.done = '1';
      animateCounter(e.target);
    }
  });
}, { threshold: 0.5 });
countEls.forEach(el => countObs.observe(el));

const bars = [30, 55, 70, 80, 90, 85, 95, 88, 75, 92, 85, 90];
const monChart = document.getElementById('monChart');
if (monChart) {
  bars.forEach((v, i) => {
    const b = document.createElement('div');
    b.className = 'mon-bar' + (i === bars.length - 1 ? ' hi' : '');
    b.style.height = v + '%';
    monChart.appendChild(b);
  });
}

let baseV = 220.4, baseA = 3.82, baseW = 840, baseE = 2.46, baseCost = 3720;
function jitter(val, range) {
  return (val + (Math.random() - 0.5) * range * 2).toFixed(val < 10 ? 3 : val < 100 ? 2 : 1);
}
function updateMonitor() {
  const v = jitter(baseV, 0.8);
  const a = jitter(baseA, 0.15);
  const w = Math.round(baseW + (Math.random() - 0.5) * 40);
  baseE += 0.001;
  baseCost += 1;
  document.getElementById('monV').innerHTML = v + ' <small>V</small>';
  document.getElementById('monA').innerHTML = a + ' <small>A</small>';
  document.getElementById('monW').innerHTML = w + ' <small>W</small>';
  document.getElementById('monE').innerHTML = baseE.toFixed(3) + ' <small>kWh</small>';
  document.getElementById('monCost').textContent = 'Rp ' + baseCost.toLocaleString('id-ID');
  const allBars = monChart.querySelectorAll('.mon-bar');
  if (allBars.length) {
    const newH = 60 + Math.random() * 35;
    allBars[allBars.length - 1].style.height = newH + '%';
  }
}
setInterval(updateMonitor, 2000);

window.addEventListener('scroll', () => {
  const n = document.querySelector('nav');
  n.style.boxShadow = window.scrollY > 40
    ? '0 8px 32px rgba(0,0,0,0.4)'
    : 'none';
});

document.querySelectorAll('.feat-item').forEach(item => {
  item.addEventListener('mouseenter', function() {
    document.querySelectorAll('.feat-item').forEach(i => i.classList.remove('active-feat'));
    this.classList.add('active-feat');
  });
});
</script>
</body>
</html>
