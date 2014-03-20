<?php
/**
 * Stream.php - PHP stream interfaces
 *
 * This is released under the New BSD, see license.txt for details
 *
 * @author       Elizabeth M Smith <auroraeosrose@php.net>
 * @copyright    Elizabeth M Smith (c) 20011
 * @link         http://forkr.net
 * @license      http://www.opensource.org/licenses/bsd-license.php New BSD
 * @since        Php 5.3
 */

/**
 * Put all of our interfaces in the Streams namespace
 */
namespace Streams;

/**
 * Stream - implement to do a basic userland stream wrapper
 *
 * This is the absolute base you can implement to create a user stream
 */
interface Stream
{
    /**
     * public function stream_open
     * 
     * This method is called immediately after your stream object is created.
     * You can use parse_url() to break path apart.
     * You are responsible for checking that mode is valid for the path requested.
     * STREAM_USE_PATH: use the include_path
     * STREAM_REPORT_ERRORS: trigger_error() during opening of the stream
     * If the path is opened successfully, and STREAM_USE_PATH is set in options, you should set opened_path to the full path of the file/resource that was actually opened.
     *
     * @param string $path url passed to fopen()
     * @param string $mode see fopen docs for mode choices
     * @param int $options STREAM_USE_PATH and STREAM_REPORT_ERRORS optionally orred together
     * @param string $opened_path path actually opened
     * @return type bool
     */
    public function stream_open($path, $mode, $options, $opened_path);

    /**
     * public function stream_close
     * 
     * called on fclose() - release resources
     *
     * @return void
     */
    public function stream_close();

    /**
     * public function stream_read
     * 
     * fread() and fgets() - return up-to count bytes of data from the current
     * read/write position as a string.  If no more data is available, return
     * either FALSE or an empty string. Update the read/write position of the
     * stream by the number of bytes that were successfully read.
     *
     * @param int $count
     * @return type about
     */
    public function stream_read($count);

    /**
     * public function stream_write
     * 
     * fwrite() calls - store data into the underlying storage used by stream.
     * If there is not enough room, store as many bytes as possible. Return the
     * number of bytes that were successfully stored in the stream, or 0 if
     * none could be stored. Update the read/write position of the stream by
     * the number of bytes that were successfully written.
     *
     * @param string $data data to write
     * @return int number of bytes stored
     */
    public function stream_write($data);

    /**
     * public function stream_eof
     * 
     * used for feof() return TRUE if the read/write position is at the end of
     * the stream and if no more data is available to be read
     *
     * @return bool
     */
    public function stream_eof();

    /**
     * public function stream_stat
     * 
     * fstat() - returns array(dev, ino, mode, nlink, uid, gid, size, atime,
     *           mtime, ctime) and may have additional items
     *
     * @return array
     */
    public function stream_stat();
}

/**
 * Stat - implement to do a basic userland stream wrapper
 *
 * Add this in addition to your basic stream wrapper for functionality
 *
 *  chmod() (only when safe_mode is enabled)
 *  copy()
 *  fileperms()
 *  fileinode()
 *  filesize()
 *  fileowner()
 *  filegroup()
 *  fileatime()
 *  filemtime()
 *  filectime()
 *  filetype()
 *  is_writable()
 *  is_readable()
 *  is_executable() (note, buggy)
 *  is_file()
 *  is_dir()
 *  is_link()
 *  file_exists()
 *  lstat()
 *  stat()
 *  SplFileInfo items
 *  RecursiveDirectoryIterator::hasChildren()
 */
interface Stat
{
    /**
     * public function url_stat()
     * 
     * stat() call wrapper
     * STREAM_URL_STAT_LINK: For resources with the ability to link to other
     * resource (such as an HTTP Location: forward, or a filesystem symlink).
     * This flag specified that only information about the link itself should
     * be returned, not the resource pointed to by the link.
     * This flag is set in response to calls to lstat(), is_link(), or filetype()
     * STREAM_URL_STAT_QUIET: If this flag is set, your wrapper should not
     * raise any errors, otherwise use trigger_error() function during stating
     *
     * 0	dev	device number
     * 1	ino	inode number
     * 2	mode	inode protection mode
     * 3	nlink	number of links
     * 4	uid	userid of owner
     * 5	gid	groupid of owner
     * 6	rdev	device type, if inode device *
     * 7	size	size in bytes
     * 8	atime	time of last access (Unix timestamp)
     * 9	mtime	time of last modification (Unix timestamp)
     * 10	ctime	time of last inode change (Unix timestamp)
     * 11	blksize	blocksize of filesystem IO *
     * 12	blocks	number of blocks allocated
     *  * - only valid on systems supporting the st_blksize type--other systems (i.e. Windows) return -1.
     *
     * @param string $path path to stat
     * @param int $flags STREAM_URL_STAT_LINK | STREAM_URL_STAT_QUIET
     * @return array
     */
    public function url_stat($path, $flags);
}

/**
 * DIR - add directory functionality
 */
interface Dir
{
    /**
     * public function dir_opendir
     * 
     * opendir() wrapper
     *
     * @param string $path path to open
     * @param int $options STREAM_REPORT_ERRORS
     * @return bool
     */
    public function dir_opendir($path, $options);

    /**
     * public function dir_readdir
     * 
     * readdir() wrapper
     *
     * @return string next filename
     */
    public function dir_readdir();

    /**
     * public function dir_rewinddir
     * 
     * rewind dir
     *
     * @return bool
     */
    public function dir_rewinddir();

    /**
     * public function dir_closedir
     * 
     * release any resources which were locked or allocated during the opening
     * and use of the directory stream. 
     *
     * @return bool
     */
    public function dir_closedir();
}

/**
 * WriteableDir adds writing and deleting directories - note this
 * is the only interface that extends another since the directory functionality
 * will be required as well
 */
interface WriteableDir extends Dir
{
    /**
     * public function mkdir
     * 
     * mkdir() calls on URL paths
     * create the directory specified by path
     *
     * @param string $path path to create
     * @param int $mode octal permissions mode
     * @param int $options STREAM_REPORT_ERRORS and STREAM_MKDIR_RECURSIVE
     * @return bool
     */
    public function mkdir($path, $mode, $options);

    /**
     * public function rmdir
     * 
     * removes a dir
     *
     * @param string $path path to delete
     * @param int $options STREAM_REPORT_ERRORS
     * @return bool
     */
    public function rmdir($path, $options);
}

/**
 * If we can create and unlink, we can rename
 * even if all the wrapper does is call create and then unlinks the old ;)
 */
interface Unlink
{
    /**
     * public function unlink
     * 
     * unlink() calls on URL paths associated with the wrapper and should
     * delete the item specified by path
     *
     * @param string $path path to delete
     * @return bool
     */
    public function unlink($path);

    /**
     * public function rename
     * 
     * rename() calls rename the item specified by path_from to the
     * specification given by path_to.
     *
     * @param string $path_from original path name
     * @param string $path_to new path name
     * @return bool
     */
    public function rename($path_from, $path_to);
}

/**
 * Seek - adds stream seek/tell functionality
 *
 * used for streams where you can move forward in the data
 */
interface Seek
{

    /**
     * public function stream_tell
     * 
     * ftell() return the current read/write position of the stream
     *
     * @return int current stream position
     */
    public function stream_tell();

    /**
     * public function stream_seek
     * 
     * fseek() update the read/write position of the stream according to offset
     * and whence. Return TRUE if the position was updated
     * SEEK_SET - Set position equal to offset bytes
     * SEEK_CUR - Set position to current location plus offset
     * SEEK_END - Set position to end-of-file plus offset
     *
     * @param int $offset bytes to seek
     * @param int $whence SEEK_SET|SEEK_CUR|SEEK_END
     * @return bool
     */
    public function stream_seek($offset, $whence);
}

/**
 * adds flushing cached data
 *
 * most streams should implement this if at all possible
 */
interface Flush
{
    /**
     * public function stream_flush
     * 
     * fflush() - If you have cached data in your stream but not yet stored it
     * into the underlying storage, you should do so now. Return TRUE if the
     * cached data was successfully stored (or if there was no data to store)
     *
     * @return bool
     */
    public function stream_flush();
}