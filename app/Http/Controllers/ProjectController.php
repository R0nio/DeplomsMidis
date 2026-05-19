<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Project_investment;
use App\Models\Project_forecast;
use App\Models\Project_photos;
use App\Http\Requests\StoreProjectRequest;
use App\Models\Favorite;
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
        
        // Получаем все проекты, прошедшие модерацию
        $projects = Project::with(['photos', 'user'])
            ->where('is_moderated', true)
            ->where('status', '!=', 'На модерации')
            ->latest()
            ->get();
        
        // Получаем избранные проекты для инвестора
        $favorites = [];
        if ($userRole === 'Investor') {
            $favorites = Favorite::where('user_id', $userId)->get();
        }
        
        return Inertia::render('Projects', [
            'projects' => $projects,
            'favorites' => $favorites
        ]);
    }

    public function map()
    {
        
        // Получаем только проекты с координатами
        $projects = Project::with(['photos', 'user'])
            ->where('is_moderated', true)
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

            // Создаем проект
            $project = Project::create([
                'user_id' => auth()->id(),
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
            //dd($project);
            // Сохраняем фотографии
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $index => $photo) {
                    $path = $photo->store('projects/' . $project->id, 'public');
                    
                    Project_photos::create([
                        'project_id' => $project->id,
                        'photo_path' => $path,
                        'order' => $index,
                    ]);
                }
            }

            // Сохраняем структуру инвестиций (расходы)
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

            // Сохраняем прогноз (если есть данные)
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
        $project->load(['photos', 'investments', 'forecasts', 'user']);

        return Inertia::render('Projects/Show', [
            'project' => $project,
            'isFavorited' => $project->isFavoritedBy(auth()->id()),
        ]);
    }
}
