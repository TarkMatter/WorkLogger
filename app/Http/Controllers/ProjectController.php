<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $projects = Project::query()
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $this->authorize('create', \App\Models\Project::class);

        return view('projects.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', \App\Models\Project::class);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:projects,code'],
            'name' => ['required', 'string', 'max:255', 'unique:projects,name'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string'],
        ], [
            'end_date.after_or_equal' => '終了日は開始日以降の日付にしてください。',
        ]);

        Project::create($data);

        return redirect()
            ->route('projects.index')
            ->with('success', '案件を作成しました。');
    }

    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('projects', 'code')->ignore($project->id)],
            'name' => ['required', 'string', 'max:255', Rule::unique('projects', 'name')->ignore($project->id)],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string'],
        ], [
            'end_date.after_or_equal' => '終了日は開始日以降の日付にしてください。',
        ]);

        $project->update($data);

        return redirect()
            ->route('projects.index')
            ->with('success', '案件を更新しました。');
    }

    public function destroy(\App\Models\Project $project)
    {
        $this->authorize('delete', $project);

        // 事前チェック：使われている案件は削除不可
        if ($project->timeEntries()->exists()) {
            return redirect()
                ->route('projects.index')
                ->with('error', 'この案件は日報の工数で使用されているため削除できません。');
        }

        try {
            $project->delete();

            return redirect()
                ->route('projects.index')
                ->with('success', '案件を削除しました。');
        } catch (\Illuminate\Database\QueryException $e) {
            // 競合などで、チェック後に参照が増えた場合もここでメッセージ化
            $message = $e->getMessage();

            if (str_contains($message, 'Integrity constraint violation') || str_contains($message, '1451')) {
                return redirect()
                    ->route('projects.index')
                    ->with('error', 'この案件は日報の工数で使用されているため削除できません。');
            }

            throw $e;
        }
    }

}
