<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Program;
use App\Models\Course;
use App\Models\Module;
use App\Models\Content;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuario administrador
        $admin = User::create([
            'name' => 'Administrador',
            'last_name' => 'Sistema',
            'email' => 'admin@academia.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'activo',
            'phone' => '999888777',
        ]);

        // Crear profesores de ejemplo
        $profesor1 = User::create([
            'name' => 'Carlos',
            'last_name' => 'Rodriguez',
            'email' => 'carlos.profesor@academia.com',
            'password' => Hash::make('profesor123'),
            'role' => 'profesor',
            'status' => 'activo',
            'phone' => '999111222',
        ]);

        $profesor2 = User::create([
            'name' => 'Maria',
            'last_name' => 'Gonzales',
            'email' => 'maria.profesora@academia.com',
            'password' => Hash::make('profesor123'),
            'role' => 'profesor',
            'status' => 'activo',
            'phone' => '999333444',
        ]);

        // Crear estudiantes de ejemplo
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => "Estudiante $i",
                'last_name' => "Apellido $i",
                'email' => "estudiante$i@academia.com",
                'password' => Hash::make('estudiante123'),
                'role' => 'estudiante',
                'status' => 'activo',
                'dni' => str_pad($i, 8, '7', STR_PAD_LEFT),
                'phone' => '9' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'qr_code' => 'QR-STU-' . str_pad($i, 6, '0', STR_PAD_LEFT),
            ]);
        }

        // Crear programa de ejemplo
        $programa = Program::create([
            'name' => 'Diplomado en Desarrollo Web Full Stack',
            'slug' => 'diplomado-desarrollo-web-full-stack',
            'description' => 'Programa completo de desarrollo web que cubre frontend, backend y bases de datos.',
            'duration_months' => 6,
            'total_hours' => 240,
            'price' => 2500.00,
            'status' => 'activo',
        ]);

        // Crear cursos del programa
        $cursos = [
            [
                'name' => 'HTML, CSS y JavaScript',
                'description' => 'Fundamentos del desarrollo web frontend',
                'order' => 1,
                'duration_hours' => 40,
            ],
            [
                'name' => 'React.js',
                'description' => 'Desarrollo de interfaces modernas con React',
                'order' => 2,
                'duration_hours' => 50,
            ],
            [
                'name' => 'Node.js y Express',
                'description' => 'Backend con JavaScript',
                'order' => 3,
                'duration_hours' => 50,
            ],
            [
                'name' => 'Bases de Datos',
                'description' => 'PostgreSQL y MongoDB',
                'order' => 4,
                'duration_hours' => 40,
            ],
            [
                'name' => 'Proyecto Final',
                'description' => 'Desarrollo de proyecto integrador',
                'order' => 5,
                'duration_hours' => 60,
            ],
        ];

        foreach ($cursos as $cursoData) {
            $curso = Course::create([
                'program_id' => $programa->id,
                'name' => $cursoData['name'],
                'slug' => \Str::slug($cursoData['name']),
                'description' => $cursoData['description'],
                'order' => $cursoData['order'],
                'duration_hours' => $cursoData['duration_hours'],
                'teacher_id' => $cursoData['order'] <= 2 ? $profesor1->id : $profesor2->id,
                'status' => 'activo',
            ]);

            // Crear modulos para cada curso
            for ($m = 1; $m <= 4; $m++) {
                $modulo = Module::create([
                    'course_id' => $curso->id,
                    'name' => "Modulo $m: Tema " . chr(64 + $m),
                    'slug' => \Str::slug("modulo-$m-tema-" . chr(64 + $m)),
                    'description' => "Descripcion del modulo $m del curso {$curso->name}",
                    'order' => $m,
                    'duration_hours' => intval($cursoData['duration_hours'] / 4),
                    'status' => 'activo',
                ]);

                // Crear contenidos para cada modulo
                $tipos = ['video', 'pdf', 'audio'];
                foreach ($tipos as $index => $tipo) {
                    Content::create([
                        'module_id' => $modulo->id,
                        'title' => ucfirst($tipo) . " - {$modulo->name}",
                        'description' => "Contenido de tipo $tipo para el modulo {$modulo->name}",
                        'type' => $tipo,
                        'file_path' => "contenidos/{$curso->slug}/{$modulo->slug}/ejemplo.$tipo",
                        'order' => $index + 1,
                        'duration_minutes' => $tipo == 'video' ? rand(15, 45) : ($tipo == 'audio' ? rand(10, 30) : null),
                        'is_free' => $m == 1 && $index == 0,
                        'status' => 'activo',
                    ]);
                }
            }
        }

        $this->command->info('Base de datos sembrada correctamente!');
        $this->command->info('');
        $this->command->info('Credenciales de acceso:');
        $this->command->info('========================');
        $this->command->info('Admin: admin@academia.com / admin123');
        $this->command->info('Profesor: carlos.profesor@academia.com / profesor123');
        $this->command->info('Estudiante: estudiante1@academia.com / estudiante123');
    }
}
