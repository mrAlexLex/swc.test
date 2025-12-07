<?php

namespace App\Http\Resources;

use App\Models\Task\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @mixin Task
 */
#[OA\Schema(
    schema: 'Task',
    title: 'Task',
    description: 'Task resource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'title', type: 'string', example: 'Complete project documentation'),
        new OA\Property(property: 'description', type: 'string', example: 'Write comprehensive documentation for the API'),
        new OA\Property(property: 'status', type: 'string', enum: ['planned', 'in_progress', 'done'], example: 'in_progress'),
        new OA\Property(property: 'status_label', type: 'string', example: 'In Progress'),
        new OA\Property(property: 'completion_date', type: 'string', format: 'date', nullable: true, example: '2024-12-31'),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
        new OA\Property(
            property: 'attachments',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Attachment')
        ),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-12-05T10:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-12-05T10:00:00.000000Z'),
    ]
)]
class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'completion_date' => $this->completion_date?->format('Y-m-d'),

            'user' => new UserResource($this->whenLoaded('user')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('media')),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

