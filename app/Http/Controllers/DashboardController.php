<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Статистика проектов
        $statistics = [
            'total_projects' => Project::where('is_moderated', true)->count(),
            'total_investment' => Project::where('is_moderated', true)->sum('collected_total_investment'),
            'total_jobs' => Project::where('is_moderated', true)->sum('count_new_job'),
        ];

        // Получаем все уникальные категории из проектов
        $allProjects = Project::where('is_moderated', true)
            ->where('status', '!=', 'На модерации')
            ->whereNotNull('category')
            ->get();

        $categoriesCount = [];

        foreach ($allProjects as $project) {
            // Получаем категории проекта
            $projectCategories = $project->category;

            // Если это строка JSON, декодируем её
            if (is_string($projectCategories)) {
                $projectCategories = json_decode($projectCategories, true);
            }

            // Если это массив, обрабатываем каждую категорию
            if (is_array($projectCategories)) {
                foreach ($projectCategories as $category) {
                    // Очищаем категорию от лишних символов и пробелов
                    $category = trim($category);
                    
                    // Пропускаем пустые категории
                    if (empty($category)) {
                        continue;
                    }

                    // Подсчитываем количество проектов в каждой категории
                    if (!isset($categoriesCount[$category])) {
                        $categoriesCount[$category] = 0;
                    }
                    $categoriesCount[$category]++;
                }
            }
        }

        // Преобразуем в массив объектов и сортируем
        $categories = collect($categoriesCount)
            ->map(function($count, $title) {
                return [
                    'title' => $title,
                    'count' => $count
                ];
            })
            ->sortByDesc('count') // Сортируем по количеству (самые популярные первыми)
            ->values() // Сбрасываем ключи
            ->take(15); // Берем топ-15 категорий

        // Рандомные проекты для слайдера (6 штук)
        $randomProjects = Project::with(['photos'])
            ->where('is_moderated', true)
            ->where('status', '!=', 'На модерации')
            ->inRandomOrder()
            ->limit(6)
            ->get()
            ->map(function($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'description' => $project->short_description,
                    'image' => $project->photos && $project->photos->count() > 0 
                        ? '/storage/' . $project->photos[0]->photo_path 
                        : null,
                ];
            });

        return Inertia::render('Dashboard', [
            'statistics' => $statistics,
            'categories' => $categories,
            'randomProjects' => $randomProjects,
        ]);
    }
}