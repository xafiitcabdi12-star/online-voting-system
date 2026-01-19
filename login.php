<?php
session_start();
include "config/db_connect.php";

$error = "";

/* =========================
   AUTO LOGIN IF COOKIE EXISTS
   (MUST be before any HTML output)
========================= */
if (!isset($_SESSION['user_id']) && !empty($_COOKIE['remember_token'])) {

    $rawToken  = $_COOKIE['remember_token'];
    $hashToken = hash("sha256", $rawToken);

    $stmt = mysqli_prepare($conn, "SELECT id, user_type, status FROM users WHERE remember_token=? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $hashToken);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($u = mysqli_fetch_assoc($res)) {

        if (isset($u['status']) && $u['status'] !== 'active') {
            setcookie("remember_token", "", time() - 3600, "/");
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$u['id'];
            $_SESSION['user_type'] = $u['user_type'];
            $_SESSION['last_activity'] = time();

            header("Location: " . ($u['user_type'] === 'admin' ? "admin_dashboard.php" : "user_dashboard.php"));
            exit();
        }

    } else {
        setcookie("remember_token", "", time() - 3600, "/");
    }
}

/* =========================
   LOGIN SUBMIT
========================= */
if (isset($_POST['login'])) {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username=? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {

        if (isset($user['status']) && $user['status'] !== 'active') {
            $error = "Account is inactive. Contact admin.";
        } else {

            $ok = false;

            // ✅ md5 fallback upgrade
            if ($user['password'] === md5($password)) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $up = mysqli_prepare($conn, "UPDATE users SET password=? WHERE id=?");
                mysqli_stmt_bind_param($up, "si", $newHash, $user['id']);
                mysqli_stmt_execute($up);
                $ok = true;
            }

            // ✅ bcrypt verify
            if (!$ok && password_verify($password, $user['password'])) {
                $ok = true;
            }

            if ($ok) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['last_activity'] = time();

                /* ✅ Remember Me */
                if (!empty($_POST['remember_me'])) {

                    $rawToken  = bin2hex(random_bytes(32));
                    $hashToken = hash("sha256", $rawToken);

                    $st = mysqli_prepare($conn, "UPDATE users SET remember_token=? WHERE id=?");
                    mysqli_stmt_bind_param($st, "si", $hashToken, $user['id']);
                    mysqli_stmt_execute($st);

                    // safer cookie flags
                    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
                    setcookie("remember_token", $rawToken, [
                        'expires'  => time() + (86400 * 30),
                        'path'     => '/',
                        'secure'   => $secure,
                        'httponly' => true,
                        'samesite' => 'Lax',
                    ]);

                } else {
                    // if not checked => clear db token + cookie
                    $uid = (int)$user['id'];
                    mysqli_query($conn, "UPDATE users SET remember_token=NULL WHERE id=$uid");
                    setcookie("remember_token", "", time() - 3600, "/");
                }

                header("Location: " . ($user['user_type'] === 'admin' ? "admin_dashboard.php" : "user_dashboard.php"));
                exit();
            }

            $error = "Invalid username or password";
        }

    } else {
        $error = "Invalid username or password";
    }
}

/* ✅ NOW include header AFTER cookies/redirects */
include "includes/header_public.php";
?>

<style>
/* Password eye for login (same style as your design) */
.password-wrap{
  position: relative;
  width: 100%;
}
.password-wrap input{
  width: 100%;
  padding-right: 52px;
}
.toggle-eye{
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  width: 38px;
  height: 38px;
  border: 0;
  background: transparent;
  display: grid;
  place-items: center;
  cursor: pointer;
  color: #666;
}
.toggle-eye:hover{ color:#111; }

/* default: hidden password => show eye-slash */
.toggle-eye .icon-eye { display: none; }
.toggle-eye .icon-eye-slash { display: block; }

/* showing password => show eye */
.toggle-eye.showing .icon-eye { display: block; }
.toggle-eye.showing .icon-eye-slash { display: none; }
</style>

<div class="auth-container">
    <h2>Login</h2>

    <form method="post" autocomplete="off">
        <input type="text" name="username" placeholder="Username" required>

        <!-- ✅ Password with eye -->
        <div class="password-wrap">
            <input type="password" id="login_password" name="password" placeholder="Password" required>

            <button type="button" class="toggle-eye" aria-label="Show password"
                    onclick="togglePassword('login_password', this)">
                <!-- Eye -->
                <svg class="icon-eye" viewBox="0 0 24 24" width="22" height="22" fill="none"
                     stroke="currentColor" stroke-width="2">
                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>

                <!-- Eye Slash (default) -->
                <svg class="icon-eye-slash" viewBox="0 0 24 24" width="22" height="22" fill="none"
                     stroke="currentColor" stroke-width="2">
                    <path d="M3 3l18 18"/>
                    <path d="M10.58 10.58A2 2 0 0 0 12 14a2 2 0 0 0 1.42-.58"/>
                    <path d="M9.88 5.09A10.94 10.94 0 0 1 12 5c6.5 0 10 7 10 7a18.16 18.16 0 0 1-3.05 4.33"/>
                    <path d="M6.61 6.61A18.66 18.66 0 0 0 2 12s3.5 7 10 7a10.9 10.9 0 0 0 5.39-1.45"/>
                </svg>
            </button>
        </div>

        <label class="remember-row" style="display:flex;gap:10px;align-items:center;margin:10px 0;">
            <input type="checkbox" name="remember_me" value="1">
            <span>Remember me</span>
        </label>

        <button type="submit" name="login" class="btn btn-primary" style="width:100%;">Login</button>

        <p class="link"><a href="forgot_password.php">Forgot Password?</a></p>
        <p class="link">No account? <a href="register.php">Register</a></p>

        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
    </form>
</div>

<script>
function togglePassword(inputId, btn){
    const input = document.getElementById(inputId);

    if (input.type === "password") {
        input.type = "text";
        btn.classList.add("showing"); // show eye
        btn.setAttribute("aria-label", "Hide password");
    } else {
        input.type = "password";
        btn.classList.remove("showing"); // show eye-slash
        btn.setAttribute("aria-label", "Show password");
    }
}
</script>

<?php include "includes/footer.php"; ?>
