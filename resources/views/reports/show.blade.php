<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{-- {{ __('reports.show_title_with_date', ['date' => $report->report_date->format('Y-m-d')]) }} --}}
                {{ __('reports.show_title') }}
                （<x-datetime :value="$report->report_date" type="date" />）
            </h2>

            <div class="flex items-center gap-3">
                @can('update', $report)
                    <a href="{{ route('reports.edit', $report) }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        {{ __('common.edit') }}
                    </a>
                @endcan

                <a href="{{ route('reports.index') }}"
                   class="inline-flex items-center px-4 py-2 border rounded-md bg-white hover:bg-gray-50">
                    {{ __('reports.buttons.back_to_index') }}
                </a>
            </div>
        </div>
    </x-slot>

    <x-page-card maxWidth="3xl" bodyClass="p-6 space-y-6" cardClass="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    {{-- @if (session('success'))
                        <div class="mb-4 p-3 border rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 p-3 border rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif --}}

                    @if ($errors->any())
                        <div class="p-3 border rounded-md">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @include('reports._show_status', ['report' => $report])

                    @include('reports._show_history', ['report' => $report])

                    @include('reports._show_approval_panel', ['report' => $report])

                    @include('reports._show_entries_summary', ['report' => $report])

                    @include('reports._report_delete_form', ['report' => $report, 'usePolicy' => true])
    </x-page-card>
</x-app-layout>
