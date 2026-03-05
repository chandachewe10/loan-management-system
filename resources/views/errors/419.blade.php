<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Page Expired</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #0c0a06;
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Aged paper texture overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 50% 40%, rgba(180, 130, 40, 0.1) 0%, transparent 65%),
                radial-gradient(ellipse 50% 50% at 80% 70%, rgba(120, 80, 20, 0.08) 0%, transparent 55%);
            pointer-events: none;
        }

        /* Dust particles */
        .dust {
            position: fixed;
            inset: 0;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(210, 170, 80, 0.4);
            animation: drift var(--d) ease-in-out infinite var(--delay);
        }

        @keyframes drift {
            0% {
                transform: translateY(0) translateX(0) rotate(0deg);
                opacity: 0;
            }

            20% {
                opacity: 1;
            }

            80% {
                opacity: 0.6;
            }

            100% {
                transform: translateY(-120px) translateX(var(--x)) rotate(360deg);
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

        /* Hourglass illustration */
        .illustration {
            width: 280px;
            height: 220px;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 0 40px rgba(200, 150, 40, 0.35));
            animation: fadeUp 0.9s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Hourglass slow flip */
        .hourglass-group {
            animation: flipHourglass 6s ease-in-out infinite;
            transform-origin: 140px 105px;
        }

        @keyframes flipHourglass {

            0%,
            40% {
                transform: rotate(0deg);
            }

            50%,
            90% {
                transform: rotate(180deg);
            }

            100% {
                transform: rotate(180deg);
            }
        }

        /* Sand falling */
        .sand-stream {
            animation: sandFall 1.5s ease-in infinite;
            transform-origin: top center;
        }

        @keyframes sandFall {
            0% {
                transform: scaleY(0);
                opacity: 0;
            }

            30% {
                opacity: 1;
            }

            80% {
                transform: scaleY(1);
                opacity: 0.8;
            }

            100% {
                transform: scaleY(0);
                opacity: 0;
            }
        }

        /* Sand pile grow */
        .sand-pile {
            animation: pileGrow 3s ease-in-out infinite;
            transform-origin: bottom center;
        }

        @keyframes pileGrow {

            0%,
            100% {
                transform: scaleX(0.7) scaleY(0.8);
            }

            50% {
                transform: scaleX(1) scaleY(1);
            }
        }

        /* Clock hands */
        .hour-hand {
            animation: hourSpin 12s linear infinite;
            transform-origin: 220px 80px;
        }

        .minute-hand {
            animation: hourSpin 2s linear infinite;
            transform-origin: 220px 80px;
        }

        @keyframes hourSpin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Crack reveal */
        .crack {
            animation: crackReveal 4s ease-in-out infinite;
        }

        @keyframes crackReveal {

            0%,
            60%,
            100% {
                opacity: 0.3;
            }

            75% {
                opacity: 1;
            }
        }

        /* Sepia fade rings */
        .ring {
            animation: ringPulse var(--rd, 3s) ease-in-out infinite var(--rdelay, 0s);
        }

        @keyframes ringPulse {

            0%,
            100% {
                opacity: 0.15;
                transform: scale(1);
            }

            50% {
                opacity: 0.4;
                transform: scale(1.04);
            }
        }

        .code {
            font-family: 'Playfair Display', serif;
            font-size: clamp(5rem, 18vw, 9rem);
            font-weight: 900;
            line-height: 1;
            letter-spacing: -3px;
            background: linear-gradient(135deg, #d4a843 20%, #8b6914 60%, #c8922a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            animation: fadeUp 0.9s 0.1s cubic-bezier(0.16, 1, 0.3, 1) both;
            position: relative;
        }

        h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: #d4a843;
            margin-bottom: 0.6rem;
            letter-spacing: 0.05em;
            animation: fadeUp 0.9s 0.2s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        p {
            font-size: 0.9rem;
            color: #78624a;
            max-width: 320px;
            line-height: 1.8;
            margin-bottom: 1.75rem;
            font-weight: 300;
            animation: fadeUp 0.9s 0.3s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Expired stamp */
        .stamp {
            border: 2px solid rgba(200, 50, 50, 0.6);
            color: rgba(200, 50, 50, 0.7);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.25em;
            padding: 0.25rem 0.75rem;
            border-radius: 3px;
            transform: rotate(-8deg);
            display: inline-block;
            margin-bottom: 1.5rem;
            animation: fadeUp 0.9s 0.25s cubic-bezier(0.16, 1, 0.3, 1) both, stampWobble 4s ease-in-out infinite 1s;
        }

        @keyframes stampWobble {

            0%,
            100% {
                transform: rotate(-8deg);
            }

            50% {
                transform: rotate(-6deg) scale(1.02);
            }
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, #b8860b, #d4a843);
            color: #0c0a06;
            font-weight: 600;
            text-decoration: none;
            border-radius: 100px;
            font-size: 0.88rem;
            letter-spacing: 0.03em;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 0 30px rgba(212, 168, 67, 0.3);
            animation: fadeUp 0.9s 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .btn:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 0 50px rgba(212, 168, 67, 0.5);
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

    <div class="dust" id="dust"></div>

    <div class="container">

        <!-- SVG: Hourglass + pocket watch -->
        <svg class="illustration" viewBox="0 0 280 220" fill="none" xmlns="http://www.w3.org/2000/svg">

            <!-- Ambient rings -->
            <circle class="ring" cx="100" cy="110" r="70" stroke="rgba(212,168,67,0.2)" stroke-width="1" fill="none"
                style="--rd:4s; --rdelay:0s;" />
            <circle class="ring" cx="100" cy="110" r="85" stroke="rgba(212,168,67,0.1)" stroke-width="1" fill="none"
                style="--rd:4s; --rdelay:-2s;" />

            <!-- Hourglass group -->
            <g class="hourglass-group">
                <!-- Frame top cap -->
                <rect x="65" y="28" width="70" height="9" rx="4" fill="#8b6914" />
                <rect x="67" y="30" width="66" height="5" rx="2" fill="#d4a843" />
                <!-- Frame bottom cap -->
                <rect x="65" y="153" width="70" height="9" rx="4" fill="#8b6914" />
                <rect x="67" y="155" width="66" height="5" rx="2" fill="#d4a843" />
                <!-- Side pillars -->
                <rect x="67" y="37" width="6" height="116" rx="3" fill="#8b6914" />
                <rect x="127" y="37" width="6" height="116" rx="3" fill="#8b6914" />

                <!-- Glass top half -->
                <path d="M73 37 L127 37 L105 105 L95 105 Z" fill="rgba(200,160,60,0.08)" stroke="rgba(212,168,67,0.3)"
                    stroke-width="1" />
                <!-- Glass bottom half -->
                <path d="M95 105 L105 105 L127 153 L73 153 Z" fill="rgba(200,160,60,0.08)" stroke="rgba(212,168,67,0.3)"
                    stroke-width="1" />

                <!-- Sand top (nearly empty) -->
                <path d="M73 37 L127 37 L115 60 L85 60 Z" fill="rgba(212,168,67,0.35)" />
                <!-- Sand stream -->
                <rect class="sand-stream" x="98" y="60" width="4" height="45" rx="2" fill="rgba(212,168,67,0.7)" />
                <!-- Sand bottom pile -->
                <g class="sand-pile">
                    <path d="M83 153 Q100 128 117 153 Z" fill="rgba(212,168,67,0.6)" />
                    <path d="M78 153 Q100 132 122 153 Z" fill="rgba(212,168,67,0.4)" />
                </g>

                <!-- Crack on glass -->
                <path class="crack" d="M95 50 L88 70 L94 72 L87 90" stroke="rgba(255,255,255,0.4)" stroke-width="1"
                    fill="none" stroke-linecap="round" />
            </g>

            <!-- Pocket watch -->
            <circle cx="210" cy="82" r="42" fill="#1a1408" stroke="#8b6914" stroke-width="2.5" />
            <circle cx="210" cy="82" r="36" fill="#0f0d05" stroke="rgba(212,168,67,0.3)" stroke-width="1" />
            <!-- Watch face markings -->
            <line x1="210" y1="50" x2="210" y2="56" stroke="rgba(212,168,67,0.7)" stroke-width="2"
                stroke-linecap="round" />
            <line x1="210" y1="108" x2="210" y2="114" stroke="rgba(212,168,67,0.7)" stroke-width="2"
                stroke-linecap="round" />
            <line x1="178" y1="82" x2="184" y2="82" stroke="rgba(212,168,67,0.7)" stroke-width="2"
                stroke-linecap="round" />
            <line x1="236" y1="82" x2="242" y2="82" stroke="rgba(212,168,67,0.7)" stroke-width="2"
                stroke-linecap="round" />
            <!-- Minor ticks -->
            <line x1="221" y1="52" x2="219" y2="57" stroke="rgba(212,168,67,0.4)" stroke-width="1"
                stroke-linecap="round" />
            <line x1="230" y1="59" x2="227" y2="63" stroke="rgba(212,168,67,0.4)" stroke-width="1"
                stroke-linecap="round" />
            <line x1="237" y1="70" x2="233" y2="72" stroke="rgba(212,168,67,0.4)" stroke-width="1"
                stroke-linecap="round" />
            <line x1="199" y1="52" x2="201" y2="57" stroke="rgba(212,168,67,0.4)" stroke-width="1"
                stroke-linecap="round" />
            <line x1="190" y1="59" x2="193" y2="63" stroke="rgba(212,168,67,0.4)" stroke-width="1"
                stroke-linecap="round" />
            <line x1="183" y1="70" x2="187" y2="72" stroke="rgba(212,168,67,0.4)" stroke-width="1"
                stroke-linecap="round" />
            <!-- Hour hand -->
            <line class="hour-hand" x1="210" y1="82" x2="210" y2="58" stroke="#d4a843" stroke-width="2.5"
                stroke-linecap="round" />
            <!-- Minute hand -->
            <line class="minute-hand" x1="210" y1="82" x2="210" y2="52" stroke="rgba(212,168,67,0.6)" stroke-width="1.5"
                stroke-linecap="round" />
            <!-- Center dot -->
            <circle cx="210" cy="82" r="3.5" fill="#d4a843" />
            <!-- Crown/stem -->
            <rect x="207" y="36" width="6" height="8" rx="3" fill="#8b6914" />
            <rect x="206" y="34" width="8" height="4" rx="2" fill="#d4a843" />

            <!-- Chain -->
            <path d="M210 36 Q230 20 250 30 Q260 35 255 50" stroke="rgba(212,168,67,0.4)" stroke-width="1.5" fill="none"
                stroke-dasharray="4 3" />

            <!-- Floating dust motes near hourglass -->
            <circle cx="58" cy="75" r="1.5" fill="rgba(212,168,67,0.4)" />
            <circle cx="148" cy="60" r="1" fill="rgba(212,168,67,0.3)" />
            <circle cx="55" cy="130" r="1" fill="rgba(212,168,67,0.25)" />
            <circle cx="150" cy="145" r="1.5" fill="rgba(212,168,67,0.3)" />

        </svg>

        <div class="code">419</div>
        <div class="stamp">SESSION EXPIRED</div>
        <h2>Your Page Has Expired</h2>
        <p>Time ran out on this session. Go back and try submitting again — it only takes a moment.</p>

        <a href="{{ url()->previous() }}" class="btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M19 12H5M12 5l-7 7 7 7" />
            </svg>
            Go Back & Retry
        </a>
    </div>

    <script>
        const dust = document.getElementById('dust');
        for (let i = 0; i < 35; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            const size = Math.random() * 3 + 1;
            p.style.cssText = `
                width:${size}px; height:${size}px;
                bottom:${Math.random() * 40}%;
                left:${Math.random() * 100}%;
                --d:${3 + Math.random() * 5}s;
                --delay:-${Math.random() * 6}s;
                --x:${(Math.random() - 0.5) * 60}px;
            `;
            dust.appendChild(p);
        }
    </script>

</body>

</html>