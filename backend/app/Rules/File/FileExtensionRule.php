<?php

namespace App\Rules\File;

use Illuminate\Contracts\Validation\Rule;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileExtensionRule implements Rule
{
    private array $availableExtensions;

    public function __construct(array $availableExtensions)
    {
        $this->availableExtensions = $availableExtensions;
    }

    public function passes($attribute, $value): bool
    {
        if (!$value instanceof UploadedFile) {
            return false;
        }

        $extension = strtolower($value->getClientOriginalExtension());

        return in_array($extension, $this->availableExtensions);
    }

    public function message(): string
    {
        return __('validation.mimes', [
            'values' => implode(', ', array_map(fn($ext) => sprintf('.%s', $ext), $this->availableExtensions)),
        ]);
    }
}
