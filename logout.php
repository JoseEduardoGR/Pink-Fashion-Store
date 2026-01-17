<?php
require_once 'includes/auth.php';

if (isLoggedIn()) {
    logout();
}

header('Location: login.php?success=logout');
exit();
?>
