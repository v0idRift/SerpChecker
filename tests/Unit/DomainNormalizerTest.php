<?php

namespace Tests\Unit;

use App\Support\DomainNormalizer;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class DomainNormalizerTest extends TestCase
{
    /**
     * @return void
     */
    public function test_it_normalizes_domains_and_urls(): void
    {
        $normalizer = new DomainNormalizer;

        $this->assertSame('example.com', $normalizer->normalize('example.com'));
        $this->assertSame('example.com', $normalizer->normalize('www.example.com'));
        $this->assertSame('example.com', $normalizer->normalize('https://www.Example.com/path?x=1'));
        $this->assertSame('sub.example.com', $normalizer->normalize('http://sub.example.com:8080/a'));
        $this->assertSame('', $normalizer->normalize('   '));
    }

    /**
     * @return void
     */
    public function test_it_matches_subdomains_against_a_target_domain(): void
    {
        $normalizer = new DomainNormalizer;

        $this->assertTrue($normalizer->matchesTarget('www.example.com', 'example.com'));
        $this->assertTrue($normalizer->matchesTarget('sub.example.com', 'example.com'));
        $this->assertTrue($normalizer->matchesTarget('https://sub.example.com/a', 'example.com'));
        $this->assertFalse($normalizer->matchesTarget('example.co', 'example.com'));
    }
}
