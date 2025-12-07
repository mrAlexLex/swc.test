<?php

namespace App\Models\Task;

use App\Enums\TaskStatus;
use App\Events\Task\TaskCreated;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Task extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use TaskMethods;

    public const ATTACHMENT_COLLECTION = 'attachments';

    protected $fillable = [
        'title',
        'description',
        'status',
        'completion_date',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'completion_date' => 'date',
        ];
    }

    public $dispatchesEvents = [
        'created' => TaskCreated::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::ATTACHMENT_COLLECTION)
            ->acceptsMimeTypes([
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/plain',
                'text/csv',
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
            ]);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->sharpen(10)
            ->nonQueued();
    }
}

