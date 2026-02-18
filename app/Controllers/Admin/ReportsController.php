<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class ReportsController extends BaseController
{
    private \CodeIgniter\Database\BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // ─── Reporte de Ventas & Ingresos ────────────────────────────────────────

    public function sales(): string
    {
        $period = $this->request->getGet('period') ?? 'month';
        $dateFrom = $this->request->getGet('date_from');
        $dateTo   = $this->request->getGet('date_to');

        [$startDate, $endDate, $groupFormat, $labelFormat] = $this->resolvePeriod($period, $dateFrom, $dateTo);

        // KPIs del período
        $kpis = $this->db->query("
            SELECT
                COUNT(*) as total_orders,
                COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END), 0) as total_revenue,
                COALESCE(AVG(CASE WHEN payment_status = 'paid' THEN total ELSE NULL END), 0) as avg_order_value,
                COUNT(CASE WHEN payment_status = 'paid' THEN 1 ELSE NULL END) as paid_orders,
                COUNT(CASE WHEN status = 'cancelled' THEN 1 ELSE NULL END) as cancelled_orders,
                COALESCE(SUM(discount), 0) as total_discounts,
                COALESCE(SUM(tax), 0) as total_tax,
                COALESCE(SUM(shipping_cost), 0) as total_shipping
            FROM orders
            WHERE deleted_at IS NULL
              AND created_at >= ? AND created_at <= ?
        ", [$startDate, $endDate])->getRowObject();

        // Tendencia de ingresos agrupada por período
        $trend = $this->db->query("
            SELECT
                DATE_FORMAT(created_at, '{$groupFormat}') as period,
                COUNT(*) as orders,
                COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END), 0) as revenue,
                COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END), 0) as paid_count
            FROM orders
            WHERE deleted_at IS NULL
              AND created_at >= ? AND created_at <= ?
            GROUP BY period
            ORDER BY period ASC
        ", [$startDate, $endDate])->getResultObject();

        // Distribución de estados de pedidos
        $statusBreakdown = $this->db->query("
            SELECT status, COUNT(*) as count, COALESCE(SUM(total), 0) as total
            FROM orders
            WHERE deleted_at IS NULL
              AND created_at >= ? AND created_at <= ?
            GROUP BY status
            ORDER BY count DESC
        ", [$startDate, $endDate])->getResultObject();

        // Ingresos por gateway de pago
        $byGateway = $this->db->query("
            SELECT
                p.gateway,
                COUNT(*) as transactions,
                COALESCE(SUM(p.amount), 0) as revenue
            FROM payments p
            JOIN orders o ON o.id = p.order_id AND o.deleted_at IS NULL
            WHERE p.status = 'approved'
              AND p.created_at >= ? AND p.created_at <= ?
            GROUP BY p.gateway
            ORDER BY revenue DESC
        ", [$startDate, $endDate])->getResultObject();

        // Pedidos por día de la semana
        $byDayOfWeek = $this->db->query("
            SELECT
                DAYOFWEEK(created_at) as dow,
                DAYNAME(created_at) as day_name,
                COUNT(*) as orders,
                COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END), 0) as revenue
            FROM orders
            WHERE deleted_at IS NULL
              AND created_at >= ? AND created_at <= ?
            GROUP BY dow, day_name
            ORDER BY dow ASC
        ", [$startDate, $endDate])->getResultObject();

        // Formatear etiquetas de la tendencia
        foreach ($trend as $row) {
            $row->label = $this->formatPeriodLabel($row->period, $labelFormat, $period);
        }

        return view('admin/reports/sales', [
            'title'          => 'Reporte de Ventas',
            'period'         => $period,
            'dateFrom'       => $dateFrom ?? date('Y-m-d', strtotime($startDate)),
            'dateTo'         => $dateTo   ?? date('Y-m-d', strtotime($endDate)),
            'kpis'           => $kpis,
            'trend'          => $trend,
            'statusBreakdown'=> $statusBreakdown,
            'byGateway'      => $byGateway,
            'byDayOfWeek'    => $byDayOfWeek,
        ]);
    }

    // ─── Reporte de Productos ─────────────────────────────────────────────────

    public function products(): string
    {
        $period   = $this->request->getGet('period') ?? 'month';
        $dateFrom = $this->request->getGet('date_from');
        $dateTo   = $this->request->getGet('date_to');
        // 'paid' = sólo pedidos con pago confirmado (producción)
        // 'all'  = todos excepto fallidos/reembolsados (útil en sandbox/desarrollo)
        $mode     = $this->request->getGet('mode') ?? 'paid';

        [$startDate, $endDate] = $this->resolvePeriod($period, $dateFrom, $dateTo);

        // Condición de pago según modo
        $paymentCond = $mode === 'all'
            ? "o.payment_status NOT IN ('failed', 'refunded')"
            : "o.payment_status = 'paid'";

        // Cuántos pedidos hay en el período con estado pendiente
        // (para mostrar aviso cuando el modo 'paid' da vacío)
        $pendingCount = (int) $this->db->query("
            SELECT COUNT(*) as cnt
            FROM orders o
            WHERE o.deleted_at IS NULL
              AND o.payment_status = 'pending'
              AND o.created_at >= ? AND o.created_at <= ?
        ", [$startDate, $endDate])->getRowObject()->cnt;

        // Top 10 productos por ingresos
        $topByRevenue = $this->db->query("
            SELECT
                oi.product_id,
                oi.name as product_name,
                oi.sku,
                SUM(oi.quantity) as total_qty,
                SUM(oi.total_price) as total_revenue,
                COUNT(DISTINCT oi.order_id) as order_count,
                AVG(oi.unit_price) as avg_price
            FROM order_items oi
            JOIN orders o ON o.id = oi.order_id AND o.deleted_at IS NULL AND {$paymentCond}
            WHERE o.created_at >= ? AND o.created_at <= ?
            GROUP BY oi.product_id, oi.name, oi.sku
            ORDER BY total_revenue DESC
            LIMIT 10
        ", [$startDate, $endDate])->getResultObject();

        // Top 10 productos por unidades vendidas
        $topByQty = $this->db->query("
            SELECT
                oi.product_id,
                oi.name as product_name,
                oi.sku,
                SUM(oi.quantity) as total_qty,
                SUM(oi.total_price) as total_revenue
            FROM order_items oi
            JOIN orders o ON o.id = oi.order_id AND o.deleted_at IS NULL AND {$paymentCond}
            WHERE o.created_at >= ? AND o.created_at <= ?
            GROUP BY oi.product_id, oi.name, oi.sku
            ORDER BY total_qty DESC
            LIMIT 10
        ", [$startDate, $endDate])->getResultObject();

        // Ingresos por categoría
        $byCategory = $this->db->query("
            SELECT
                c.name as category,
                c.id as category_id,
                SUM(oi.total_price) as revenue,
                SUM(oi.quantity) as units_sold,
                COUNT(DISTINCT oi.product_id) as products_count
            FROM order_items oi
            JOIN orders o ON o.id = oi.order_id AND o.deleted_at IS NULL AND {$paymentCond}
            JOIN products p ON p.id = oi.product_id AND p.deleted_at IS NULL
            JOIN categories c ON c.id = p.category_id AND c.deleted_at IS NULL
            WHERE o.created_at >= ? AND o.created_at <= ?
            GROUP BY c.id, c.name
            ORDER BY revenue DESC
        ", [$startDate, $endDate])->getResultObject();

        // Productos sin ventas en el período
        $noSales = $this->db->query("
            SELECT p.id, p.name, p.sku, p.price, p.is_active,
                   COALESCE(s.quantity, 0) as stock
            FROM products p
            LEFT JOIN stock s ON s.product_id = p.id
            WHERE p.deleted_at IS NULL
              AND p.id NOT IN (
                  SELECT DISTINCT oi.product_id
                  FROM order_items oi
                  JOIN orders o ON o.id = oi.order_id AND o.deleted_at IS NULL AND {$paymentCond}
                  WHERE o.created_at >= ? AND o.created_at <= ?
              )
            ORDER BY p.name ASC
            LIMIT 20
        ", [$startDate, $endDate])->getResultObject();

        // Productos con bajo stock
        $lowStock = $this->db->query("
            SELECT p.id, p.name, p.sku, p.price,
                   s.quantity, s.reserved,
                   (s.quantity - s.reserved) as available,
                   s.low_stock_threshold,
                   s.allow_backorder
            FROM products p
            JOIN stock s ON s.product_id = p.id
            WHERE p.deleted_at IS NULL
              AND s.track_inventory = 1
              AND (s.quantity - s.reserved) <= s.low_stock_threshold
            ORDER BY available ASC
            LIMIT 20
        ")->getResultObject();

        // KPI rápido de productos
        $productKpis = $this->db->query("
            SELECT
                COUNT(DISTINCT oi.product_id) as products_sold,
                SUM(oi.quantity) as units_sold,
                SUM(oi.total_price) as gross_revenue
            FROM order_items oi
            JOIN orders o ON o.id = oi.order_id AND o.deleted_at IS NULL AND {$paymentCond}
            WHERE o.created_at >= ? AND o.created_at <= ?
        ", [$startDate, $endDate])->getRowObject();

        return view('admin/reports/products', [
            'title'        => 'Reporte de Productos',
            'period'       => $period,
            'mode'         => $mode,
            'pendingCount' => $pendingCount,
            'dateFrom'     => $dateFrom ?? date('Y-m-d', strtotime($startDate)),
            'dateTo'       => $dateTo   ?? date('Y-m-d', strtotime($endDate)),
            'topByRevenue' => $topByRevenue,
            'topByQty'     => $topByQty,
            'byCategory'   => $byCategory,
            'noSales'      => $noSales,
            'lowStock'     => $lowStock,
            'productKpis'  => $productKpis,
        ]);
    }

    // ─── Reporte de Clientes ──────────────────────────────────────────────────

    public function customers(): string
    {
        $period   = $this->request->getGet('period') ?? 'month';
        $dateFrom = $this->request->getGet('date_from');
        $dateTo   = $this->request->getGet('date_to');

        [$startDate, $endDate, $groupFormat, $labelFormat] = $this->resolvePeriod($period, $dateFrom, $dateTo);

        // KPIs de clientes
        $kpis = $this->db->query("
            SELECT
                (SELECT COUNT(*) FROM users WHERE deleted_at IS NULL AND created_at >= ? AND created_at <= ?) as new_customers,
                (SELECT COUNT(*) FROM users WHERE deleted_at IS NULL) as total_customers,
                (
                    SELECT COUNT(DISTINCT user_id)
                    FROM orders
                    WHERE deleted_at IS NULL AND payment_status = 'paid'
                      AND created_at >= ? AND created_at <= ?
                      AND user_id IS NOT NULL
                ) as active_buyers,
                (
                    SELECT COALESCE(AVG(total_per_user), 0)
                    FROM (
                        SELECT SUM(total) as total_per_user
                        FROM orders
                        WHERE deleted_at IS NULL AND payment_status = 'paid'
                        GROUP BY user_id
                    ) t
                ) as avg_ltv
        ", [$startDate, $endDate, $startDate, $endDate])->getRowObject();

        // Nuevos clientes por período
        $newCustomersTrend = $this->db->query("
            SELECT
                DATE_FORMAT(created_at, '{$groupFormat}') as period,
                COUNT(*) as new_customers
            FROM users
            WHERE deleted_at IS NULL
              AND created_at >= ? AND created_at <= ?
            GROUP BY period
            ORDER BY period ASC
        ", [$startDate, $endDate])->getResultObject();

        foreach ($newCustomersTrend as $row) {
            $row->label = $this->formatPeriodLabel($row->period, $labelFormat, $period);
        }

        // Top 10 clientes por gasto
        $topCustomers = $this->db->query("
            SELECT
                u.id,
                u.first_name,
                u.last_name,
                u.email,
                COUNT(o.id) as order_count,
                SUM(o.total) as total_spent,
                AVG(o.total) as avg_order,
                MAX(o.created_at) as last_order_at
            FROM users u
            JOIN orders o ON o.user_id = u.id AND o.deleted_at IS NULL AND o.payment_status = 'paid'
            WHERE u.deleted_at IS NULL
            GROUP BY u.id, u.first_name, u.last_name, u.email
            ORDER BY total_spent DESC
            LIMIT 10
        ")->getResultObject();

        // Clientes recurrentes vs nuevos (en el período)
        $retention = $this->db->query("
            SELECT
                SUM(CASE WHEN order_count = 1 THEN 1 ELSE 0 END) as one_time,
                SUM(CASE WHEN order_count BETWEEN 2 AND 4 THEN 1 ELSE 0 END) as occasional,
                SUM(CASE WHEN order_count >= 5 THEN 1 ELSE 0 END) as loyal
            FROM (
                SELECT user_id, COUNT(*) as order_count
                FROM orders
                WHERE deleted_at IS NULL
                  AND payment_status = 'paid'
                  AND user_id IS NOT NULL
                GROUP BY user_id
            ) t
        ")->getRowObject();

        // Clientes con pedidos pendientes de entrega
        $pendingDelivery = $this->db->query("
            SELECT
                u.first_name, u.last_name, u.email,
                COUNT(o.id) as pending_orders,
                SUM(o.total) as pending_total,
                MIN(o.created_at) as oldest_order
            FROM orders o
            JOIN users u ON u.id = o.user_id AND u.deleted_at IS NULL
            WHERE o.deleted_at IS NULL
              AND o.status IN ('pending', 'processing', 'confirmed', 'shipped')
            GROUP BY u.id, u.first_name, u.last_name, u.email
            ORDER BY oldest_order ASC
            LIMIT 10
        ")->getResultObject();

        return view('admin/reports/customers', [
            'title'              => 'Reporte de Clientes',
            'period'             => $period,
            'dateFrom'           => $dateFrom ?? date('Y-m-d', strtotime($startDate)),
            'dateTo'             => $dateTo   ?? date('Y-m-d', strtotime($endDate)),
            'kpis'               => $kpis,
            'newCustomersTrend'  => $newCustomersTrend,
            'topCustomers'       => $topCustomers,
            'retention'          => $retention,
            'pendingDelivery'    => $pendingDelivery,
        ]);
    }

    // ─── Reporte de Inventario ────────────────────────────────────────────────

    public function inventory(): string
    {
        $search = $this->request->getGet('search') ?? '';
        $filter = $this->request->getGet('filter') ?? 'all'; // all | low | out | untracked

        // KPIs de inventario
        $kpis = $this->db->query("
            SELECT
                COUNT(p.id) as total_products,
                SUM(s.quantity) as total_units,
                SUM(s.reserved) as total_reserved,
                SUM(CASE WHEN s.quantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
                SUM(CASE WHEN s.track_inventory = 1 AND (s.quantity - s.reserved) <= s.low_stock_threshold AND s.quantity > 0 THEN 1 ELSE 0 END) as low_stock,
                SUM(CASE WHEN s.track_inventory = 0 THEN 1 ELSE 0 END) as untracked
            FROM products p
            JOIN stock s ON s.product_id = p.id
            WHERE p.deleted_at IS NULL
        ")->getRowObject();

        // Stock total valorizado
        $stockValue = $this->db->query("
            SELECT
                COALESCE(SUM(p.cost * s.quantity), 0) as cost_value,
                COALESCE(SUM(p.price * s.quantity), 0) as sale_value
            FROM products p
            JOIN stock s ON s.product_id = p.id
            WHERE p.deleted_at IS NULL AND p.cost > 0
        ")->getRowObject();

        // Lista de productos con stock
        $query = "
            SELECT
                p.id, p.name, p.sku, p.price, p.cost, p.is_active,
                c.name as category,
                s.quantity, s.reserved,
                (s.quantity - s.reserved) as available,
                s.low_stock_threshold,
                s.allow_backorder,
                s.track_inventory
            FROM products p
            LEFT JOIN stock s ON s.product_id = p.id
            LEFT JOIN categories c ON c.id = p.category_id AND c.deleted_at IS NULL
            WHERE p.deleted_at IS NULL
        ";

        $params = [];

        if ($search !== '') {
            $query .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $query .= match ($filter) {
            'low'       => " AND s.track_inventory = 1 AND (s.quantity - s.reserved) <= s.low_stock_threshold AND s.quantity > 0",
            'out'       => " AND s.quantity = 0",
            'untracked' => " AND (s.track_inventory = 0 OR s.id IS NULL)",
            default     => '',
        };

        $query .= " ORDER BY available ASC";

        $stockList = $this->db->query($query, $params)->getResultObject();

        // Últimos 30 movimientos de stock
        $movements = $this->db->query("
            SELECT
                sm.id, sm.type, sm.quantity, sm.quantity_before, sm.quantity_after,
                sm.reference_type, sm.reference_id, sm.created_at,
                p.name as product_name, p.sku,
                u.first_name, u.last_name
            FROM stock_movements sm
            JOIN products p ON p.id = sm.product_id
            LEFT JOIN users u ON u.id = sm.user_id
            ORDER BY sm.created_at DESC
            LIMIT 30
        ")->getResultObject();

        return view('admin/reports/inventory', [
            'title'       => 'Reporte de Inventario',
            'kpis'        => $kpis,
            'stockValue'  => $stockValue,
            'stockList'   => $stockList,
            'movements'   => $movements,
            'search'      => $search,
            'filter'      => $filter,
        ]);
    }

    // ─── Helpers privados ─────────────────────────────────────────────────────

    private function resolvePeriod(string $period, ?string $dateFrom, ?string $dateTo): array
    {
        if ($period === 'custom' && $dateFrom && $dateTo) {
            return [
                $dateFrom . ' 00:00:00',
                $dateTo   . ' 23:59:59',
                '%Y-%m-%d',
                'd/m',
            ];
        }

        return match ($period) {
            'today'  => [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59'), '%Y-%m-%d %H:00', 'H:00'],
            'week'   => [date('Y-m-d 00:00:00', strtotime('-6 days')), date('Y-m-d 23:59:59'), '%Y-%m-%d', 'd/m'],
            'month'  => [date('Y-m-01 00:00:00'), date('Y-m-t 23:59:59'), '%Y-%m-%d', 'd/m'],
            'year'   => [date('Y-01-01 00:00:00'), date('Y-12-31 23:59:59'), '%Y-%m', 'M Y'],
            default  => [date('Y-m-01 00:00:00'), date('Y-m-t 23:59:59'), '%Y-%m-%d', 'd/m'],
        };
    }

    private function formatPeriodLabel(string $period, string $format, string $periodType): string
    {
        $months = ['01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr',
                   '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago',
                   '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic'];

        if ($periodType === 'year') {
            [$y, $m] = explode('-', $period);
            return ($months[$m] ?? $m) . ' ' . $y;
        }

        if ($periodType === 'today') {
            return substr($period, 11, 5); // HH:00
        }

        // week / month / custom → d/m
        $parts = explode('-', $period);
        if (count($parts) === 3) {
            return $parts[2] . '/' . $parts[1];
        }

        return $period;
    }
}
