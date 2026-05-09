<?php

class User
{
    public static function findByEmail(string $email): array|null
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM Users WHERE Email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public static function findByUsername(string $username): array|null
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM Users WHERE Username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public static function findById(int $id): array|null
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM Users WHERE Id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public static function getRole(int $userId): string|false
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            SELECT r.RoleName 
            FROM Roles r
            JOIN UserRoles ur ON r.RoleId = ur.RoleId
            WHERE ur.UserId = ?
            LIMIT 1
        ');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? $row['RoleName'] : false;
    }

    public static function create(string $username, string $email, string $password, string $fullname, string $roleId, string $phone = ''): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            INSERT INTO Users (Username, Email, Password, FullName, Phone) 
            VALUES (?, ?, ?, ?, ?)
        ');
        $stmt->bind_param('sssss', $username, $email, $password, $fullname, $phone);
        $stmt->execute();
        $userId = $stmt->insert_id;

        $roleStmt = $db->prepare('
            INSERT INTO UserRoles (UserId, RoleId) VALUES (?, ?)
        ');
        $roleStmt->bind_param('ii', $userId, $roleId);
        $roleStmt->execute();
        if ($roleId == 3) {
            $profileStmt = $db->prepare('
                INSERT INTO PatientProfile (UserId) VALUES (?)
                
            ');
            $profileStmt->bind_param('i', $userId);
        }
        if ($roleId == 1) {
            $adminlevel = 1;
            $profileStmt = $db->prepare('INSERT INTO adminprofile values(?,?)');
            $profileStmt->bind_param("ii", $userId, $adminlevel);
        }
        $profileStmt->execute();

        return $userId;
    }

    public static function getAll(): array
    {
        $db = Database::getConnection();
        $result = $db->query('
            SELECT u.*, r.RoleName 
            FROM Users u
            LEFT JOIN UserRoles ur ON u.Id = ur.UserId
            LEFT JOIN Roles r ON ur.RoleId = r.RoleId
            ORDER BY u.CreatedAt ASC
        ');
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function setActive(int $userId, bool $status): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('UPDATE Users SET IsActive = ? WHERE Id = ?');
        $active = $status ? 1 : 0;
        $stmt->bind_param('ii', $active, $userId);
        $stmt->execute();
    }
}
