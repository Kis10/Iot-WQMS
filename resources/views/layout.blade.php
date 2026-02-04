<!DOCTYPE html>
<html>
<head>
    <title>Water Quality System</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-white">
<div class="flex min-h-screen">

    <!-- Sidebar -->
    <div class="w-64 border-r">
        <h2 class="text-xl font-bold p-6">Water Quality</h2>

        <ul>
            <li><a href="/" class="block p-4 hover:bg-gray-100">Dashboard</a></li>
            <li><a href="/history" class="block p-4 hover:bg-gray-100">History</a></li>
            <li><a href="/alerts" class="block p-4 hover:bg-gray-100">Alerts</a></li>
        </ul>
 
    </div>

    <!-- Content -->
    <div class="flex-1 p-6 bg-gray-50">
        @yield('content')
    </div>

</div>
</body>
</html>
