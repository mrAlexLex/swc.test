<?php

namespace Database\Factories;

use App\Enums\TaskStatus;
use App\Models\Task\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $status = fake()->randomElement(TaskStatus::cases());
        $completionDate = null;

        if ($status === TaskStatus::DONE) {
            $completionDate = fake()->dateTimeBetween('-30 days', 'now');
        } elseif (fake()->boolean(60)) {
            $completionDate = fake()->dateTimeBetween('now', '+60 days');
        }

        return [
            'title' => fake()->sentence(rand(3, 8)),
            'description' => fake()->paragraphs(rand(1, 3), true),
            'status' => $status,
            'completion_date' => $completionDate,
            'user_id' => User::factory(),
        ];
    }

    public function planned(): static
    {
        return $this->state(fn(array $attributes): array => [
            'status' => TaskStatus::PLANNED,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn(array $attributes): array => [
            'status' => TaskStatus::IN_PROGRESS,
        ]);
    }

    public function done(): static
    {
        return $this->state(fn(array $attributes): array => [
            'status' => TaskStatus::DONE,
            'completion_date' => fake()->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    public function forUser(User $user): static
    {
        return $this->state(fn(array $attributes): array => [
            'user_id' => $user->id,
        ]);
    }

    public function withCompletionDate(\DateTimeInterface|string|null $date = null): static
    {
        return $this->state(fn(array $attributes): array => [
            'completion_date' => $date ?? fake()->dateTimeBetween('now', '+30 days'),
        ]);
    }
}

