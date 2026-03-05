<?php

namespace App\Support;

use JsonException;
use RuntimeException;

final class SerpLocations
{
    private const DEFAULT_FILE = 'serp/locations.json';

    /**
     * @var array<int, array{label: string, value: int, type?: string}>|null
     */
    private static ?array $items = null;

    /**
     * @var array<int, string>|null location_code => location_name
     */
    private static ?array $labelsByCode = null;

    /**
     * @return array<int, array{label: string, value: int, type?: string}>
     */
    public static function suggest(string $query, int $limit = 20): array
    {
        $query = trim($query);
        if ($query === '' || $limit <= 0) {
            return [];
        }

        $matches = [];
        foreach (self::items() as $item) {
            $label = $item['label'];
            $pos = stripos($label, $query);
            if ($pos === false) {
                continue;
            }

            $matches[] = [$pos, $label, $item];
        }

        usort($matches, static function (array $a, array $b): int {
            if ($a[0] !== $b[0]) {
                return $a[0] <=> $b[0];
            }

            $lenA = strlen($a[1]);
            $lenB = strlen($b[1]);
            if ($lenA !== $lenB) {
                return $lenA <=> $lenB;
            }

            return strcmp($a[1], $b[1]);
        });

        $out = [];
        foreach ($matches as $match) {
            $out[] = $match[2];
            if (count($out) >= $limit) {
                break;
            }
        }

        return $out;
    }

    /**
     * @return array<int, array{label: string, value: int, type?: string}>
     */
    private static function items(): array
    {
        if (self::$items !== null) {
            return self::$items;
        }

        $path = self::path();
        if (! is_file($path)) {
            throw new RuntimeException('Missing locations catalog file: '.$path);
        }

        $json = file_get_contents($path);
        if ($json === false) {
            throw new RuntimeException('Unable to read locations catalog file: '.$path);
        }

        try {
            $raw = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException('Invalid JSON in locations catalog file: '.$path, 0, $e);
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

                if (is_int($value)) {
                    $code = $value;
                } elseif (is_numeric($value)) {
                    $code = (int) $value;
                } else {
                    continue;
                }

                $type = $row['type'] ?? null;
                $item = [
                    'label' => $label,
                    'value' => $code,
                ];
                if (is_string($type) && $type !== '') {
                    $item['type'] = $type;
                }

                $items[] = $item;
            }
        }

        self::$items = $items;

        return $items;
    }

    private static function path(): string
    {
        $relative = config('serp.catalogs.locations', self::DEFAULT_FILE);
        $relative = is_string($relative) && $relative !== '' ? $relative : self::DEFAULT_FILE;

        return storage_path($relative);
    }

    public static function isValidCode(int $code): bool
    {
        if ($code <= 0) {
            return false;
        }

        return array_key_exists($code, self::labelsByCode());
    }

    /**
     * @return array<int, string>
     */
    private static function labelsByCode(): array
    {
        if (self::$labelsByCode !== null) {
            return self::$labelsByCode;
        }

        $map = [];
        foreach (self::items() as $item) {
            $map[(int) $item['value']] = $item['label'];
        }

        self::$labelsByCode = $map;

        return $map;
    }

    public static function labelByCode(int $code): ?string
    {
        return self::labelsByCode()[$code] ?? null;
    }
}
