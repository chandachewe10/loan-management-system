<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Not Found</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="flex flex-col items-center justify-center min-h-screen">
        <h1 class="text-6xl font-bold">404</h1>
        <h2 class="text-2xl font-semibold mt-4">Page Not Found</h2>
        <p class="text-gray-500 mt-2">The page you're looking for doesn't exist or has been moved.</p>
        <a href="{{ url('/') }}" class="mt-6 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Go Home
        </a>
    </div>
</body>

</html>