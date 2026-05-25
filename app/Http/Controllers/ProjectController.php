<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Project_investment;
use App\Models\Project_forecast;
use App\Models\Project_photos;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $userRole = Auth::user()?->role;
        
        $projects = Project::with(['photos', 'user'])
            ->where('status', '!=', 'На модерации')
            ->where('status', '!=', 'Отклонен')
            ->where('status', '!=', 'Заблокирован')
            ->where('is_moderated', true)
            ->latest()
            ->get();
        
        $favorites = [];
        if ($userRole === 'Investor') {
            $favorites = Favorite::where('user_id', $userId)->get();
        }
        $Adminprojects = Project::with(['photos', 'user'])->get();
        return Inertia::render('Projects', [
            'projects' => $projects,
            'Adminprojects' => $Adminprojects,
            'favorites' => $favorites
        ]);
    }

    public function map()
    {
        
        $projects = Project::with(['photos', 'user'])
            ->where('is_moderated', true)
            ->where('status', '!=', 'На модерации')
            ->where('status', '!=', 'Отклонен')
            ->where('status', '!=', 'Заблокирован')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest()
            ->get();

        return Inertia::render('MapProject', [
            'projects' => $projects
        ]);
    }

    public function store(StoreProjectRequest $request)
    {
        try {
            $project = Project::create([
                'user_id' => 1,
                'title' => $request->name,
                'short_description' => $request->shotr_descr,
                'full_description' => $request->full_descr,
                'category' => $request->categories ? json_encode($request->categories) : null,
                'ownership' => $request->ownShip,
                'activity' => $request->activity,
                'type_build' => $request->type_building,
                'address' => $request->addres,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'total_investment' => $request->total_investment,
                'number_date_realise' => $request->number_date_realise,
                'count_new_job' => $request->count_new_job,
                'collected_total_investment' => $request->collected_total_investment ?? 0,
                'status' => 'На модерации',
                'is_moderated' => false,
            ]);
            
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $index => $photo) {
                    $path = $photo->store('projectsPhoto/' . $project->id, 'public');
                    
                    Project_photos::create([
                        'project_id' => $project->id,
                        'photo_path' => $path,
                        'order' => $index,
                    ]);
                }
            }

            if ($request->expenses && is_array($request->expenses)) {
                foreach ($request->expenses as $expense) {
                    if (!empty($expense['item_name']) && !empty($expense['amount'])) {
                        Project_investment::create([
                            'project_id' => $project->id,
                            'item_name' => $expense['item_name'],
                            'amount' => $expense['amount'],
                        ]);
                    }
                }
            }


            if ($request->expected_revenue || $request->expected_expenses) {
                Project_forecast::create([
                    'project_id' => $project->id,
                    'year' => now()->year,
                    'expected_revenue' => $request->expected_revenue,
                    'expected_expenses' => $request->expected_expenses,
                ]);
            }

            

            return redirect()->back()
                ->with('success', 'Проект успешно создан и отправлен на модерацию!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withErrors(['error' => 'Ошибка при создании проекта: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Project $project)
    {
        $userId = Auth::id();
        $userRole = Auth::user()?->role;
        $project->load(['photos', 'investments', 'forecasts', 'user']);
        $favorites = [];
        if ($userRole === 'Investor') {
             $favorites = Favorite::where('user_id', $userId)->get();
        }

        return Inertia::render('ProjectDetail', [
            'project' => $project,
            'isFavorited' => $favorites,
        ]);
    }
    
    public function edit(Project $project)
    {

        if ($project->user_id !== auth()->id()) {
            abort(403);
        }

        $project->load(['photos', 'investments', 'forecasts']);

        return Inertia::render('UpdateProject', [
            'project' => $project,
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }

        try {

            $project->update([
                'title'                      => $request->name,
                'short_description'          => $request->shotr_descr,
                'full_description'           => $request->full_descr,
                'category'                   => $request->categories ? json_encode($request->categories) : null,
                'ownership'                  => $request->ownShip,
                'activity'                   => $request->activity,
                'type_build'                 => $request->type_building,
                'address'                    => $request->addres,
                'latitude'                   => $request->latitude,
                'longitude'                  => $request->longitude,
                'total_investment'           => $request->total_investment,
                'number_date_realise'        => $request->number_date_realise,
                'count_new_job'              => $request->count_new_job,
                'collected_total_investment' => $request->collected_total_investment ?? 0,
                'status'                     => 'На модерации',
                'is_moderated'               => false,
            ]);


            if ($request->deleted_photos && is_array($request->deleted_photos)) {
                foreach ($request->deleted_photos as $photoId) {
                    $photo = Project_photos::find($photoId);
                    if ($photo && $photo->project_id === $project->id) {
                        Storage::disk('public')->delete($photo->photo_path);
                        $photo->delete();
                    }
                }
            }

            if ($request->hasFile('fotos')) {
                $lastOrder = Project_photos::where('project_id', $project->id)->max('order') ?? -1;
                foreach ($request->file('fotos') as $index => $photo) {
                    $path = $photo->store('projectsPhoto/' . $project->id, 'public');
                    Project_photos::create([
                        'project_id' => $project->id,
                        'photo_path' => $path,
                        'order'      => $lastOrder + $index + 1,
                    ]);
                }
            }

            Project_investment::where('project_id', $project->id)->delete();
            if ($request->expenses && is_array($request->expenses)) {
                foreach ($request->expenses as $expense) {
                    if (!empty($expense['item_name']) && $expense['amount'] !== '') {
                        Project_investment::create([
                            'project_id' => $project->id,
                            'item_name'  => $expense['item_name'],
                            'amount'     => $expense['amount'],
                        ]);
                    }
                }
            }

            Project_forecast::updateOrCreate(
                ['project_id' => $project->id],
                [
                    'year'              => now()->year,
                    'expected_revenue'  => $request->expected_revenue,
                    'expected_expenses' => $request->expected_expenses,
                ]
            );

            return redirect()->route('profile.edit')
                ->with('success', 'Проект обновлён и отправлен на повторную модерацию!');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Ошибка при обновлении: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
