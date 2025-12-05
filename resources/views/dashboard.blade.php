<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Dashboard Cards Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Total Students Card -->
                <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg flex items-center justify-between">
                    <div class="text-gray-800 dark:text-gray-200">
                        <h3 class="text-xl font-semibold">Jumlah Siswa</h3>
                        <p class="text-gray-500 dark:text-gray-400">Total students enrolled</p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-blue-600">500</p>
                    </div>
                </div>

                <!-- Total Teachers Card -->
                <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg flex items-center justify-between">
                    <div class="text-gray-800 dark:text-gray-200">
                        <h3 class="text-xl font-semibold">Jumlah Guru</h3>
                        <p class="text-gray-500 dark:text-gray-400">Total teachers</p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-green-600">30</p>
                    </div>
                </div>

                <!-- Total Admins Card -->
                <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg flex items-center justify-between">
                    <div class="text-gray-800 dark:text-gray-200">
                        <h3 class="text-xl font-semibold">Jumlah Admin</h3>
                        <p class="text-gray-500 dark:text-gray-400">Total administrative staff</p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-yellow-600">10</p>
                    </div>
                </div>

            </div>

            <!-- Financial Info Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">

                <!-- Total Invoices Card -->
                <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg flex items-center justify-between">
                    <div class="text-gray-800 dark:text-gray-200">
                        <h3 class="text-xl font-semibold">Total Tagihan</h3>
                        <p class="text-gray-500 dark:text-gray-400">Total unpaid invoices</p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-red-600">Rp 10,000,000</p>
                    </div>
                </div>

                <!-- Total Income Card -->
                <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg flex items-center justify-between">
                    <div class="text-gray-800 dark:text-gray-200">
                        <h3 class="text-xl font-semibold">Total Pendapatan</h3>
                        <p class="text-gray-500 dark:text-gray-400">Total income this month</p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-green-600">Rp 25,000,000</p>
                    </div>
                </div>

                <!-- Filter Date Section -->
                <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg">
                    <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Filter Pendapatan & Tagihan
                    </h4>
                    <form action="#" method="GET">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="start_date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-200">Start
                                    Date</label>
                                <input type="date" name="start_date" id="start_date"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:ring-indigo-200 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label for="end_date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-200">End Date</label>
                                <input type="date" name="end_date" id="end_date"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:ring-indigo-200 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                        <button type="submit"
                            class="mt-4 w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Filter</button>
                    </form>
                </div>

            </div>

            <div>

                <!-- Dashboard Cards Section -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Cards for Total Students, Teachers, Admins, etc. -->
                    <!-- Add similar cards as mentioned in previous examples -->
                </div>

                <!-- Financial Info Section with Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">

                    <!-- Total Income Chart -->
                    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg">
                        <h4 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Pendapatan Bulanan</h4>
                        <canvas id="incomeChart"></canvas>
                    </div>

                    <!-- Total Invoice Chart -->
                    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg">
                        <h4 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Tagihan Bulanan</h4>
                        <canvas id="invoiceChart"></canvas>
                    </div>

                </div>

            </div>


            <!-- Activity Section -->
            <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg mt-8">
                <h4 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Aktivitas Terbaru</h4>
                <ul class="space-y-4">
                    <li class="flex justify-between">
                        <span class="text-gray-700 dark:text-gray-200">Belajar Siswa</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">10/05/2025</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-700 dark:text-gray-200">Pembayaran Tagihan Siswa</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">09/05/2025</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-700 dark:text-gray-200">Posyandu</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">08/05/2025</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-gray-700 dark:text-gray-200">Kegiatan SPA</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">07/05/2025</span>
                    </li>
                </ul>
            </div>

        </div>
    </div>
    <script>
        // Pendapatan Chart Data (Example)
        var incomeData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'], // Labels for months
            datasets: [{
                label: 'Pendapatan Bulanan',
                data: [5000000, 7000000, 6000000, 8000000, 7500000, 9000000], // Data for monthly income
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        };

        // Tagihan Chart Data (Example)
        var invoiceData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'], // Labels for months
            datasets: [{
                label: 'Tagihan Bulanan',
                data: [3000000, 5000000, 4000000, 6000000, 5500000, 7000000], // Data for monthly invoices
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        };

        // Configurations for Income Chart
        var incomeChartConfig = {
            type: 'bar', // Type of chart (bar chart in this case)
            data: incomeData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        // Configurations for Invoice Chart
        var invoiceChartConfig = {
            type: 'line', // Type of chart (line chart in this case)
            data: invoiceData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        // Render Income Chart
        var incomeChart = new Chart(document.getElementById('incomeChart'), incomeChartConfig);

        // Render Invoice Chart
        var invoiceChart = new Chart(document.getElementById('invoiceChart'), invoiceChartConfig);
    </script>
</x-app-layout>
