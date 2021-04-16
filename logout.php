<?php
session_start();
?>

<?php
// Initialize the session.
// If you are using session_name("something"), don't forget it now!
session_start();
// Finally, destroy the session.
$_SESSION['user_id'] = null;
unset($_SESSION['user_id']);
session_destroy();
header('Location: index.php');
die();
