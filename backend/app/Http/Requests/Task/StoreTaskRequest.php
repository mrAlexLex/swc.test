<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskStatus;
use App\Rules\File\FileExtensionRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255'
            ],
            'description' => [
                'required',
                'string',
                'max:10000'
            ],
            'status' => [
                'nullable',
                Rule::enum(TaskStatus::class)
            ],
            'attachment' => [
                'nullable',
                'file',
                'max:5120', // 5MB
                new FileExtensionRule(['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv', 'jpeg', 'jpg', 'png', 'gif', 'webp']),
            ],
        ];
    }

    public function getData(): array
    {
        return $this->merge([
            'status' => $this->input('status', TaskStatus::PLANNED->value),
        ])->only([
            'title',
            'description',
            'status',
        ]);
    }

    public function getAttachment(): array
    {
        return $this->input('attachment');
    }
}

