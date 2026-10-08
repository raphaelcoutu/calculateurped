<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

#[Signature('app:create-superuser {name} {email}')]
#[Description('Crée un superutilisateur et lui envoie un lien de définition du mot de passe.')]
class CreateSuperuserCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $name = trim((string) $this->argument('name'));

        if ($name === '' || mb_strlen($name) > 255 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->components->error('Fournissez un nom (255 caractères maximum) et une adresse courriel valides.');

            return self::FAILURE;
        }

        if (User::query()->where('email', $email)->exists()) {
            $this->components->error('Un compte utilise déjà cette adresse courriel.');

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make(Str::random(64)),
            'is_superuser' => true,
        ]);

        Password::sendResetLink(['email' => $user->email]);

        $this->components->info('Le superutilisateur a été créé. Un lien de définition du mot de passe a été envoyé.');

        return self::SUCCESS;
    }
}
