<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #0a0a0f;
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Atmospheric background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 50%, rgba(99, 60, 180, 0.18) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 80% 30%, rgba(30, 120, 200, 0.15) 0%, transparent 60%),
                radial-gradient(ellipse 40% 40% at 60% 80%, rgba(200, 60, 100, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        /* Stars */
        .stars {
            position: fixed;
            inset: 0;
            pointer-events: none;
        }

        .star {
            position: absolute;
            background: white;
            border-radius: 50%;
            animation: twinkle var(--d, 3s) ease-in-out infinite var(--delay, 0s);
        }

        @keyframes twinkle {

            0%,
            100% {
                opacity: 0.1;
                transform: scale(1);
            }

            50% {
                opacity: 0.8;
                transform: scale(1.3);
            }
        }

        .container {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 2rem;
            z-index: 10;
            animation: fadeUp 0.9s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* SVG illustration */
        .illustration {
            width: 320px;
            height: 220px;
            margin-bottom: 2rem;
            filter: drop-shadow(0 0 40px rgba(120, 80, 220, 0.4));
        }

        /* Astronaut float */
        .astronaut-group {
            animation: float 5s ease-in-out infinite;
            transform-origin: center;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(-3deg);
            }

            50% {
                transform: translateY(-14px) rotate(3deg);
            }
        }

        /* Planet pulse */
        .planet-glow {
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 0.6;
                r: 52;
            }

            50% {
                opacity: 1;
                r: 55;
            }
        }

        /* Orbit spin */
        .orbit-ring {
            animation: spin 12s linear infinite;
            transform-origin: 160px 130px;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .code {
            font-family: 'Syne', sans-serif;
            font-size: clamp(5rem, 18vw, 9rem);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -4px;
            background: linear-gradient(135deg, #ffffff 30%, #a78bfa 70%, #60a5fa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            animation: fadeUp 0.9s 0.1s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        h2 {
            font-family: 'Syne', sans-serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: #e2e8f0;
            margin-bottom: 0.75rem;
            letter-spacing: 0.02em;
            animation: fadeUp 0.9s 0.2s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        p {
            font-size: 0.95rem;
            color: #94a3b8;
            max-width: 340px;
            line-height: 1.7;
            margin-bottom: 2.5rem;
            font-weight: 300;
            animation: fadeUp 0.9s 0.3s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, #7c3aed, #3b82f6);
            color: #fff;
            text-decoration: none;
            border-radius: 100px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 400;
            letter-spacing: 0.03em;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 0 30px rgba(124, 58, 237, 0.4);
            animation: fadeUp 0.9s 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .btn:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 0 50px rgba(124, 58, 237, 0.6);
        }

        .btn svg {
            width: 16px;
            height: 16px;
        }
    </style>
</head>

<body>

    <!-- Stars -->
    <div class="stars" id="stars"></div>

    <div class="container">

        <!-- SVG Illustration -->
        <svg class="illustration" viewBox="0 0 320 220" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Planet -->
            <circle cx="160" cy="130" r="52" fill="url(#planetGrad)" />
            <circle class="planet-glow" cx="160" cy="130" r="52" fill="url(#planetGlow)" opacity="0.6" />
            <!-- Planet surface texture -->
            <ellipse cx="145" cy="115" rx="18" ry="8" fill="rgba(255,255,255,0.06)" transform="rotate(-20 145 115)" />
            <ellipse cx="170" cy="140" rx="12" ry="5" fill="rgba(255,255,255,0.05)" transform="rotate(10 170 140)" />

            <!-- Orbit ring -->
            <ellipse class="orbit-ring" cx="160" cy="130" rx="80" ry="22" stroke="rgba(167,139,250,0.35)"
                stroke-width="1.5" stroke-dasharray="6 4" fill="none" />

            <!-- Small moon on orbit -->
            <circle cx="240" cy="130" r="7" fill="#a78bfa" opacity="0.9" />
            <circle cx="238" cy="128" r="2" fill="rgba(255,255,255,0.3)" />

            <!-- Astronaut group -->
            <g class="astronaut-group" transform="translate(130, 30)">
                <!-- Tether -->
                <path d="M30 70 Q10 90 -10 110" stroke="rgba(255,255,255,0.3)" stroke-width="1.2" fill="none"
                    stroke-dasharray="3 3" />
                <!-- Body -->
                <rect x="14" y="28" width="32" height="36" rx="12" fill="url(#suitGrad)" />
                <!-- Helmet -->
                <circle cx="30" cy="20" r="16" fill="url(#helmetGrad)" />
                <circle cx="30" cy="20" r="11" fill="rgba(100,180,255,0.25)" stroke="rgba(255,255,255,0.15)"
                    stroke-width="1" />
                <!-- Visor reflection -->
                <ellipse cx="25" cy="15" rx="4" ry="6" fill="rgba(255,255,255,0.2)" transform="rotate(-15 25 15)" />
                <!-- Arms -->
                <rect x="2" y="32" width="12" height="22" rx="6" fill="url(#suitGrad)" />
                <rect x="46" y="32" width="12" height="22" rx="6" fill="url(#suitGrad)" />
                <!-- Legs -->
                <rect x="16" y="60" width="12" height="20" rx="6" fill="url(#suitGrad)" />
                <rect x="32" y="60" width="12" height="20" rx="6" fill="url(#suitGrad)" />
                <!-- Chest detail -->
                <rect x="22" y="36" width="16" height="10" rx="3" fill="rgba(255,255,255,0.15)" />
                <circle cx="26" cy="41" r="2" fill="rgba(100,220,180,0.8)" />
                <circle cx="34" cy="41" r="2" fill="rgba(255,150,100,0.8)" />
            </g>

            <!-- Floating debris -->
            <rect x="60" y="55" width="7" height="7" rx="2" fill="rgba(167,139,250,0.5)" transform="rotate(20 60 55)" />
            <rect x="255" y="80" width="5" height="5" rx="1" fill="rgba(96,165,250,0.5)"
                transform="rotate(-15 255 80)" />
            <circle cx="85" cy="160" r="3" fill="rgba(255,255,255,0.2)" />
            <circle cx="270" cy="165" r="4" fill="rgba(167,139,250,0.3)" />

            <defs>
                <radialGradient id="planetGrad" cx="35%" cy="35%">
                    <stop offset="0%" stop-color="#6d3fc7" />
                    <stop offset="100%" stop-color="#1e3a6e" />
                </radialGradient>
                <radialGradient id="planetGlow" cx="50%" cy="50%">
                    <stop offset="60%" stop-color="transparent" />
                    <stop offset="100%" stop-color="#7c3aed" stop-opacity="0.5" />
                </radialGradient>
                <linearGradient id="suitGrad" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#cbd5e1" />
                    <stop offset="100%" stop-color="#94a3b8" />
                </linearGradient>
                <radialGradient id="helmetGrad" cx="35%" cy="30%">
                    <stop offset="0%" stop-color="#e2e8f0" />
                    <stop offset="100%" stop-color="#94a3b8" />
                </radialGradient>
            </defs>
        </svg>

        <div class="code">404</div>
        <h2>Lost in Space</h2>
        <p>Looks like this page drifted off into the void. It may have been moved, deleted, or never existed.</p>

        <a href="{{ url('/') }}" class="btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 12l9-9 9 9M5 10v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9" />
            </svg>
            Back to Home
        </a>
    </div>

    <script>
        // Generate stars
        const container = document.getElementById('stars');
        for (let i = 0; i < 120; i++) {
            const s = document.createElement('div');
            s.className = 'star';
            const size = Math.random() * 2.5 + 0.5;
            s.style.cssText = `
                width:${size}px; height:${size}px;
                top:${Math.random() * 100}%; left:${Math.random() * 100}%;
                --d:${2 + Math.random() * 4}s;
                --delay:-${Math.random() * 5}s;
            `;
            container.appendChild(s);
        }
    </script>
</body>

</html>