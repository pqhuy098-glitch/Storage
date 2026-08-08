<?php
include __DIR__ . '/../../../includes/admin_header.php';
include __DIR__ . '/../../../config/db.php';
include_once __DIR__ . '/../../../config/helper.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $role = trim($_POST['role'] ?? '');

    if ($username === '') {
        $errors['username'] = "Username is required";
    }
    else if (strlen($username) < 6 || strlen($username) > 30) {
        $errors['username'] = "Username must be between 6 and 30 characters";
        unset($_POST['username']);
    }
    else if (
        !preg_match('/[a-z]/', $username) ||
        !preg_match('/[0-9]/', $username)
        ) {
        $errors['username'] = "Username can only contain letters, numbers";
        unset($_POST['username']);
    }
    
    if ($email === '') {
        $errors['email'] = "Email is required";
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please input valid email";
        unset($_POST['email']);
    }

    if ($password === '') {
        $errors['password'] = "Password is required";
    }
    else if (strlen($password) < 8 || strlen($password) > 16) {
        $errors['password'] = "Password must be between 8 and 16 characters long";
        unset($_POST['password']);
    }
    else if (
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[\W_]/', $password)
        ) {
        $errors['password'] = "Password must include uppercase, lowercase, digit, and special character (!@#$%^&*)";
        unset($_POST['password']);
    }
    
    if ($fullName === '') {
        $errors['full_name'] = "Name is required";
    }

    if ($role === '') {
        $errors['role'] = "Please select a valid role";
    }
    
    if (!$errors)
    {
        $hased = password_hash($password, PASSWORD_DEFAULT);
        $stm = $con->prepare("INSERT INTO users (username, password, email, full_name, role) VALUES (?,?,?,?,?)");
        $stm->bind_param("sssss", $username, $hased, $email, $fullName, $role);
        $stm->execute();
        redirect("index.php");
    }
}
?>
<h1>Create User</h1>
<?php
if (!empty($errors)):
?>
<div class ="alert alert-danger"><?= e(array_values($errors)[0])?></div>
<?php endif ?>
<?php
include __DIR__ . '/form.php'
?>
<?php
include __DIR__ . '/../../../includes/admin_footer.php';
?>