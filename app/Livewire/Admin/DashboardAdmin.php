<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Rooms;
use App\Models\Bookings;
use App\Models\Bills;
use App\Models\Complaints;
use App\Models\PaymentProofs;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardAdmin extends Component
{
    public $stats = [];
    public $revenueChart = [];
    public $roomStatusChart = [];
    public $bookingTrend = [];
    public $latestBookings = [];
    public $latestComplaints = [];
    public $pendingPaymentProofs = [];

    public function mount()
    {
        // ----- STATISTICS -----
        $this->stats = [
            'total_rooms' => Rooms::count(),
            'available_rooms' => Rooms::where('status', 'available')->count(),
            'total_tenants' => User::where('role', 'penyewa')->count(),
            'active_bookings' => Bookings::whereIn('status', ['menunggu', 'menunggu_verifikasi', 'dikonfirmasi'])->count(),
            'unpaid_bills' => Bills::whereIn('status', ['belum_dibayar', 'overdue'])->count(),
            'monthly_income' => Bills::where('status', 'dibayar')->whereMonth('payment_date', Carbon::now()->month)->sum('amount'),
            'pending_payments' => PaymentProofs::where('status', 'menunggu')->count(),
            'active_complaints' => Complaints::whereIn('status', ['dikirim', 'diproses'])->count(),
        ];

        // ----- 6 MONTH REVENUE CHART -----
        $this->revenueChart = Bills::select(
                DB::raw("DATE_FORMAT(payment_date, '%Y-%m') as month"),
                DB::raw("SUM(amount) as total")
            )
            ->where('status', 'dibayar')
            ->whereNotNull('payment_date')
            ->where('payment_date', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();

         // ----- YEARLY REVENUE CHART (12 MONTHS) -----
        //  grafik penpatan bulanan (1 tahun)
        $yearStart = Carbon::now()->startOfYear();
        $yearEnd = Carbon::now()->endOfYear();
        
        // Digunakan untuk menghitung total pendapatan 
        // tiap bulan berdasarkan tagihan yang sudah dibayar
        $revenueByMonth = Bills::select(
                DB::raw("DATE_FORMAT(payment_date, '%Y-%m') as month"),
                DB::raw("SUM(amount) as total")
            )
            ->where('status', 'dibayar')
            ->whereNotNull('payment_date')
            ->whereBetween('payment_date', [$yearStart, $yearEnd])
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');
        
        // Generate all 12 months with data
        // kode ini memastikan semua 12 bulan dalam satu tahun 
        // tetap tampil grafik, meskipun pada bulan teretmtu tidak ada transaksi
        $this->revenueChart = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthKey = Carbon::now()->startOfYear()->addMonths($i - 1)->format('Y-m');
            $this->revenueChart[] = [
                'month' => $monthKey,
                'total' => $revenueByMonth->get($monthKey, 0)
            ];
        }
        // ----- ROOM STATUS PIE CHART -----
        // grafik status kamar
        $this->roomStatusChart = Rooms::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get()
            ->toArray();

        // ----- 7-DAY BOOKING TREND -----
        // booking 7 hari
        $this->bookingTrend = Bookings::select(
                DB::raw("DATE(created_at) as date"),
                DB::raw("COUNT(*) as total")
            )
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();

        // ----- LATEST TABLES -----
        // latest booking untuk booking baru
        $this->latestBookings = Bookings::with(['user', 'room'])->latest()->limit(5)->get();  
        $this->latestComplaints = Complaints::with(['user', 'room'])->latest()->limit(5)->get();
        $this->pendingPaymentProofs = PaymentProofs::where('status', 'menunggu')
            ->with(['user', 'bill.booking.room'])
            ->latest()
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.dashboard-admin');
    }
}