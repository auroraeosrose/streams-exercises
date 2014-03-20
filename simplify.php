<?php 

$tempfile = tempnam(sys_get_temp_dir());

// get the file from FTP to local disk
$fh = ftp_connect('ftphost.com', 21);
ftp_login($fh, 'username', 'password');
ftp_get($fh,$tempfile, '/path/to/file.dat.gz');
ftp_close($fh);

// read data from local .gz into var
$gh = gzopen($tempfile, 'r');
$data = gzread($gh, 1000000);
gzclose($gh);
unlink($tempfile);

// write data to local .dat
file_put_contents('/local/copy/of/file.dat', $data); 

?>

Or you can use

<?php 

copy('compress.zlib://ftp://username:password@ftphost.com:21/path/to/file.dat.gz', '/local/copy/of/file.dat'); 