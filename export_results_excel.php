<?php
include "includes/session_check.php";
include "config/db_connect.php";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=results.xls");

$q = mysqli_query($conn, "
SELECT c.full_name, c.position, COUNT(v.id) AS votes
FROM candidates c
LEFT JOIN votes v ON v.candidate_id=c.id
GROUP BY c.id
ORDER BY c.position, votes DESC
");

echo "Candidate\tPosition\tVotes\n";
while($r=mysqli_fetch_assoc($q)){
  echo $r['full_name']."\t".$r['position']."\t".$r['votes']."\n";
}
