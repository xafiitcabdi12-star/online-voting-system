<?php
include "includes/session_check.php";
include "config/db_connect.php";
include "includes/header_dashboard.php";

if ($_SESSION['user_type'] !== 'voter') {
    header("Location: admin_dashboard.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

/* ✅ positions list */
$positions = ["President", "Vice President", "Secretary"];

/* ✅ positions already voted */
$votedPos = [];
$resVP = mysqli_query($conn, "SELECT DISTINCT position FROM votes WHERE user_id=$user_id");
while ($r = mysqli_fetch_assoc($resVP)) {
    $votedPos[$r['position']] = true;
}

/* ✅ STOP ONLY IF USER VOTED ALL POSITIONS */
$check = mysqli_query($conn, "SELECT COUNT(DISTINCT position) AS total FROM votes WHERE user_id=$user_id");
$row = mysqli_fetch_assoc($check);
if ((int)$row['total'] >= count($positions)) {
    header("Location: user_dashboard.php");
    exit();
}

$error = "";
$success = "";

/* ✅ SUBMIT VOTES (user can submit 1 position or many positions) */
if (isset($_POST['submit_vote'])) {

    if (empty($_POST['vote']) || !is_array($_POST['vote'])) {
        $error = "Please select at least one candidate.";
    } else {

        $selectedAny = false;

        foreach ($_POST['vote'] as $position => $candidate_id) {

            $position = trim($position);
            $candidate_id = (int)$candidate_id;

            if ($candidate_id <= 0) continue;
            $selectedAny = true;

            // ✅ must be valid position
            if (!in_array($position, $positions, true)) {
                $error = "Invalid position selected.";
                break;
            }

            // ✅ already voted this position?
            if (isset($votedPos[$position])) {
                $error = "You already voted for: " . htmlspecialchars($position);
                break;
            }

            // ✅ candidate exists and belongs to this position?
            $stmt = mysqli_prepare($conn, "SELECT id FROM candidates WHERE id=? AND position=? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "is", $candidate_id, $position);
            mysqli_stmt_execute($stmt);
            $ok = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($ok) !== 1) {
                $error = "Invalid candidate selected for: " . htmlspecialchars($position);
                break;
            }

            // ✅ insert vote (DB must allow unique user_id + position)
            $ins = mysqli_prepare($conn, "INSERT INTO votes (user_id, candidate_id, position) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($ins, "iis", $user_id, $candidate_id, $position);

            if (!mysqli_stmt_execute($ins)) {
                $error = "Vote failed for: " . htmlspecialchars($position);
                break;
            }

            // ✅ update local voted list
            $votedPos[$position] = true;
        }

        if (!$selectedAny && $error === "") {
            $error = "Please select at least one candidate.";
        }

        if ($error === "") {
            $success = "✅ Vote submitted successfully!";
        }
    }
}

/* ✅ LOAD CANDIDATES GROUPED BY POSITION */
$candidates = mysqli_query($conn, "SELECT * FROM candidates ORDER BY position, full_name");
$byPosition = [];
while ($c = mysqli_fetch_assoc($candidates)) {
    $byPosition[$c['position']][] = $c;
}
?>

<div class="layout">
    <aside class="sidebar">
        <h3>User Panel</h3>
        <a href="user_dashboard.php">Dashboard</a>
        <a href="vote.php" class="active">Vote Now</a>
        <a href="view_candidates.php">View Candidates</a>
        <a href="results.php">Results</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </aside>

    <main class="content">
        <h2 class="page-title">Vote Now</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
            <a class="btn btn-success" href="results.php">View Results</a>
        <?php endif; ?>

        <form method="post">

            <?php foreach ($positions as $pos): ?>
                <div class="info-box">
                    <h3 style="margin-bottom:12px;"><?php echo htmlspecialchars($pos); ?></h3>

                    <?php if (isset($votedPos[$pos])): ?>
                        <p class="success">✅ You already voted for <?php echo htmlspecialchars($pos); ?></p>
                    <?php else: ?>

                        <?php if (empty($byPosition[$pos])): ?>
                            <p class="error">No candidates available for this position.</p>
                        <?php else: ?>
                            <div class="candidate-list">
                                <?php foreach ($byPosition[$pos] as $row): ?>
                                    <label class="candidate vote-card">
                                        <input
                                            type="radio"
                                            name="vote[<?php echo htmlspecialchars($pos); ?>]"
                                            value="<?php echo (int)$row['id']; ?>"
                                            class="vote-radio"
                                        >
                                        <img src="assets/images/<?php echo htmlspecialchars($row['photo'] ?: 'default.png'); ?>" alt="">
                                        <div class="candidate-info">
                                            <h4><?php echo htmlspecialchars($row['full_name']); ?></h4>
                                            <p><?php echo htmlspecialchars($row['position']); ?></p>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit" name="submit_vote" class="btn btn-success" style="width:100%; padding:16px;">
                Submit Vote
            </button>
        </form>
    </main>
</div>

<?php include "includes/footer.php"; ?>
