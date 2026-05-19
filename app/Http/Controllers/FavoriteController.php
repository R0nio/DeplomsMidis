<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FavoriteController extends Controller
{
    /**
     * Переключение избранного (добавить/удалить)
     */
    public function toggle(Project $project)
    {
        $user = Auth::user();

        $favorite = Favorite::all()->where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->first();

        if ($favorite) {
            // Удаляем из избранного
            $favorite->delete();
            
            return response()->json([
                'success' => true,
                'isFavorited' => false,
                'message' => 'Проект удален из избранного'
            ]);
        } else {
            // Добавляем в избранное
            Favorite::create([
                'user_id' => $user->id,
                'project_id' => $project->id,
            ]);
            
            return response()->json([
                'success' => true,
                'isFavorited' => true,
                'message' => 'Проект добавлен в избранное'
            ]);
        }
    }

    /**
     * Список избранных проектов
     */
    public function index()
    {
        $favorites = Auth::user()
            ->favoriteProjects()
            ->with(['photos', 'user'])
            ->latest('favorites.created_at') // Исправлено с favorite на favorites
            ->paginate(12);

        return Inertia::render('Favorites/Index', [
            'favorites' => $favorites
        ]);
    }

    /**
     * Проверка, находится ли проект в избранном
     */
    public function check(Project $project)
    {
        $isFavorited = Favorite::where('user_id', Auth::user()->id())
            ->where('project_id', $project->id)
            ->exists();

        return response()->json([
            'isFavorited' => $isFavorited
        ]);
    }

    /**
     * Удалить из избранного
     */
    public function destroy(Project $project)
    {
        $deleted = Favorite::where('user_id', Auth::user()->id())
            ->where('project_id', $project->id)
            ->delete();

        if ($deleted) {
            return back()->with('success', 'Проект удален из избранного');
        }

        return back()->with('error', 'Проект не найден в избранном');
    }
}