<div class="flex min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <x-sidebar />

    <!-- Main content -->
    <main class="flex-1 p-6">
        {{ $slot }}
    </main>

    <!-- AlpineJS for dropdown -->
    <script src="//unpkg.com/alpinejs" defer></script>
</div>
