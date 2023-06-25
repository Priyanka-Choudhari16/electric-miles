<?php

namespace App\Controller\v1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Manager\OrderManager;
use App\Entity\OrderDetails;
use App\Repository\OrderDetailsRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use DateTime;
use Exception;


class OrderController extends AbstractController
{

    /** @var OrderManager */
    private $orderManager;

    /** @var OrderDetailsRepository */
    private $orderDetailsRepository;

    public function __construct(
        OrderManager $orderManager,
        OrderDetailsRepository $orderDetailsRepository
    ) {
        $this->orderManager  = $orderManager;
        $this->orderDetailsRepository   = $orderDetailsRepository;
    }

    /**
     * Get all orders based on status or by orderId.
     *
     * @Route("/get/orders", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns all orders",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=OrderDetails::class))
     *     )
     * )
     * @OA\Parameter(
     *     name="order_id",
     *     in="query",
     *     description="Enter one or multiple order id with comma seperated",
     *     @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="status",
     *     in="query",
     *     description="Get all orders by status",
     *     @OA\Schema(type="string")
     * )
     * @OA\Tag(name="Orders")
     */
    public function getOrders(Request $request): JsonResponse
    {
        try {
            $orderId = $request->query->get("order_id") ? $request->query->get("order_id") : null;
            $status = $request->query->get("status") ? $request->query->get("status") : null;
            $ids = null;
            if (!empty($orderId) || !empty($status)) {
                if ($orderId != null && $status == null) {
                    $ids = (explode(",", $orderId));
                    foreach ($ids as $id) {
                        $id = trim($id);
                        $orders[] = $this->orderManager->findByOrderId($id);
                    }
                } else {
                    $orders = $this->orderManager->findOrdersByStatus($status);
                }
            }

            //Prepare order result
            if ($orders != null) {
                foreach ($orders as $order) {
                    $temp = [];
                    $temp['orderId'] = $order->getOrderId();
                    $temp['cutomerId'] = $order->getCustomerId();
                    $temp['status'] =  $order->getStatus();
                    $result['order'][] = $temp;
                }
            }
            return new JsonResponse(['orders' => $result], Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Create new order
     *
     * @Route("/create/order", methods={"POST"})
     *  @OA\RequestBody(
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(required={"order_id"}, @OA\Property(property="order_id", type="string"),
     *          required={"customer_id"}, @OA\Property(property="customer_id", type="string"),
     *          @OA\Property(property="order_item_id", type="string"),
     *          @OA\Property(property="order_item_qty", type="integer"),
     *          @OA\Property(property="billing_address", type="string"),
     *          @OA\Property(property="delivery_address", type="string")
     *          )
     *      )
     * ),
     *  @OA\Response(
     *     response=200,
     *     description="Returns all orders"
     * )
     *  @OA\Response(
     *     response=404,
     *     description="Not Found"
     * )
     *  @OA\Tag(name="Orders")
     */
    public function createOrder(Request $request): JsonResponse
    {
        try {
            $result = [];
            $orderId = $request->request->get("order_id");
            $customerId = $request->request->get("customer_id");
            $sku = $request->request->get("order_item_id");
            $quantity = $request->request->get("order_item_qty");
            $billingAddress = $request->request->get("billing_address");
            $deliveryAddress = $request->request->get("delivery_address");
            $orderDate = new \DateTime();
            $estimatedDate = $orderDate->modify('+6 day');

            //create orderdetails object
            $orderDetails = new OrderDetails();
            $orderDetails->setOrderId($orderId);
            $orderDetails->setCustomerId($customerId);
            $orderDetails->setOrderItemId($sku);
            $orderDetails->setOrderItemQty($quantity);
            $orderDetails->setBillingAddress($billingAddress);
            $orderDetails->setDeliveryAddress($deliveryAddress);
            $orderDetails->setStatus("ready-to-ship");
            $orderDetails->setEtd($estimatedDate);
            $order = $this->orderManager->create($orderDetails);

            $result['Order Status'] = $orderDetails->getStatus();
            $result['estimated time of delivery'] = $orderDetails->getEtd();

            return new JsonResponse(['result' => $result], Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }


    /**
     * Update order status
     *
     * @Route("/update/orderStatus", methods={"PATCH"})
     *  @OA\Parameter(
     *     name="order_id",
     *     in="query",
     *     description="Enter orderId to update order status to shipped",
     *     @OA\Schema(type="string")
     * )
     *  @OA\Response(
     *     response=200,
     *     description="Returns all orders"
     * )
     *  @OA\Response(
     *     response=404,
     *     description="Not Found"
     * )
     *  @OA\Tag(name="Orders")
     */
    public function updateOrder(Request $request): JsonResponse
    {
        try {
            $orderId = $request->query->get("order_id");
            if ($orderId != null) {
                $orderDetails = $this->orderManager->findByOrderId($orderId);
                $order = [];
                if (!empty($orderDetails)) {
                    //update order status
                    $orderDetails = $orderDetails->setStatus("shipped");

                    //To Do: order object is not coming
                    $updatedRow = $this->orderManager->update($orderDetails);
                    $order["status"] = $orderDetails->getStatus();
                    $order["orderId"] = $orderDetails->getOrderId();
                }
                return new JsonResponse(['order' => $order], Response::HTTP_OK);
            } else {
                return new JsonResponse(['error' => 'please enter the orderId'], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get all delayed orders based on start time and end time.
     *
     * @Route("/get/all/delayedOrders", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns all orders",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=OrderDetails::class))
     *     )
     * )
     * @OA\Parameter(
     *     name="cur_time",
     *     in="query",
     *     description="Enter current time",
     *     @OA\Schema(type="date")
     * )
     * @OA\Parameter(
     *     name="etd",
     *     in="query",
     *     description="Enter estimated time",
     *     @OA\Schema(type="date")
     * )
     * @OA\Tag(name="Orders")
     */
    public function delayedOrders(Request $request): JsonResponse
    {
        $result = [];
        $currentTime = $request->query->get("cur_time");
        $estimatedTime = $request->query->get("etd");

        if ($currentTime != null && $estimatedTime != null) {
            $currentTime = new DateTime($currentTime);
            $estimatedTime = new DateTime($estimatedTime);
            $delayedOrders = $this->orderManager->findDelayedOrder($currentTime, $estimatedTime);
            if ($delayedOrders != null) {
                foreach ($delayedOrders as $order) {
                    $temp = [];
                    $temp['orderId'] = $order->getOrderId();
                    $result['order'][] = $temp;
                }
            }
            return new JsonResponse([$result], Response::HTTP_OK);
        } else {
            return new JsonResponse(['message' => 'please enter the current time and estimated time'], Response::HTTP_OK);
        }
    }
}
