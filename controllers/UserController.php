<?php
require_once "../../models/User.php";
require_once "../../config/db.php";
class UserController extends BaseController
{
    public static function GetAllUsers(mysqli $db): array
    {
        return User::getAll($db);
    }
    public static function GetUserById(mysqli $db, int $userId): ?array
    {
        return User::findById($db, $userId);
    }
    public static function GetUserByEmail(mysqli $db, string $email): ?array
    {
        return User::findByEmail($db, $email);
    }
    public static function GetUserRole(mysqli $db, int $userId): string|false
    {
        return User::getRole($db, $userId);
    }
    public static function CreateUser(mysqli $db, string $username, string $email, string $password, string $fullname, string $roleId, string $phone = ''): int
    {
        return User::create($db, $username, $email, $password, $fullname, $roleId, $phone);
    }
    public static function SetActive(mysqli $db, int $userId, bool $isActive): void
    {
        User::SetActive($db, $userId, $isActive);
    }
}
