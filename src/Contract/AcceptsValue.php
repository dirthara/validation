<?php

declare(strict_types=1);

namespace Dirthara\Validation\Contract;

interface AcceptsValue extends Rule
{
    public function accepts(mixed $value): bool;
}
