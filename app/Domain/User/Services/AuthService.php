<?php
declare(strict_types=1);

namespace App\Domain\User\Services;

use App\Models\User;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Phone;
use App\Domain\User\ValueObjects\Password;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

final class AuthService
{
    public function register(string $name, Email $email, Phone $phone, Password $password, string $role): array
    {
        // Проверка существования email или телефона
        if (User::where('email', (string)$email)->exists() || User::where('phone', (string)$phone)->exists()) {
            throw new \InvalidArgumentException('User with this email or phone already exists.');
        }

    $user = User::create([
    'name' => $name,
    'email' => $email->getValue(),
    'phone' => $phone->getValue(),
    'password' => $password->getHashedValue(),
     'role' => $request->role ?? 'passenger', // по умолчанию пассажир
    ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function login(Email $email, Password $password): array
    {
        $user = User::where('email', (string)$email)->first();

        if (!$user || !$password->verifyAgainstHash($user->password)) {
            throw new AuthenticationException('Invalid credentials');
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

     public function logout(User $user): void
    {
        $user->tokens()->delete();
    }
}
