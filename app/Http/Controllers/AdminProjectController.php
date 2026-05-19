<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminProjectController extends Controller
{
    public function updateStatus(Request $request, Project $project)
    {
        // Проверка прав администратора
        if (auth()->user()->role !== 'Admin') {
            abort(403, 'Доступ запрещен');
        }

        $request->validate([
            'status' => 'required|string|in:На модерации,Одобрен,Отклонен,В процессе,Завершен,Заблокирован'
        ]);

        // Обновляем статус и флаг модерации
        $project->update([
            'status' => $request->status,
            'is_moderated' => in_array($request->status, ['Одобрен', 'В процессе', 'Завершен'])
        ]);

        Log::info('Project status updated by admin', [
            'admin_id' => auth()->id(),
            'project_id' => $project->id,
            'new_status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Статус проекта успешно обновлен',
            'project' => $project
        ]);
    }
    public function index()
    {
        // Проверка прав администратора
        if (auth()->user()->role !== 'Admin') {
            abort(403, 'Доступ запрещен');
        }

        $projects = Project::with(['photos', 'user'])
            ->latest()
            ->paginate(20);

        return inertia('Admin/Projects', [
            'projects' => $projects
        ]);
    }
}
