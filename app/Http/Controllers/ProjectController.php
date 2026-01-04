<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $projects = Project::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 画面は次。いったん到達確認用
        // return response()->json(['message' => 'create form: coming soon']);
        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validated($request);

        $project = new Project($data);
        $project->user()->associate($request->user()); // ← ここで user_id をセット
        $project->save();

        return redirect()->route('projects.show', $project);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,Project $project)
    {
        $this->ensureOwner($request, $project);

        // return response()->json($project);
        return view('projects.show',compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request,Project $project)
    {
        $this->ensureOwner($request, $project);

        // return response()->json(['message' => 'edit form: coming soon', 'project' => $project]);
        return view('projects.edit',compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->ensureOwner($request, $project);

        $data = $this->validated($request);
        $project->update($data);

        return redirect()->route('projects.show', $project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Project $project)
    {
        $this->ensureOwner($request, $project);

        $project->delete();

        return redirect()->route('projects.index');
    }

    //projectpolicyをつくる？
    private function ensureOwner(Request $request, Project $project): void
    {
        // 他人のProjectは「存在しない」扱いにする（情報漏えい防止）
        abort_if($project->user_id !== $request->user()->id, 404);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:active,archived'],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'description' => ['nullable', 'string'],
        ]);
    }
}
