<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        
        $statistics = [
            'total_projects' => Project::where('is_moderated', true)->count(),
            'total_investment' => Project::where('is_moderated', true)->sum('collected_total_investment'),
            'total_jobs' => Project::where('is_moderated', true)->sum('count_new_job'),
        ];

        $allProjects = Project::where('is_moderated', true)
            ->whereNotNull('category')
            ->get();

        $categoriesCount = [];

        foreach ($allProjects as $project) {

            $projectCategories = $project->category;


            if (is_string($projectCategories)) {
                $projectCategories = json_decode($projectCategories, true);
            }


            if (is_array($projectCategories)) {
                foreach ($projectCategories as $category) {

                    $category = trim($category);
                    

                    if (empty($category)) {
                        continue;
                    }

                    if (!isset($categoriesCount[$category])) {
                        $categoriesCount[$category] = 0;
                    }
                    $categoriesCount[$category]++;
                }
            }
        }


        $categories = collect($categoriesCount)
            ->map(function($count, $title) {
                return [
                    'title' => $title,
                    'count' => $count
                ];
            })
            ->sortByDesc('count') 
            ->values() 
            ->take(15);


        $randomProjects = Project::with(['photos'])
            ->where('is_moderated', true)
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