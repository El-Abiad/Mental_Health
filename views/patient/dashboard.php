<?php $title = 'Patient Dashboard'; include __DIR__ . '/../shared/header.php'; ?>
<div class="container my-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Patient Dashboard</h1>
            <p class="text-muted mb-0">Welcome back, <?= htmlspecialchars($_SESSION['name'] ?? 'Patient') ?></p>
        </div>
        <div class="btn-group">
            <a class="btn btn-outline-primary" href="/clinic/controllers/patient_run.php?action=sessions">Sessions</a>
            <a class="btn btn-outline-primary" href="/clinic/controllers/patient_run.php?action=favorites">Therapists</a>
            <a class="btn btn-outline-primary" href="/clinic/controllers/patient_run.php?action=emergency">Emergency</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <section class="card shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0">Upcoming Sessions</h2>
                </div>
                <div class="card-body">
                    <?php if (empty($upcomingSessions)): ?>
                        <p class="text-muted mb-0">No upcoming sessions.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Therapist</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcomingSessions as $session): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($session['therapist_name'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($session['date'] ?? '') ?></td>
                                            <td><span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($session['status'] ?? '') ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <div class="col-lg-5">
            <section class="card shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0">Log Mood</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="/clinic/controllers/patient_run.php?action=logMood">
                        <div class="mb-3">
                            <label class="form-label" for="mood">Current Mood</label>
                            <select class="form-select" id="mood" name="mood">
                                <option value="happy">Happy</option>
                                <option value="calm">Calm</option>
                                <option value="anxious">Anxious</option>
                                <option value="sad">Sad</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="notes">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="4"></textarea>
                        </div>
                        <button class="btn btn-primary" type="submit">Log Mood</button>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <section class="card shadow-sm mt-4">
        <div class="card-header bg-white">
            <h2 class="h5 mb-0">Mood Logs</h2>
        </div>
        <div class="card-body">
            <?php if (empty($moodLogs)): ?>
                <p class="text-muted mb-0">No mood logs yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Mood</th>
                                <th>Notes</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($moodLogs as $log): ?>
                                <tr>
                                    <td><?= htmlspecialchars((string)($log['mood'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($log['notes'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($log['date'] ?? '')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php include __DIR__ . '/../shared/footer.php'; ?>
