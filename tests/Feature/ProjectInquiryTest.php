<?php

use App\Enums\ProjectTimeframe;
use Livewire\Livewire;

// --- Portada ---

test('el cta principal de la portada lleva al formulario y ya no abre WhatsApp', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee(route('contact'));
    $response->assertDontSee('Solicitar Cotización');
    $response->assertDontSee('Hablemos de tu proyecto');
});

test('WhatsApp sigue disponible en la portada como salida secundaria', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('wa.me', false);
});

// --- Formulario público ---

test('un visitante sin cuenta puede abrir el formulario de contacto', function () {
    $response = $this->get(route('contact'));

    $response->assertOk();
    $response->assertSee('Cuéntanos qué quieres construir');
});

test('un visitante puede enviar lo que quiere construir sin que nadie intervenga', function () {
    Livewire::test('pages::contact.index')
        ->set('name', 'Ana López')
        ->set('email', 'ana@example.com')
        ->set('company', 'Hotel La Piedra')
        ->set('projectDescription', 'Un sistema para controlar las salidas de tours y sus cupos.')
        ->set('timeframe', ProjectTimeframe::NextThreeMonths->value)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('submitted', true);

    $this->assertDatabaseHas('project_inquiries', [
        'name' => 'Ana López',
        'email' => 'ana@example.com',
        'company' => 'Hotel La Piedra',
        'project_description' => 'Un sistema para controlar las salidas de tours y sus cupos.',
        'timeframe' => ProjectTimeframe::NextThreeMonths->value,
    ]);
});

test('la empresa es opcional y se guarda vacía como nula', function () {
    Livewire::test('pages::contact.index')
        ->set('name', 'Beto Ramírez')
        ->set('email', 'beto@example.com')
        ->set('projectDescription', 'Una app interna para registrar las visitas guiadas.')
        ->set('timeframe', ProjectTimeframe::Exploring->value)
        ->call('submit')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('project_inquiries', [
        'email' => 'beto@example.com',
        'company' => null,
    ]);
});

test('el formulario exige nombre, correo, descripción y plazo', function () {
    Livewire::test('pages::contact.index')
        ->call('submit')
        ->assertHasErrors([
            'name' => 'required',
            'email' => 'required',
            'projectDescription' => 'required',
            'timeframe' => 'required',
        ]);

    $this->assertDatabaseCount('project_inquiries', 0);
});

test('una descripción demasiado corta no se guarda', function () {
    Livewire::test('pages::contact.index')
        ->set('name', 'Carla Díaz')
        ->set('email', 'carla@example.com')
        ->set('projectDescription', 'Una app')
        ->set('timeframe', ProjectTimeframe::ThisYear->value)
        ->call('submit')
        ->assertHasErrors(['projectDescription' => 'min']);

    $this->assertDatabaseCount('project_inquiries', 0);
});

test('un plazo fuera de la lista no se acepta', function () {
    Livewire::test('pages::contact.index')
        ->set('name', 'Dan Ortiz')
        ->set('email', 'dan@example.com')
        ->set('projectDescription', 'Un portal para reservar cabañas con pago en línea.')
        ->set('timeframe', 'cuando-sea')
        ->call('submit')
        ->assertHasErrors(['timeframe' => 'in']);

    $this->assertDatabaseCount('project_inquiries', 0);
});

test('al enviar, la pantalla confirma el registro y deja WhatsApp como salida', function () {
    Livewire::test('pages::contact.index')
        ->set('name', 'Elena Cruz')
        ->set('email', 'elena@example.com')
        ->set('projectDescription', 'Un panel para administrar los guías y sus horarios.')
        ->set('timeframe', ProjectTimeframe::AsSoonAsPossible->value)
        ->call('submit')
        ->assertSee('Tu mensaje quedó registrado')
        ->assertSee('Continuar por WhatsApp');
});
