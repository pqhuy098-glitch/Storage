<?php
include __DIR__ . '/../../config/db.php';
include_once __DIR__ . '/../../config/helper.php';

// echo password_hash('123',PASSWORD_DEFAULT);

$errors=[];
if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $u = trim($_POST['username'] ?? '');
        $p = trim($_POST['password'] ?? '');
        $stm = $con->prepare("SELECT id, username, password, role, address FROM users WHERE username =?");
        $stm->bind_param("s",$u);
        $stm->execute();
        $user=$stm->get_result()->fetch_assoc();
        if($user && password_verify($p, $user['password']))
            {
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role'],
                    'address' => $user['address']
                ];
                redirect($user['role'] === 'admin' ? '/Lesson5/public/backend' : '/Lesson5/public/frontend');
            } else {
                $errors[] = 'Invalid username or password';
            }
    }
include __DIR__ . '/../../includes/frontend_header.php';
?>
<h1>Login</h1>
<?php
foreach($errors as $err):
?>
<div class ="alert alert-danger"><?= e($err)?></div>
<?php endforeach ?>
<form method="post">
    <div class="form-row">
        <label for="username">Username</label>
        <input name="username" id="username" required/>
    </div>
    <div class="form-row">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required/>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
</form>
<?php
include __DIR__ . '/../../includes/frontend_footer.php';
?>