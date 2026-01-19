<?php
include "includes/session_check.php";
include "config/db_connect.php";
include "includes/header_dashboard.php";

// Winner per position (highest votes)
$sql = "
SELECT t.position, t.full_name, t.photo, t.total_votes
FROM (
    SELECT c.position, c.full_name, c.photo, COUNT(v.id) AS total_votes,
           RANK() OVER (PARTITION BY c.position ORDER BY COUNT(v.id) DESC) AS rnk
    FROM candidates c
    LEFT JOIN votes v ON v.candidate_id = c.id
    GROUP BY c.id, c.position, c.full_name, c.photo
) t
WHERE t.rnk = 1
ORDER BY t.position
";
$resWinners = mysqli_query($conn, $sql);

// Full table results
$sqlAll = "
SELECT c.id, c.full_name, c.position, c.photo, COUNT(v.id) AS votes
FROM candidates c
LEFT JOIN votes v ON v.candidate_id = c.id
GROUP BY c.id
ORDER BY c.position, votes DESC, c.full_name
";
$resAll = mysqli_query($conn, $sqlAll);
?>
<div class="layout">
  <aside class="sidebar">
    <h3><?php echo ($_SESSION['user_type']==='admin')?'Admin Panel':'User Panel'; ?></h3>
    <?php if($_SESSION['user_type']==='admin'): ?>
      <a href="admin_dashboard.php">Dashboard</a>
      <a href="manage_candidates.php">Manage Candidates</a>
      <a class="active" href="results.php">Results</a>
      <a class="logout" href="logout.php">Logout</a>
    <?php else: ?>
      <a href="user_dashboard.php">Dashboard</a>
      <a href="vote.php">Vote Now</a>
      <a href="view_candidates.php">View Candidates</a>
      <a class="active" href="results.php">Results</a>
      <a href="profile.php">Profile</a>
      <a href="logout.php">Logout</a>
    <?php endif; ?>
  </aside>

  <main class="content">
    <h2 class="page-title">Results</h2>

    <div class="info-box">
      <h3 style="margin-bottom:12px;">🏆 Winners (Per Position)</h3>

      <div class="candidate-grid">
        <?php while($w = mysqli_fetch_assoc($resWinners)): ?>
          <div class="candidate-card">
            <img src="assets/images/<?php echo htmlspecialchars($w['photo'] ?: 'default.png'); ?>">
            <div class="candidate-text">
              <h4><?php echo htmlspecialchars($w['full_name']); ?></h4>
              <div class="candidate-badge"><?php echo htmlspecialchars($w['position']); ?> — <?php echo (int)$w['total_votes']; ?> votes</div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>

    <div class="info-box">
      <h3 style="margin-bottom:12px;">📊 All Candidates</h3>

      <table>
        <thead>
          <tr>
            <th>Candidate</th>
            <th>Position</th>
            <th>Votes</th>
          </tr>
        </thead>
        <tbody>
        <?php while($r = mysqli_fetch_assoc($resAll)): ?>
          <tr>
            <td style="display:flex;align-items:center;gap:10px;">
              <img src="assets/images/<?php echo htmlspecialchars($r['photo'] ?: 'default.png'); ?>"
                   style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
              <?php echo htmlspecialchars($r['full_name']); ?>
            </td>
            <td><?php echo htmlspecialchars($r['position']); ?></td>
            <td><strong><?php echo (int)$r['votes']; ?></strong></td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>

      <div style="display:flex;gap:10px;margin-top:15px;flex-wrap:wrap;">
        <a class="btn btn-primary" href="export_results_excel.php">Export Excel</a>
        <a class="btn btn-danger" href="export_results_pdf.php">Export PDF</a>
      </div>
    </div>

  </main>
</div>

<?php include "includes/footer.php"; ?>
