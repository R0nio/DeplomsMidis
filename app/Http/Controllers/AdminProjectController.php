<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminProjectController extends Controller
{
    public function updateStatus(Request $request, Project $project)
    {
        try {
            // Проверка прав администратора
            if (auth()->user()->role !== 'Admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Доступ запрещен'
                ], 403);
            }

            // Валидация с обработкой кириллицы
            $validated = $request->validate([
                'status' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) {
                        $allowedStatuses = [
                            'На модерации',
                            'Одобрен',
                            'Отклонен',
                            'Заблокирован'
                        ];
                        
                        if (!in_array($value, $allowedStatuses)) {
                            $fail('Недопустимый статус: ' . $value);
                        }
                    }
                ]
            ]);
            
            $project->status = $validated['status'];
            $project->is_moderated = in_array($validated['status'], ['Одобрен']);
            $project->save();

            return response()->json([
                'success' => true,
                'message' => 'Статус проекта успешно обновлен',
                'project' => $project->fresh()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении статуса: ' . $e->getMessage()
            ], 500);
        }
    }
}
