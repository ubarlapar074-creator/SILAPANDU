<?php

namespace App\Services;

use App\Models\Registration;
use Carbon\Carbon;

class MarriageRequirementService
{
    /**
     * Batas usia N5.
     */
    private const N5_MINIMUM_AGE = 21;

    /**
     * Evaluasi kebutuhan N5/N6/surat kematian berdasarkan data yang sudah tersimpan di DB (SSOT).
     * Dipakai di RegistrationDocumentController untuk gate cetak N5/N6.
     * N5: catin < 21 tahun pada tanggal nikah. N6: ayah wanita meninggal / wali bukan ayah kandung.
     */
    public function evaluate(Registration $registration): array
    {
        $registration->loadMissing([
            'catins',
            'catins.parents',
            'guardian.parent',
            'witnesses',
        ]);

        $pria = $registration->catins
            ->firstWhere('role', 'PRIA');

        $wanita = $registration->catins
            ->firstWhere('role', 'WANITA');

        $marriageDate = $registration->marriage_date
            ? Carbon::parse($registration->marriage_date)
            : null;

        /*
        |--------------------------------------------------------------------------
        | N5 (Batas Usia Pengantin di Bawah 21 Tahun)
        |--------------------------------------------------------------------------
        */

        $n5Required = false;
        $n5Reasons = [];

        $agePria = null;
        $ageWanita = null;

        if ($marriageDate) {

            if ($pria?->birth_date) {

                $agePria = Carbon::parse(
                    $pria->birth_date
                )->diffInYears($marriageDate);

                if ($agePria < self::N5_MINIMUM_AGE) {

                    $n5Required = true;

                    $n5Reasons[] = [
                        'role' => 'PRIA',
                        'name' => $pria->name,
                        'age' => $agePria,
                        'reason' =>
                            'Catin pria berusia di bawah 21 tahun pada tanggal pernikahan.',
                    ];
                }
            }

            if ($wanita?->birth_date) {

                $ageWanita = Carbon::parse(
                    $wanita->birth_date
                )->diffInYears($marriageDate);

                if ($ageWanita < self::N5_MINIMUM_AGE) {

                    $n5Required = true;

                    $n5Reasons[] = [
                        'role' => 'WANITA',
                        'name' => $wanita->name,
                        'age' => $ageWanita,
                        'reason' =>
                            'Catin wanita berusia di bawah 21 tahun pada tanggal pernikahan.',
                    ];
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | N6 (Wali Berdasarkan Ayah Meninggal atau Wali Selain Ayah Kandung)
        |--------------------------------------------------------------------------
        */

        $fatherWanita = $wanita?->parents
            ?->firstWhere('role', 'AYAH');

        $guardian = $registration->guardian;

        $guardianIsFather = false;

        if (
            $guardian
            && $guardian->guardian_source === 'PARENT'
            && $guardian->relationship === 'AYAH_KANDUNG'
            && $fatherWanita
            && $fatherWanita->status === 'HIDUP'
            && $guardian->parent_id == $fatherWanita->id
        ) {
            $guardianIsFather = true;
        }

        $n6Required = false;
        $n6Reason = null;

        // Perbaikan Logika N6 yang lebih akurat:
        if (!$fatherWanita) {
            $n6Required = true;
            $n6Reason = 'Data Ayah Catin Wanita tidak ditemukan sehingga status wali perlu ditindaklanjuti.';
        } elseif ($fatherWanita->status === 'MENINGGAL') {
            $n6Required = true;
            $n6Reason = 'Ayah kandung Catin Wanita berstatus meninggal sehingga diperlukan N6.';
        } elseif (!$guardianIsFather) {
            $n6Required = true;
            $n6Reason = 'Wali yang dipilih bukan Ayah Kandung sehingga diperlukan N6.';
        } else {
            // Jika ayah hidup dan wali adalah ayah kandung, pastikan N6 false
            $n6Required = false;
            $n6Reason = null;
        }

        /*
        |--------------------------------------------------------------------------
        | SURAT KEMATIAN ORANG TUA (Dibatasi khusus Ayah Kandung Catin Wanita)
        |--------------------------------------------------------------------------
        */

        $deathParents = [];

        foreach ($registration->catins as $catin) {
            foreach ($catin->parents as $parent) {
                if (strtoupper(trim((string) $parent->status)) !== 'MENINGGAL' && strtoupper(trim((string) $parent->status)) !== 'WAFAT') continue;
                $deathParents[] = [
                    'catin_id' => $catin->id,
                    'catin_role' => $catin->role,
                    'catin_name' => $catin->name,
                    'parent_id' => $parent->id,
                    'parent_role' => $parent->role,
                    'parent_name' => $parent->display_name,
                    'parent_name_raw' => $parent->getAttributes()['name'] ?? $parent->name,
                    'status' => $parent->status,
                ];
            }
        }

        return [

            'n5' => [
                'required' => $n5Required,

                'status' => $n5Required
                    ? 'PENDING'
                    : 'NOT_REQUIRED',

                'age' => [
                    'pria' => $agePria,
                    'wanita' => $ageWanita,
                ],

                'reasons' => $n5Reasons,
            ],

            'n6' => [
                'required' => $n6Required,

                'status' => $n6Required
                    ? 'PENDING'
                    : 'NOT_REQUIRED',

                'reason' => $n6Reason,

                'guardian' => [
                    'source' => $guardian?->guardian_source,
                    'relationship' => $guardian?->relationship,
                    'parent_id' => $guardian?->parent_id,
                    'is_father' => $guardianIsFather,
                ],

                'father' => [
                    'id' => $fatherWanita?->id,
                    'name' => $fatherWanita?->display_name,
                    'name_raw' => $fatherWanita ? ($fatherWanita->getAttributes()['name'] ?? $fatherWanita->name) : null,
                    'status' => $fatherWanita?->status,
                ],
            ],

            'death' => [
                'required' => !empty($deathParents),

                'status' => !empty($deathParents)
                    ? 'PENDING'
                    : 'NOT_REQUIRED',

                'parents' => $deathParents,
            ],
        ];
    }
}