<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 - Service Unavailable</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@300;600;800&family=Share+Tech+Mono&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Lexend', sans-serif;
            background: #080c14;
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 70% 50% at 50% 60%, rgba(56, 189, 248, 0.07) 0%, transparent 65%),
                radial-gradient(ellipse 40% 40% at 15% 85%, rgba(99, 102, 241, 0.06) 0%, transparent 55%),
                radial-gradient(ellipse 35% 35% at 85% 15%, rgba(56, 189, 248, 0.05) 0%, transparent 50%);
            pointer-events: none;
        }

        /* Construction diagonal stripes top & bottom */
        .stripe-bar {
            position: fixed;
            left: 0;
            right: 0;
            height: 10px;
            background: repeating-linear-gradient(90deg,
                    #f59e0b 0px, #f59e0b 24px,
                    #1a1a1a 24px, #1a1a1a 48px);
            z-index: 100;
            opacity: 0.85;
        }

        .stripe-bar.top {
            top: 0;
        }

        .stripe-bar.bottom {
            bottom: 0;
        }

        /* Floating bolt/nut particles */
        .bolts {
            position: fixed;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .bolt-particle {
            position: absolute;
            animation: boltDrift var(--d) ease-in-out infinite var(--delay);
            opacity: 0;
        }

        @keyframes boltDrift {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }

            10% {
                opacity: 0.6;
            }

            90% {
                opacity: 0.3;
            }

            100% {
                transform: translateY(-20px) rotate(360deg);
                opacity: 0;
            }
        }

        .container {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 2rem;
        }

        .illustration {
            width: 310px;
            height: 215px;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 0 35px rgba(56, 189, 248, 0.2));
            animation: fadeUp 0.9s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Hard hat bob */
        .hardhat {
            animation: hatBob 3s ease-in-out infinite;
            transform-origin: 150px 55px;
        }

        @keyframes hatBob {

            0%,
            100% {
                transform: translateY(0) rotate(-2deg);
            }

            50% {
                transform: translateY(-6px) rotate(2deg);
            }
        }

        /* Wrench spin */
        .wrench {
            animation: wrenchSpin 4s ease-in-out infinite;
            transform-origin: 85px 140px;
        }

        @keyframes wrenchSpin {

            0%,
            100% {
                transform: rotate(-25deg);
            }

            50% {
                transform: rotate(25deg);
            }
        }

        /* Screwdriver tap */
        .screwdriver {
            animation: screwTap 2s ease-in-out infinite;
            transform-origin: 220px 130px;
        }

        @keyframes screwTap {

            0%,
            100% {
                transform: rotate(15deg);
            }

            50% {
                transform: rotate(-10deg) translateY(-4px);
            }
        }

        /* Gear turns */
        .gear-a {
            animation: gearSpin 6s linear infinite;
            transform-origin: 150px 125px;
        }

        .gear-b {
            animation: gearSpin 3s linear infinite reverse;
            transform-origin: 178px 143px;
        }

        @keyframes gearSpin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Progress bar fill loop */
        .progress-fill {
            animation: progressLoop 3s ease-in-out infinite;
            transform-origin: left center;
        }

        @keyframes progressLoop {
            0% {
                transform: scaleX(0);
                opacity: 1;
            }

            80% {
                transform: scaleX(1);
                opacity: 1;
            }

            90% {
                transform: scaleX(1);
                opacity: 0.5;
            }

            100% {
                transform: scaleX(0);
                opacity: 0;
            }
        }

        /* LED row blink */
        .led-a {
            animation: ledCycle 3s ease-in-out infinite 0s;
        }

        .led-b {
            animation: ledCycle 3s ease-in-out infinite -1s;
        }

        .led-c {
            animation: ledCycle 3s ease-in-out infinite -2s;
        }

        @keyframes ledCycle {

            0%,
            100% {
                fill: #1e3a5f;
            }

            33% {
                fill: #38bdf8;
            }
        }

        /* Antenna pulse */
        .antenna-pulse {
            animation: antPulse 1.5s ease-in-out infinite;
        }

        @keyframes antPulse {

            0%,
            100% {
                opacity: 0.2;
                r: 5;
            }

            50% {
                opacity: 1;
                r: 8;
            }
        }

        /* Hammer hit */
        .hammer {
            animation: hammerHit 1s ease-in-out infinite;
            transform-origin: 240px 75px;
        }

        @keyframes hammerHit {

            0%,
            100% {
                transform: rotate(-30deg);
            }

            50% {
                transform: rotate(10deg);
            }
        }

        .code {
            font-family: 'Lexend', sans-serif;
            font-size: clamp(5rem, 18vw, 9rem);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -4px;
            background: linear-gradient(135deg, #e0f2fe 10%, #38bdf8 50%, #0284c7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            animation: fadeUp 0.9s 0.1s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Maintenance badge */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.35);
            color: #fbbf24;
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.68rem;
            letter-spacing: 0.2em;
            padding: 0.3rem 0.9rem;
            border-radius: 4px;
            margin-bottom: 1.25rem;
            animation: fadeUp 0.9s 0.2s cubic-bezier(0.16, 1, 0.3, 1) both, badgeGlow 2s ease-in-out infinite 1s;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #f59e0b;
            animation: dotBlink 1s ease-in-out infinite;
        }

        @keyframes dotBlink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.1;
            }
        }

        @keyframes badgeGlow {

            0%,
            100% {
                box-shadow: none;
            }

            50% {
                box-shadow: 0 0 14px rgba(245, 158, 11, 0.25);
            }
        }

        h2 {
            font-size: 1.35rem;
            font-weight: 600;
            color: #e2e8f0;
            margin-bottom: 0.6rem;
            animation: fadeUp 0.9s 0.2s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        p {
            font-size: 0.88rem;
            color: #475569;
            max-width: 320px;
            line-height: 1.8;
            margin-bottom: 1.25rem;
            font-weight: 300;
            animation: fadeUp 0.9s 0.25s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Terminal status */
        .terminal {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(56, 189, 248, 0.15);
            border-radius: 8px;
            padding: 0.85rem 1.25rem;
            margin-bottom: 2rem;
            text-align: left;
            width: 100%;
            max-width: 360px;
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.7rem;
            color: #475569;
            line-height: 2;
            animation: fadeUp 0.9s 0.3s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .terminal .ok {
            color: #4ade80;
        }

        .terminal .info {
            color: #38bdf8;
        }

        .terminal .warn {
            color: #fbbf24;
        }

        .terminal .cursor {
            display: inline-block;
            width: 7px;
            height: 12px;
            background: #38bdf8;
            vertical-align: middle;
            animation: blink 1s step-end infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, #0369a1, #38bdf8);
            color: #fff;
            font-weight: 600;
            text-decoration: none;
            border-radius: 100px;
            font-size: 0.88rem;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 0 25px rgba(56, 189, 248, 0.25);
            animation: fadeUp 0.9s 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .btn:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 0 45px rgba(56, 189, 248, 0.45);
        }

        .btn svg {
            width: 15px;
            height: 15px;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <div class="stripe-bar top"></div>
    <div class="stripe-bar bottom"></div>
    <div class="bolts" id="bolts"></div>

    <div class="container">

        <svg class="illustration" viewBox="0 0 310 215" fill="none" xmlns="http://www.w3.org/2000/svg">

            <!-- Server/panel base -->
            <rect x="95" y="90" width="120" height="115" rx="8" fill="#0d1628" stroke="rgba(56,189,248,0.2)"
                stroke-width="1.5" />
            <rect x="103" y="98" width="104" height="99" rx="5" fill="#080c14" />

            <!-- Progress bars on server -->
            <rect x="110" y="108" width="72" height="8" rx="3" fill="#1e293b" />
            <rect class="progress-fill" x="110" y="108" width="72" height="8" rx="3" fill="url(#progGrad)" />

            <rect x="110" y="122" width="72" height="8" rx="3" fill="#1e293b" />
            <rect class="progress-fill" x="110" y="122" width="72" height="8" rx="3" fill="url(#progGrad)"
                style="animation-delay:-1s" />

            <rect x="110" y="136" width="72" height="8" rx="3" fill="#1e293b" />
            <rect class="progress-fill" x="110" y="136" width="72" height="8" rx="3" fill="url(#progGrad)"
                style="animation-delay:-2s" />

            <!-- LED status row -->
            <circle class="led-a" cx="188" cy="112" r="4" fill="#1e3a5f" />
            <circle class="led-b" cx="188" cy="126" r="4" fill="#1e3a5f" />
            <circle class="led-c" cx="188" cy="140" r="4" fill="#1e3a5f" />

            <!-- Antenna on server -->
            <line x1="155" y1="90" x2="155" y2="68" stroke="rgba(56,189,248,0.5)" stroke-width="1.5"
                stroke-linecap="round" />
            <circle class="antenna-pulse" cx="155" cy="63" r="5" fill="#38bdf8" />

            <!-- Gears -->
            <g class="gear-a">
                <circle cx="150" cy="125" r="20" fill="none" stroke="rgba(56,189,248,0.2)" stroke-width="6" />
                <circle cx="150" cy="125" r="12" fill="#0d1628" stroke="rgba(56,189,248,0.3)" stroke-width="1.5" />
                <circle cx="150" cy="125" r="4" fill="rgba(56,189,248,0.5)" />
                <rect x="146" y="100" width="8" height="6" rx="2" fill="rgba(56,189,248,0.35)" />
                <rect x="146" y="144" width="8" height="6" rx="2" fill="rgba(56,189,248,0.35)" />
                <rect x="125" y="121" width="6" height="8" rx="2" fill="rgba(56,189,248,0.35)" />
                <rect x="169" y="121" width="6" height="8" rx="2" fill="rgba(56,189,248,0.35)" />
                <rect x="131" y="106" width="6" height="8" rx="2" fill="rgba(56,189,248,0.35)"
                    transform="rotate(-45 134 110)" />
                <rect x="163" y="106" width="6" height="8" rx="2" fill="rgba(56,189,248,0.35)"
                    transform="rotate(45 166 110)" />
                <rect x="131" y="134" width="6" height="8" rx="2" fill="rgba(56,189,248,0.35)"
                    transform="rotate(45 134 138)" />
                <rect x="163" y="134" width="6" height="8" rx="2" fill="rgba(56,189,248,0.35)"
                    transform="rotate(-45 166 138)" />
            </g>
            <g class="gear-b">
                <circle cx="178" cy="143" r="12" fill="none" stroke="rgba(56,189,248,0.15)" stroke-width="4" />
                <circle cx="178" cy="143" r="7" fill="#0d1628" stroke="rgba(56,189,248,0.2)" stroke-width="1" />
                <circle cx="178" cy="143" r="2.5" fill="rgba(56,189,248,0.4)" />
                <rect x="174" y="128" width="8" height="5" rx="1.5" fill="rgba(56,189,248,0.3)" />
                <rect x="174" y="155" width="8" height="5" rx="1.5" fill="rgba(56,189,248,0.3)" />
                <rect x="163" y="139" width="5" height="8" rx="1.5" fill="rgba(56,189,248,0.3)" />
                <rect x="190" y="139" width="5" height="8" rx="1.5" fill="rgba(56,189,248,0.3)" />
            </g>

            <!-- Hard hat -->
            <g class="hardhat">
                <ellipse cx="155" cy="58" rx="38" ry="14" fill="#f59e0b" />
                <rect x="117" y="52" width="76" height="18" rx="6" fill="#f59e0b" />
                <rect x="113" y="64" width="84" height="6" rx="3" fill="#d97706" />
                <rect x="138" y="42" width="34" height="18" rx="8" fill="#fbbf24" />
                <rect x="145" y="56" width="20" height="4" rx="2" fill="#d97706" opacity="0.5" />
            </g>

            <!-- Wrench -->
            <g class="wrench">
                <path d="M72 108 Q65 120 70 135 L88 153 Q96 158 102 150 L85 133 L90 118 Z" fill="#334155"
                    stroke="rgba(148,163,184,0.3)" stroke-width="1" />
                <circle cx="74" cy="108" r="10" fill="none" stroke="#475569" stroke-width="3" />
                <circle cx="74" cy="108" r="5" fill="#334155" />
                <circle cx="98" cy="152" r="8" fill="none" stroke="#475569" stroke-width="3" />
                <circle cx="98" cy="152" r="4" fill="#334155" />
            </g>

            <!-- Screwdriver -->
            <g class="screwdriver">
                <rect x="210" y="95" width="10" height="50" rx="5" fill="#6366f1" />
                <rect x="212" y="92" width="6" height="12" rx="2" fill="#818cf8" />
                <rect x="213" y="145" width="4" height="30" rx="1" fill="#94a3b8" />
                <rect x="212" y="172" width="6" height="4" rx="1" fill="#64748b" />
            </g>

            <!-- Hammer -->
            <g class="hammer">
                <rect x="225" y="55" width="30" height="18" rx="4" fill="#374151" />
                <rect x="225" y="55" width="14" height="18" rx="4" fill="#4b5563" />
                <rect x="238" y="70" width="6" height="40" rx="3" fill="#6b7280" />
            </g>

            <!-- Caution tape across bottom of server -->
            <clipPath id="serverClip">
                <rect x="95" y="185" width="120" height="20" rx="0 0 8 8" />
            </clipPath>
            <rect x="95" y="185" width="120" height="20" fill="#1a1500" />
            <g clip-path="url(#serverClip)">
                <rect x="95" y="185" width="24" height="20" fill="#f59e0b" opacity="0.8" />
                <rect x="143" y="185" width="24" height="20" fill="#f59e0b" opacity="0.8" />
                <rect x="191" y="185" width="24" height="20" fill="#f59e0b" opacity="0.8" />
            </g>

            <!-- Floating nuts & bolts -->
            <circle cx="55" cy="100" r="5" fill="none" stroke="rgba(148,163,184,0.3)" stroke-width="1.5" />
            <circle cx="55" cy="100" r="2" fill="rgba(148,163,184,0.2)" />
            <circle cx="262" cy="130" r="4" fill="none" stroke="rgba(148,163,184,0.25)" stroke-width="1.5" />
            <circle cx="262" cy="130" r="2" fill="rgba(148,163,184,0.15)" />
            <rect x="48" y="155" width="8" height="10" rx="1" fill="none" stroke="rgba(148,163,184,0.25)"
                stroke-width="1.5" transform="rotate(20 52 160)" />
            <rect x="260" cy="80" width="8" height="10" rx="1" fill="none" stroke="rgba(148,163,184,0.2)"
                stroke-width="1.5" transform="rotate(-15 264 85)" />

            <defs>
                <linearGradient id="progGrad" x1="0" y1="0" x2="1" y2="0">
                    <stop offset="0%" stop-color="#0284c7" />
                    <stop offset="100%" stop-color="#38bdf8" />
                </linearGradient>
            </defs>

        </svg>

        <div class="code">503</div>
        <div class="badge"><span class="badge-dot"></span> MAINTENANCE IN PROGRESS</div>
        <h2>We're Under Construction</h2>
        <p>Our team is working hard to improve things. We'll be back up shortly — thanks for your patience.</p>

        <div class="terminal">
            <span class="ok">[OK]</span>&nbsp;&nbsp;&nbsp; Backup completed successfully<br>
            <span class="info">[RUN]</span>&nbsp;&nbsp; Applying system updates...<br>
            <span class="info">[RUN]</span>&nbsp;&nbsp; Rebuilding service containers...<br>
            <span class="warn">[WAIT]</span>&nbsp; Restarting core services<br>
            <span class="ok">$</span>&nbsp;&nbsp;&nbsp;&nbsp; estimated downtime ~10 min <span class="cursor"></span>
        </div>

        <a href="{{ url('/') }}" class="btn" onclick="window.location.reload(); return false;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="1 4 1 10 7 10" />
                <path d="M3.51 15a9 9 0 1 0 .49-4.5" />
            </svg>
            Check Again
        </a>

    </div>

    <script>
        // Floating bolt particles
        const bolts = document.getElementById('bolts');
        for (let i = 0; i < 18; i++) {
            const b = document.createElement('div');
            b.className = 'bolt-particle';
            const size = 4 + Math.random() * 6;
            b.innerHTML = `<svg width="${size * 3}" height="${size * 3}" viewBox="0 0 12 12">
                <circle cx="6" cy="6" r="5" fill="none" stroke="rgba(148,163,184,0.2)" stroke-width="1.5"/>
                <circle cx="6" cy="6" r="2" fill="rgba(148,163,184,0.15)"/>
            </svg>`;
            b.style.cssText = `
                left:${Math.random() * 100}%;
                bottom:-20px;
                --d:${5 + Math.random() * 8}s;
                --delay:-${Math.random() * 10}s;
            `;
            bolts.appendChild(b);
        }
    </script>

</body>

</html>