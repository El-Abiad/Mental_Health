<?php
require_once "../../models/Admin.php";
require_once "../../controllers/UserController.php";
require_once "../../config/db.php";
class AdminController extends UserController
{
    public static function GetAllRoles(mysqli $db): array
    {
        return Admin::GetAllRoles($db);
    }
    public static function DeleteUser(mysqli $db, int $userId): bool
    {
        return Admin::DeleteUser($db, $userId);
    }
    public static function UpdateUser(mysqli $db, int $userId, string $username, string $email, string $fullname, string $phone, int $roleId): bool
    {
        return Admin::UpdateUser($db, $userId, $username, $email, $fullname, $phone, $roleId);
    }
}
?>