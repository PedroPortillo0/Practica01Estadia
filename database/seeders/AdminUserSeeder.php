<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener credenciales de variables de entorno (requeridas)
        $adminEmail = env('ADMIN_EMAIL');
        $adminPassword = env('ADMIN_PASSWORD');
        $adminName = env('ADMIN_NAME', 'Administrador');
        $adminLastname = env('ADMIN_LASTNAME', 'Sistema');
        
        // Validar que las variables de entorno estén definidas
        if (empty($adminEmail) || empty($adminPassword)) {
            $this->command->error('ERROR: Las variables de entorno ADMIN_EMAIL y ADMIN_PASSWORD son requeridas.');
            $this->command->info('');
            $this->command->info('Por favor, agrega estas variables a tu archivo .env:');
            $this->command->info('ADMIN_EMAIL=tu_email@example.com');
            $this->command->info('ADMIN_PASSWORD=tu_contraseña_segura');
            $this->command->info('ADMIN_NAME=Tu Nombre');
            $this->command->info('ADMIN_LASTNAME=Tu Apellido');
            return;
        }
        
        // Asegurar que solo haya un administrador: remover privilegios de otros administradores
        $otherAdmins = User::where('is_admin', true)
            ->where('email', '!=', $adminEmail)
            ->get();
        
        if ($otherAdmins->count() > 0) {
            foreach ($otherAdmins as $otherAdmin) {
                $otherAdmin->update(['is_admin' => false]);
                $this->command->warn("Se removieron privilegios de administrador del usuario: {$otherAdmin->email}");
            }
        }
        
        // Buscar o crear el usuario administrador
        $adminUser = User::where('email', $adminEmail)->first();
        
        if ($adminUser) {
            // Si existe, actualizarlo para que sea el único administrador
            $adminUser->update([
                'nombre' => $adminName,
                'apellidos' => $adminLastname,
                'is_admin' => true,
                'email_verificado' => true,
                'password' => Hash::make($adminPassword),
                'auth_provider' => 'local'
            ]);
            $this->command->info("✓ Usuario administrador actualizado: {$adminEmail}");
        } else {
            // Crear nuevo usuario administrador
            User::create([
                'id' => Str::uuid()->toString(),
                'nombre' => $adminName,
                'apellidos' => $adminLastname,
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'email_verificado' => true, // Email verificado para poder iniciar sesión
                'quiz_completed' => false,
                'is_admin' => true,
                'auth_provider' => 'local',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->command->info("✓ Usuario administrador creado: {$adminEmail}");
        }
        
        // Verificar que solo hay un administrador
        $adminCount = User::where('is_admin', true)->count();
        if ($adminCount === 1) {
            $this->command->info("✓ Sistema configurado correctamente: 1 administrador activo");
        } else {
            $this->command->warn("⚠ Advertencia: Se encontraron {$adminCount} administradores en el sistema");
        }
        
        $this->command->info('');
        $this->command->info('Credenciales de administrador configuradas desde variables de entorno.');
        $this->command->info("Email: {$adminEmail}");
        $this->command->info("Nombre: {$adminName} {$adminLastname}");
    }
}
