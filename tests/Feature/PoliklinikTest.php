<?php

namespace Tests\Feature;

use App\Models\Poli;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PoliklinikTest extends TestCase
{
    public function test_guest_is_redirected_when_accessing_admin(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $pasien = User::where('role', 'pasien')->first();
        if (!$pasien) {
            $pasien = User::create([
                'nama' => 'Test Pasien',
                'email' => 'testpasien@example.com',
                'password' => bcrypt('password'),
                'role' => 'pasien',
            ]);
        }

        $response = $this->actingAs($pasien)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_dashboard_and_views(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::create([
                'nama' => 'Test Admin',
                'email' => 'testadmin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);
        }

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);

        // Test Poli Views
        $this->actingAs($admin)->get('/admin/polis')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/polis/create')->assertStatus(200);

        // Test Dokter Views
        $this->actingAs($admin)->get('/admin/dokter')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/dokter/create')->assertStatus(200);

        // Test Pasien Views
        $this->actingAs($admin)->get('/admin/pasien')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/pasien/create')->assertStatus(200);
    }

    public function test_dokter_can_access_dokter_dashboard(): void
    {
        $dokter = User::where('role', 'dokter')->first();
        if (!$dokter) {
            $dokter = User::create([
                'nama' => 'Test Dokter',
                'email' => 'testdokter@example.com',
                'password' => bcrypt('password'),
                'role' => 'dokter',
            ]);
        }

        $response = $this->actingAs($dokter)->get('/dokter/dashboard');
        $response->assertStatus(200);
    }

    public function test_pasien_can_access_pasien_dashboard(): void
    {
        $pasien = User::where('role', 'pasien')->first();
        if (!$pasien) {
            $pasien = User::create([
                'nama' => 'Test Pasien',
                'email' => 'testpasien2@example.com',
                'password' => bcrypt('password'),
                'role' => 'pasien',
            ]);
        }

        $response = $this->actingAs($pasien)->get('/pasien/dashboard');
        $response->assertStatus(200);
    }

    public function test_universal_dashboard_redirect(): void
    {
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $this->actingAs($admin)->get('/dashboard')->assertRedirect(route('admin.dashboard'));
        }

        $dokter = User::where('role', 'dokter')->first();
        if ($dokter) {
            $this->actingAs($dokter)->get('/dashboard')->assertRedirect(route('dokter.dashboard'));
        }

        $pasien = User::where('role', 'pasien')->first();
        if ($pasien) {
            $this->actingAs($pasien)->get('/dashboard')->assertRedirect(route('pasien.dashboard'));
        }
    }

    public function test_pasien_registration_generates_no_rm(): void
    {
        $unique = time();
        $response = $this->post('/register', [
            'nama' => 'Pasien Baru ' . $unique,
            'alamat' => 'Jl. Mawar No. ' . $unique,
            'no_ktp' => '330101' . rand(1000000000, 9999999999),
            'no_hp' => '0812' . rand(10000000, 99999999),
            'email' => "pasien_{$unique}@example.com",
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/login');
        $user = User::where('email', "pasien_{$unique}@example.com")->first();
        $this->assertNotNull($user);
        $this->assertEquals('pasien', $user->role);
        $this->assertNotNull($user->no_rm);
        $this->assertMatchesRegularExpression('/^\d{6}-\d{3,}$/', $user->no_rm);
    }

    public function test_admin_can_create_and_update_pasien_without_retyping_password(): void
    {
        $admin = User::where('role', 'admin')->first();
        $unique = time() . rand(10, 99);

        // Create Patient as Admin
        $response = $this->actingAs($admin)->post('/admin/pasien', [
            'nama' => 'Pasien Admin ' . $unique,
            'alamat' => 'Jl. Pasien Admin',
            'no_ktp' => '330202' . rand(1000000000, 9999999999),
            'no_hp' => '0813' . rand(10000000, 99999999),
            'email' => "pasien_admin_{$unique}@example.com",
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('pasien.index'));
        $pasien = User::where('email', "pasien_admin_{$unique}@example.com")->first();
        $this->assertNotNull($pasien);
        $this->assertNotNull($pasien->no_rm);

        // Edit Patient without password
        $oldPasswordHash = $pasien->password;
        $editResponse = $this->actingAs($admin)->put('/admin/pasien/' . $pasien->id, [
            'nama' => 'Pasien Admin Updated',
            'alamat' => 'Jl. Pasien Admin Updated',
            'no_ktp' => $pasien->no_ktp,
            'no_hp' => $pasien->no_hp,
            'email' => $pasien->email,
            'password' => '', // Empty password should not fail or wipe password
        ]);

        $editResponse->assertRedirect(route('pasien.index'));
        $pasien->refresh();
        $this->assertEquals('Pasien Admin Updated', $pasien->nama);
        $this->assertEquals($oldPasswordHash, $pasien->password);
    }

    public function test_admin_can_create_and_update_poli(): void
    {
        $admin = User::where('role', 'admin')->first();
        $unique = rand(100, 999);

        $response = $this->actingAs($admin)->post('/admin/polis', [
            'nama_poli' => 'Poli ' . $unique,
            'keterangan' => 'Keterangan ' . $unique,
        ]);

        $response->assertRedirect(route('polis.index'));
        $poli = Poli::where('nama_poli', 'Poli ' . $unique)->first();
        $this->assertNotNull($poli);

        $updateResponse = $this->actingAs($admin)->put('/admin/polis/' . $poli->id, [
            'nama_poli' => 'Poli Edit ' . $unique,
            'keterangan' => null,
        ]);

        $updateResponse->assertRedirect(route('polis.index'));
        $poli->refresh();
        $this->assertEquals('Poli Edit ' . $unique, $poli->nama_poli);
    }
}
