<?php

include_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/helper.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0 ) 
    {
        $stmt = $con->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

redirect("index.php");

?>