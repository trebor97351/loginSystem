<html>
<head>
</head>
<body>
<?php
$file = "users.csv";
include("auth.php");
function checkUserPass($file,$data){
	$searchfor = $data[0];
	$fileContents = file_get_contents($file);
	$linesEncrypted = explode("\n", $fileContents);
	foreach ($linesEncrypted as $line){
		$linesDrecryped[] = encryptDecrypt("decrypt", $line);
	}
	foreach ($linesDrecryped as $line) {
		$linesCsv[] = str_getcsv($line);
	}
	foreach ($linesCsv as $index => $line){
		if ($searchfor == $line[1]){
			$userdata = $line;
		}
	}
	if (($data[0] == $userdata[1]) and ($data[1] == $userdata[3])){
		setcookie("UUID", $userdata[4], time() + (86400), "/");
		header('Location: '.$_SERVER['PHP_SELF'].$stringData);
		return(TRUE);
	}else{
		header('Location: '.$_SERVER['PHP_SELF'].$stringData);
		return(FALSE);
	}
	header('Location: '.$_SERVER['PHP_SELF'].$stringData);
	return(FALSE);
}

$uuid = $_COOKIE['UUID'];
$email = $_POST["email"];
$password = $_POST["password"];
if (isset($email)){
	$data = [$email,$password];
	checkUserPass($file, $data);
}
if (!isset($email)and isset($uuid)){
	if(checkValidUUID($file,$uuid)){
		echo"good UUID";
	}else{
		echo"bad UUID";
	}
}
?>
<h1>Login</h1>
<form action="" method="post">
<input name="email" type="text" placeholder="Email" required>
<input name="password" type="text" placeholder="Password" required>
<input type="submit">
</form>
</body>
</html>