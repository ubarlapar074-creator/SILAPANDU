<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class ResidentsController extends Controller
{
    public function __invoke(Request $request)
    {
        // ?q= dari pencarian global di header, ?search= dari pencarian di halaman ini
        $search = $request->query('q') ?? $request->query('search');

        $wargas = Warga::when($search, function ($q, $s) {
            $q->where('nik', 'like', "%{$s}%")
                ->orWhere('nama_lengkap', 'like', "%{$s}%");
        })->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('residents.index', compact('wargas', 'search'));
    }

    /**
     * Simpan data warga baru, atau perbarui warga yang sudah ada (route PUT {warga}).
     */
    public function store(Request $request, ?Warga $warga = null): JsonResponse
    {
        $validated = $request->validate([
            'nik' => [
                'required', 'string', 'size:16',
                Rule::unique('wargas', 'nik')->ignore($warga?->id),
            ],
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'nullable|string|max:100',
            'tgl_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'nullable|string|max:50',
            'pekerjaan' => 'nullable|string|max:100',
            'alamat' => 'nullable|string|max:255',
            'rt' => 'nullable|string|max:3',
            'rw' => 'nullable|string|max:3',
            'status_kawin' => 'nullable|string|max:50',
        ]);

        if ($warga !== null) {
            $warga->update($validated);
        } else {
            $warga = Warga::create($validated);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data warga berhasil disimpan.',
            'data' => $warga,
        ]);
    }

    public function destroy(Warga $warga): JsonResponse
    {
        $warga->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data warga berhasil dihapus.',
        ]);
    }
}
