<?php
include 'auth.php';
$uuid = $_COOKIE["UUID"];
$file = "users.csv";
if(getUserData($file,$uuid)[2]=="1"){
    //echo "you have an elevation";
}
else {
    header('Location: /members/login/login.php');
    echo "If you are seeing this message, you should have been redirected. Make sure your browser supports redirects or <a href='/members/login/login.php'>click here</a>.";
}
?>
<head>
<style>
#people {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

#people td, #people th {
    border: 1px solid #ddd;
    padding: 8px;
}

#people tr:nth-child(even){background-color: #f2f2f2;}

#people tr:hover {background-color: #ddd;}

#people th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #4CAF50;
    color: white;
}
</style>
</head>

<?php
$file = "users.csv";

function genUuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

function editUserdata($data, $file){
	$searchfor = $data[1];
	$contents = file_get_contents($file);
	$pattern = preg_quote($searchfor, '/');
	$pattern = "/^.*$pattern.*\$/m";
	$lines = explode("\n", $contents);
	foreach ($lines as $line){
		$linesDrecryped[] = encryptDecrypt("decrypt", $line);
	}
	$contents = implode(",",$linesDrecryped);
	// finalise the regular expression, matching the whole line
	if (preg_match_all($pattern, $contents, $matches)) {
		echo "<p>that user allready exists</p><br>";
		header('Location: '.$_SERVER['PHP_SELF']."?allreadyExists=True");
	} else {
		$stringData = implode (",", $data);
		$fh = fopen($file, 'a') or die("can't open file");
		fwrite($fh, "\n".encryptDecrypt("encrypt", $stringData));
		fclose($fh);
		header('Location: '.$_SERVER['PHP_SELF'].$stringData);
	}
	die;
}
function removeLine($line,$file){
	$fileData = file($file); // Read the whole file into an array

	//Delete the recorded line
	unset($fileData[$line-1]);

	//Recorded in a file
	file_put_contents($file, rtrim(implode("", $fileData)));
	header('Location: '.$_SERVER['PHP_SELF']);
	die;
}
function displayTable($file){
	$fileContents = file_get_contents($file);
	$lines = explode("\n", $fileContents);
	$id = 0;
	foreach ($lines as $line){
		$linesDrecryped[] = encryptDecrypt("decrypt", $line);
	}
	foreach ($linesDrecryped as $line) {
		$linesCsv[] = str_getcsv($line);
	}
	echo "<center>\n\t<table id='people' style='width:80%'>\n";
	echo <<<END
			<tr>
				<th>Username</th>
				<th>Email</th>
				<th>Elevation</th>
				<th>Password</th>
				<th>AuthUUID</th>
				<th>ID</th>
			</tr>
END;
	foreach ($linesCsv as $line){
		$id += 1;
		echo "\t\t<tr>\n";
		foreach ($line as $cell){
			echo "\t\t\t<td>" . htmlspecialchars($cell) . "</td>\n";
		}
		echo "\t\t\t<td> ". ($id) ."</td>\n";
		echo "\t\t</tr>\n";
	}
	echo "\t</table>\n";
}
$username = $_POST["username"];
$email = $_POST["email"];
$elevation = $_POST["elevation"];
$password = $_POST["password"];
$uuid = genUuid();
if (isset($email)){
	$data = ['"'.$username.'"','"'.$email.'"','"'.$elevation.'"','"'.$password.'"','"'.$uuid.'"'];
	editUserdata($data, $file);
}
if (isset($id)){
	removeLine($id,$file);
}
displayTable($file);

?>
<h3>
		Add a user!!!
	</h3>
	<form action="" method="post">
		<input name="username"	type="text" placeholder="username"	required><br>
		<input name="email"		type="text" placeholder="email"		required><br>
		<input name="elevation"	type="text" placeholder="elevation"			><br>
		<input name="password"	type="text" placeholder="password"			><br>
		<input type="submit">
	</form>