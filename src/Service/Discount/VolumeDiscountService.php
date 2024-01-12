<?php

namespace App\Service\Discount;

use App\Dto\Discount;
use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Repository\VolumeDiscountRepository;
use App\Type\DiscountType;
use Doctrine\ORM\NonUniqueResultException;

class VolumeDiscountService implements DiscountServiceInterface
{
    public const DISCOUNT_NAME = 'volume';

    public function __construct(
        private readonly VolumeDiscountRepository $volumeDiscountRepository,
        private readonly OrderRepository $orderRepository
    ) {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function computeDiscount(Order $order): array
    {
        $user = $order->getUser();

        $userOrders = $this->orderRepository->findBy(['user' => $user->getId()]);

        $totalUserVolume = 0;
        foreach ($userOrders as $userOrder) {
            $totalUserVolume += $userOrder->getQuantity() * $userOrder->getLot()->getCost();
        }

        $discount = [];

        $volumeDiscount = $this->volumeDiscountRepository->findBiggestDiscountByAmount($totalUserVolume);

        if ($volumeDiscount) {
            $discount[0] = new Discount(
                self::DISCOUNT_NAME,
                $volumeDiscount->getDiscount() * $order->getFullPrice(),
                ['value_type' => DiscountType::Percent->name, 'value' => $volumeDiscount->getDiscount()]
            );
        }

        return $discount;
    }
}
