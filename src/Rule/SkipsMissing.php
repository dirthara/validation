<?php

declare(strict_types=1);

namespace Dirthara\Validation\Rule;

trait SkipsMissing
{
    public bool $validatesMissing {
        get => false;
    }
}
