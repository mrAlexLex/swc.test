<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @mixin Media
 */
#[OA\Schema(
    schema: 'Attachment',
    title: 'Attachment',
    description: 'File attachment resource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'url', type: 'string', example: 'http://localhost:8080/storage/media/1/document.pdf'),
        new OA\Property(property: 'name', type: 'string', example: 'document.pdf'),
        new OA\Property(property: 'mime_type', type: 'string', example: 'application/pdf'),
        new OA\Property(property: 'size', type: 'integer', description: 'File size in bytes', example: 102400),
    ]
)]
class AttachmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->getUrl(),
            'name' => $this->file_name,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
        ];
    }
}

