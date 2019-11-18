<?php
error_reporting(E_ALL);

$config = include("config.php");
$url = $config['url'];
$db = $config['db'];
$server = $config['server'];
$user = $config['user'];
$pass = $config['pass'];
$dir = $config['directory'];
$length = $config['randomstringlength'];
$randomstring = $config['randomstring'];

try {
$connection = new PDO("mysql:host=$server;dbname=$db", $conndata, $user, $pass);
$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
	echo $e->getMessage();
	die();
}


function generateRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// I don't really know if this function works
function getuserinfo($db, $uid, $row) {
    global $connection;
    $query = $connection->prepare("SELECT * FROM ? WHERE UID = ?");
    $result = $query->execute(array($db, $uid));
    $rowresult = $query->fetchAll();
    return $rowresult;
}

function TokenExists(string $token) {
    global $connection;
    $query = $connection->prepare('SELECT COUNT(UserPassword) FROM sharex WHERE UserPassword = "?"');
    $result = $query->execute(array($token));
    $row = $query->fetchAll();
    return $row > 0;
}


if(isset($_POST['token'])) {
    if(TokenExists($_POST['token'])) {
        if($randomstring) {
            $filename = generateRandomString($length); // TODO MOVE THIS SO I DONT NEED TO REPEAT CODE
            $target = $_FILES["x"]["name"];
            $extension = pathinfo($target, PATHINFO_EXTENSION);

            if (move_uploaded_file($_FILES["x"]["tmp_name"], $dir.$filename.'.'.$extension)) {
                echo $url . $dir . $filename . '.' . $extension;
            } else {
                echo "Possible permission error contact the server administrator.";
            }


        } else {
            // TODO: get file name from sharex and dont use generateRandomString
        }
    } else {
        echo "Wrong Token\nContact server administrator";
    }
} else {
    echo "No POST data received from client.";
}

//remove the connection and unset the db credentials AND config variable
$connection = null;

unset($config, $db, $server, $user, $pass);
