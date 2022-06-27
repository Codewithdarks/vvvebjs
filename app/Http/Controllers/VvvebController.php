<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VvvebController extends Controller
{
   public function sanitizeFileName($file)
    {
        //sanitize, remove double dot .. and remove get parameters if any
        $file = __DIR__ . '/' . preg_replace('@\?.*$@' , '', preg_replace('@\.{2,}@' , '', preg_replace('@[^\/\\a-zA-Z0-9\-\._]@', '', $file)));
        return $file;
    }

    public function  SavePages()
    {
        define('MAX_FILE_LIMIT', 1024 * 1024 * 2);//2 Megabytes max html file size

        $html = "";
        if (isset($_POST['startTemplateUrl']) && !empty($_POST['startTemplateUrl'])) {
            $startTemplateUrl = sanitizeFileName($_POST['startTemplateUrl']);
            $html = file_get_contents($startTemplateUrl);
        } else if (isset($_POST['html'])) {
            $html = substr($_POST['html'], 0, MAX_FILE_LIMIT);
        }

        $file = sanitizeFileName($_POST['file']);

        if (file_put_contents($file, $html)) {
            echo "File saved $file";
        } else {
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            echo "Error saving file  $file\nPossible causes are missing write permission or incorrect file path!";
        }
    }

    public function ScanPages(){
        if (isset($_POST['mediaPath'])) {
            define('UPLOAD_PATH', $_POST['mediaPath']);
        } else {
            define('UPLOAD_PATH', 'media');
        }

        $scandir = __DIR__ . '/' . UPLOAD_PATH;

// Run the recursive function
// This function scans the files folder recursively, and builds a large array

        $scan = function ($dir) use ($scandir, &$scan) {
            $files = [];

            // Is there actually such a folder/file?
            if (file_exists($dir)) {
                foreach (scandir($dir) as $f) {
                    if (! $f || $f[0] == '.') {
                        continue; // Ignore hidden files
                    }

                    if (is_dir($dir . '/' . $f)) {
                        // The path is a folder

                        $files[] = [
                            'name'  => $f,
                            'type'  => 'folder',
                            'path'  => str_replace($scandir, '', $dir) . '/' . $f,
                            'items' => $scan($dir . '/' . $f), // Recursively get the contents of the folder
                        ];
                    } else {
                        // It is a file

                        $files[] = [
                            'name' => $f,
                            'type' => 'file',
                            'path' => str_replace($scandir, '', $dir) . '/' . $f,
                            'size' => filesize($dir . '/' . $f), // Gets the size of this file
                        ];
                    }
                }
            }

            return $files;
        };

        $response = $scan($scandir);

// Output the directory listing as JSON

        header('Content-type: application/json');

        echo json_encode([
            'name'  => '',
            'type'  => 'folder',
            'path'  => '',
            'items' => $response,
        ]);
    }

    public function UploadPages(){
        define('UPLOAD_FOLDER', __DIR__ . '/');
        if (isset($_POST['mediaPath'])) {
            define('UPLOAD_PATH', sanitizeFileName($_POST['mediaPath']) .'/');
        } else {
            define('UPLOAD_PATH', '/');
        }

        $destination = UPLOAD_FOLDER . UPLOAD_PATH . '/' . $_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], $destination);

        if (isset($_POST['onlyFilename'])) {
            echo $_FILES['file']['name'];
        } else {
            echo UPLOAD_PATH . $_FILES['file']['name'];
        }
    }
}
