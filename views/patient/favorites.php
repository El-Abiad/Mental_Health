<?php $title = 'Therapists'; include __DIR__ . '/../shared/header.php'; ?>
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Therapists</h1>
        <a class="btn btn-outline-primary" href="/clinic/controllers/patient_run.php?action=dashboard">Dashboard</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0">Available Therapists</h2>
                </div>
                <div class="card-body">
                    <?php if (empty($favorites)): ?>
                        <p class="text-muted mb-0">No therapists found.</p>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($favorites as $therapist): ?>
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h3 class="h6 mb-2"><?= htmlspecialchars($therapist['name'] ?? '') ?></h3>
                                        <p class="mb-1"><strong>Specialty:</strong> <?= htmlspecialchars($therapist['specialties'] ?? 'Not specified') ?></p>
                                        <p class="mb-1"><strong>License:</strong> <?= !empty($therapist['license_verified']) ? 'Verified' : 'Pending' ?></p>
                                        <p class="mb-0"><strong>Status:</strong> <?= !empty($therapist['is_snoozed']) ? 'Unavailable' : 'Available' ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0">Add Favorite</h2>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label" for="therapist_id">Therapist ID</label>
                            <input class="form-control" type="number" id="therapist_id" name="therapist_id" required>
                        </div>
                        <button class="btn btn-primary" type="submit">Add to Favorites</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../shared/footer.php'; ?>
