<?php
if (!isset($_SESSION['publisher_id'])) {
    header('Location: auth/login.php');
    exit;
}
