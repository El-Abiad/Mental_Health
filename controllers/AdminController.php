<?php
require_once "../../models/Admin.php";
require_once "../../controllers/UserController.php";
class AdminController extends UserController
{
    public static function GetAllRoles(): array
    {
        return Admin::GetAllRoles();
    }
    public static function DeleteUser(int $userId): bool
    {
        return Admin::DeleteUser($userId);
    }
    public static function UpdateUser(int $userId, string $username, string $email, string $fullname, string $phone, int $roleId): bool
    {
        return Admin::UpdateUser($userId, $username, $email, $fullname, $phone, $roleId);
    }
    public static function GetAllViolationReports()
    {
        return Admin::GetAllViolationReports();
    }
    public static function ChangeViolationReportStatus(int $reportid, string $status)
    {
        return Admin::ChangeViolationReportStatus($reportid, $status);
    }
    public static function UpdateViolationReport(int $reportid, string $reason, string $status, int $ResolvedBy)
    {
        return Admin::UpdateViolationReport($reportid, $reason, $status, $ResolvedBy);
    }
    public static function GiveWarning(int $userid, string $reason)
    {
        return Admin::GiveWarning($userid, $reason);
    }
    public static function GiveBan(int $userId)
    {
        return Admin::GiveBan($userId);
    }
}
