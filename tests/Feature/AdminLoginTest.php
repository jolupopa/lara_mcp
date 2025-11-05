<?php

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('authenticates admin via admin guard', function (): void {
    // Crear admin con contraseña hasheada
    $admin = Admin::create([
        'name' => 'Admin',
        'email' => 'admin@gmail.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->post('/admin/login', [
        'email' => 'admin@gmail.com',
        'password' => 'password',
        'guard' => 'admin',
    ]);

    // Comprobar que el guard admin está autenticado
    expect(Auth::guard('admin')->check())->toBeTrue();
    expect(Auth::guard('admin')->user()->id)->toBe($admin->id);

    // The POST should redirect to the admin dashboard
    $response->assertRedirect('/admin/dashboard');

    // After login, the admin should be able to access the dashboard
    $this->get('/admin/dashboard')->assertOk();
});
