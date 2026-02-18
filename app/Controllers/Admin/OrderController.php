<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Infrastructure\Persistence\Models\OrderModel;
use App\Infrastructure\Persistence\Models\OrderItemModel;
use App\Infrastructure\Persistence\Models\PaymentModel;
use App\Infrastructure\Persistence\Models\ShipmentModel;
use App\Infrastructure\Persistence\Models\UserModel;

class OrderController extends BaseController
{
    protected OrderModel $orderModel;
    protected OrderItemModel $orderItemModel;
    protected PaymentModel $paymentModel;
    protected ShipmentModel $shipmentModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->paymentModel = new PaymentModel();
        $this->shipmentModel = new ShipmentModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $statusFilter = $this->request->getGet('status');
        $paymentFilter = $this->request->getGet('payment_status');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');

        $builder = $this->orderModel->select('orders.*, users.first_name, users.last_name, users.email as user_email_join')
            ->join('users', 'users.id = orders.user_id', 'left');

        if ($search) {
            $builder->groupStart()
                ->like('orders.order_number', $search)
                ->orLike('orders.customer_email', $search)
                ->orLike('users.first_name', $search)
                ->orLike('users.last_name', $search)
                ->groupEnd();
        }

        if ($statusFilter) {
            $builder->where('orders.status', $statusFilter);
        }

        if ($paymentFilter) {
            $builder->where('orders.payment_status', $paymentFilter);
        }

        if ($dateFrom) {
            $builder->where('orders.created_at >=', $dateFrom . ' 00:00:00');
        }

        if ($dateTo) {
            $builder->where('orders.created_at <=', $dateTo . ' 23:59:59');
        }

        $perPage = 15;
        $orders  = $builder->orderBy('orders.created_at', 'DESC')->paginate($perPage, 'default');
        $pager   = $this->orderModel->pager;

        // Estadísticas rápidas
        $stats = [
            'pending'    => $this->orderModel->where('status', OrderModel::STATUS_PENDING)->countAllResults(),
            'processing' => $this->orderModel->where('status', OrderModel::STATUS_PROCESSING)->countAllResults(),
            'shipped'    => $this->orderModel->where('status', OrderModel::STATUS_SHIPPED)->countAllResults(),
        ];

        return view('admin/orders/index', [
            'title'         => 'Gestión de Pedidos',
            'orders'        => $orders,
            'stats'         => $stats,
            'search'        => $search,
            'statusFilter'  => $statusFilter,
            'paymentFilter' => $paymentFilter,
            'dateFrom'      => $dateFrom,
            'dateTo'        => $dateTo,
            'pager'         => $pager,
        ]);
    }

    public function show(int $id)
    {
        $order = $this->orderModel->getFullOrder($id);

        if (!$order) {
            return redirect()->to('/admin/orders')->with('error', 'Pedido no encontrado.');
        }

        // Obtener datos del cliente
        $customer = null;
        if ($order->user_id) {
            $customer = (new UserModel())->find($order->user_id);
        }

        return view('admin/orders/show', [
            'title'    => 'Pedido ' . $order->order_number,
            'order'    => $order,
            'customer' => $customer,
        ]);
    }

    public function updateStatus(int $id)
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            return redirect()->to('/admin/orders')->with('error', 'Pedido no encontrado.');
        }

        $newStatus = $this->request->getPost('status');
        $validStatuses = [
            OrderModel::STATUS_PENDING,
            OrderModel::STATUS_PROCESSING,
            OrderModel::STATUS_CONFIRMED,
            OrderModel::STATUS_SHIPPED,
            OrderModel::STATUS_DELIVERED,
            OrderModel::STATUS_CANCELLED,
            OrderModel::STATUS_REFUNDED,
        ];

        if (!in_array($newStatus, $validStatuses, true)) {
            return redirect()->back()->with('error', 'Estado no válido.');
        }

        $this->orderModel->updateStatus($id, $newStatus);

        $adminNotes = $this->request->getPost('admin_notes');
        if ($adminNotes !== null) {
            $this->orderModel->skipValidation(true)->update($id, ['admin_notes' => $adminNotes]);
        }

        return redirect()->to("/admin/orders/{$id}")->with('success', 'Estado del pedido actualizado.');
    }

    public function updatePaymentStatus(int $id)
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            return redirect()->to('/admin/orders')->with('error', 'Pedido no encontrado.');
        }

        $newStatus = $this->request->getPost('payment_status');
        $validStatuses = [
            OrderModel::PAYMENT_PENDING,
            OrderModel::PAYMENT_PAID,
            OrderModel::PAYMENT_FAILED,
            OrderModel::PAYMENT_REFUNDED,
            OrderModel::PAYMENT_PARTIALLY_REFUNDED,
        ];

        if (!in_array($newStatus, $validStatuses, true)) {
            return redirect()->back()->with('error', 'Estado de pago no válido.');
        }

        $this->orderModel->updatePaymentStatus($id, $newStatus);

        return redirect()->to("/admin/orders/{$id}")->with('success', 'Estado de pago actualizado.');
    }

    public function addShipment(int $id)
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            return redirect()->to('/admin/orders')->with('error', 'Pedido no encontrado.');
        }

        $rules = [
            'carrier' => 'required|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $shipmentId = $this->shipmentModel->createShipment(
            $id,
            $this->request->getPost('carrier'),
            $this->request->getPost('cost') ?: null
        );

        $trackingNumber = $this->request->getPost('tracking_number');
        if ($trackingNumber) {
            $this->shipmentModel->setTracking(
                $shipmentId,
                $trackingNumber,
                $this->request->getPost('tracking_url') ?: null
            );
        }

        $estimatedDelivery = $this->request->getPost('estimated_delivery');
        if ($estimatedDelivery) {
            $this->shipmentModel->update($shipmentId, ['estimated_delivery' => $estimatedDelivery]);
        }

        // Actualizar estado del pedido a enviado si no lo está
        if (!in_array($order->status, [OrderModel::STATUS_SHIPPED, OrderModel::STATUS_DELIVERED])) {
            $this->orderModel->updateStatus($id, OrderModel::STATUS_SHIPPED);
        }

        return redirect()->to("/admin/orders/{$id}")->with('success', 'Envío registrado exitosamente.');
    }

    public function updateShipmentStatus(int $orderId, int $shipmentId)
    {
        $shipment = $this->shipmentModel->find($shipmentId);

        if (!$shipment || (int) $shipment->order_id !== $orderId) {
            return redirect()->back()->with('error', 'Envío no encontrado.');
        }

        $newStatus = $this->request->getPost('shipment_status');
        $this->shipmentModel->updateStatus($shipmentId, $newStatus);

        // Si se entregó, actualizar la orden también
        if ($newStatus === ShipmentModel::STATUS_DELIVERED) {
            $this->orderModel->updateStatus($orderId, OrderModel::STATUS_DELIVERED);
        }

        return redirect()->to("/admin/orders/{$orderId}")->with('success', 'Estado del envío actualizado.');
    }
}
