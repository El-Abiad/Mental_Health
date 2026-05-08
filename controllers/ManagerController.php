<?php

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/Schedule.php';
require_once __DIR__ . '/../models/Session.php';

class ManagerController extends BaseController
{
    private Schedule $scheduleModel;
    private Session $sessionModel;

    public function __construct()
    {
        parent::__construct();

        $this->scheduleModel = new Schedule($this->db);
        $this->sessionModel  = new Session($this->db);
    }


    public function dashboard(): void
    {
        $this->requireRole('manager');

        $stats      = $this->sessionModel->getStats();
        $upcoming   = $this->scheduleModel->getUpcomingAppointments();
        $cancelled  = $this->sessionModel->getCancelledSessions();
        $unverified = $this->countUnverifiedLicenses();

        $this->view('manager/dashboard', [
            'stats'      => $stats,
            'upcoming'   => $upcoming,
            'cancelled'  => $cancelled,
            'unverified' => $unverified
        ]);
    }


    public function assignTherapist(): void
    {
        $this->requireRole('manager');

        $therapists = $this->scheduleModel->getAllTherapists();
        $patients   = $this->scheduleModel->getAllPatients();

        $message = '';
        $error   = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $patientId   = intval($_POST['patient_id'] ?? 0);
            $therapistId = intval($_POST['therapist_id'] ?? 0);
            $scheduledAt = trim($_POST['scheduled_at'] ?? '');
            $amount      = floatval($_POST['amount'] ?? 200);

            if (
                empty($patientId) ||
                empty($therapistId) ||
                empty($scheduledAt)
            ) {

                $error = 'All fields are required.';
            } else {

                try {

                    $appointmentId = $this->scheduleModel->createAppointment(
                        $patientId,
                        $therapistId,
                        $scheduledAt,
                        $amount
                    );

                    $message = "Appointment #{$appointmentId} created successfully.";
                } catch (Exception $e) {

                    $error = $e->getMessage();
                }
            }
        }

        $this->view('manager/assign_therapist', [
            'therapists' => $therapists,
            'patients'   => $patients,
            'message'    => $message,
            'error'      => $error
        ]);
    }


    public function verifyTherapists(): void
    {
        $this->requireRole('manager');

        $message = '';
        $error   = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $action    = $_POST['action'] ?? '';
            $licenseId = intval($_POST['license_id'] ?? 0);

            if (!$licenseId) {

                $error = 'Invalid license ID.';
            } else {

                switch ($action) {

                    case 'verify':

                        $this->setLicenseStatus($licenseId, 'valid');
                        $message = 'License verified successfully.';
                        break;

                    case 'renew':

                        $newExpiry = trim($_POST['new_expiry'] ?? '');

                        if (empty($newExpiry)) {

                            $error = 'New expiry date is required.';
                        } else {

                            $this->renewLicense($licenseId, $newExpiry);
                            $message = 'License renewed successfully.';
                        }

                        break;

                    case 'revoke':

                        $this->setLicenseStatus($licenseId, 'revoked');
                        $message = 'License revoked successfully.';
                        break;

                    default:

                        $error = 'Unknown action.';
                }
            }
        }

        $therapists = $this->getAllTherapistsWithLicenses();

        $this->view('manager/verify_therapists', [
            'therapists' => $therapists,
            'message'    => $message,
            'error'      => $error
        ]);
    }

    private function setLicenseStatus(int $licenseId, string $status): void
    {
        $profileStatus = ($status === 'valid')
            ? 'active'
            : $status;

        $stmt = $this->db->prepare("
            UPDATE TherapistLicense
            SET Status = ?
            WHERE LicenseId = ?
        ");

        $stmt->bind_param("si", $status, $licenseId);
        $stmt->execute();

        $stmt2 = $this->db->prepare("
            UPDATE TherapistProfile tp
            JOIN TherapistLicense tl
            ON tp.UserId = tl.UserId
            SET tp.LicenseStatus = ?
            WHERE tl.LicenseId = ?
        ");

        $stmt2->bind_param("si", $profileStatus, $licenseId);
        $stmt2->execute();
    }

    private function renewLicense(int $licenseId, string $newExpiry): void
    {
        $stmt = $this->db->prepare("
            UPDATE TherapistLicense
            SET Status = 'valid',
                ExpiryDate = ?
            WHERE LicenseId = ?
        ");

        $stmt->bind_param("si", $newExpiry, $licenseId);
        $stmt->execute();

        $stmt2 = $this->db->prepare("
            UPDATE TherapistProfile tp
            JOIN TherapistLicense tl
            ON tp.UserId = tl.UserId
            SET tp.LicenseStatus = 'active',
                tp.LicenseExpiry = ?
            WHERE tl.LicenseId = ?
        ");

        $stmt2->bind_param("si", $newExpiry, $licenseId);
        $stmt2->execute();
    }

    private function getAllTherapistsWithLicenses(): array
    {
        $sql = "
            SELECT
                u.Id,
                u.FullName,
                u.Email,

                tp.Specialization,
                tp.LicenseStatus,
                tp.LicenseExpiry,

                tl.LicenseId,
                tl.LicenseNumber,
                tl.Issuer,
                tl.ExpiryDate,
                tl.Status AS LicStatus

            FROM Users u

            JOIN TherapistProfile tp
                ON u.Id = tp.UserId

            JOIN UserRoles ur
                ON u.Id = ur.UserId

            JOIN Roles r
                ON ur.RoleId = r.RoleId

            LEFT JOIN TherapistLicense tl
                ON u.Id = tl.UserId

            WHERE r.RoleName = 'therapist'

            ORDER BY tp.LicenseStatus, u.FullName
        ";

        $result = $this->db->query($sql);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    private function countUnverifiedLicenses(): int
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM TherapistLicense
            WHERE Status IN ('pending', 'expired', 'revoked')
        ";

        $result = $this->db->query($sql);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();

        return intval($row['total']);
    }
}
