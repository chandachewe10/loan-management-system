<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Syne:wght@700;800&family=DM+Sans:wght@300;400&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #060608;
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Grid background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 50, 50, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 50, 50, 0.04) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        /* Atmospheric glow */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 70% 50% at 50% 50%, rgba(200, 30, 30, 0.12) 0%, transparent 70%),
                radial-gradient(ellipse 40% 40% at 20% 80%, rgba(255, 100, 0, 0.08) 0%, transparent 60%);
            pointer-events: none;
        }

        .container {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 2rem;
            animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
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

        /* SVG illustration */
        .illustration {
            width: 300px;
            height: 200px;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 0 30px rgba(255, 50, 50, 0.3));
            animation: fadeUp 0.8s 0.1s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Gear spin */
        .gear-big {
            animation: spin 8s linear infinite;
            transform-origin: 105px 95px;
        }

        .gear-small {
            animation: spin 4s linear infinite reverse;
            transform-origin: 175px 115px;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Warning flash */
        .warning-flash {
            animation: warnFlash 2s ease-in-out infinite;
        }

        @keyframes warnFlash {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        /* Smoke puff */
        .smoke {
            animation: smokeDrift 3s ease-in-out infinite;
            transform-origin: center bottom;
        }

        .smoke-2 {
            animation-delay: -1s;
        }

        .smoke-3 {
            animation-delay: -2s;
        }

        @keyframes smokeDrift {
            0% {
                transform: translateY(0) scale(1);
                opacity: 0.5;
            }

            100% {
                transform: translateY(-30px) scale(1.8);
                opacity: 0;
            }
        }

        /* Spark */
        .spark {
            animation: sparkle 1.5s ease-in-out infinite;
        }

        .spark-2 {
            animation-delay: -0.5s;
        }

        .spark-3 {
            animation-delay: -1s;
        }

        @keyframes sparkle {

            0%,
            100% {
                opacity: 0;
                transform: scale(0);
            }

            50% {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Glitch on 500 */
        .code {
            font-family: 'Syne', sans-serif;
            font-size: clamp(5rem, 18vw, 9rem);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -4px;
            color: #fff;
            position: relative;
            margin-bottom: 0.5rem;
            animation: fadeUp 0.8s 0.15s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .code::before,
        .code::after {
            content: '500';
            position: absolute;
            inset: 0;
            background: transparent;
        }

        .code::before {
            color: #ff3030;
            clip-path: polygon(0 20%, 100% 20%, 100% 40%, 0 40%);
            animation: glitch1 4s infinite;
            left: -3px;
        }

        .code::after {
            color: #00cfff;
            clip-path: polygon(0 60%, 100% 60%, 100% 75%, 0 75%);
            animation: glitch2 4s infinite;
            left: 3px;
        }

        @keyframes glitch1 {

            0%,
            90%,
            100% {
                transform: translateX(0);
                opacity: 0;
            }

            92% {
                transform: translateX(-4px);
                opacity: 0.8;
            }

            94% {
                transform: translateX(4px);
                opacity: 0.8;
            }

            96% {
                transform: translateX(0);
                opacity: 0;
            }
        }

        @keyframes glitch2 {

            0%,
            90%,
            100% {
                transform: translateX(0);
                opacity: 0;
            }

            93% {
                transform: translateX(4px);
                opacity: 0.8;
            }

            95% {
                transform: translateX(-4px);
                opacity: 0.8;
            }

            97% {
                transform: translateX(0);
                opacity: 0;
            }
        }

        /* Terminal error log */
        .terminal {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 50, 50, 0.2);
            border-radius: 8px;
            padding: 1rem 1.25rem;
            margin-bottom: 2rem;
            text-align: left;
            width: 100%;
            max-width: 380px;
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.72rem;
            color: #94a3b8;
            line-height: 1.9;
            animation: fadeUp 0.8s 0.35s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .terminal .err {
            color: #f87171;
        }

        .terminal .warn {
            color: #fb923c;
        }

        .terminal .ok {
            color: #4ade80;
        }

        .terminal .cursor {
            display: inline-block;
            width: 7px;
            height: 13px;
            background: #94a3b8;
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

        h2 {
            font-family: 'Syne', sans-serif;
            font-size: 1.3rem;
            font-weight: 700;
            color: #e2e8f0;
            margin-bottom: 0.6rem;
            animation: fadeUp 0.8s 0.2s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        p {
            font-size: 0.9rem;
            color: #64748b;
            max-width: 340px;
            line-height: 1.7;
            margin-bottom: 1.5rem;
            font-weight: 300;
            animation: fadeUp 0.8s 0.25s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, #dc2626, #ea580c);
            color: #fff;
            text-decoration: none;
            border-radius: 100px;
            font-size: 0.9rem;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 0 30px rgba(220, 38, 38, 0.35);
            animation: fadeUp 0.8s 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .btn:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 0 50px rgba(220, 38, 38, 0.55);
        }

        .btn svg {
            width: 16px;
            height: 16px;
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- SVG: Broken server / machine -->
        <svg class="illustration" viewBox="0 0 300 200" fill="none" xmlns="http://www.w3.org/2000/svg">

            <!-- Server box -->
            <rect x="60" y="60" width="140" height="100" rx="8" fill="#1e1e2e" stroke="rgba(255,50,50,0.4)"
                stroke-width="1.5" />
            <!-- Server slots -->
            <rect x="74" y="75" width="112" height="12" rx="3" fill="#2a2a3e" stroke="rgba(255,255,255,0.06)"
                stroke-width="1" />
            <rect x="74" y="95" width="112" height="12" rx="3" fill="#2a2a3e" stroke="rgba(255,255,255,0.06)"
                stroke-width="1" />
            <rect x="74" y="115" width="112" height="12" rx="3" fill="#2a2a3e" stroke="rgba(255,255,255,0.06)"
                stroke-width="1" />
            <!-- Status LEDs -->
            <circle cx="172" cy="81" r="3" fill="#f87171" class="warning-flash" />
            <circle cx="172" cy="101" r="3" fill="#f87171" class="warning-flash" style="animation-delay:-0.7s" />
            <circle cx="172" cy="121" r="3" fill="#fb923c" class="warning-flash" style="animation-delay:-1.3s" />
            <circle cx="180" cy="81" r="3" fill="#f87171" class="warning-flash" style="animation-delay:-0.3s" />
            <circle cx="180" cy="101" r="3" fill="#4ade80" />
            <circle cx="180" cy="121" r="3" fill="#f87171" class="warning-flash" style="animation-delay:-0.9s" />

            <!-- Crack on server -->
            <path d="M130 60 L122 85 L135 85 L125 120" stroke="#ff3030" stroke-width="1.5" stroke-linecap="round"
                opacity="0.7" />

            <!-- Big gear -->
            <g class="gear-big">
                <circle cx="105" cy="95" r="28" fill="none" stroke="rgba(251,146,60,0.25)" stroke-width="8" />
                <circle cx="105" cy="95" r="18" fill="#1a1a2e" stroke="rgba(251,146,60,0.4)" stroke-width="2" />
                <circle cx="105" cy="95" r="5" fill="rgba(251,146,60,0.6)" />
                <!-- Gear teeth -->
                <rect x="100" y="62" width="10" height="8" rx="2" fill="rgba(251,146,60,0.5)" />
                <rect x="100" y="120" width="10" height="8" rx="2" fill="rgba(251,146,60,0.5)" />
                <rect x="72" y="90" width="8" height="10" rx="2" fill="rgba(251,146,60,0.5)" />
                <rect x="125" y="90" width="8" height="10" rx="2" fill="rgba(251,146,60,0.5)" />
                <rect x="80" y="70" width="8" height="10" rx="2" fill="rgba(251,146,60,0.5)"
                    transform="rotate(-45 84 75)" />
                <rect x="117" y="70" width="8" height="10" rx="2" fill="rgba(251,146,60,0.5)"
                    transform="rotate(45 121 75)" />
                <rect x="80" y="105" width="8" height="10" rx="2" fill="rgba(251,146,60,0.5)"
                    transform="rotate(45 84 110)" />
                <rect x="117" y="105" width="8" height="10" rx="2" fill="rgba(251,146,60,0.5)"
                    transform="rotate(-45 121 110)" />
            </g>

            <!-- Small gear -->
            <g class="gear-small">
                <circle cx="175" cy="115" r="18" fill="none" stroke="rgba(251,146,60,0.2)" stroke-width="6" />
                <circle cx="175" cy="115" r="11" fill="#1a1a2e" stroke="rgba(251,146,60,0.35)" stroke-width="1.5" />
                <circle cx="175" cy="115" r="3.5" fill="rgba(251,146,60,0.5)" />
                <rect x="171" y="93" width="8" height="6" rx="1.5" fill="rgba(251,146,60,0.45)" />
                <rect x="171" y="130" width="8" height="6" rx="1.5" fill="rgba(251,146,60,0.45)" />
                <rect x="158" y="111" width="6" height="8" rx="1.5" fill="rgba(251,146,60,0.45)" />
                <rect x="186" y="111" width="6" height="8" rx="1.5" fill="rgba(251,146,60,0.45)" />
            </g>

            <!-- Smoke puffs -->
            <circle class="smoke" cx="155" cy="60" r="7" fill="rgba(148,163,184,0.3)" />
            <circle class="smoke smoke-2" cx="165" cy="55" r="5" fill="rgba(148,163,184,0.2)" />
            <circle class="smoke smoke-3" cx="148" cy="52" r="9" fill="rgba(148,163,184,0.15)" />

            <!-- Sparks -->
            <path class="spark" d="M200 80 L205 70 L208 80 L215 75" stroke="#fbbf24" stroke-width="1.5" fill="none"
                stroke-linecap="round" />
            <path class="spark spark-2" d="M220 95 L225 85 L227 96 L233 90" stroke="#f87171" stroke-width="1.5"
                fill="none" stroke-linecap="round" />
            <path class="spark spark-3" d="M55 75 L50 65 L48 76 L42 70" stroke="#fbbf24" stroke-width="1.5" fill="none"
                stroke-linecap="round" />

            <!-- Warning triangle -->
            <g class="warning-flash" transform="translate(195, 55)">
                <path d="M15 2 L28 24 L2 24 Z" fill="#1e1e2e" stroke="#fb923c" stroke-width="1.5" />
                <text x="15" y="20" text-anchor="middle" font-size="12" font-weight="bold" fill="#fb923c">!</text>
            </g>

        </svg>

        <div class="code">Whooops</div>
        <h2>Something Broke on Our End</h2>
        <p>Our servers hit an unexpected snag. We're already on it — try again in a moment.</p>

        <!-- Terminal log -->
        <div class="terminal">
            <span class="err">[ERROR]</span> Unhandled exception in request pipeline<br>
            <span class="warn">[WARN]</span>&nbsp; Memory threshold exceeded: 94%<br>
            <span class="err">[ERROR]</span> Service unavailable — retrying...<br>
            <span class="ok">[INFO]</span>&nbsp;&nbsp; Engineers notified automatically<br>
            <span class="ok">$</span> awaiting recovery <span class="cursor"></span>
        </div>

        <a href="{{ url('/') }}" class="btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="1 4 1 10 7 10" />
                <path d="M3.51 15a9 9 0 1 0 .49-4.5" />
            </svg>
            Try Again
        </a>

    </div>
</body>

</html>