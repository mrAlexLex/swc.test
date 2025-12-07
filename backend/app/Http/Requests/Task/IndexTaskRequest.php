<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'nullable',
                Rule::enum(TaskStatus::class)
            ],
            'user_id' => [
                'nullable',
            ],
            'completion_date' => [
                'nullable',
                'date'
            ],
            'completion_date_from' => [
                'nullable',
                'date'
            ],
            'completion_date_to' => [
                'nullable',
                'date',
                'after_or_equal:completion_date_from'
            ],
        ];
    }

    public function getStatus(): ?int
    {
        return $this->integer('status');
    }

    public function getUserId(): ?int
    {
        return $this->integer('user_id');
    }

    public function getCompletionDate(): ?string
    {
        return $this->integer('completion_date');
    }

    public function getCompletionDateFrom(): ?string
    {
        return $this->integer('completion_date_from');
    }

    public function getCompletionDateTo(): ?string
    {
        return $this->integer('completion_date_to');
    }
}

