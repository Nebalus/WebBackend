<?php

namespace Nebalus\Webapi\Value;

readonly class Range
{
    private function __construct(
        private int $min,
        private int $max
    ) {
    }

    public static function from(int $min, int $max): self
    {
        if ($min > $max) {
            $maxTemp = $max;
            $max = $min;
            $min = $maxTemp;
        }
        return new self($min, $max);
    }

    public function isInRange(int $value): bool
    {
        return $value >= $this->min && $value <= $this->max;
    }
}
