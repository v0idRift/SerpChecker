<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SerpSearchRequest extends FormRequest
{
    /**
     * Allow guests to make requests
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'keyword' => ['required', 'string', 'max:200'],
            'site' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:200'],
            'language' => ['required', 'string', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'keyword' => is_string($this->keyword) ? trim($this->keyword) : $this->keyword,
            'site' => is_string($this->site) ? trim($this->site) : $this->site,
            'location' => is_string($this->location) ? trim($this->location) : $this->location,
            'language' => is_string($this->language) ? trim($this->language) : $this->language,
        ]);
    }
}
