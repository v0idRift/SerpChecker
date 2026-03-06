<?php

namespace App\Services\Serp;

use App\Support\DomainNormalizer;
use Throwable;

final readonly class GoogleOrganicRankService
{
    public function __construct(
        private SerpApiClient $serpApiClient,
        private DomainNormalizer $domainNormalizer,
    ) {}

    public function findRank(string $keyword, string $site, int $locationCode, string $languageCode): GoogleOrganicRankResult
    {
        $targetDomain = $this->domainNormalizer->normalize($site);
        if ($targetDomain === '') {
            return GoogleOrganicRankResult::clientError('Invalid site value: unable to parse domain.', $site);
        }

        $task = [
            'keyword' => $keyword,
            'location_code' => $locationCode,
            'language_code' => $languageCode,
            'device' => (string) config('serp.google.device', 'desktop'),
            'depth' => (int) config('serp.google.depth', 100),
            'stop_crawl_on_match' => [
                [
                    'match_type' => 'with_subdomains',
                    'match_value' => $targetDomain,
                ],
            ],
            'find_targets_in' => ['organic'],
        ];

        $seDomain = config('serp.google.se_domain');
        if (is_string($seDomain) && $seDomain !== '') {
            $task['se_domain'] = $seDomain;
        }

        try {
            $json = $this->serpApiClient->post('serp/google/organic/live/regular', [$task]);
        } catch (Throwable $e) {
            return GoogleOrganicRankResult::clientError($e->getMessage(), $targetDomain);
        }

        $apiTask = $json['tasks'][0] ?? null;
        if (! is_array($apiTask)) {
            return GoogleOrganicRankResult::apiError('Unexpected API response: missing tasks.', $targetDomain);
        }

        $statusCode = $apiTask['status_code'] ?? null;
        $statusMessage = $apiTask['status_message'] ?? null;
        $statusMessage = is_string($statusMessage) ? $statusMessage : null;

        if ($statusCode !== 20000) {
            return GoogleOrganicRankResult::apiError($statusMessage ?: 'SERP API returned an error.', $targetDomain);
        }

        $result = $apiTask['result'][0] ?? null;
        if (! is_array($result)) {
            return GoogleOrganicRankResult::apiError('Unexpected API response: missing result.', $targetDomain);
        }

        $items = $result['items'] ?? [];
        $items = is_array($items) ? $items : [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            if (($item['type'] ?? null) !== 'organic') {
                continue;
            }

            $candidate = $item['domain'] ?? null;
            if (! is_string($candidate) || $candidate === '') {
                $candidate = is_string($item['url'] ?? null) ? $item['url'] : '';
            }

            if (! $this->domainNormalizer->matchesTarget($candidate, $targetDomain)) {
                continue;
            }

            $rankGroup = $this->toNullableInt($item['rank_group'] ?? null);
            $rankAbsolute = $this->toNullableInt($item['rank_absolute'] ?? null);

            return GoogleOrganicRankResult::found(
                targetDomain: $targetDomain,
                rankGroup: $rankGroup,
                rankAbsolute: $rankAbsolute,
                url: is_string($item['url'] ?? null) ? $item['url'] : null,
                title: is_string($item['title'] ?? null) ? $item['title'] : null,
                apiMessage: $statusMessage,
            );
        }

        return GoogleOrganicRankResult::notFound($targetDomain, $statusMessage);
    }

    private function toNullableInt(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return null;
    }
}
