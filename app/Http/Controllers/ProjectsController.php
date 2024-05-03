<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class ProjectsController extends Controller
{
    public function index(): JsonResponse
    {
        $projects = Project::all();

        return response()->json([
            'projects' => $projects,
        ], 200);
    }

    public function show(Project $project): JsonResponse
    {
        return response()->json([
            'project' => $project,
        ], 200);
    }

    public function store(CreateProjectRequest $request): JsonResponse
    {
        $project = Project::create($request->validated());

        return response()->json([
            'project' => $project,
            'message' => 'Project Created Successfully',
        ], 200);
    }

    public function update(CreateProjectRequest $request, Project $project): JsonResponse
    {
        $project->update($request->validated());

        return response()->json([
            'project' => $project,
            'message' => 'Project Updated Successfully',
        ], 200);
    }
}
