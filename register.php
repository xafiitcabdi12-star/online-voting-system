<?php
session_start();
include "config/db_connect.php";
include "includes/header_public.php";

$error = "";

if (isset($_POST['register'])) {

    $fname     = trim($_POST['fname'] ?? '');
    $lname     = trim($_POST['lname'] ?? '');
    $gender    = $_POST['gender'] ?? '';
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $address   = trim($_POST['address'] ?? '');
    $passwordPlain   = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    /* =========================
       ✅ VALIDATIONS (SERVER)
    ========================= */

    // names letters + spaces only
    if (!preg_match("/^[a-zA-Z\s]{2,50}$/", $fname)) {
        $error = "First name must contain letters only (2-50).";
    } elseif (!preg_match("/^[a-zA-Z\s]{2,50}$/", $lname)) {
        $error = "Last name must contain letters only (2-50).";
    }

    // username letters/numbers/_ only (3-30)
    elseif (!preg_match("/^[a-zA-Z0-9_]{3,30}$/", $username)) {
        $error = "Username must be 3-30 chars, only letters, numbers, underscore (_).";
    }

    // email required + valid
    elseif ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email.";
    }

    // phone optional but numbers only (7-15 digits)
    elseif ($phone !== "" && !preg_match("/^[0-9]{7,15}$/", $phone)) {
        $error = "Phone must be numbers only (7-15 digits).";
    }

    // address optional but must be reasonable length if provided
    elseif ($address !== "" && (strlen($address) < 5 || strlen($address) > 255)) {
        $error = "Address must be 5 to 255 characters.";
    }

    // password 6..100
    elseif (strlen($passwordPlain) < 6 || strlen($passwordPlain) > 100) {
        $error = "Password must be 6 to 100 characters.";
    }

    // confirm password match
    elseif ($passwordPlain !== $confirmPassword) {
        $error = "Passwords do not match.";
    }

    // gender
    elseif ($gender !== "Male" && $gender !== "Female") {
        $error = "Please select gender.";
    }

    if ($error === "") {

        $passwordHash = password_hash($passwordPlain, PASSWORD_DEFAULT);

        // check username exists
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username=? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($res) > 0) {
            $error = "Username already exists.";
        } else {

            // check email exists
            $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email=? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $res2 = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($res2) > 0) {
                $error = "This email is already registered.";
            } else {

                // insert user (includes address)
                $stmt = mysqli_prepare($conn, "
                    INSERT INTO users (first_name,last_name,gender,username,password,email,phone,address,user_type,status)
                    VALUES (?,?,?,?,?,?,?,?, 'voter','active')
                ");
                mysqli_stmt_bind_param($stmt, "ssssssss",
                    $fname, $lname, $gender, $username, $passwordHash, $email, $phone, $address
                );

                if (mysqli_stmt_execute($stmt)) {

                    $newUserId = mysqli_insert_id($conn);

                    // session login
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $newUserId;
                    $_SESSION['user_type'] = 'voter';
                    $_SESSION['last_activity'] = time();

                    // remember token (30 days)
                    $token = bin2hex(random_bytes(32));
                    $st = mysqli_prepare($conn, "UPDATE users SET remember_token=? WHERE id=?");
                    mysqli_stmt_bind_param($st, "si", $token, $newUserId);
                    mysqli_stmt_execute($st);

                    // safer cookie flags
                    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
                    setcookie("remember_token", $token, [
                        'expires'  => time() + (86400 * 30),
                        'path'     => '/',
                        'secure'   => $secure,
                        'httponly' => true,
                        'samesite' => 'Lax',
                    ]);

                    header("Location: user_dashboard.php");
                    exit();

                } else {
                    $error = "Something went wrong. Please try again.";
                }
            }
        }
    }
}
?>

<style>
/* Password eye style like your screenshot */
.password-wrap{
  position: relative;
  width: 100%;
}
.password-wrap input{
  width: 100%;
  padding-right: 52px;      /* space for eye */
  height: 52px;
  border-radius: 10px;
  border: 2px solid #cfcfcf;
  outline: none;
}
.password-wrap input:focus{
  border-color: #000;
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

/* ✅ default: hidden password => show eye-slash */
.toggle-eye .icon-eye { display: none; }
.toggle-eye .icon-eye-slash { display: block; }

/* ✅ showing password => show eye */
.toggle-eye.showing .icon-eye { display: block; }
.toggle-eye.showing .icon-eye-slash { display: none; }
</style>

<div class="auth-container">
    <h2>Register</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post" autocomplete="off">
        <div class="register-grid">

            <input type="text" name="fname" placeholder="First Name" required
                   pattern="[A-Za-z\s]{2,50}" title="Letters only (2-50)"
                   value="<?php echo htmlspecialchars($_POST['fname'] ?? ''); ?>">

            <input type="text" name="lname" placeholder="Last Name" required
                   pattern="[A-Za-z\s]{2,50}" title="Letters only (2-50)"
                   value="<?php echo htmlspecialchars($_POST['lname'] ?? ''); ?>">

            <select name="gender" class="full" required>
                <option value="">Select Gender</option>
                <option value="Male"   <?php echo (($_POST['gender'] ?? '')==='Male')?'selected':''; ?>>Male</option>
                <option value="Female" <?php echo (($_POST['gender'] ?? '')==='Female')?'selected':''; ?>>Female</option>
            </select>

            <input type="text" name="username" placeholder="Username" required
                   pattern="[A-Za-z0-9_]{3,30}" title="3-30 chars. Letters, numbers, underscore only."
                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">

            <input type="email" name="email" placeholder="Email" required
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

            <input type="text" name="phone" placeholder="Phone" class="full"
                   inputmode="numeric" pattern="[0-9]{7,15}" title="Numbers only (7-15 digits)"
                   value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">

            <input type="text" name="address" placeholder="Address" class="full"
                   minlength="5" maxlength="255"
                   value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>">

            <!-- ✅ Password with eye-slash by default -->
            <div class="password-wrap full">
                <input type="password" id="password" name="password" placeholder="Password" required minlength="6" maxlength="100">
                <button type="button" class="toggle-eye" aria-label="Show password" onclick="togglePassword('password', this)">
                    <!-- Eye -->
                    <svg class="icon-eye" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <!-- Eye Slash -->
                    <svg class="icon-eye-slash" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 3l18 18"/>
                        <path d="M10.58 10.58A2 2 0 0 0 12 14a2 2 0 0 0 1.42-.58"/>
                        <path d="M9.88 5.09A10.94 10.94 0 0 1 12 5c6.5 0 10 7 10 7a18.16 18.16 0 0 1-3.05 4.33"/>
                        <path d="M6.61 6.61A18.66 18.66 0 0 0 2 12s3.5 7 10 7a10.9 10.9 0 0 0 5.39-1.45"/>
                    </svg>
                </button>
            </div>

            <!-- ✅ Confirm Password with eye-slash by default -->
            <div class="password-wrap full">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required minlength="6" maxlength="100">
                <button type="button" class="toggle-eye" aria-label="Show confirm password" onclick="togglePassword('confirm_password', this)">
                    <!-- Eye -->
                    <svg class="icon-eye" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <!-- Eye Slash -->
                    <svg class="icon-eye-slash" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 3l18 18"/>
                        <path d="M10.58 10.58A2 2 0 0 0 12 14a2 2 0 0 0 1.42-.58"/>
                        <path d="M9.88 5.09A10.94 10.94 0 0 1 12 5c6.5 0 10 7 10 7a18.16 18.16 0 0 1-3.05 4.33"/>
                        <path d="M6.61 6.61A18.66 18.66 0 0 0 2 12s3.5 7 10 7a10.9 10.9 0 0 0 5.39-1.45"/>
                    </svg>
                </button>
            </div>

        </div>

        <button type="submit" name="register" class="btn btn-primary" style="width:100%;">
            Register
        </button>

        <p class="link">
            Already have an account? <a href="login.php">Login</a>
        </p>
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
