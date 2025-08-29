<x-filament-widgets::widget>
    <x-filament::section>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow h-32 flex flex-col items-center justify-center">
                <div class="text-lg font-bold">{{ \App\Models\User::count() }}</div>
                <div class="text-sm text-gray-500">Userlar</div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow h-32 flex flex-col items-center justify-center">
                <div class="text-lg font-bold">{{ \App\Models\Driver::count() }}</div>
                <div class="text-sm text-gray-500">Haydovchilar</div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow h-32 flex flex-col items-center justify-center">
                <div class="text-lg font-bold">{{ \App\Models\Client::count() }}</div>
                <div class="text-sm text-gray-500">Mijozlar</div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow h-32 flex flex-col items-center justify-center">
                <div class="text-lg font-bold">{{ \App\Models\Dispatcher::count() }}</div>
                <div class="text-sm text-gray-500">TaxoPark Adminlari</div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow h-32 flex flex-col items-center justify-center">
                <div class="text-lg font-bold">{{ \App\Models\SuperAdmin::count() }}</div>
                <div class="text-sm text-gray-500">SuperAdminlar</div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>