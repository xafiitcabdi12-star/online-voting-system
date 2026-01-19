<?php
include "includes/session_check.php";
include "config/db_connect.php";

$q = mysqli_query($conn, "
SELECT c.full_name, c.position, COUNT(v.id) AS votes
FROM candidates c
LEFT JOIN votes v ON v.candidate_id=c.id
GROUP BY c.id
ORDER BY c.position, votes DESC
");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Results PDF</title>
  <style>
    body{font-family:Arial;padding:20px;}
    h2{color:#0d6efd;}
    table{width:100%;border-collapse:collapse;margin-top:15px;}
    th,td{border:1px solid #ddd;padding:10px;text-align:left;}
    th{background:#0d6efd;color:#fff;}
  </style>
</head>
<body>
  <h2>Election Results</h2>
  <p>Print this page and choose “Save as PDF”.</p>
  <table>
    <thead>
      <tr><th>Candidate</th><th>Position</th><th>Votes</th></tr>
    </thead>
    <tbody>
      <?php while($r=mysqli_fetch_assoc($q)): ?>
        <tr>
          <td><?php echo htmlspecialchars($r['full_name']); ?></td>
          <td><?php echo htmlspecialchars($r['position']); ?></td>
          <td><?php echo (int)$r['votes']; ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <script>
    window.print();
  </script>
</body>
</html>
