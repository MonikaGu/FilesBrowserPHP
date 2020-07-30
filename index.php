<?php

session_start();
if (!$_SESSION['logged_in']) {
    header('Location: login.php');
}

// logout logic
if(isset($_POST['action']) and $_POST['action'] == 'logout'){
    session_destroy();
    header('Location: login.php');
}

function create_dir($name, $path) {
    if (!$name) {
        return;
    }

    @mkdir("$path/$name");
}

function delete_file($filePath) {
    @unlink($filePath);
}

function getBackPath($currentPath){
    return dirname($currentPath);
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>FsBrowser</title>
</head>
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%; 
        }
        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #388f3a;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #fffbbd;
        }
        tr:hover{
            background-color: #d0d9d0;
        }
    </style>
<body>
    <?php
        if (isset($_GET['path'])) {
            $path = $_GET['path'];
        } else {
            $path = ".";
        }

        if (isset($_GET['create_dir'])){
            create_dir($_GET['create_dir'], $path);
        }
        if (isset($_POST['delete'])){
            delete_file($_POST['delete']);
        }
        if (isset($_POST['download'])){
            $fileName = basename($_POST['download']);
            
            
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"$fileName\""); 
            readfile($_POST['download']);
        }

        if (isset($_FILES['uploaded_file'])){
           $saveTo = $path . '/' . basename( $_FILES['uploaded_file']['name']);
           move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $saveTo);
        }

        $dirContents = scandir ($path);

        print('<h2>Directory contents: ' . str_replace('?path=/','',$_SERVER['REQUEST_URI']) . '</h2>');

        print("<table><th>Type</th><th>Name</th><th>Actions</th>");
        foreach ($dirContents as $filesAndDirs) {
            if ($filesAndDirs != ".." and $filesAndDirs != ".") {
                
                $fullPath = "$path/$filesAndDirs";
                print ("<tr>");
                if (is_dir($fullPath)) {
                    print("<td>" . "Directory" . "</td>");
                    print("<td> <a href= '?path=" . $fullPath . "'>" . $filesAndDirs . "</a></td>");
                    print("<td></td>");
                    
                } else {
                    print("<td>" . "Files" . "</td>");
                    print("<td>" . $filesAndDirs . "</td>");
                    print('<td>
                        <form style="display: inline-block" action="" method="post">
                            <input type="hidden" name="delete" value="' . $fullPath . '">
                            <input type="submit" value="Delete">
                        </form>
                        <form style="display: inline-block" action="" method="post">
                            <input type="hidden" name="download" value="' . $fullPath . '">
                            <input type="submit" value="Download">
                        </form>
                   </td>');
                }
                print ("</tr>");
            }
        }
        print("</table>");
    ?>

    <button style="display: block; width: 50px"><a href="<?php echo('?path='. getBackPath($path)) ?>">Back</a></button>
    
    <br>
    <form action="/FilesBrowserPHP" method="get">
        <input type="hidden" name="path" value="<?php echo($path) ?>" /> 
        <input placeholder="Name of new directory" type="text" id="create_dir" name="create_dir">
        <button type="submit">Submit</button>
    </form>

    <form action="" method="post">
        <input type="hidden" name="action" value="logout" /> 
        <button type="submit">Logout</button>
    </form>

    <form enctype="multipart/form-data" method="POST">
    <input type="file" name="uploaded_file"></input><br />
    <input type="submit" value="Upload file"></input>
  </form>
</body>
</html>
