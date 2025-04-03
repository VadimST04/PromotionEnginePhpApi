<?php

namespace App\Tests\unit;

use App\DTO\LowestPriceEnquiry;
use App\Entity\Promotion;
use App\Filter\Modifier\DateRangeMultiplier;
use App\Filter\Modifier\EvenItemsMultiplier;
use App\Filter\Modifier\FixedPriceVoucher;
use App\Tests\ServiceTestCase;

class PriceModifiersTest extends ServiceTestCase
{
    public function test_DateRangeMultiplier_returns_a_correct_modifier_price(): void
    {
        // Given
        $enquiry = new LowestPriceEnquiry();
        $enquiry->setQuantity(5);
        $enquiry->setRequestDate('2022-11-26');

        $promotion = new Promotion();
        $promotion->setName('Black Friday half price sale');
        $promotion->setAdjustment(0.5);
        $promotion->setCriteria(["from" => "2022-11-25", "to" => "2022-11-28"]);
        $promotion->setType('date_range_multiplier');

        $dateRangeModifier = new DateRangeMultiplier();

        // When
        $modifiedPrice = $dateRangeModifier->modify(100, 5, $promotion, $enquiry);

        // Then
        $this->assertEquals(250, $modifiedPrice);
    }

    public function test_FixedPriceVoucherModifier_returns_a_correct_result(): void
    {
        $promotion = new Promotion();
        $promotion->setName('Voucher OU812');
        $promotion->setAdjustment(100);
        $promotion->setCriteria(["code" => "OU812"]);
        $promotion->setType('fixed_price_voucher');

        $enquiry = new LowestPriceEnquiry();
        $enquiry->setQuantity(5);
        $enquiry->setVoucherCode('OU812');

        $fixedPriceVoucherModifier = new FixedPriceVoucher();
        $modifiedPrice = $fixedPriceVoucherModifier->modify(150, 5, $promotion, $enquiry);

        $this->assertEquals(500, $modifiedPrice);
    }

    public function test_EvenItemsMultiplier_returns_a_correct_result(): void
    {
        $promotion = new Promotion();
        $promotion->setName('Buy one get one free');
        $promotion->setAdjustment(0.5);
        $promotion->setCriteria(["minimum_quantity" => 2]);
        $promotion->setType('even_items_multiplier');

        $enquiry = new LowestPriceEnquiry();
        $enquiry->setQuantity(5);

        $evenItemsMultiplier = new EvenItemsMultiplier();
        $modifiedPrice = $evenItemsMultiplier->modify(100, 5, $promotion, $enquiry);

        // ((100 * 4) * 0.5) + (1 * 100)
        $this->assertEquals(300, $modifiedPrice);
    }

    public function test_EvenItemsMultiplier_correctly_calculates_alternatives(): void
    {
        $promotion = new Promotion();
        $promotion->setName('Buy one get half price');
        $promotion->setAdjustment(0.75);
        $promotion->setCriteria(["minimum_quantity" => 2]);
        $promotion->setType('even_items_multiplier');

        $enquiry = new LowestPriceEnquiry();
        $enquiry->setQuantity(5);

        $evenItemsMultiplier = new EvenItemsMultiplier();
        $modifiedPrice = $evenItemsMultiplier->modify(100, 5, $promotion, $enquiry);

        // 300 + 100
        $this->assertEquals(400, $modifiedPrice);
    }
}