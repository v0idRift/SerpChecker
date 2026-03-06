<?php

namespace App\Support;

use JsonException;
use RuntimeException;

final class SerpLanguages
{
    private const DEFAULT_FILE = 'serp/languages.json';

    /**
     * @var array<int, array{label: string, value: string}>|null
     */
    private static ?array $items = null;

    /**
     * @var array<string, string>|null language_code => language_name
     */
    private static ?array $labelsByCode = null;

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public static function all(): array
    {
        return self::items();
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    private static function items(): array
    {
        if (self::$items !== null) {
            return self::$items;
        }

        $path = self::path();
        if (! is_file($path)) {
            throw new RuntimeException('Missing languages catalog file: '.$path);
        }

        $json = file_get_contents($path);
        if ($json === false) {
            throw new RuntimeException('Unable to read languages catalog file: '.$path);
        }

        try {
            $raw = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException('Invalid JSON in languages catalog file: '.$path, 0, $e);
        }

        $items = [];
        if (is_array($raw)) {
            foreach ($raw as $row) {
                if (! is_array($row)) {
                    continue;
                }

                $label = $row['label'] ?? null;
                $value = $row['value'] ?? null;

                if (! is_string($label) || $label === '') {
                    continue;
                }

                if (! is_string($value) || $value === '') {
                    continue;
                }

                $items[] = [
                    'label' => $label,
                    'value' => $value,
                ];
            }
        }

        self::$items = $items;

        return $items;
    }

    private static function path(): string
    {
        $relative = config('serp.catalogs.languages', self::DEFAULT_FILE);
        $relative = is_string($relative) && $relative !== '' ? $relative : self::DEFAULT_FILE;

        return storage_path($relative);
    }

    public static function isValidCode(string $code): bool
    {
        $code = trim($code);
        if ($code === '') {
            return false;
        }

        return array_key_exists($code, self::labelsByCode());
    }

    /**
     * @return array<string, string>
     */
    private static function labelsByCode(): array
    {
        if (self::$labelsByCode !== null) {
            return self::$labelsByCode;
        }

        $map = array_column(self::items(), 'label', 'value');

        self::$labelsByCode = $map;

        return $map;
    }
}
