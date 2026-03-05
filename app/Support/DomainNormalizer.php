<?php

namespace App\Support;

final class DomainNormalizer
{
    public function matchesTarget(string $candidateDomainOrUrl, string $targetDomain): bool
    {
        $candidate = $this->normalize($candidateDomainOrUrl);
        $target = $this->normalize($targetDomain);

        if ($candidate === '' || $target === '') {
            return false;
        }

        if ($candidate === $target) {
            return true;
        }

        return str_ends_with($candidate, '.'.$target);
    }

    public function normalize(string $input): string
    {
        $input = strtolower(trim($input));
        if ($input === '') {
            return '';
        }

        $host = parse_url($input, PHP_URL_HOST);
        if (is_string($host) && $host !== '') {
            $domain = $host;
        } else {
            $input = preg_replace('#^[a-z][a-z0-9+.-]*://#i', '', $input) ?? $input;
            $input = preg_replace('#^[^@/]+@#', '', $input) ?? $input;
            $domain = preg_split('~[/?:#]~', $input, 2)[0] ?? $input;
        }

        $domain = preg_replace('#^www\\.#i', '', $domain) ?? $domain;

        return rtrim($domain, '.');
    }
}
