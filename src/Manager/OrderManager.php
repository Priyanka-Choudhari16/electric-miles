<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\OrderDetails;
use App\Entity\DelayedOrder;
use App\Repository\OrderDetailsRepository;
use App\Repository\DelayedOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Validator\Constraints\Uuid;

class OrderManager
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var OrderDetailsRepository */
    private $orderDetailsRepository;

    /** @var DelayedOrdersRepository */
    private $delayedOrdersRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        DelayedOrderRepository $delayedOrdersRepository,
        OrderDetailsRepository $orderDetailsRepository,  
    ) {
        $this->entityManager  = $entityManager;
        $this->orderDetailsRepository   = $orderDetailsRepository;
        $this->delayedOrdersRepository   = $delayedOrdersRepository;
    }

    /**
     * @param OrderDetails $order
     *
     * @throws \Exception
     */
    public function create(OrderDetails $order)
    {
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    /**
     * @param OrderDetails $order
     *
     * @throws \Exception
     */
    public function update(OrderDetails $order)
    {
        $this->entityManager->flush();
    }


    public function findByOrderId($orderId)
    {
        return $this->orderDetailsRepository->findOneBy(['order_id'=>$orderId]);
    }

    /**
     * @return OrderDetails[]|array
     */
    public function findOrdersByStatus($status): array
    {
        return $this->orderDetailsRepository->findBy(['status'=>$status]);
    }


    /**
     * @return DelayedOrder[]|array
     */
    public function findDelayedOrder($currentTime,$estimatedTime): array
    {
        return $this->delayedOrdersRepository->findBy(['curr_time'=>$currentTime,'etd'=>$estimatedTime]);
    }

}
