<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    /**
     * Layanan yang bisa dipilih pada filter tabel dashboard.
     *
     * @var list<string>
     */
    private const FILTERABLE_TYPES = ['kantor', 'bedol', 'andon'];

    public function index()
    {
        // Bersihkan draft kosong yang ditinggalkan operator sebelum tabel dimuat.
        Registration::emptyDrafts()->delete();

        $type = in_array(request()->query('type'), self::FILTERABLE_TYPES, true)
            ? request()->query('type')
            : null;

        $registrations = Registration::with(['parties.warga'])
            ->when($type, function ($query, string $serviceType) {
                $query->where('service_type', $serviceType);
            })
            ->latest()
            ->get();

        $stats = [
            'total' => $this->countThisMonth(),
            'kantor' => $this->countThisMonth('kantor'),
            'bedol' => $this->countThisMonth('bedol'),
            'andon' => $this->countThisMonth('andon'),
            'belum_lengkap' => Registration::where('step', '<', 4)->whereNotIn('status', ['lengkap', 'valid', 'selesai'])->count(),
            'lengkap' => Registration::where(function ($q) {
                $q->where('step', '>=', 4)->orWhereIn('status', ['lengkap', 'valid', 'selesai']);
            })->count(),
            'akad_bulan_ini' => Registration::whereMonth('tanggal_akad', now()->month)->whereYear('tanggal_akad', now()->year)->count(),
        ];

        return view('dashboard.index', [
            'registrations' => $registrations,
            'stats' => $stats,
            'activeType' => $type,
        ]);
    }

    /**
     * Jumlah pendaftaran bulan berjalan, difilter jenis layanannya bila diberikan.
     */
    private function countThisMonth(?string $serviceType = null): int
    {
        return Registration::query()
            ->when($serviceType, fn ($query) => $query->where('service_type', $serviceType))
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }
}
