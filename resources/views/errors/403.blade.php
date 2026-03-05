<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;600&family=Share+Tech+Mono&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #0a0900;
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
                radial-gradient(ellipse 70% 50% at 50% 50%, rgba(234, 179, 8, 0.07) 0%, transparent 65%),
                radial-gradient(ellipse 40% 40% at 10% 90%, rgba(234, 179, 8, 0.05) 0%, transparent 55%);
            pointer-events: none;
        }

        /* Flashing police light - top */
        .police-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            z-index: 100;
            overflow: hidden;
        }

        .police-bar::before,
        .police-bar::after {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 50%;
            animation: policeFlash 1s ease-in-out infinite;
        }

        .police-bar::before {
            left: 0;
            background: #ef4444;
        }

        .police-bar::after {
            right: 0;
            background: #3b82f6;
            animation-delay: 0.5s;
        }

        @keyframes policeFlash {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.1;
            }
        }

        /* Crime tape strips */
        .tape-container {
            position: fixed;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            opacity: 0.06;
        }

        .tape {
            position: absolute;
            height: 38px;
            width: 200%;
            left: -50%;
            background: repeating-linear-gradient(90deg,
                    #eab308 0px, #eab308 80px,
                    #0a0900 80px, #0a0900 160px);
            transform-origin: center;
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
            width: 300px;
            height: 210px;
            margin-bottom: 1rem;
            filter: drop-shadow(0 0 30px rgba(234, 179, 8, 0.2));
            animation: fadeUp 0.9s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Tape sway */
        .tape-svg {
            animation: tapeSway 4s ease-in-out infinite;
            transform-origin: center top;
        }

        @keyframes tapeSway {

            0%,
            100% {
                transform: rotate(-1deg) skewX(-1deg);
            }

            50% {
                transform: rotate(1deg) skewX(1deg);
            }
        }

        /* Light sweep */
        .spotlight {
            animation: spotSweep 3s ease-in-out infinite;
            transform-origin: 150px 0;
        }

        @keyframes spotSweep {

            0%,
            100% {
                transform: rotate(-15deg);
                opacity: 0.6;
            }

            50% {
                transform: rotate(15deg);
                opacity: 0.9;
            }
        }

        /* Shield pulse */
        .shield-pulse {
            animation: shieldPulse 2s ease-in-out infinite;
        }

        @keyframes shieldPulse {

            0%,
            100% {
                filter: drop-shadow(0 0 4px rgba(234, 179, 8, 0.4));
            }

            50% {
                filter: drop-shadow(0 0 12px rgba(234, 179, 8, 0.9));
            }
        }

        /* No entry spin */
        .no-entry {
            animation: noSpin 8s linear infinite;
            transform-origin: 218px 68px;
        }

        @keyframes noSpin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Barrier blink */
        .barrier-light {
            animation: barrierBlink 1.2s ease-in-out infinite;
        }

        @keyframes barrierBlink {

            0%,
            100% {
                fill: #ef4444;
                opacity: 1;
            }

            50% {
                fill: #fca5a5;
                opacity: 0.4;
            }
        }

        .code {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(5rem, 18vw, 9rem);
            line-height: 1;
            letter-spacing: 4px;
            background: linear-gradient(135deg, #fde047 10%, #eab308 50%, #ca8a04 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.25rem;
            animation: fadeUp 0.9s 0.1s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Crime tape text badge */
        .tape-badge {
            background: #eab308;
            color: #0a0900;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 0.85rem;
            letter-spacing: 0.3em;
            padding: 0.3rem 1.5rem;
            margin-bottom: 1.25rem;
            transform: rotate(-2deg);
            display: inline-block;
            position: relative;
            animation: fadeUp 0.9s 0.15s cubic-bezier(0.16, 1, 0.3, 1) both, tapeSwayBadge 3s ease-in-out infinite 1s;
            box-shadow: 4px 4px 0 rgba(0, 0, 0, 0.5);
        }

        .tape-badge::before,
        .tape-badge::after {
            content: '//';
            margin: 0 0.5rem;
            opacity: 0.5;
        }

        @keyframes tapeSwayBadge {

            0%,
            100% {
                transform: rotate(-2deg);
            }

            50% {
                transform: rotate(-1deg) scale(1.01);
            }
        }

        h2 {
            font-family: 'DM Sans', sans-serif;
            font-size: 1.3rem;
            font-weight: 600;
            color: #e2e8f0;
            margin-bottom: 0.6rem;
            animation: fadeUp 0.9s 0.2s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        p {
            font-size: 0.9rem;
            color: #57534e;
            max-width: 320px;
            line-height: 1.8;
            margin-bottom: 0.75rem;
            font-weight: 300;
            animation: fadeUp 0.9s 0.25s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Exception message if present */
        .exception-msg {
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.72rem;
            color: rgba(234, 179, 8, 0.5);
            background: rgba(234, 179, 8, 0.05);
            border: 1px solid rgba(234, 179, 8, 0.15);
            border-radius: 4px;
            padding: 0.4rem 1rem;
            margin-bottom: 1.75rem;
            max-width: 340px;
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
            background: linear-gradient(135deg, #ca8a04, #eab308);
            color: #0a0900;
            box-shadow: 0 0 25px rgba(234, 179, 8, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 0 45px rgba(234, 179, 8, 0.5);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.04);
            color: #78716c;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.07);
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

    <!-- Police light bar -->
    <div class="police-bar"></div>

    <!-- Background tape -->
    <div class="tape-container" id="tapeContainer"></div>

    <div class="container">

        <svg class="illustration" viewBox="0 0 300 210" fill="none" xmlns="http://www.w3.org/2000/svg">

            <!-- Spotlight sweep -->
            <g class="spotlight">
                <path d="M150 0 L90 180 L210 180 Z" fill="url(#spotGrad)" opacity="0.15" />
            </g>

            <!-- Crime scene tape - back layer -->
            <g class="tape-svg">
                <rect x="0" y="88" width="300" height="28" rx="3" fill="#1a1500" stroke="rgba(234,179,8,0.2)"
                    stroke-width="1" />
                <!-- Yellow/black pattern -->
                <clipPath id="tapeClip1">
                    <rect x="0" y="88" width="300" height="28" rx="3" />
                </clipPath>
                <g clip-path="url(#tapeClip1)">
                    <rect x="0" y="88" width="50" height="28" fill="#eab308" opacity="0.9" />
                    <rect x="100" y="88" width="50" height="28" fill="#eab308" opacity="0.9" />
                    <rect x="200" y="88" width="50" height="28" fill="#eab308" opacity="0.9" />
                    <text x="10" y="108" font-family="Bebas Neue, sans-serif" font-size="13" fill="#0a0900"
                        letter-spacing="2">DO NOT CROSS</text>
                    <text x="110" y="108" font-family="Bebas Neue, sans-serif" font-size="13" fill="#0a0900"
                        letter-spacing="2">DO NOT CROSS</text>
                    <text x="210" y="108" font-family="Bebas Neue, sans-serif" font-size="13" fill="#0a0900"
                        letter-spacing="2">DO NOT CROSS</text>
                </g>
            </g>

            <!-- Barrier poles -->
            <rect x="18" y="50" width="8" height="150" rx="4" fill="#374151" />
            <rect x="274" y="50" width="8" height="150" rx="4" fill="#374151" />
            <!-- Pole tops with lights -->
            <circle class="barrier-light" cx="22" cy="52" r="7" fill="#ef4444" />
            <circle class="barrier-light" cx="278" cy="52" r="7" fill="#ef4444" style="animation-delay:-0.6s" />

            <!-- Second tape layer -->
            <g class="tape-svg" style="animation-delay:-2s">
                <rect x="0" y="138" width="300" height="24" rx="2" fill="#1a1500" stroke="rgba(234,179,8,0.15)"
                    stroke-width="1" />
                <clipPath id="tapeClip2">
                    <rect x="0" y="138" width="300" height="24" rx="2" />
                </clipPath>
                <g clip-path="url(#tapeClip2)">
                    <rect x="50" y="138" width="50" height="24" fill="#eab308" opacity="0.85" />
                    <rect x="150" y="138" width="50" height="24" fill="#eab308" opacity="0.85" />
                    <rect x="250" y="138" width="50" height="24" fill="#eab308" opacity="0.85" />
                    <text x="55" y="155" font-family="Bebas Neue, sans-serif" font-size="11" fill="#0a0900"
                        letter-spacing="2">FORBIDDEN</text>
                    <text x="155" y="155" font-family="Bebas Neue, sans-serif" font-size="11" fill="#0a0900"
                        letter-spacing="2">FORBIDDEN</text>
                    <text x="255" y="155" font-family="Bebas Neue, sans-serif" font-size="11" fill="#0a0900"
                        letter-spacing="2">FORBIDDEN</text>
                </g>
            </g>

            <!-- Shield -->
            <g class="shield-pulse" transform="translate(108, 18)">
                <path d="M42 0 L84 15 L84 45 Q84 72 42 85 Q0 72 0 45 L0 15 Z" fill="#1c1a00" stroke="#eab308"
                    stroke-width="2" />
                <path d="M42 8 L76 21 L76 46 Q76 68 42 79 Q8 68 8 46 L8 21 Z" fill="rgba(234,179,8,0.08)" />
                <!-- Shield X mark -->
                <line x1="28" y1="30" x2="56" y2="58" stroke="#ef4444" stroke-width="3" stroke-linecap="round" />
                <line x1="56" y1="30" x2="28" y2="58" stroke="#ef4444" stroke-width="3" stroke-linecap="round" />
            </g>

            <!-- No entry sign -->
            <g class="no-entry">
                <circle cx="218" cy="68" r="22" fill="#ef4444" opacity="0.9" />
                <circle cx="218" cy="68" r="18" fill="#dc2626" />
                <rect x="206" y="62" width="24" height="12" rx="4" fill="white" />
            </g>

            <!-- Floating warning triangles -->
            <g opacity="0.6">
                <path d="M48 170 L58 155 L68 170 Z" fill="none" stroke="#eab308" stroke-width="1.5" />
                <text x="58" y="167" text-anchor="middle" font-size="8" fill="#eab308" font-weight="bold">!</text>
            </g>
            <g opacity="0.4">
                <path d="M235 48 L243 35 L251 48 Z" fill="none" stroke="#eab308" stroke-width="1.5" />
                <text x="243" y="45" text-anchor="middle" font-size="7" fill="#eab308" font-weight="bold">!</text>
            </g>

            <defs>
                <radialGradient id="spotGrad" cx="50%" cy="0%">
                    <stop offset="0%" stop-color="#fde047" stop-opacity="1" />
                    <stop offset="100%" stop-color="#fde047" stop-opacity="0" />
                </radialGradient>
            </defs>

        </svg>

        <div class="code">403</div>
        <div class="tape-badge">DO NOT CROSS</div>
        <h2>Access Forbidden</h2>
        <p>You don't have permission to enter this area. If you think this is a mistake, contact your administrator.</p>

        @if(!empty($exception) && $exception->getMessage() && $exception->getMessage() !== 'Forbidden')
            <div class="exception-msg">⚠ {{ $exception->getMessage() }}</div>
        @endif

        <div class="buttons">
            <a href="{{ url('/') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 12l9-9 9 9M5 10v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9" />
                </svg>
                Go Home
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 5l-7 7 7 7" />
                </svg>
                Go Back
            </a>
        </div>
    </div>

    <script>
        // Background tape strips
        const container = document.getElementById('tapeContainer');
        for (let i = 0; i < 6; i++) {
            const tape = document.createElement('div');
            tape.className = 'tape';
            tape.style.cssText = `
                top: ${10 + i * 16}%;
                transform: rotate(${-3 + Math.random() * 6}deg);
                opacity: ${0.4 + Math.random() * 0.4};
            `;
            container.appendChild(tape);
        }
    </script>

</body>

</html>