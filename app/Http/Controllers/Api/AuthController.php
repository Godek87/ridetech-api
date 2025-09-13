<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domain\User\Services\AuthService;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Phone;
use App\Domain\User\ValueObjects\Password;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

final class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware('auth:sanctum')->only(['logout']);
    }

 public function register(RegisterRequest $request): JsonResponse
{
    $data = $request->validated();

    try {
        $email = new Email($data['email']);
        $phone = new Phone($data['phone']);
        $password = new Password($data['password']);

        $result = $this->authService->register(
            (string) $data['name'],
            $email,
            $phone,
            $password,
            (string) $data['role']
        );

        return (new UserResource($result['user']))
            ->additional(['meta' => ['token' => $result['token']]])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);

    } catch (QueryException $e) {
        return response()->json(['message' => 'User creation failed'], Response::HTTP_CONFLICT);
    } catch (\InvalidArgumentException $e) {
        return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
    } catch (\Throwable $e) {
        \Log::error($e->getMessage(), ['exception' => $e]);
        return response()->json(['message' => 'Server error', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

public function login(LoginRequest $request): JsonResponse
{
    $data = $request->validated();

    try {
        $email = new Email($data['email']);
        $password = new Password($data['password']);

        $result = $this->authService->login($email, $password);

        return (new UserResource($result['user']))
            ->additional(['meta' => ['token' => $result['token']]])
            ->response()
            ->setStatusCode(Response::HTTP_OK);

    } catch (AuthenticationException $e) {
        return response()->json(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
    } catch (\InvalidArgumentException $e) {
        return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Server error',
            'error' => $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}



    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user === null) {
            return response()->json(['message' => 'Unauthenticated'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $this->authService->logout($user);
            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
