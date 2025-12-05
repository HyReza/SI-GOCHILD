<x-app-layout>
    <div class="max-w-5xl mx-auto bg-white dark:bg-gray-900 p-6 sm:p-8 rounded-lg shadow-lg space-y-6">
        <h2 class="text-3xl font-semibold text-center text-gray-900 dark:text-white">Detail Laporan Harian</h2>

        <div class="space-y-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Nama Siswa:</h3>
                <p class="text-gray-900 dark:text-gray-200">
                    {{ $dailyReport->activityTransaction->student->student_name }}</p>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Periode:</h3>
                <p class="text-gray-900 dark:text-gray-200">{{ $dailyReport->period }}</p>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Suhu Tubuh (°C):</h3>
                <p class="text-gray-900 dark:text-gray-200">{{ $dailyReport->body_temperature }}</p>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Status Kesehatan:</h3>
                <p class="text-gray-900 dark:text-gray-200">
                    {{ $dailyReport->health_status == 'sehat' ? 'Sehat' : 'Sakit' }}
                </p>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Jam Datang:</h3>
                <p class="text-gray-900 dark:text-gray-200">{{ $dailyReport->arrival_time ?? 'Belum Check-in' }}</p>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Jam Pulang:</h3>
                <p class="text-gray-900 dark:text-gray-200">{{ $dailyReport->departure_time ?? 'Belum Check-out' }}</p>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Deskripsi Kesehatan:</h3>
                <p class="text-gray-900 dark:text-gray-200">{{ $dailyReport->sickness_description ?? 'Tidak ada' }}</p>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Makan Pagi:</h3>
                <p class="text-gray-900 dark:text-gray-200">{{ $dailyReport->breakfast }}</p>
            </div>

            <!-- Add other details here as per your model attributes -->

            <div class="flex justify-end mt-6">
                <x-secondary-button id="back-btn" class="mr-2">Kembali</x-secondary-button>
                <x-primary-button id="edit-btn" class="ml-2">Edit Laporan</x-primary-button>
            </div>
        </div>
    </div>
</x-app-layout>
