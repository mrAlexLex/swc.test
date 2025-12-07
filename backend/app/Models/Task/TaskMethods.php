<?php

namespace App\Models\Task;

trait TaskMethods
{
    public function saveAttachments()
    {
        $this->addMediaFromRequest('attachment')->toMediaCollection(Task::ATTACHMENT_COLLECTION);
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia(self::ATTACHMENT_COLLECTION);

        return $media?->getUrl();
    }

    public function getAttachmentNameAttribute(): ?string
    {
        $media = $this->getFirstMedia(self::ATTACHMENT_COLLECTION);

        return $media?->file_name;
    }
}
