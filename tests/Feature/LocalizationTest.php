<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_app_uses_pt_br_locale_and_sao_paulo_timezone()
    {
        $this->assertSame('pt_BR', app()->getLocale());
        $this->assertSame('America/Sao_Paulo', config('app.timezone'));
    }

    public function test_registration_validation_errors_are_in_portuguese()
    {
        $response = $this->post(route('register.store'), [
            'name' => '',
            'email' => 'nao-e-email',
            'password' => 'senha-forte-123',
            'password_confirmation' => 'outra-senha',
        ]);

        $response->assertSessionHasErrors([
            'name' => 'O campo nome é obrigatório.',
            'email' => 'O campo e-mail deve ser um endereço de e-mail válido.',
            'password' => 'O campo senha de confirmação não confere.',
        ]);
    }

    public function test_invalid_login_message_is_in_portuguese()
    {
        $user = User::factory()->create();

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'senha-errada',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'Essas credenciais não foram encontradas em nossos registros.',
        ]);
    }

    public function test_flash_messages_are_translated()
    {
        $this->assertSame('Perfil atualizado.', __('Profile updated.'));
        $this->assertSame('Senha atualizada.', __('Password updated.'));
    }
}
