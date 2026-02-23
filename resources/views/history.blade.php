<x-app-layout>
    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden min-h-[600px]">
                <!-- Desktop Table View -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Device</th>
                                <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Date Modified</th>
                                <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Size</th>
                                <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                    <label class="inline-flex items-center gap-2">
                                        <span>Delete</span>
                                        <input type="checkbox" id="selectAllReadings" class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                    </label>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($readings as $reading)
                                <tr class="hover:bg-indigo-50/30 transition duration-150 cursor-pointer" onclick="if(!event.target.closest('input') && !event.target.closest('label')) window.location='{{ route('history.show', $reading) }}'">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        {{ $reading->device_id ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $reading->created_at ? $reading->created_at->setTimezone('Asia/Manila')->format('M j, Y g:i A') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format(strlen($reading->toJson()) / 1024, 2, ',', '.') }} KB
                                    </td>
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
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m9 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            No readings recorded yet.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="sm:hidden divide-y divide-gray-100">
                    @forelse($readings as $reading)
                        <div class="p-4 hover:bg-indigo-50/20 transition-colors cursor-pointer" onclick="if(!event.target.closest('input') && !event.target.closest('label')) window.location='{{ route('history.show', $reading) }}'">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-bold text-gray-900">{{ $reading->device_id ?? 'N/A' }}</span>
                                <label class="p-2 inline-flex items-center cursor-pointer">
                                    <input type="checkbox"
                                        class="h-5 w-5 text-red-600 border-gray-300 rounded focus:ring-red-500 delete-checkbox"
                                        data-reading-id="{{ $reading->id }}"
                                        data-form-id="delete-form-mobile-{{ $reading->id }}">
                                </label>
                                <form id="delete-form-mobile-{{ $reading->id }}" action="{{ route('history.destroy', $reading) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                            <div class="flex justify-between items-end">
                                <div class="space-y-1">
                                    <div class="flex items-center text-xs text-gray-500">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $reading->created_at ? $reading->created_at->setTimezone('Asia/Manila')->format('M j, Y g:i A') : 'N/A' }}
                                    </div>
                                    <div class="flex items-center text-xs text-gray-400">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        {{ number_format(strlen($reading->toJson()) / 1024, 2, ',', '.') }} KB
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-gray-400 italic text-sm">
                            No readings yet.
                        </div>
                    @endforelse
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
