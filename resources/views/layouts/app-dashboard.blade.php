<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water Quality System</title>
    @vite('resources/css/app.css')
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-knob/1.2.13/jquery.knob.min.js"></script>
</head>
<body class="bg-gray-50">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    @include('layouts.sidebar')

    <!-- Main Content -->
    <main class="flex-1 p-6">
        @yield('content')
    </main>
</div>

<!-- jQuery Knob Init -->
<script>
    $(function() {
        $('#turbidityKnob, #tdsKnob, #phKnob, #tempKnob').knob({
            min: 0,
            max: 100,
            width: 100,
            height: 100,
            readOnly: true,
            fgColor: '#4B5563',
            bgColor: '#E5E7EB',
            thickness: 0.3
        });
    });
</script>

</body>
</html>
