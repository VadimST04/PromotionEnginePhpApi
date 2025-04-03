<?php

namespace App\Filter\Modifier;

use App\DTO\PromotionEnquiryInterface;
use App\Entity\Promotion;

class EvenItemsMultiplier implements PriceModifierInterface
{

    public function modify(int $price, int $quantity, Promotion $promotion, PromotionEnquiryInterface $enquiry): int
    {
        if (!($enquiry->getQuantity() >= 2)) {
            return $price * $quantity;
        }

        $oddQuantity = ($quantity % 2);
        $evenQuantity = $quantity - $oddQuantity;
        return $evenQuantity * $price * $promotion->getAdjustment() + $oddQuantity * $price;
    }
}