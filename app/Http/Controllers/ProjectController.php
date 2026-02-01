<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Database\QueryException;

class ProjectController extends Controller
{
    // フラッシュメッセージは Controller のヘルパで統一。
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

    public function store(StoreProjectRequest $request)
    {
        $this->authorize('create', \App\Models\Project::class);

        $data = $request->validated();

        Project::create($data);

        return $this->redirectRouteWithSuccess(
            'projects.index',
            [],
            __('flash.created', ['item' => __('models.project')])
        );
    }

    public function show(Project $project)
    {
        // 読み取りは全員OKの想定（要件通り）
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        return view('projects.edit', compact('project'));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $data = $request->validated();

        $project->update($data);

        return $this->redirectRouteWithSuccess(
            'projects.index',
            [],
            __('flash.updated', ['item' => __('models.project')])
        );
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        // 事前チェック：使われている案件は削除不可
        if ($project->timeEntries()->exists()) {
            return $this->redirectRouteWithError(
                'projects.index',
                [],
                __('projects.flash.cannot_delete_in_use')
            );
        }

        try {
            $project->delete();

            return $this->redirectRouteWithSuccess(
                'projects.index',
                [],
                __('flash.deleted', ['item' => __('models.project')])
            );
        } catch (QueryException $e) {
            // 競合などで、チェック後に参照が増えた場合もここでメッセージ化
            $message = $e->getMessage();

            if (str_contains($message, 'Integrity constraint violation') || str_contains($message, '1451')) {
                return $this->redirectRouteWithError(
                    'projects.index',
                    [],
                    __('projects.flash.cannot_delete_in_use')
                );
            }

            throw $e;
        }
    }
}
