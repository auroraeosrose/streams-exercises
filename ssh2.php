<?php 
$connection = ssh2_connect('example.com', 22);
ssh2_auth_password($connection, 'username', password');

// clean our remote directory
$stream = ssh2_exec($connection, 'rm -$f /home/myfiles/*', false);
stream_set_blocking($stream, true);
fclose($stream);

// stick new stuff into the remote directory
$sftp = ssh2_sftp($connection);
foreach($files as $remote => $local) {
	$path = dirname($remote);
	if(!is_dir("ssh2.sftp://{$sftp}$path")) {
		mkdir("ssh2.sftp://{$sftp}$path", 0755, true);
	}
	if(file_exists($local)) {
		copy($local, "ssh2.sftp://{$sftp}$remote");
	}
}