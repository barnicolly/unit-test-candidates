<?php

declare(strict_types=1);

namespace App;

final class TestabilityScoreCounter
{
    private int $points = 0;

    public function increase(int $points): void
    {
        $this->points += $points;
    }

    public function getScore(): int
    {
        return $this->points;
    }
}
