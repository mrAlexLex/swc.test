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
            'attachment' => [
                'nullable',
                'file',
                'max:5120', // 5MB
                new FileExtensionRule(['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv', 'jpeg', 'jpg', 'png', 'gif', 'webp'])
            ],
            'remove_attachment_id' => [
                'nullable',
                'integer',
                Rule::exists('media', 'id'),
            ],
        ];
    }

    public function getData(): array
    {
        return $this->only([
            'title',
            'description',
            'status',
            'completion_date',
        ]);
    }

    public function getAttachment(): ?string
    {
        return $this->input('attachment');
    }

    public function getRemoveAttachment(): ?int
    {
        return $this->input('remove_attachment_id');
    }
}

