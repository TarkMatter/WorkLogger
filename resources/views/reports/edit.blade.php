<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('reports.edit_title') }}
                （<x-datetime :value="$report->report_date" type="date" />）
        </h2>
    </x-slot>

    <x-page-card maxWidth="3xl" bodyClass="p-6 space-y-6" cardClass="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    @include('reports._edit_forms', ['report' => $report])

                    <hr class="my-2">

                    @include('reports._edit_entries', ['report' => $report, 'projects' => $projects])

                    @include('reports._report_delete_form', ['report' => $report])
    </x-page-card>
</x-app-layout>
