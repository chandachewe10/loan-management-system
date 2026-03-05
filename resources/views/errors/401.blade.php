<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>401 - Unauthorized</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Outfit:wght@300;600;800&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #030712;
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
                radial-gradient(ellipse 60% 50% at 50% 50%, rgba(16, 185, 129, 0.08) 0%, transparent 65%),
                radial-gradient(ellipse 40% 40% at 80% 20%, rgba(6, 182, 212, 0.06) 0%, transparent 55%);
            pointer-events: none;
        }

        /* Scanning line */
        .scan-line {
            position: fixed;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(16, 185, 129, 0.4), transparent);
            animation: scan 4s linear infinite;
            pointer-events: none;
            z-index: 5;
        }

        @keyframes scan {
            0% {
                top: -2px;
                opacity: 0;
            }

            5% {
                opacity: 1;
            }

            95% {
                opacity: 1;
            }

            100% {
                top: 100vh;
                opacity: 0;
            }
        }

        /* Binary rain columns */
        .matrix {
            position: fixed;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            opacity: 0.07;
        }

        .col {
            position: absolute;
            top: 0;
            font-family: 'Share Tech Mono', monospace;
            font-size: 12px;
            color: #10b981;
            animation: fall var(--d) linear infinite var(--delay);
            white-space: nowrap;
        }

        @keyframes fall {
            from {
                transform: translateY(-100%);
            }

            to {
                transform: translateY(100vh);
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
            width: 280px;
            height: 220px;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 0 40px rgba(16, 185, 129, 0.25));
            animation: fadeUp 0.9s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Vault door spin attempt */
        .dial {
            animation: dialSpin 3s ease-in-out infinite;
            transform-origin: 140px 100px;
        }

        @keyframes dialSpin {
            0% {
                transform: rotate(0deg);
            }

            30% {
                transform: rotate(120deg);
            }

            50% {
                transform: rotate(80deg);
            }

            70% {
                transform: rotate(200deg);
            }

            100% {
                transform: rotate(200deg);
            }
        }

        /* Lock bolt slide */
        .bolt {
            animation: boltShake 3s ease-in-out infinite;
        }

        @keyframes boltShake {

            0%,
            100% {
                transform: translateX(0);
            }

            20% {
                transform: translateX(6px);
            }

            40% {
                transform: translateX(-3px);
            }

            60% {
                transform: translateX(6px);
            }

            80% {
                transform: translateX(0);
            }
        }

        /* Red denied flash */
        .denied-ring {
            animation: deniedFlash 3s ease-in-out infinite;
        }

        @keyframes deniedFlash {

            0%,
            60%,
            100% {
                opacity: 0;
            }

            70%,
            90% {
                opacity: 1;
            }
        }

        /* LED blink */
        .led {
            animation: ledBlink 3s ease-in-out infinite;
        }

        @keyframes ledBlink {

            0%,
            55%,
            100% {
                fill: #10b981;
            }

            65%,
            85% {
                fill: #ef4444;
            }
        }

        /* Keypad press */
        .key {
            transition: opacity 0.1s;
        }

        .key:nth-child(1) {
            animation: keyPress 3s 0.2s infinite;
        }

        .key:nth-child(2) {
            animation: keyPress 3s 0.5s infinite;
        }

        .key:nth-child(3) {
            animation: keyPress 3s 0.8s infinite;
        }

        .key:nth-child(4) {
            animation: keyPress 3s 1.1s infinite;
        }

        @keyframes keyPress {

            0%,
            10%,
            100% {
                opacity: 0.4;
            }

            5% {
                opacity: 1;
            }
        }

        .code {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(5rem, 18vw, 9rem);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -4px;
            background: linear-gradient(135deg, #ffffff 20%, #10b981 60%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            animation: fadeUp 0.9s 0.1s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* ACCESS DENIED badge */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #f87171;
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.7rem;
            letter-spacing: 0.2em;
            padding: 0.3rem 0.9rem;
            border-radius: 4px;
            margin-bottom: 1.25rem;
            animation: fadeUp 0.9s 0.2s cubic-bezier(0.16, 1, 0.3, 1) both, badgePulse 2s ease-in-out infinite 1s;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #ef4444;
            animation: badgeDot 2s ease-in-out infinite;
        }

        @keyframes badgeDot {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.2;
            }
        }

        @keyframes badgePulse {

            0%,
            100% {
                box-shadow: 0 0 0 rgba(239, 68, 68, 0);
            }

            50% {
                box-shadow: 0 0 12px rgba(239, 68, 68, 0.3);
            }
        }

        h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.35rem;
            font-weight: 600;
            color: #e2e8f0;
            margin-bottom: 0.6rem;
            animation: fadeUp 0.9s 0.25s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        p {
            font-size: 0.9rem;
            color: #475569;
            max-width: 320px;
            line-height: 1.8;
            margin-bottom: 2rem;
            font-weight: 300;
            animation: fadeUp 0.9s 0.3s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .buttons {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            justify-content: center;
            animation: fadeUp 0.9s 0.35s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.7rem 1.75rem;
            border-radius: 100px;
            font-size: 0.88rem;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #059669, #0891b2);
            color: #fff;
            box-shadow: 0 0 25px rgba(16, 185, 129, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 0 45px rgba(16, 185, 129, 0.5);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: #94a3b8;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-2px);
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

    <div class="scan-line"></div>
    <div class="matrix" id="matrix"></div>

    <div class="container">

        <svg class="illustration" viewBox="0 0 280 220" fill="none" xmlns="http://www.w3.org/2000/svg">

            <!-- Vault door outer frame -->
            <rect x="30" y="20" width="160" height="175" rx="12" fill="#0f1623" stroke="rgba(16,185,129,0.2)"
                stroke-width="2" />
            <rect x="38" y="28" width="144" height="159" rx="9" fill="#0a1020" stroke="rgba(16,185,129,0.15)"
                stroke-width="1.5" />

            <!-- Vault door inner ring -->
            <circle cx="110" cy="108" r="58" fill="#0d1528" stroke="rgba(16,185,129,0.2)" stroke-width="2" />
            <circle cx="110" cy="108" r="48" fill="#0a1020" stroke="rgba(16,185,129,0.12)" stroke-width="1" />

            <!-- Bolt bars -->
            <g class="bolt">
                <rect x="188" y="55" width="22" height="12" rx="6" fill="#1e3a5f" stroke="rgba(16,185,129,0.3)"
                    stroke-width="1" />
                <rect x="188" y="100" width="22" height="12" rx="6" fill="#1e3a5f" stroke="rgba(16,185,129,0.3)"
                    stroke-width="1" />
                <rect x="188" y="145" width="22" height="12" rx="6" fill="#1e3a5f" stroke="rgba(16,185,129,0.3)"
                    stroke-width="1" />
            </g>

            <!-- Dial -->
            <g class="dial">
                <circle cx="110" cy="108" r="32" fill="#111827" stroke="rgba(16,185,129,0.35)" stroke-width="2" />
                <!-- Dial notches -->
                <line x1="110" y1="78" x2="110" y2="84" stroke="rgba(16,185,129,0.6)" stroke-width="2"
                    stroke-linecap="round" />
                <line x1="110" y1="132" x2="110" y2="138" stroke="rgba(16,185,129,0.6)" stroke-width="2"
                    stroke-linecap="round" />
                <line x1="80" y1="108" x2="86" y2="108" stroke="rgba(16,185,129,0.6)" stroke-width="2"
                    stroke-linecap="round" />
                <line x1="134" y1="108" x2="140" y2="108" stroke="rgba(16,185,129,0.6)" stroke-width="2"
                    stroke-linecap="round" />
                <line x1="89" y1="87" x2="93" y2="91" stroke="rgba(16,185,129,0.4)" stroke-width="1.5"
                    stroke-linecap="round" />
                <line x1="127" y1="87" x2="123" y2="91" stroke="rgba(16,185,129,0.4)" stroke-width="1.5"
                    stroke-linecap="round" />
                <line x1="89" y1="129" x2="93" y2="125" stroke="rgba(16,185,129,0.4)" stroke-width="1.5"
                    stroke-linecap="round" />
                <line x1="127" y1="129" x2="123" y2="125" stroke="rgba(16,185,129,0.4)" stroke-width="1.5"
                    stroke-linecap="round" />
                <!-- Dial pointer -->
                <line x1="110" y1="108" x2="110" y2="82" stroke="#10b981" stroke-width="2" stroke-linecap="round" />
                <circle cx="110" cy="108" r="5" fill="#10b981" />
                <circle cx="110" cy="108" r="2" fill="#030712" />
            </g>

            <!-- Denied ring flash -->
            <circle class="denied-ring" cx="110" cy="108" r="58" stroke="#ef4444" stroke-width="3" fill="none"
                opacity="0" />

            <!-- Keypad (right side) -->
            <rect x="205" y="55" width="55" height="110" rx="8" fill="#0f1623" stroke="rgba(16,185,129,0.15)"
                stroke-width="1" />
            <g class="key">
                <rect x="212" y="65" width="18" height="14" rx="3" fill="rgba(16,185,129,0.15)"
                    stroke="rgba(16,185,129,0.3)" stroke-width="1" />
            </g>
            <g class="key">
                <rect x="234" y="65" width="18" height="14" rx="3" fill="rgba(16,185,129,0.15)"
                    stroke="rgba(16,185,129,0.3)" stroke-width="1" />
            </g>
            <g class="key">
                <rect x="212" y="84" width="18" height="14" rx="3" fill="rgba(16,185,129,0.15)"
                    stroke="rgba(16,185,129,0.3)" stroke-width="1" />
            </g>
            <g class="key">
                <rect x="234" y="84" width="18" height="14" rx="3" fill="rgba(16,185,129,0.15)"
                    stroke="rgba(16,185,129,0.3)" stroke-width="1" />
            </g>
            <rect x="212" y="103" width="18" height="14" rx="3" fill="rgba(16,185,129,0.1)"
                stroke="rgba(16,185,129,0.2)" stroke-width="1" />
            <rect x="234" y="103" width="18" height="14" rx="3" fill="rgba(16,185,129,0.1)"
                stroke="rgba(16,185,129,0.2)" stroke-width="1" />
            <rect x="212" y="122" width="40" height="14" rx="3" fill="rgba(239,68,68,0.15)" stroke="rgba(239,68,68,0.3)"
                stroke-width="1" />
            <!-- LED status -->
            <circle class="led" cx="232" cy="148" r="5" fill="#10b981" />
            <text x="232" y="162" text-anchor="middle" font-family="Share Tech Mono, monospace" font-size="7"
                fill="rgba(16,185,129,0.5)">STATUS</text>

            <!-- Lock icon center -->
            <rect x="100" y="100" width="20" height="16" rx="3" fill="rgba(239,68,68,0.2)" stroke="rgba(239,68,68,0.5)"
                stroke-width="1.5" />
            <path d="M104 100 v-5 a6 6 0 0 1 12 0 v5" stroke="rgba(239,68,68,0.5)" stroke-width="1.5" fill="none"
                stroke-linecap="round" />
            <circle cx="110" cy="108" r="2.5" fill="rgba(239,68,68,0.7)" />

        </svg>

        <div class="code">401</div>
        <div class="badge"><span class="badge-dot"></span> ACCESS DENIED</div>
        <h2>You're Not Authorized</h2>
        <p>You don't have clearance to access this area. Please log in with the right credentials to continue.</p>

        <div class="buttons">
            <a href="{{ route('login') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3" />
                </svg>
                Sign In
            </a>
            <a href="{{ url('/') }}" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 12l9-9 9 9M5 10v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9" />
                </svg>
                Go Home
            </a>
        </div>
    </div>

    <script>
        // Matrix rain
        const matrix = document.getElementById('matrix');
        const chars = '01アイウエオカキクケコ';
        for (let i = 0; i < 20; i++) {
            const col = document.createElement('div');
            col.className = 'col';
            col.style.cssText = `left:${i * 5 + Math.random() * 3}%; --d:${4 + Math.random() * 6}s; --delay:-${Math.random() * 8}s;`;
            col.textContent = Array.from({ length: 20 }, () => chars[Math.floor(Math.random() * chars.length)]).join('\n');
            matrix.appendChild(col);
        }
    </script>

</body>

</html>