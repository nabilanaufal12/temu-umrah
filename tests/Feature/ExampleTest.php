<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
use App\Models\Registration;
use App\Models\User;
use App\Models\Event;
use App\Models\Ormawa;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * A basic test example.
     */
    public function test_guest_is_sent_to_login_before_opening_the_application(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
        $this->get(route('login'))->assertOk();
    }

    public function test_authenticated_student_can_open_the_catalog(): void
    {
        $student = User::findOrFail(7);

        $this->actingAs($student)->get(route('katalog.index'))->assertOk();
    }

    public function test_active_student_can_register_for_a_free_event_once(): void
    {
        $student = User::findOrFail(7);

        $response = $this->actingAs($student)->postJson(
            route('event.register', ['slug' => 'seminar-kemaritiman-blue-economy-kepri'])
        );

        $response
            ->assertOk()
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('registrations', [
            'user_id' => $student->id,
            'event_id' => 4,
            'status' => 'terkonfirmasi',
        ]);

        $this->assertSame(1, Registration::where('user_id', $student->id)->where('event_id', 4)->count());

        $duplicate = $this->actingAs($student)->postJson(
            route('event.register', ['slug' => 'seminar-kemaritiman-blue-economy-kepri'])
        );

        $duplicate->assertStatus(422)->assertJsonPath('status', 'error');
    }

    public function test_role_middleware_blocks_wrong_dashboard_access(): void
    {
        $student = User::findOrFail(7);
        $panitia = User::findOrFail(2);
        $admin = User::findOrFail(1);

        $this->actingAs($student)->get(route('admin.panel'))->assertForbidden();
        $this->actingAs($student)->get(route('panitia.dashboard'))->assertForbidden();
        $this->actingAs($panitia)->get(route('panitia.dashboard'))->assertOk();
        $this->actingAs($panitia)->get(route('panitia.peserta', ['id' => 1]))->assertNotFound();
        $this->actingAs($panitia)->get(route('panitia.peserta', ['id' => 2]))->assertOk();
        $this->actingAs($admin)->get(route('admin.panel'))->assertOk();
    }

    public function test_admin_can_approve_pending_event_and_publish_it(): void
    {
        $admin = User::findOrFail(1);
        $event = Event::create([
            'ormawa_id' => 1,
            'judul' => 'Event Uji Moderasi Admin',
            'slug' => 'event-uji-moderasi-admin',
            'kategori' => 'SEMINAR',
            'tipe_biaya' => 'gratis',
            'harga' => 0,
            'kuota_maks' => 50,
            'kuota_terisi' => 0,
            'tanggal_kegiatan' => '2026-12-10',
            'waktu_mulai' => '09:00:00',
            'waktu_selesai' => '11:00:00',
            'lokasi' => 'Ruang Uji UMRAH',
            'deskripsi' => 'Event untuk menguji alur moderasi Admin.',
            'status' => 'pending_approval',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.event.approve', $event));

        $response->assertOk()->assertJsonPath('status', 'success');
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'status' => 'published',
            'disetujui_oleh' => $admin->id,
        ]);
    }

    public function test_admin_approval_activates_pending_ormawa_account(): void
    {
        $admin = User::findOrFail(1);
        $ormawa = Ormawa::findOrFail(1);
        $ormawa->update(['status_verifikasi' => 'pending']);
        User::findOrFail($ormawa->user_id)->update(['status' => 'nonaktif']);

        $response = $this->actingAs($admin)->postJson(
            route('admin.ormawa.approve', ['id' => $ormawa->id])
        );

        $response->assertOk()->assertJsonPath('status', 'success');
        $this->assertDatabaseHas('ormawa', [
            'id' => $ormawa->id,
            'status_verifikasi' => 'verified',
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $ormawa->user_id,
            'status' => 'aktif',
        ]);
    }
}
