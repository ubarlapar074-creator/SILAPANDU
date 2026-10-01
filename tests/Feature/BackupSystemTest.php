<?php

namespace Tests\Feature;

use App\Models\BackupSetting;
use App\Services\BackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupSystemTest extends TestCase
{
    use RefreshDatabase;

    private function service(): BackupService
    {
        return app(BackupService::class);
    }

    public function test_backup_creates_file_and_updates_last_backup_at(): void
    {
        $before = BackupSetting::singleton()->last_backup_at;
        $path = $this->service()->backup();
        $this->assertTrue(Storage::disk('local')->exists($path));
        $full = Storage::disk('local')->path($path);
        $this->assertTrue(filesize($full) > 100);
        $after = BackupSetting::singleton()->fresh()->last_backup_at;
        $this->assertNotNull($after);
        $this->assertTrue($after->greaterThan($before ?? now()->subYear()));
    }

    public function test_backup_generates_usable_sqlite_file(): void
    {
        $path = $this->service()->backup();
        $full = Storage::disk('local')->path($path);
        $this->assertTrue($this->service()->validateSqliteFile($full));
        $pdo = new \PDO('sqlite:' . $full);
        $count = $pdo->query('SELECT COUNT(*) FROM migrations')->fetchColumn();
        $this->assertGreaterThanOrEqual(1, (int) $count);
    }

    public function test_successful_backup_clears_dismiss(): void
    {
        BackupSetting::singleton()->update(['reminder_dismissed_at' => now()]);
        $this->service()->backup();
        $this->assertNull(BackupSetting::singleton()->fresh()->reminder_dismissed_at);
    }

    public function test_restore_valid_backup(): void
    {
        $path = $this->service()->backup();
        $full = Storage::disk('local')->path($path);
        $tmp = tempnam(sys_get_temp_dir(), 'restore_valid');
        copy($full, $tmp);
        $this->service()->restore($tmp);
        $this->assertTrue(true);
        @unlink($tmp);
    }

    public function test_restore_invalid_file_is_rejected_and_db_intact(): void
    {
        $countBefore = \Illuminate\Support\Facades\DB::table('migrations')->count();
        $tmp = tempnam(sys_get_temp_dir(), 'restore_invalid');
        file_put_contents($tmp, 'not a sqlite file corrupt content ' . str_repeat('x', 1000));
        try {
            $this->service()->restore($tmp);
            $this->fail('Should have thrown');
        } catch (\Throwable $e) {
            $this->assertStringContainsString('valid', strtolower($e->getMessage()));
        }
        $countAfter = \Illuminate\Support\Facades\DB::table('migrations')->count();
        $this->assertEquals($countBefore, $countAfter);
        @unlink($tmp);
    }

    public function test_restore_corrupt_header_rejected(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'restore_corrupt');
        file_put_contents($tmp, str_repeat("\0", 500));
        $this->expectException(\Throwable::class);
        $this->service()->restore($tmp);
        @unlink($tmp);
    }

    public function test_reminder_needs_when_never_backed_up(): void
    {
        BackupSetting::singleton()->update(['last_backup_at' => null, 'reminder_dismissed_at' => null]);
        $s = BackupSetting::singleton()->fresh();
        $this->assertTrue($s->needsReminder());
        $this->assertTrue($s->shouldShowWarning());
    }

    public function test_reminder_not_needed_when_recent_backup(): void
    {
        BackupSetting::singleton()->update(['last_backup_at' => now(), 'reminder_dismissed_at' => null]);
        $s = BackupSetting::singleton()->fresh();
        $this->assertFalse($s->needsReminder());
        $this->assertFalse($s->shouldShowWarning());
    }

    public function test_reminder_needed_after_14_days(): void
    {
        BackupSetting::singleton()->update(['last_backup_at' => now()->subDays(14), 'reminder_dismissed_at' => null]);
        $this->assertTrue(BackupSetting::singleton()->fresh()->needsReminder());
        BackupSetting::singleton()->update(['last_backup_at' => now()->subDays(15), 'reminder_dismissed_at' => null]);
        $this->assertTrue(BackupSetting::singleton()->fresh()->needsReminder());
        BackupSetting::singleton()->update(['last_backup_at' => now()->subDays(13), 'reminder_dismissed_at' => null]);
        $this->assertFalse(BackupSetting::singleton()->fresh()->needsReminder());
    }

    public function test_dismiss_hides_warning_for_14d_then_shows_again(): void
    {
        BackupSetting::singleton()->update(['last_backup_at' => null, 'reminder_dismissed_at' => now(), 'reminder_snooze_days' => 14]);
        $s = BackupSetting::singleton()->fresh();
        $this->assertTrue($s->needsReminder());
        $this->assertTrue($s->isDismissed());
        $this->assertFalse($s->shouldShowWarning());

        BackupSetting::singleton()->update(['reminder_dismissed_at' => now()->subDays(15)]);
        $s2 = BackupSetting::singleton()->fresh();
        $this->assertFalse($s2->isDismissed());
        $this->assertTrue($s2->shouldShowWarning());
    }

    public function test_dismiss_with_7_days_option(): void
    {
        BackupSetting::singleton()->update(['last_backup_at' => null, 'reminder_dismissed_at' => now(), 'reminder_snooze_days' => 7]);
        $this->assertTrue(BackupSetting::singleton()->fresh()->isDismissed());
        BackupSetting::singleton()->update(['reminder_dismissed_at' => now()->subDays(8)]);
        $this->assertFalse(BackupSetting::singleton()->fresh()->isDismissed());
    }

    public function test_dismiss_does_not_require_backup(): void
    {
        BackupSetting::singleton()->update(['last_backup_at' => null, 'reminder_dismissed_at' => null]);
        $this->post(route('backups.dismiss'));
        $this->assertNotNull(BackupSetting::singleton()->fresh()->reminder_dismissed_at);
        $this->assertNull(BackupSetting::singleton()->fresh()->last_backup_at);
    }

    public function test_backup_http_updates_last_backup_at(): void
    {
        BackupSetting::singleton()->update(['last_backup_at' => null]);
        $resp = $this->post(route('backups.store'));
        $resp->assertRedirect(route('backups.index'));
        $this->assertNotNull(BackupSetting::singleton()->fresh()->last_backup_at);
    }

    public function test_restore_invalid_via_http_does_not_corrupt_and_shows_error(): void
    {
        $countBefore = \Illuminate\Support\Facades\DB::table('migrations')->count();
        $file = UploadedFile::fake()->create('evil.sqlite', 10, 'application/octet-stream');
        $resp = $this->post(route('backups.restore'), ['backup_file' => $file]);
        $resp->assertRedirect(route('backups.index'));
        $resp->assertSessionHas('error');
        $this->assertEquals($countBefore, \Illuminate\Support\Facades\DB::table('migrations')->count());
    }

    public function test_backups_are_not_auto_deleted(): void
    {
        $p1 = $this->service()->backup();
        sleep(1);
        $p2 = $this->service()->backup();
        $this->assertTrue(Storage::disk('local')->exists($p1));
        $this->assertTrue(Storage::disk('local')->exists($p2));
        $files = $this->service()->listBackups();
        $this->assertGreaterThanOrEqual(2, count($files));
    }
}
