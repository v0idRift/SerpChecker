<?php

namespace App\Http\Requests;

use App\Support\SerpLanguages;
use App\Support\SerpLocations;
use Illuminate\Foundation\Http\FormRequest;

final class SerpSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'keyword' => ['required', 'string', 'max:200'],
            'site' => ['required', 'string', 'max:255'],
            'location_code' => [
                'required',
                'integer',
                function (string $attribute, mixed $value, callable $fail): void {
                    $code = is_numeric($value) ? (int) $value : 0;

                    if (! SerpLocations::isValidCode($code)) {
                        $fail(__('serp.validation.location'));
                    }
                },
            ],
            'language_code' => [
                'required',
                'string',
                function (string $attribute, mixed $value, callable $fail): void {
                    $code = is_string($value) ? trim($value) : '';

                    if (! SerpLanguages::isValidCode($code)) {
                        $fail(__('serp.validation.language'));
                    }
                },
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'keyword.required' => __('serp.validation.keyword_required'),
            'keyword.string' => __('serp.validation.keyword_required'),
            'keyword.max' => __('serp.validation.keyword_max'),

            'site.required' => __('serp.validation.site_required'),
            'site.string' => __('serp.validation.site_required'),
            'site.max' => __('serp.validation.site_max'),

            'location_code.required' => __('serp.validation.location'),
            'location_code.integer' => __('serp.validation.location'),

            'language_code.required' => __('serp.validation.language'),
            'language_code.string' => __('serp.validation.language'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $keyword = is_string($this->keyword) ? trim($this->keyword) : $this->keyword;
        $site = is_string($this->site) ? trim($this->site) : $this->site;

        $locationCode = is_string($this->location_code) ? trim($this->location_code) : $this->location_code;
        $languageCode = is_string($this->language_code) ? strtolower(trim($this->language_code)) : $this->language_code;

        $this->merge([
            'keyword' => $keyword,
            'site' => $site,
            'location_code' => $locationCode,
            'language_code' => $languageCode,
        ]);
    }
}
