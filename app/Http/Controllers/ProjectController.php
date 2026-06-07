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
        
        $projects = Project::with(['photos', 'user', 'investments', 'forecasts'])
            ->where('status', '!=', 'На модерации')
            ->where('status', '!=', 'Отклонен')
            ->where('status', '!=', 'Заблокирован')
            ->where('is_moderated', true)
            ->latest()
            ->get();
        
        $adminProjects = [];
        if ($userRole === 'Admin') {
            $adminProjects = Project::with(['photos', 'user', 'investments', 'forecasts'])
                ->latest()
                ->get();
        }
        
        $favorites = [];
        if ($userRole === 'Investor') {
            $favorites = Favorite::where('user_id', $userId)->get();
        }
        
        return Inertia::render('Projects', [
            'projects' => $projects,
            'Adminprojects' => $adminProjects,
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
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'shotr_descr' => ['required', 'string', 'min:5', 'max:500'],
            'full_descr' => ['required', 'string', 'min:10', 'max:10000'],

            'categories' => ['nullable', 'array', 'max:4'],
            'categories.*' => ['nullable', 'string', 'max:100'],

            'ownShip' => ['nullable', 'string', 'max:255'],
            'activity' => ['nullable', 'string', 'max:255'],
            'type_building' => ['nullable', 'string', 'max:255'],
            'addres' => ['required', 'string', 'max:500'],

            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],

            'total_investment' => ['required', 'numeric', 'min:0'],
            'number_date_realise' => ['required', 'integer', 'min:1', 'max:1200'],
            'count_new_job' => ['required', 'integer', 'min:0'],
            'collected_total_investment' => ['nullable', 'numeric', 'min:0'],

            'expenses' => ['nullable', 'array'],
            'expenses.*.item_name' => ['required_with:expenses.*.amount', 'string', 'max:255'],
            'expenses.*.amount' => ['required_with:expenses.*.item_name', 'numeric', 'min:0'],

            // Вот твои реальные поля
            'revenues' => ['nullable', 'array'],
            'revenues.*.year' => ['required_with:revenues.*.amount', 'integer', 'min:1', 'max:100'],
            'revenues.*.amount' => ['required_with:revenues.*.year', 'numeric', 'min:0'],

            'yearly_expenses' => ['nullable', 'array'],
            'yearly_expenses.*.year' => ['required_with:yearly_expenses.*.amount', 'integer', 'min:1', 'max:100'],
            'yearly_expenses.*.amount' => ['required_with:yearly_expenses.*.year', 'numeric', 'min:0'],

            'fotos' => ['nullable', 'array', 'max:20'],
            'fotos.*' => ['required', 'file', 'mimes:jpg,jpeg,png,webp'],
        ], [
            'fotos.*.mimes' => 'Разрешены только jpg, jpeg, png, webp.',
            'fotos.*.max' => 'Каждое фото не больше 5MB.',
            'fotos.*.file' => 'Некорректный файл.',
        ]);

        try {
            DB::beginTransaction();

            $project = Project::create([
                'user_id' => auth()->id() ?? 1,
                'title' => $validated['name'],
                'short_description' => $validated['shotr_descr'],
                'full_description' => $validated['full_descr'],
                'category' => !empty($validated['categories']) ? json_encode(array_values(array_filter($validated['categories']))) : null,
                'ownership' => $validated['ownShip'] ?? null,
                'activity' => $validated['activity'] ?? null,
                'type_build' => $validated['type_building'] ?? null,
                'address' => $validated['addres'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'total_investment' => $validated['total_investment'],
                'number_date_realise' => $validated['number_date_realise'],
                'count_new_job' => $validated['count_new_job'],
                'collected_total_investment' => $validated['collected_total_investment'] ?? 0,
                'status' => 'На модерации',
                'is_moderated' => false,
            ]);

            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $index => $photo) {
                    $path = $photo->store("projectsPhoto/{$project->id}", 'public');

                    Project_photos::create([
                        'project_id' => $project->id,
                        'photo_path' => $path,
                        'order' => $index,
                    ]);
                }
            }

            if (!empty($validated['expenses'])) {
                foreach ($validated['expenses'] as $expense) {
                    Project_investment::create([
                        'project_id' => $project->id,
                        'item_name' => $expense['item_name'],
                        'amount' => $expense['amount'],
                    ]);
                }
            }

            // Сбор forecast по годам
            $revenueMap = [];
            foreach (($validated['revenues'] ?? []) as $row) {
                $revenueMap[(int)$row['year']] = (float)$row['amount'];
            }

            $expenseMap = [];
            foreach (($validated['yearly_expenses'] ?? []) as $row) {
                $expenseMap[(int)$row['year']] = (float)$row['amount'];
            }

            $years = array_unique(array_merge(array_keys($revenueMap), array_keys($expenseMap)));
            sort($years);

            foreach ($years as $year) {
                Project_forecast::create([
                    'project_id' => $project->id,
                    'year' => $year,
                    'expected_revenue' => $revenueMap[$year] ?? 0,
                    'expected_expenses' => $expenseMap[$year] ?? 0,
                ]);
            }

            DB::commit();

            return redirect()->route('projects.show', $project->id)->with('success', 'Проект успешно создан и отправлен на модерацию!');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Ошибка при создании проекта: ' . $e->getMessage()])
                ->withInput();
        }
    }

    private function parseForecastSeries($raw): array
    {
        if (empty($raw)) {
            return [];
        }

        $decoded = $raw;

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
        }

        if (!is_array($decoded)) {
            return [];
        }

        $result = [];
        foreach ($decoded as $row) {
            if (!is_array($row)) {
                continue;
            }

            $year = isset($row['year']) ? (int) $row['year'] : null;
            $amount = isset($row['amount']) ? (float) $row['amount'] : null;

            if ($year && $year > 0 && $amount !== null && $amount >= 0) {
                $result[$year] = $amount;
            }
        }

        return $result;
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

        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $project->update([
                'title' => $validated['name'],
                'short_description' => $validated['shotr_descr'],
                'full_description' => $validated['full_descr'],
                'category' => !empty($validated['categories']) ? json_encode(array_values(array_filter($validated['categories']))) : null,
                'ownership' => $validated['ownShip'] ?? null,
                'activity' => $validated['activity'] ?? null,
                'type_build' => $validated['type_building'] ?? null,
                'address' => $validated['addres'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'total_investment' => $validated['total_investment'],
                'number_date_realise' => $validated['number_date_realise'],
                'count_new_job' => $validated['count_new_job'],
                'collected_total_investment' => $validated['collected_total_investment'] ?? 0,
                'status' => 'На модерации',
                'is_moderated' => false,
            ]);

            if (!empty($validated['deleted_photos']) && is_array($validated['deleted_photos'])) {
                foreach ($validated['deleted_photos'] as $photoId) {
                    $photo = Project_photos::find($photoId);
                    if ($photo && (int)$photo->project_id === (int)$project->id) {
                        Storage::disk('public')->delete($photo->photo_path);
                        $photo->delete();
                    }
                }
            }

            if ($request->hasFile('fotos')) {
                $lastOrder = Project_photos::where('project_id', $project->id)->max('order') ?? -1;

                foreach ($request->file('fotos') as $index => $photo) {
                    $path = $photo->store("projectsPhoto/{$project->id}", 'public');

                    Project_photos::create([
                        'project_id' => $project->id,
                        'photo_path' => $path,
                        'order' => $lastOrder + $index + 1,
                    ]);
                }
            }

            Project_investment::where('project_id', $project->id)->delete();
            foreach (($validated['expenses'] ?? []) as $expense) {
                Project_investment::create([
                    'project_id' => $project->id,
                    'item_name' => $expense['item_name'],
                    'amount' => $expense['amount'],
                ]);
            }

            // Пересохраняем прогнозы по годам
            Project_forecast::where('project_id', $project->id)->delete();

            $revenueMap = [];
            foreach (($validated['revenues'] ?? []) as $row) {
                $revenueMap[(int)$row['year']] = (float)$row['amount'];
            }

            $expenseMap = [];
            foreach (($validated['yearly_expenses'] ?? []) as $row) {
                $expenseMap[(int)$row['year']] = (float)$row['amount'];
            }

            $years = array_unique(array_merge(array_keys($revenueMap), array_keys($expenseMap)));
            sort($years);

            foreach ($years as $year) {
                Project_forecast::create([
                    'project_id' => $project->id,
                    'year' => $year,
                    'expected_revenue' => $revenueMap[$year] ?? 0,
                    'expected_expenses' => $expenseMap[$year] ?? 0,
                ]);
            }

            DB::commit();

            return redirect()->route('projects.show', $project->id)
                ->with('success', 'Проект обновлён и отправлен на повторную модерацию!');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Ошибка при обновлении: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
