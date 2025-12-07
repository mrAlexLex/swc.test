<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskStatus;
use App\Rules\File\FileExtensionRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'nullable',
                'string',
                'max:255'
            ],
            'description' => [
                'nullable',
                'string',
                'max:10000'
            ],
            'status' => [
                'nullable',
                Rule::enum(TaskStatus::class)
            ],
            'completion_date' => [
                'nullable',
                'date'
            ],
            'attachments' => [
                'nullable',
                'array',
            ],
            'attachments.*' => [
                'file',
                'max:5120', // 5MB
                new FileExtensionRule(['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv', 'jpeg', 'jpg', 'png', 'gif', 'webp'])
            ],
            'remove_attachment_ids' => [
                'nullable',
                'array',
            ],
            'remove_attachment_ids.*' => [
                'integer',
                Rule::exists('media', 'id'),
            ],
        ];
    }

    public function getData(): array
    {
        return $this->merge([
            'completion_date' => now()->parse($this->input('completion_date')),
        ])->only([
            'title',
            'description',
            'status',
            'completion_date',
        ]);
    }

    public function getAttachments(): array
    {
        return $this->file('attachments', []);
    }

    public function getRemoveAttachmentIds(): array
    {
        return $this->input('remove_attachment_ids', []);
    }
}

