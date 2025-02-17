<?php
set_time_limit(0);
ini_set('memory_limit', '512M');

function scanShellBackdoor($dir) {
    $backdoorPatterns = [
        'system(', 'exec(', 'shell_exec(', 'passthru(', 'popen(', 'proc_open(', 'eval(', 'base64_decode(',
        'gzinflate(', 'str_rot13(', 'file_get_contents("php://input")'
    ];

    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    $suspiciousFiles = [];

    foreach ($files as $file) {
        if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $content = file_get_contents($file->getRealPath());

            foreach ($backdoorPatterns as $pattern) {
                if (strpos($content, $pattern) !== false) {
                    $suspiciousFiles[] = $file->getRealPath();
                    break;
                }
            }
        }
    }

    return $suspiciousFiles;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = '';

    if (isset($_POST['killBackdoors'])) {
        // Kill all suspicious backdoors
        $websiteRoot = __DIR__; // Adjust based on your website's root directory
        $suspiciousFiles = scanShellBackdoor($websiteRoot);

        if (!empty($suspiciousFiles)) {
            foreach ($suspiciousFiles as $file) {
                exec("rm -rf " . escapeshellarg($file)); // Safe execution of rm command
            }
            $message = "All suspicious backdoors have been removed.";
        } else {
            $message = "No suspicious backdoors found.";
        }
    }

    if (isset($_POST['killWithFilter'])) {
        // Remove files excluding those entered by the user
        $websiteRoot = __DIR__; // Adjust based on your website's root directory
        $suspiciousFiles = scanShellBackdoor($websiteRoot);

        $excludeFiles = isset($_POST['excludeFiles']) ? explode(',', $_POST['excludeFiles']) : [];

        if (!empty($suspiciousFiles)) {
            foreach ($suspiciousFiles as $file) {
                // Check if the file is in the exclude list
                $fileName = basename($file);
                if (!in_array($fileName, $excludeFiles)) {
                    exec("rm -rf " . escapeshellarg($file)); // Safe execution of rm command
                }
            }
            $message = "Suspicious backdoors removed, excluding specified files.";
        } else {
            $message = "No suspicious backdoors found.";
        }
    }
} else {
    $message = "";
}

$websiteRoot = __DIR__;
$suspiciousFiles = scanShellBackdoor($websiteRoot);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backdoor Scanner</title>
    <style>
        body {
            background-color: black;
            color: red;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
        }
        h2 {
            font-size: 24px;
        }
        button {
            padding: 10px 20px;
            background-color: red;
            color: white;
            font-size: 16px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: darkred;
        }
        .ascii {
            font-family: monospace;
            font-size: 18px;
            margin-bottom: 30px;
        }
        .result {
            margin-top: 20px;
        }
        input[type="text"] {
            padding: 5px;
            font-size: 16px;
            margin-top: 10px;
            margin-bottom: 20px;
            width: 60%;
        }
        .copy-button {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="ascii">
         █████╗ ███████╗███╗   ███╗ ██████╗ ██████╗ ███████╗██╗   ██╗███████╗     ██╗ █████╗  █████╗ ███████╗<br>
        ██╔══██╗██╔════╝████╗ ████║██╔═══██╗██╔══██╗██╔════╝██║   ██║██╔════╝    ███║██╔══██╗██╔══██╗██╔════╝<br>
        ███████║███████╗██╔████╔██║██║   ██║██║  ██║█████╗  ██║   ██║███████╗    ╚██║╚█████╔╝╚██████║███████╗<br>
        ██╔══██║╚════██║██║╚██╔╝██║██║   ██║██║  ██║██╔══╝  ██║   ██║╚════██║     ██║██╔══██╗ ╚═══██║╚════██║<br>
        ██║  ██║███████║██║ ╚═╝ ██║╚██████╔╝██████╔╝███████╗╚██████╔╝███████║     ██║╚█████╔╝ █████╔╝███████║<br>
        ╚═╝  ╚═╝╚══════╝╚═╝     ╚═╝ ╚═════╝ ╚═════╝ ╚══════╝ ╚═════╝ ╚══════╝     ╚═╝ ╚════╝  ╚════╝ ╚══════╝
    </div>

    <h2>Backdoor Scanner</h2>

    <form method="POST">
        <button type="submit" name="killBackdoors">KILL ALL BACKDOOR</button>
    </form>

    <br><br>

    <h3>Removed with Filter</h3>
    <form method="POST">
        <label for="excludeFiles">Masukkan nama file yang terkecuali (misal: filemanager.php): </label><br>
        <input type="text" name="excludeFiles" id="excludeFiles" placeholder="filemanager.php, example.php"><br>
        <button type="submit" name="killWithFilter">REMOVE WITH FILTER</button>
    </form>

    <div class="result">
        <?php if ($message) { echo "<h3>$message</h3>"; } ?>

        <?php if (!empty($suspiciousFiles)) { ?>
            <h3>🚨 Ditemukan kemungkinan shell backdoor di:</h3>
            <ul>
                <?php foreach ($suspiciousFiles as $file) { ?>
                    <li><?php echo $file; ?></li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <h3>✅ Tidak ditemukan shell backdoor.</h3>
        <?php } ?>
    </div>

    <?php if (!empty($suspiciousFiles)) { ?>
        <div class="copy-button">
            <button onclick="copyCommand()">Copy rm -rf Command</button>
        </div>
    <?php } ?>

    <script>
        function copyCommand() {
            var suspiciousFiles = <?php echo json_encode($suspiciousFiles); ?>;
            var rmCommand = "rm -rf " + suspiciousFiles.map(file => '"' + file + '"').join(' ');
            navigator.clipboard.writeText(rmCommand).then(function() {
                alert("Copied the rm -rf command: " + rmCommand);
            }, function() {
                alert("Failed to copy the command.");
            });
        }
    </script>

</body>
</html>
