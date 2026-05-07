<?php
require_once "User.php";
class Admin extends User
{
    public static function GetAllRoles(mysqli $db): array
    {
        $result = $db->query('SELECT * FROM Roles');
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public static function DeleteUser(mysqli $db, int $userId): bool
    {
        $stmt = $db->prepare('DELETE FROM Users WHERE Id = ?');
        $stmt->bind_param('i', $userId);
        return $stmt->execute();
    }
    public static function UpdateUser(mysqli $db, int $userId, string $username, string $email, string $fullname, string $phone, int $roleId): bool
    {
        $stmt = $db->prepare('
            UPDATE Users 
            SET Username = ?, Email = ?, FullName = ?, Phone = ? 
            WHERE Id = ?
        ');
        $stmt->bind_param('ssssi', $username, $email, $fullname, $phone, $userId);
        $rolestmt = $db->prepare('
            UPDATE UserRoles 
            SET RoleId = ? 
            WHERE UserId = ?
            ');
        $rolestmt->bind_param('ii', $roleId, $userId);
        $rolestmt->execute();
        return $stmt->execute();
    }
}
