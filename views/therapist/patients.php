<?php $title = 'Patients'; include __DIR__ . '/../shared/header.php'; ?>
<div class="container my-4">
    <h1 class="h3">Patients</h1>
    <table class="table table-striped">
        <thead><tr><th>Name</th><th>Email</th></tr></thead>
        <tbody>
        <?php foreach ($patients as $patient): ?>
            <tr>
                <td><?= htmlspecialchars($patient['FullName']) ?></td>
                <td><?= htmlspecialchars($patient['Email']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <h2 class="h5 mt-4">Shared Journals</h2>
    <?php foreach ($sharedJournals as $journal): ?>
        <div class="border rounded p-2 mb-2">
            <strong><?= htmlspecialchars($journal['patient_name'] ?? '') ?></strong>
            <div><?= nl2br(htmlspecialchars($journal['Content'] ?? '')) ?></div>
        </div>
    <?php endforeach; ?>
    <a href="/Mental_Health/controllers/therapist_run.php?action=dashboard">Back to Dashboard</a>
</div>
<?php include __DIR__ . '/../shared/footer.php'; ?>
