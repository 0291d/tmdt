<x-filament::page>
    <form method="GET" class="mb-6">
        <div class="fi-section rounded-xl border bg-white p-4">
            <div class="text-sm font-medium text-gray-700 mb-3">Lọc theo danh mục</div>
            <div class="flex flex-wrap gap-4 items-center">
                @foreach($allCategories as $c)
                    @php $checked = collect(request('categories', []))->contains($c['id']); @endphp
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="categories[]" value="{{ $c['id'] }}" class="rounded border-gray-300 text-primary-600 focus:ring-primary-600" @checked($checked)>
                        <span>{{ $c['name'] }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </form>

    <div class="bg-white p-4 rounded shadow">
        <canvas id="productChart" height="120"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = @json($labels);
        const data = @json($series);
        const ctx = document.getElementById('productChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Số lượng bán',
                    data,
                    backgroundColor: 'rgba(59,130,246,0.5)',
                    borderColor: 'rgb(59,130,246)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });

        // Auto-submit on checkbox change (GET), no submit button
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form[method="GET"]');
            form?.addEventListener('change', () => form.submit());
        });
    </script>
</x-filament::page>
