<?php 

include __DIR__ . '/../../includes/frontend_header.php';
include __DIR__ . '/../../config/db.php';
include_once __DIR__ . '/../../config/helper.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if ($username === '') {
        $errors['username'] = "Username is required";
    }
    else if (strlen($username) < 6 || strlen($username) > 30) {
        $errors['username'] = "Username must be between 6 and 30 characters";
    }
    else if (
        !preg_match('/[a-z]/', $username) ||
        !preg_match('/[0-9]/', $username)
    ) {
        $errors['username'] = "Username must contain both letters and numbers";
    }
    
    if ($fullName === '') {
        $errors['full_name'] = "Full name is required";
    }

    if ($email === '') {
        $errors['email'] = "Email is required";
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please input a valid email address";
    }

    if ($password === '') {
        $errors['password'] = "Password is required";
    }
    else if (strlen($password) < 8 || strlen($password) > 16) {
        $errors['password'] = "Password must be between 8 and 16 characters long";
    }
    else if (
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[\W_]/', $password)
    ) {
        $errors['password'] = "Password must include uppercase, lowercase, digit, and special character (!@#$%^&*)";
    }
    else if ($password !== $confirmPassword) {
        $errors['confirm_password'] = "Confirm password does not match";
    }

    if (empty($errors)) {
        $stm = $con->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stm->bind_param("ss", $username, $email);
        $stm->execute();
        if ($stm->get_result()->num_rows > 0) {
            $errors['exist'] = 'This username or email already exists.';
        }
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user';

        $stm = $con->prepare("INSERT INTO users (username, password, full_name, email, phone, address, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stm->bind_param("sssssss", $username, $hashed_password, $fullName, $email, $phone, $address, $role);

        if ($stm->execute()) {
            redirect('/Lesson5/public/auth/login.php');
            exit;
        } else {
            $errors['db'] = 'An error occurred when creating account!';
        }
    }            
}
?>

<h1>Register</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger"><?= e(array_values($errors)[0]) ?></div>
<?php endif; ?>

<form method="post">
    <div class="form-row">
        <label for="username">Username</label>
        <input name="username" id="username" value="<?= e($_POST['username'] ?? '') ?>" />
    </div>

    <div class="form-row">
        <label for="email">Email</label>
        <input name="email" id="email" value="<?= e($_POST['email'] ?? '') ?>" />
    </div>

    <div class="form-row">
        <label for="full_name">Full Name</label>
        <input name="full_name" id="full_name" value="<?= e($_POST['full_name'] ?? '') ?>" />
    </div>

    <div class="form-row">
        <label for="phone">Phone Number</label>
        <input name="phone" id="phone" value="<?= e($_POST['phone'] ?? '') ?>" />
    </div>

    <div class="form-row">
        <label for="address">Address</label>
        <input name="address" id="address" value="<?= e($_POST['address'] ?? '') ?>" />
    </div>

    <div class="form-row">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" />
    </div>

    <div class="form-row">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" />
    </div>

    <a href="/Lesson4/public/auth/login.php" class="btn">Already have an account? Login</a>
    <button type="submit" class="btn btn-primary">Register</button>
</form>

<?php 
include __DIR__ . '/../../includes/frontend_footer.php'; 
?>