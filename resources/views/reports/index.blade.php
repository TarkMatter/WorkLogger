<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('reports.title') }}</h2>

            <a href="{{ route('reports.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                {{ __('common.create') }}
            </a>
        </div>
    </x-slot>

    <x-page-card maxWidth="7xl" bodyClass="p-6 text-gray-900 space-y-6" cardClass="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                    {{-- flash messages --}}
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

                    {{-- tabs / filters --}}
                    @php
                        $statusLabels = [
                            'all'       => __('reports.tabs.all'),
                            'draft'     => __('reports.tabs.draft'),
                            'submitted' => __('reports.tabs.submitted'),
                            'approved'  => __('reports.tabs.approved'),
                            'rejected'  => __('reports.tabs.rejected'),
                        ];

                        $current = $status ?? 'all';

                        $tabs = auth()->user()->canApprove()
                            ? ['submitted', 'all', 'rejected', 'approved']
                            : ['all', 'draft', 'submitted', 'rejected', 'approved'];

                        $sort = $sort ?? 'report_date';
                        $dir  = $dir ?? 'desc';
                        $warn = $warn ?? 'all';
                        $warningCount = (int) ($warningCount ?? 0);
                    @endphp

                    @include('reports._tabs', [
                        'tabs' => $tabs,
                        'current' => $current,
                        'sort' => $sort,
                        'dir' => $dir,
                        'warn' => $warn,
                        'totalCount' => $totalCount,
                        'counts' => $counts,
                        'statusLabels' => $statusLabels,
                    ])

                    @include('reports._warnings', [
                        'current' => $current,
                        'sort' => $sort,
                        'dir' => $dir,
                        'warn' => $warn,
                        'warningCount' => $warningCount,
                    ])

                    @include('reports._sort', [
                        'current' => $current,
                        'warn' => $warn,
                        'sort' => $sort,
                        'dir' => $dir,
                    ])

                    {{-- table --}}
                    @include('reports._table', ['reports' => $reports])

    </x-page-card>
</x-app-layout>
