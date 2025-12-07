<?php

namespace App\Models\Task;

trait TaskMethods
{
    public function saveAttachments(array $attachments): void
    {
        foreach ($attachments as $file) {
            $this->addMedia($file)->toMediaCollection(Task::ATTACHMENT_COLLECTION);
        }
    }

    public function getAttachmentsAttribute(): array
    {
        return $this->getMedia(self::ATTACHMENT_COLLECTION)->map(fn($media) => [
            'id' => $media->id,
            'url' => $media->getUrl(),
            'name' => $media->file_name,
        ])->toArray();
    }
}
