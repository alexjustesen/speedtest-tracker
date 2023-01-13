<x-filament::page>
    <x-filament::card>
        <x-slot name="header">
            <h2 class="text-lg font-bold -tracking-tight">Results Data</h2>
        </x-slot>

        <div class="space-y-4">
            <div>
                <p>Deleting results data will remove all speedtest results from the database, this cannot be undone.</p>
            </div>

            <div>
                <p>The following <span class="underline">will not</span> be reset by clearing results data.</p>

                <ul class="list-disc list-inside mt-2">
                    <li>- Settings including general settings, notification settings and threshold settings.</li>
                    <li>- Integrations and data sent to external data destinations like InfluxDB.</li>
                </ul>
            </div>
        </div>
    </x-filament::card>
</x-filament::page>
