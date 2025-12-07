<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\IndexTaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task\Task;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Tasks', description: 'Task management endpoints')]
class TaskController extends Controller
{
    #[OA\Get(
        path: '/api/auth/tasks',
        summary: 'Get list of tasks',
        security: [['sanctum' => []]],
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['planned', 'in_progress', 'done'])),
            new OA\Parameter(name: 'user_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'completion_date', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'completion_date_from', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'completion_date_to', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'sort_by', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['id', 'title', 'status', 'completion_date', 'created_at', 'updated_at'])),
            new OA\Parameter(name: 'sort_direction', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of tasks',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Task')),
                        new OA\Property(
                            property: 'meta',
                            properties: [
                                new OA\Property(property: 'current_page', type: 'integer'),
                                new OA\Property(property: 'last_page', type: 'integer'),
                                new OA\Property(property: 'per_page', type: 'integer'),
                                new OA\Property(property: 'total', type: 'integer'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function index(IndexTaskRequest $request)
    {
        $perPage = $request->input('per_page', config('constants.per_page'));
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $status = $request->getStatus();
        $userId = $request->getUserId();
        $completionDate = $request->getCompletionDate();
        $completionDateFrom = $request->getCompletionDateFrom();
        $completionDateTo = $request->getCompletionDateTo();

        $tasks = Task::query()
            ->when($status, fn(Builder $q) => $q->where('status', $status))
            ->when($userId, fn(Builder $q) => $q->where('user_id', $userId))
            ->when($completionDate, fn(Builder $q) => $q->whereDate('completion_date', $completionDate))
            ->when($completionDateFrom, fn(Builder $q) => $q->whereDate('completion_date', '>=', $completionDateFrom))
            ->when($completionDateTo, fn(Builder $q) => $q->whereDate('completion_date', '<=', $completionDateTo))
            ->with([
                'user',
                'media',
            ])
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);

        return TaskResource::collection($tasks);
    }

    #[OA\Post(
        path: '/api/auth/tasks',
        summary: 'Create a new task',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(
                        required: ['title', 'description'],
                        properties: [
                            new OA\Property(property: 'title', type: 'string', maxLength: 255, example: 'Complete project documentation'),
                            new OA\Property(property: 'description', type: 'string', maxLength: 10000, example: 'Write comprehensive documentation for the API'),
                            new OA\Property(property: 'status', type: 'string', enum: ['planned', 'in_progress', 'done'], example: 'planned'),
                            new OA\Property(
                                property: 'attachments',
                                type: 'array',
                                items: new OA\Items(type: 'string', format: 'binary'),
                                description: 'Max 5MB each. Allowed: pdf, doc, docx, xls, xlsx, txt, csv, jpeg, jpg, png, gif, webp'
                            ),
                        ]
                    )
                ),
            ]
        ),
        tags: ['Tasks'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Task created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Task'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(StoreTaskRequest $request): TaskResource
    {
        $user = auth()->user();

        DB::beginTransaction();

        $task = $user->tasks()
            ->create($request->getData());

        $task->saveAttachments($request->getAttachments());

        DB::commit();

        return new TaskResource($task);
    }

    #[OA\Get(
        path: '/api/auth/tasks/{id}',
        summary: 'Get a specific task',
        security: [['sanctum' => []]],
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Task details',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Task'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Task not found'),
        ]
    )]
    public function show(Task $task): TaskResource
    {
        $task->load(['user', 'media']);

        return new TaskResource($task);
    }

    #[OA\Post(
        path: '/api/auth/tasks/{id}',
        summary: 'Update a task (use POST with _method=PUT for file upload)',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: false,
            content: [
                new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: '_method', type: 'string', example: 'PUT'),
                            new OA\Property(property: 'title', type: 'string', maxLength: 255, example: 'Updated task title'),
                            new OA\Property(property: 'description', type: 'string', maxLength: 10000, example: 'Updated task description'),
                            new OA\Property(property: 'status', type: 'string', enum: ['planned', 'in_progress', 'done']),
                            new OA\Property(property: 'completion_date', type: 'string', format: 'date'),
                            new OA\Property(
                                property: 'attachments',
                                type: 'array',
                                items: new OA\Items(type: 'string', format: 'binary'),
                                description: 'Max 5MB each. Allowed: pdf, doc, docx, xls, xlsx, txt, csv, jpeg, jpg, png, gif, webp'
                            ),
                            new OA\Property(
                                property: 'remove_attachment_ids',
                                type: 'array',
                                items: new OA\Items(type: 'integer'),
                                description: 'Array of Media IDs to remove'
                            ),
                        ]
                    )
                ),
            ]
        ),
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Task updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Task'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Task not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        DB::beginTransaction();

        $task->update($request->getData());

        if ($request->has('remove_attachment_ids')) {
            $task->media()->whereIn('id', $request->getRemoveAttachmentIds())->delete();
        }

        $task->saveAttachments($request->getAttachments());

        DB::commit();

        $task->load(['user', 'media']);

        return new TaskResource($task);
    }

    #[OA\Delete(
        path: '/api/auth/tasks/{id}',
        summary: 'Delete a task',
        security: [['sanctum' => []]],
        tags: ['Tasks'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Task deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Task deleted successfully.'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Task not found'),
        ]
    )]
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully.',
        ]);
    }
}

