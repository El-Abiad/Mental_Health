<?php include __DIR__ . '/../shared/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manager Dashboard</title>
  <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
  <div class="container-fluid px-4 py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Manager Dashboard</h2>
    <span class="text-muted">Welcome, <?= htmlspecialchars($_SESSION['full_name'] ?? 'Manager') ?></span>
  </div>
  <div class="row g-3 mb-4">
    <?php foreach ([['Total','total','primary'],['Scheduled','scheduled','warning'],['Live Now','live','success'],['Cancelled','cancelled','danger']] as [$label,$key,$color]): ?>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-1 fw-bold text-<?= $color ?>"><?= (int)($stats[$key] ?? 0) ?></div>
        <div class="text-muted"><?= $label ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <a href="manager_run.php?action=assignTherapist" class="card border-0 shadow-sm text-decoration-none h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="fs-2">📅</div>
          <div><div class="fw-semibold">Assign Therapist</div><div class="text-muted small">Book an appointment for a patient</div></div>
        </div>
      </a>
    </div>
    <div class="col-md-4">
      <a href="manager_run.php?action=verifyTherapists" class="card border-0 shadow-sm text-decoration-none h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="fs-2">✅</div>
          <div>
            <div class="fw-semibold">Verify Licenses <?php if (!empty($unverified)): ?><span class="badge bg-danger"><?= $unverified ?></span><?php endif; ?></div>
            <div class="text-muted small">Review &amp; renew therapist licenses</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-md-4">
      <a href="schedule_run.php?action=viewSchedule" class="card border-0 shadow-sm text-decoration-none h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="fs-2">🗓️</div>
          <div><div class="fw-semibold">View Schedule</div><div class="text-muted small">Full appointment calendar</div></div>
        </div>
      </a>
    </div>
  </div>
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold border-0 pt-3">🕐 Upcoming Appointments</div>
    <div class="card-body p-0">
      <?php if (empty($upcoming)): ?>
        <p class="text-muted p-3 mb-0">No upcoming appointments.</p>
      <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light"><tr><th>#</th><th>Patient</th><th>Therapist</th><th>Scheduled At</th><th>Status</th></tr></thead>
          <tbody>
            <?php foreach ($upcoming as $a): ?>
            <tr>
              <td><?= $a['AppointmentId'] ?></td>
              <td><?= htmlspecialchars($a['PatientName']) ?></td>
              <td><?= htmlspecialchars($a['TherapistName']) ?></td>
              <td><?= date('d M Y, h:i A', strtotime($a['ScheduledAt'])) ?></td>
              <td><span class="badge bg-warning text-dark"><?= $a['Status'] ?></span></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <?php if (!empty($cancelled)): ?>
  <div class="card border-0 shadow-sm border-start border-danger border-3">
    <div class="card-header bg-white fw-semibold border-0 pt-3 text-danger">⚠️ Cancelled Sessions (<?= count($cancelled) ?>)</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light"><tr><th>Patient</th><th>Was Scheduled</th><th>Reason</th><th>Refund</th><th></th></tr></thead>
          <tbody>
            <?php foreach (array_slice($cancelled, 0, 5) as $c): ?>
            <tr>
              <td><?= htmlspecialchars($c['PatientName']) ?></td>
              <td><?= date('d M Y', strtotime($c['ScheduledAt'])) ?></td>
              <td><?= htmlspecialchars($c['CancelReason'] ?? '—') ?></td>
              <td><span class="badge bg-<?= ($c['RefundStatus'] ?? '') === 'refunded' ? 'success' : 'secondary' ?>"><?= $c['RefundStatus'] ?? 'none' ?></span></td>
              <td><a href="session_run.php?action=cancelledSessions" class="btn btn-sm btn-outline-danger">Review</a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/../shared/footer.php'; ?>

</body>
</html>
