<?php

namespace App\Filter\Modifier\Factory;

use App\Filter\Modifier\PriceModifierInterface;

interface PriceModifierFactoryInterface
{
    public const PRICE_MODIFIER_NAMESPACE = "App\Filter\Modifier\\";

    public function create(string $modifierType): PriceModifierInterface;
}