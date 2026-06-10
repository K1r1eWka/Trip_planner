<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? "Trip Planner" }}</title>
    <link rel="icon" href="{{ asset('images/trip-planner-logo.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <style>
        body {
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .bg-hero {
            min-height: 100vh;
            background-image: var(--bg-image, url('{{ asset("images/image7.png") }}'));
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .bg-hero::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 0;
        }

        .bg-hero > * {
            position: relative;
            z-index: 1;
        }

        .navbar-brand {
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body class="{{ $fullBg ?? false ? 'bg-hero' : '' }}">
    <x-nav />
    <main>
        {{ $slot }}
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>