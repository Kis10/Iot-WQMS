<x-app-layout>
    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Turbidity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TDS</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">pH Level</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Temperature</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Humidity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date and Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <label class="inline-flex items-center gap-2">
                                        <span>Delete</span>
                                        <input type="checkbox" id="selectAllReadings" class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                    </label>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($readings as $reading)
                                <!-- Data Row -->
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $reading->device_id ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reading->turbidity ?? 'N/A' }}%</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reading->tds ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-3 py-1 rounded-full {{ ($reading->ph >= 5.0 && $reading->ph <= 9.0) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $reading->ph ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reading->temperature ?? 'N/A' }}Â°C</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reading->humidity !== null ? $reading->humidity . '%' : 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reading->created_at ? $reading->created_at->setTimezone('Asia/Manila')->format('M j, Y g:i A') : 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox"
                                                class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500 delete-checkbox"
                                                data-reading-id="{{ $reading->id }}"
                                                data-form-id="delete-form-{{ $reading->id }}">
                                        </label>
                                        <form id="delete-form-{{ $reading->id }}" action="{{ route('history.destroy', $reading) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500 text-sm">
                                        No readings recorded yet. Data will appear here when the sensor starts transmitting.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            </div>

          

            <!-- Pagination -->
            @if(isset($readings) && method_exists($readings, 'links'))
                <div class="mt-6 flex items-center justify-center gap-2 text-sm text-gray-700">
                    <a href="{{ $readings->previousPageUrl() ?? $readings->url(1) }}"
                        class="px-2 py-1 rounded border border-gray-300 text-gray-600 hover:bg-gray-50">
                        &lt;
                    </a>
                    <span class="px-2 py-1 text-gray-700">
                        {{ $readings->currentPage() }} out of {{ $readings->lastPage() }}
                    </span>
                    <a href="{{ $readings->nextPageUrl() ?? $readings->url($readings->lastPage()) }}"
                        class="px-2 py-1 rounded border border-gray-300 text-gray-600 hover:bg-gray-50">
                        &gt;
                    </a>
                </div>
            @endif
        </div>
    </div>

    <form id="bulkDeleteForm" action="{{ route('history.bulk-delete') }}" method="POST" class="hidden">
        @csrf
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.delete-checkbox');
            const selectAll = document.getElementById('selectAllReadings');
            const bulkDeleteForm = document.getElementById('bulkDeleteForm');

            function syncSelectAll() {
                if (!selectAll) return;
                const allChecked = Array.from(checkboxes).length > 0 && Array.from(checkboxes).every(cb => cb.checked);
                const noneChecked = Array.from(checkboxes).every(cb => !cb.checked);
                selectAll.checked = allChecked && !noneChecked;
            }

            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', function() {
                    syncSelectAll();
                });
            });

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => {
                        cb.checked = this.checked;
                    });
                });
            }

            function submitBulkDelete() {
                bulkDeleteForm.innerHTML = '';
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = '{{ csrf_token() }}';
                bulkDeleteForm.appendChild(tokenInput);

                checkboxes.forEach(cb => {
                    if (cb.checked) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'reading_ids[]';
                        input.value = cb.dataset.readingId;
                        bulkDeleteForm.appendChild(input);
                    }
                });

                if (bulkDeleteForm.querySelectorAll('input[name="reading_ids[]"]').length === 0) {
                    return false;
                }

                bulkDeleteForm.submit();
                return true;
            }

            function submitSingleDelete() {
                const checked = Array.from(checkboxes).filter(cb => cb.checked);
                if (checked.length !== 1) {
                    return false;
                }
                const formId = checked[0].dataset.formId;
                const form = document.getElementById(formId);
                if (!form) {
                    return false;
                }
                form.submit();
                return true;
            }

            document.addEventListener('keydown', function(event) {
                if (event.key !== 'Backspace') {
                    return;
                }
                const checked = Array.from(checkboxes).filter(cb => cb.checked);
                if (checked.length === 0) {
                    return;
                }
                event.preventDefault();
                if (checked.length === 1) {
                    submitSingleDelete();
                } else {
                    submitBulkDelete();
                }
            });
        });
    </script>
</x-app-layout>
