<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 - Too Many Requests</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;600;800&family=Share+Tech+Mono&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Space Grotesk', sans-serif;
            background: #020817;
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
                radial-gradient(ellipse 70% 50% at 50% 50%, rgba(249, 115, 22, 0.09) 0%, transparent 65%),
                radial-gradient(ellipse 40% 40% at 20% 80%, rgba(239, 68, 68, 0.06) 0%, transparent 55%),
                radial-gradient(ellipse 30% 30% at 80% 20%, rgba(249, 115, 22, 0.05) 0%, transparent 50%);
            pointer-events: none;
        }

        /* Incoming request packets */
        .packets {
            position: fixed;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .packet {
            position: absolute;
            border-radius: 3px;
            animation: packetFly var(--d) linear infinite var(--delay);
            opacity: 0;
        }

        @keyframes packetFly {
            0% {
                transform: translateX(-20px) translateY(var(--sy));
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 0.8;
            }

            100% {
                transform: translateX(110vw) translateY(var(--ey));
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
            width: 300px;
            height: 210px;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 0 35px rgba(249, 115, 22, 0.3));
            animation: fadeUp 0.9s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Server shake when overwhelmed */
        .server-body {
            animation: serverShake 0.5s ease-in-out infinite;
        }

        @keyframes serverShake {

            0%,
            100% {
                transform: translateX(0);
            }

            20% {
                transform: translateX(-2px) rotate(-0.5deg);
            }

            40% {
                transform: translateX(2px) rotate(0.5deg);
            }

            60% {
                transform: translateX(-1px);
            }

            80% {
                transform: translateX(1px);
            }
        }

        /* Overload meter fill */
        .meter-fill {
            animation: meterFill 2s ease-in-out infinite;
            transform-origin: left center;
        }

        @keyframes meterFill {

            0%,
            100% {
                transform: scaleX(0.92);
            }

            50% {
                transform: scaleX(1);
            }
        }

        /* Overflow sparks */
        .spark {
            animation: sparkBurst var(--sd, 1s) ease-out infinite var(--sdelay, 0s);
            transform-origin: center;
        }

        @keyframes sparkBurst {
            0% {
                transform: translate(0, 0) scale(1);
                opacity: 1;
            }

            100% {
                transform: translate(var(--sx), var(--sy)) scale(0);
                opacity: 0;
            }
        }

        /* Warning indicators pulse */
        .warn-dot {
            animation: warnPulse 0.8s ease-in-out infinite var(--wd, 0s);
        }

        @keyframes warnPulse {

            0%,
            100% {
                opacity: 0.3;
                r: 4;
            }

            50% {
                opacity: 1;
                r: 5.5;
            }
        }

        /* Request arrows fly in */
        .arrow {
            animation: arrowFly var(--ad, 1.5s) ease-in infinite var(--adelay, 0s);
            transform-origin: right center;
        }

        @keyframes arrowFly {
            0% {
                transform: translateX(40px);
                opacity: 0;
            }

            20% {
                opacity: 1;
            }

            80% {
                opacity: 1;
            }

            100% {
                transform: translateX(0px);
                opacity: 0;
            }
        }

        /* Thermometer rise */
        .thermo-fill {
            animation: thermoRise 2s ease-in-out infinite;
            transform-origin: bottom;
        }

        @keyframes thermoRise {

            0%,
            100% {
                transform: scaleY(0.85);
            }

            50% {
                transform: scaleY(1);
            }
        }

        .code {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(5rem, 18vw, 9rem);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -4px;
            background: linear-gradient(135deg, #fed7aa 10%, #f97316 50%, #dc2626 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            animation: fadeUp 0.9s 0.1s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Rate limit badge */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(249, 115, 22, 0.1);
            border: 1px solid rgba(249, 115, 22, 0.3);
            color: #fb923c;
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.68rem;
            letter-spacing: 0.18em;
            padding: 0.3rem 0.9rem;
            border-radius: 4px;
            margin-bottom: 1.25rem;
            animation: fadeUp 0.9s 0.2s cubic-bezier(0.16, 1, 0.3, 1) both, badgePulse 1.5s ease-in-out infinite 1s;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #f97316;
            animation: dotBlink 0.8s ease-in-out infinite;
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

        @keyframes badgePulse {

            0%,
            100% {
                box-shadow: none;
            }

            50% {
                box-shadow: 0 0 14px rgba(249, 115, 22, 0.25);
            }
        }

        /* Countdown timer */
        .cooldown {
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.8rem;
            color: rgba(249, 115, 22, 0.6);
            margin-bottom: 1.5rem;
            animation: fadeUp 0.9s 0.3s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .cooldown span {
            color: #f97316;
            font-size: 1.1rem;
        }

        h2 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.35rem;
            font-weight: 600;
            color: #e2e8f0;
            margin-bottom: 0.6rem;
            animation: fadeUp 0.9s 0.2s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        p {
            font-size: 0.9rem;
            color: #475569;
            max-width: 320px;
            line-height: 1.8;
            margin-bottom: 1rem;
            font-weight: 300;
            animation: fadeUp 0.9s 0.25s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, #c2410c, #f97316);
            color: #fff;
            font-weight: 600;
            text-decoration: none;
            border-radius: 100px;
            font-size: 0.88rem;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 0 25px rgba(249, 115, 22, 0.3);
            animation: fadeUp 0.9s 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
            cursor: pointer;
            border: none;
        }

        .btn:hover:not(:disabled) {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 0 45px rgba(249, 115, 22, 0.5);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
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

    <div class="packets" id="packets"></div>

    <div class="container">

        <svg class="illustration" viewBox="0 0 300 210" fill="none" xmlns="http://www.w3.org/2000/svg">

            <!-- Incoming request arrows -->
            <g class="arrow" style="--ad:1.8s; --adelay:0s;">
                <rect x="20" y="58" width="40" height="10" rx="3" fill="rgba(249,115,22,0.5)" />
                <path d="M58 55 L68 63 L58 71 Z" fill="rgba(249,115,22,0.5)" />
            </g>
            <g class="arrow" style="--ad:1.8s; --adelay:-0.6s;">
                <rect x="20" y="95" width="35" height="8" rx="3" fill="rgba(249,115,22,0.4)" />
                <path d="M53 92 L63 99 L53 106 Z" fill="rgba(249,115,22,0.4)" />
            </g>
            <g class="arrow" style="--ad:1.8s; --adelay:-1.2s;">
                <rect x="20" y="128" width="45" height="10" rx="3" fill="rgba(249,115,22,0.35)" />
                <path d="M63 125 L73 133 L63 141 Z" fill="rgba(249,115,22,0.35)" />
            </g>
            <g class="arrow" style="--ad:1.8s; --adelay:-0.3s;">
                <rect x="15" y="75" width="30" height="7" rx="3" fill="rgba(239,68,68,0.3)" />
                <path d="M43 72 L52 78 L43 85 Z" fill="rgba(239,68,68,0.3)" />
            </g>
            <g class="arrow" style="--ad:1.8s; --adelay:-0.9s;">
                <rect x="18" y="112" width="38" height="8" rx="3" fill="rgba(239,68,68,0.25)" />
                <path d="M54 109 L64 116 L54 123 Z" fill="rgba(239,68,68,0.25)" />
            </g>

            <!-- Server body -->
            <g class="server-body">
                <rect x="80" y="30" width="130" height="150" rx="10" fill="#0f172a" stroke="rgba(249,115,22,0.3)"
                    stroke-width="1.5" />
                <rect x="88" y="38" width="114" height="134" rx="7" fill="#0a0f1e" />

                <!-- Server rack slots -->
                <rect x="94" y="48" width="102" height="18" rx="4" fill="#111827" stroke="rgba(249,115,22,0.15)"
                    stroke-width="1" />
                <rect x="94" y="72" width="102" height="18" rx="4" fill="#111827" stroke="rgba(249,115,22,0.15)"
                    stroke-width="1" />
                <rect x="94" y="96" width="102" height="18" rx="4" fill="#111827" stroke="rgba(249,115,22,0.15)"
                    stroke-width="1" />
                <rect x="94" y="120" width="102" height="18" rx="4" fill="#111827" stroke="rgba(249,115,22,0.15)"
                    stroke-width="1" />

                <!-- Overload meter bars -->
                <rect x="100" y="52" width="72" height="10" rx="3" fill="#1e293b" />
                <rect class="meter-fill" x="100" y="52" width="72" height="10" rx="3" fill="url(#meterGrad1)" />
                <rect x="100" y="76" width="72" height="10" rx="3" fill="#1e293b" />
                <rect class="meter-fill" x="100" y="76" width="68" height="10" rx="3" fill="url(#meterGrad2)"
                    style="animation-delay:-0.5s" />
                <rect x="100" y="100" width="72" height="10" rx="3" fill="#1e293b" />
                <rect class="meter-fill" x="100" y="100" width="70" height="10" rx="3" fill="url(#meterGrad1)"
                    style="animation-delay:-1s" />
                <rect x="100" y="124" width="72" height="10" rx="3" fill="#1e293b" />
                <rect class="meter-fill" x="100" y="124" width="72" height="10" rx="3" fill="url(#meterGrad2)"
                    style="animation-delay:-0.3s" />

                <!-- Warning dots -->
                <circle class="warn-dot" cx="182" cy="57" r="4" fill="#ef4444" style="--wd:0s" />
                <circle class="warn-dot" cx="182" cy="81" r="4" fill="#f97316" style="--wd:-0.3s" />
                <circle class="warn-dot" cx="182" cy="105" r="4" fill="#ef4444" style="--wd:-0.6s" />
                <circle class="warn-dot" cx="182" cy="129" r="4" fill="#ef4444" style="--wd:-0.9s" />

                <!-- Overload text on server -->
                <text x="145" y="155" text-anchor="middle" font-family="Share Tech Mono, monospace" font-size="7"
                    fill="rgba(249,115,22,0.4)" letter-spacing="2">OVERLOADED</text>
            </g>

            <!-- Thermometer (right side) -->
            <rect x="228" y="40" width="16" height="100" rx="8" fill="#0f172a" stroke="rgba(249,115,22,0.2)"
                stroke-width="1.5" />
            <rect x="232" y="44" width="8" height="92" rx="4" fill="#1e293b" />
            <g class="thermo-fill">
                <rect x="232" y="80" width="8" height="56" rx="4" fill="url(#thermoGrad)" />
            </g>
            <circle cx="236" cy="152" r="10" fill="#0f172a" stroke="rgba(249,115,22,0.3)" stroke-width="1.5" />
            <circle cx="236" cy="152" r="7" fill="#ef4444" class="warn-dot" style="--wd:0s" />
            <!-- Temp marks -->
            <line x1="244" y1="50" x2="248" y2="50" stroke="rgba(249,115,22,0.4)" stroke-width="1" />
            <line x1="244" y1="70" x2="248" y2="70" stroke="rgba(249,115,22,0.4)" stroke-width="1" />
            <line x1="244" y1="90" x2="248" y2="90" stroke="rgba(249,115,22,0.4)" stroke-width="1" />
            <line x1="244" y1="110" x2="248" y2="110" stroke="rgba(249,115,22,0.4)" stroke-width="1" />
            <line x1="244" y1="130" x2="248" y2="130" stroke="rgba(249,115,22,0.4)" stroke-width="1" />
            <text x="250" y="53" font-family="Share Tech Mono, monospace" font-size="6"
                fill="rgba(249,115,22,0.4)">100</text>
            <text x="250" y="133" font-family="Share Tech Mono, monospace" font-size="6"
                fill="rgba(249,115,22,0.4)">0</text>

            <!-- Overflow sparks from server top -->
            <line class="spark" x1="130" y1="30" x2="120" y2="15" stroke="#fbbf24" stroke-width="1.5"
                stroke-linecap="round" style="--sd:1.2s; --sdelay:0s;   --sx:-8px; --sy:-12px" />
            <line class="spark" x1="145" y1="30" x2="145" y2="12" stroke="#f97316" stroke-width="1.5"
                stroke-linecap="round" style="--sd:1.2s; --sdelay:-0.4s; --sx:0px;  --sy:-15px" />
            <line class="spark" x1="160" y1="30" x2="172" y2="14" stroke="#fbbf24" stroke-width="1.5"
                stroke-linecap="round" style="--sd:1.2s; --sdelay:-0.8s; --sx:10px; --sy:-13px" />
            <line class="spark" x1="138" y1="30" x2="125" y2="10" stroke="#ef4444" stroke-width="1"
                stroke-linecap="round" style="--sd:1.2s; --sdelay:-0.2s; --sx:-5px; --sy:-18px" />
            <line class="spark" x1="155" y1="30" x2="168" y2="10" stroke="#ef4444" stroke-width="1"
                stroke-linecap="round" style="--sd:1.2s; --sdelay:-1s;   --sx:8px;  --sy:-18px" />

            <!-- Queue overflow stack (left) -->
            <rect x="30" y="155" width="40" height="8" rx="2" fill="rgba(249,115,22,0.25)" stroke="rgba(249,115,22,0.4)"
                stroke-width="1" />
            <rect x="33" y="146" width="34" height="8" rx="2" fill="rgba(249,115,22,0.2)" stroke="rgba(249,115,22,0.3)"
                stroke-width="1" />
            <rect x="36" y="137" width="28" height="8" rx="2" fill="rgba(249,115,22,0.15)" stroke="rgba(249,115,22,0.2)"
                stroke-width="1" />
            <rect x="38" y="128" width="24" height="8" rx="2" fill="rgba(249,115,22,0.1)" stroke="rgba(249,115,22,0.15)"
                stroke-width="1" />
            <text x="50" y="162" text-anchor="middle" font-family="Share Tech Mono, monospace" font-size="6"
                fill="rgba(249,115,22,0.5)">QUEUE</text>
            <text x="50" y="170" text-anchor="middle" font-family="Share Tech Mono, monospace" font-size="6"
                fill="rgba(249,115,22,0.5)">FULL</text>

            <defs>
                <linearGradient id="meterGrad1" x1="0" y1="0" x2="1" y2="0">
                    <stop offset="0%" stop-color="#16a34a" />
                    <stop offset="70%" stop-color="#f97316" />
                    <stop offset="100%" stop-color="#ef4444" />
                </linearGradient>
                <linearGradient id="meterGrad2" x1="0" y1="0" x2="1" y2="0">
                    <stop offset="0%" stop-color="#16a34a" />
                    <stop offset="60%" stop-color="#eab308" />
                    <stop offset="100%" stop-color="#f97316" />
                </linearGradient>
                <linearGradient id="thermoGrad" x1="0" y1="1" x2="0" y2="0">
                    <stop offset="0%" stop-color="#16a34a" />
                    <stop offset="60%" stop-color="#f97316" />
                    <stop offset="100%" stop-color="#ef4444" />
                </linearGradient>
            </defs>

        </svg>

        <div class="code">429</div>
        <div class="badge"><span class="badge-dot"></span> RATE LIMIT EXCEEDED</div>
        <h2>Slow Down, You're Overloading Us</h2>
        <p>You've sent too many requests in a short time. Our server needs a moment to breathe — please wait before
            trying again.</p>

        <div class="cooldown">
            cooldown: <span id="timer">30</span>s remaining
        </div>

        <button class="btn" id="retryBtn" disabled onclick="window.location.reload()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="1 4 1 10 7 10" />
                <path d="M3.51 15a9 9 0 1 0 .49-4.5" />
            </svg>
            <span id="btnText">Please wait...</span>
        </button>
    </div>

    <script>
        // Floating request packets
        const packets = document.getElementById('packets');
        const colors = ['rgba(249,115,22,0.6)', 'rgba(239,68,68,0.5)', 'rgba(251,191,36,0.4)'];
        for (let i = 0; i < 25; i++) {
            const p = document.createElement('div');
            p.className = 'packet';
            const w = 20 + Math.random() * 40;
            const h = 6 + Math.random() * 6;
            p.style.cssText = `
                width:${w}px; height:${h}px;
                top:${Math.random() * 100}%;
                background:${colors[Math.floor(Math.random() * colors.length)]};
                --d:${1.5 + Math.random() * 3}s;
                --delay:-${Math.random() * 4}s;
                --sy:${(Math.random() - 0.5) * 40}px;
                --ey:${(Math.random() - 0.5) * 80}px;
            `;
            packets.appendChild(p);
        }

        // Countdown timer
        let seconds = 30;
        const timerEl = document.getElementById('timer');
        const btnEl = document.getElementById('retryBtn');
        const btnText = document.getElementById('btnText');

        const interval = setInterval(() => {
            seconds--;
            timerEl.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
                btnEl.disabled = false;
                btnText.textContent = 'Try Again';
                timerEl.parentElement.textContent = 'cooldown complete — ready to retry';
            }
        }, 1000);
    </script>

</body>

</html>