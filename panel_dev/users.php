<?php
include '../db.php';
include 'header.php';

// Fetch data from the database
$stmt = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
// $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="mb-4">Registered Users</h1>

<table class="table table-bordered table-striped">
  <thead class="table-dark">
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Registered At</th>
    </tr>
  </thead>
  <tbody>
    <?php if (count($user) > 0): ?>
      <?php foreach ($user as $user): ?>
        <tr>
          <td><?= htmlspecialchars($user['id']) ?></td>
          <td><?= htmlspecialchars($user['name']) ?></td>
          <td><?= htmlspecialchars($user['email']) ?></td>
          <td><?= htmlspecialchars($user['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="4" class="text-center">No users found.</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>

<?php include 'footer.php'; ?>
