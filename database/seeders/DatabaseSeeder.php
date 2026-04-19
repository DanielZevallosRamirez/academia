<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Program;
use App\Models\Course;
use App\Models\Module;
use App\Models\Content;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario administrador
        $admin = User::create([
            'name' => 'Administrador',
            'last_name' => 'Sistema',
            'email' => 'admin@academia.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'activo',
            'phone' => '999888777',
            'qr_code' => Str::uuid(),
        ]);

        // Crear profesor
        $profesor = User::create([
            'name' => 'Carlos',
            'last_name' => 'Rodriguez',
            'email' => 'profesor@academia.com',
            'password' => Hash::make('password'),
            'role' => 'profesor',
            'status' => 'activo',
            'phone' => '999777666',
            'qr_code' => Str::uuid(),
        ]);

        // Crear estudiantes de ejemplo
        $estudiante1 = User::create([
            'name' => 'Maria',
            'last_name' => 'Garcia',
            'email' => 'estudiante@academia.com',
            'password' => Hash::make('password'),
            'role' => 'estudiante',
            'status' => 'activo',
            'phone' => '999666555',
            'dni' => '12345678',
            'qr_code' => Str::uuid(),
        ]);

        $estudiante2 = User::create([
            'name' => 'Juan',
            'last_name' => 'Lopez',
            'email' => 'juan@academia.com',
            'password' => Hash::make('password'),
            'role' => 'estudiante',
            'status' => 'activo',
            'phone' => '999555444',
            'dni' => '87654321',
            'qr_code' => Str::uuid(),
        ]);

        // Crear programa
        $program = Program::create([
            'name' => 'Diplomado en Desarrollo Web Full Stack',
            'slug' => 'diplomado-desarrollo-web-full-stack',
            'description' => 'Programa completo de desarrollo web que cubre frontend, backend y bases de datos.',
            'duration_months' => 6,
            'total_hours' => 240,
            'price' => 2500.00,
            'status' => 'activo',
        ]);

        // Crear cursos
        $curso1 = Course::create([
            'program_id' => $program->id,
            'teacher_id' => $profesor->id,
            'name' => 'HTML, CSS y JavaScript',
            'slug' => 'html-css-javascript',
            'description' => 'Fundamentos del desarrollo web frontend.',
            'order' => 1,
            'duration_hours' => 40,
            'status' => 'activo',
        ]);

        $curso2 = Course::create([
            'program_id' => $program->id,
            'teacher_id' => $profesor->id,
            'name' => 'PHP y Laravel',
            'slug' => 'php-laravel',
            'description' => 'Desarrollo backend con PHP y el framework Laravel.',
            'order' => 2,
            'duration_hours' => 60,
            'status' => 'activo',
        ]);

        // Crear modulos para curso 1
        $modulo1 = Module::create([
            'course_id' => $curso1->id,
            'name' => 'Introduccion a HTML',
            'slug' => 'introduccion-html',
            'description' => 'Aprende las bases de HTML.',
            'order' => 1,
            'duration_hours' => 10,
            'status' => 'activo',
        ]);

        $modulo2 = Module::create([
            'course_id' => $curso1->id,
            'name' => 'Estilos con CSS',
            'slug' => 'estilos-css',
            'description' => 'Aprende a dar estilo a tus paginas.',
            'order' => 2,
            'duration_hours' => 15,
            'status' => 'activo',
        ]);

        // Crear contenidos para modulo 1
        Content::create([
            'module_id' => $modulo1->id,
            'title' => 'Video: Que es HTML',
            'description' => 'Introduccion al lenguaje HTML.',
            'type' => 'video',
            'file_path' => 'contenidos/html/intro.mp4',
            'order' => 1,
            'duration_minutes' => 15,
            'is_free' => true,
            'status' => 'activo',
        ]);

        Content::create([
            'module_id' => $modulo1->id,
            'title' => 'PDF: Estructura basica HTML',
            'description' => 'Documento con la estructura basica de un archivo HTML.',
            'type' => 'pdf',
            'file_path' => 'contenidos/html/estructura.pdf',
            'order' => 2,
            'duration_minutes' => 0,
            'is_free' => false,
            'status' => 'activo',
        ]);

        Content::create([
            'module_id' => $modulo1->id,
            'title' => 'Video: Etiquetas principales',
            'description' => 'Las etiquetas mas usadas en HTML.',
            'type' => 'video',
            'file_path' => 'contenidos/html/etiquetas.mp4',
            'order' => 3,
            'duration_minutes' => 25,
            'is_free' => false,
            'status' => 'activo',
        ]);

        // Crear contenidos para modulo 2
        Content::create([
            'module_id' => $modulo2->id,
            'title' => 'Video: Introduccion a CSS',
            'description' => 'Que es CSS y como funciona.',
            'type' => 'video',
            'file_path' => 'contenidos/css/intro.mp4',
            'order' => 1,
            'duration_minutes' => 20,
            'is_free' => true,
            'status' => 'activo',
        ]);

        Content::create([
            'module_id' => $modulo2->id,
            'title' => 'PDF: Selectores CSS',
            'description' => 'Guia completa de selectores CSS.',
            'type' => 'pdf',
            'file_path' => 'contenidos/css/selectores.pdf',
            'order' => 2,
            'duration_minutes' => 0,
            'is_free' => false,
            'status' => 'activo',
        ]);

        $this->command->info('Base de datos poblada exitosamente!');
        $this->command->info('');
        $this->command->info('Usuarios creados:');
        $this->command->info('  Admin: admin@academia.com / password');
        $this->command->info('  Profesor: profesor@academia.com / password');
        $this->command->info('  Estudiante: estudiante@academia.com / password');
    }
}