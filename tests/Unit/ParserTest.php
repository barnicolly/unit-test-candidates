<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Parser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * @see Parser
 */
class ParserTest extends TestCase
{
    private Parser $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new Parser();
    }

    public static function calculateTestabilityScoreProvider(): array
    {
        return [
            [
                file_get_contents(__DIR__ . '/../fixtures/ChatController.php.txt'),
            ],
        ];
    }

    #[DataProvider('calculateTestabilityScoreProvider')]
    public function testCalculateTestabilityScore(string $content): void
    {
        $phpParser = (new ParserFactory())->createForNewestSupportedVersion();
        $ast = $phpParser->parse($content);

        $result = $this->service->calculateTestabilityScore($ast);
        $this->assertMatchesSnapshot($result);
    }
}
