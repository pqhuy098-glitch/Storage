<?php
if(session_status() === PHP_SESSION_NONE) 
{
    session_start();
}

function e(string $value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function vnd(float $amount)
{
    return number_format($amount, 0, ",", ".") . " VNĐ";
}

function redirect(string $url)
{
    header("Location: $url");
    exit();
}

function flash($key, $msg=null) 
{
  if ($msg != null) {
    $_SESSION['flash'][$key] = $msg;
    return null;
  }

  // check if session contain key
  if (!isset($_SESSION['flash'][$key])) {
    return null;
  }

  $value = $_SESSION['flash'][$key];
  unset($_SESSION['flash'][$key]);  // remove key in session
  return $value;
}

function current_user() 
{
    return $_SESSION['user']?? null;
}

function  is_admin() 
{
    return (current_user()['role'] ?? '') === 'admin';
}

function require_login()
{
    if (!current_user()) 
    {
        redirect('/Lesson5/public/auth/login.php');
    }
}

function require_admin()
{
    require_login();
    if(!is_admin()) 
    {
        flash('error','You do not have permission to access this area');
        redirect('/Lesson5/public/frontend/index.php');
    }
}

function cart_count() {
    return array_sum(array_column($_SESSION['cart'] ?? [], 'quantity'));
}

function csrf_token()
{
    if (empty($_SESSION['csrf_token']))
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_input()
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '" />';
}

function verify_csrf_token($token)
{
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    return $sessionToken !== '' && is_string($token) && hash_equals($sessionToken, $token);
}

function handle_upload($fileField, $currentImage = 'assets/img/placeholder.svg')
{
    if (!isset($_FILES[$fileField]) || !is_array($_FILES[$fileField]))
    {
        return [
            'ok' => true,
            'path' => $currentImage,
            'error' => null,
            'uploaded' => false
        ];
    }

    $file = $_FILES[$fileField];
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) == UPLOAD_ERR_NO_FILE)
    {
        return [
            'ok' => true,
            'path' => $currentImage,
            'error' => null,
            'uploaded' => false
        ];
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK)
    {
        return [
            'ok' => false,
            'path' => $currentImage,
            'error' => "Image upload failed. Please try again",
            'uploaded' => false
        ];
    }

    if($file['size'] > 10*1024*1024)
    {
        return [
            'ok' => false,
            'path' => $currentImage,
            'error' => "Image size must be less than 10Mb",
            'uploaded' => false
        ];
    }

    $tmpName = $file['tmp_name'] ?? '';
    if(!is_uploaded_file($tmpName))
    {
        return [
            'ok' => false,
            'path' => $currentImage,
            'error' => "Invalid upload file.",
            'uploaded' => false
        ];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = $finfo ? finfo_file($finfo, $tmpName): ''; //read mime type
    if($finfo)
    {
        finfo_close($finfo);
    }

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg',
        'image/svg' => 'svg'
    ];

    if(!isset($allowedTypes[$mimeType]))
    {
        return [
            'ok' => false,
            'path' => $currentImage,
            'error' => "Only jpg, png, gif, webl, svg are allowed.",
            'uploaded' => false
        ];
    }

    $uploadDir = dirname(__DIR__) . '/assets/img';
    if(!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir))
    {
        return [
            'ok' => false,
            'path' => $currentImage,
            'error' => "Cannot create upload directory.",
            'uploaded' => false
        ];
    }

    $filename = "img_" . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $allowedTypes[$mimeType];

    $filePath = "$uploadDir/$filename";

    if(!move_uploaded_file($tmpName, $filePath))
    {
        return [
            'ok' => false,
            'path' => $currentImage,
            'error' => "Cannot save uploaded file.",
            'uploaded' => false
        ];
    }

    return [
            'ok' => true,
            'path' => "assets/img/$filename",
            'error' => null,
            'uploaded' => true
        ];
}

