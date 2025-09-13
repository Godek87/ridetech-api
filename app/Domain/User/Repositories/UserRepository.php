<?php
declare(strict_types=1);

namespace App\Domain\User\Repositories;

use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Phone;
use App\Domain\User\ValueObjects\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

/**
 * Репозиторий пользователей.
 *
 * Отвечает за операции персистенции модели User.
 * - findByEmail — получение пользователя по Email VO;
 * - createFromData — создание новой записи на основе value-objects (типобезопасно);
 * - save — сохранение/обновление уже созданной модели (принимает экземпляр User).
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * Найти пользователя по email.
     *
     * Использует Email VO для гарантии нормализованного значения (lowercase/trim).
     * Возвращает Eloquent-модель User или null, если пользователь не найден.
     *
     * @param Email $email value-object email
     * @return User|null
     */
    public function findByEmail(Email $email): ?User
    {
        return User::where('email', $email->getValue())->first();
    }

    /**
     * Создать нового пользователя в БД на основе типобезопасных value-objects.
     *
     * Почему так:
     * - Принимая Email/Phone/Password VO и примитивное имя/роль, мы явно знаем
     *   ожидаемые типы и формат данных, уменьшаем шанс опечатки в ключе массива.
     * - В рамках DDD фабрика сущности может остаться в домене (User::createFromData),
     *   репозиторий отвечает только за персистенцию (транзакция, события, и т.д.).
     *
     * @param string   $name     Имя пользователя
     * @param Email    $email    Value-object Email
     * @param Phone    $phone    Value-object Phone
     * @param Password $password Value-object Password (может предоставлять хеш или plain в зависим. от реализации)
     * @param string   $role     Роль пользователя
     * @return User
     */
    public function createFromData(string $name, Email $email, Phone $phone, Password $password, string $role): User
    {
        return DB::transaction(function () use ($name, $email, $phone, $password, $role): User {
            // Используем фабричный метод доменной сущности — он инкапсулирует создание модели.
            // Если в проекте вы предпочитаете, чтобы репозиторий создавал сущность напрямую,
            // замените вызов на соответствующую логику.
            return User::createFromData($name, $email, $phone, $password, $role);
        });
    }

    /**
     * Сохранить (insert/update) переданную сущность User.
     *
     * Удобно, когда бизнес-логика формирует модель (фабрика в домене) и передаёт её репозиторию на сохранение.
     * Обёрнуто в транзакцию — при необходимости можно расширить логикой событий/валидаторов.
     *
     * @param User $user
     * @return User возвращает актуальную модель (с id, timestamps и т.д.)
     */
    public function save(User $user): User
    {
        DB::transaction(function () use ($user): void {
            $user->save();
        });

        return $user;
    }

    /**
     * Создать нового пользователя из ассоциативного массива атрибутов.
     *
     * Реализует метод интерфейса UserRepositoryInterface::create(array).
     * Оставлен для совместимости с кодом, который может передавать "сырые" данные.
     *
     * @param array<string,mixed> $data
     * @return User
     *
     * @throws \RuntimeException при ошибке персистенции
     */
    public function create(array $data): User
    {
        try {
            return DB::transaction(function () use ($data): User {
                return User::create($data);
            });
        } catch (QueryException $e) {
            throw new \RuntimeException('Failed to create user: ' . $e->getMessage(), 0, $e);
        }
    }
}
