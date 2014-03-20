<?php
/**
 * This class implements a user-space stream that reads/writes to the wincache
 * user cache - this is really rather evil
 *
 * Specify the key of the item after "wincache://foobar:optionalttl"
 */
class Wincache_Stream {

    /**
     * current position in the stream
     *
     * @var int position
     */
    public $pos = 0;

    /**
     * name of wincache key
     *
     * @var  string key
     */
    public $key;

    /**
     * ttl to use for the value
     *
     * @var  int ttl
     */
    public $ttl;

    /**
     * data in our key
     *
     * @var mixed data
     */
    public $data;

    /**
     * stat data is cached because
     * it uses an expensive call
     *
     * @var array
     */
    public $stat;

    /**
     * can we read this?
     *
     * @var bool
     */
    public $read = false;

    /**
     * can we write this?
     *
     * @var bool
     */
    public $write = false;

    /**
     * Stream opener
     *
     * @param string  $path         URL-style path to the segment
     * @param string  $mode         mode to open the segment with
     * @param integer $options      stream options
     * @param string  &$opened_path (not used)
     * @return boolean              Stream opened sucessfully?
     */
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $url = parse_url($path);
        $this->key = $url['host'];

        if (isset($url['port'])) {
            $this->ttl = intval($url['port']);
        } else {
            $this->ttl = 0;
        }

        $mode = str_split($mode);
        // isset is faster then in_array
        $mode = array_flip($mode);

        // read and write flags
        if (isset($mode['+']) || isset($mode['r'])) {
            $this->read = true;
        }
        if (!isset($mode['r']) ||
            (isset($mode['r']) && isset($mode['+']))) {
            $this->write = true;
        }

        // error if does not exist
        if (isset($mode['r'])) {
            if (!wincache_ucache_exists($this->key)) {
                if ($options & STREAM_REPORT_ERRORS)  {
                    trigger_error(__METHOD__ . ' ' . $this->key . ' does not exist', E_USER_ERROR);
                }
                return false;
            }
        // error if does exist
        } elseif (isset($mode['x'])) {
            if (wincache_ucache_exists($this->key)) {
                if ($options & STREAM_REPORT_ERRORS)  {
                    trigger_error(__METHOD__ . ' ' . $this->key . ' already exists', E_USER_ERROR);
                }
                return false;
            }
         // create if not exists, truncate if exists
        } elseif (isset($mode['w'])) {
            wincache_ucache_set($this->key, '', $this->ttl);
        // create if not exists, ignore if exists
        } else {
            wincache_ucache_add($this->key, '', $this->ttl);
        }

        // grab our data and cache it locally
        $this->data = wincache_ucache_get($this->key);

        // pointer at end of file
        if (isset($mode['a'])) {
            $this->pos = strlen($this->data);
        // otherwise pointer at beginning
        } else {
            $this->pos = 0;
        }
        return true;
    }

    /**
     * Stream closer
     * save the data out to wincache
     */
    public function stream_close()
    {
        $this->stream_flush();
    }


    /**
     * Read from stream
     *
     * @param integer $count How many bytes to read from the stream
     * @return string        Data read from the stream
     */
    public function stream_read($count)
    {
        if (!$this->read) {
            return false;
        }

        // Don't read past the end of the stream
        if ($count + $this->pos > strlen($this->data)) {
            $count = strlen($this->data) - $this->pos;
        }

        $data = substr($this->data, $this->pos, $count);
        $this->pos += strlen($data);
        return $data;
    }

    /**
     * Write to stream
     *
     * @param  mixed   $data Data to write to the stream
     * @return integer       Bytes actually written to the stream
     */
    public function stream_write($data)
    {
        if (!$this->write) {
            return false;
        }
        $length = strlen($data);

        $this->data .= $data;

        $this->pos += $length;
        return $length;
    }

    /**
     * Check stream end-of-file
     *
     * @return boolean Is the stream position at the end of the stream?
     */
    public function stream_eof()
    {
        return ($this->pos == (strlen($this->data)));
    }

    /**
     * Get stream position
     *
     * @return integer The current position in the stream
     */
    public function stream_tell()
    {
        return $this->pos;
    }

    /**
     * Adjust current position in the stream
     *
     * @param  integer $offset How many bytes to move the position
     * @param  integer $whence Where to start counting from
     * @return boolean         Was the position adjustment successful?
     */
    public function stream_seek($offset,$whence)
    {
        $size = strlen($this->data);
        switch ($whence) {
            case SEEK_SET:
                if (($offset >= 0) && ($offset < $size)) {
                    $this->pos = $offset;
                    return true;
                } else {
                    return false;
                }
                break;
            case SEEK_CUR:
                if (($offset >= 0) && (($this->pos + $offset) < $size)) {
                    $this->pos += $offset;
                    return true;
                } else {
                    return false;
                }
                break;
            case SEEK_END:
                if (($size + $offset) >= 0) {
                    $this->pos = $size + $offset;
                    return true;
                } else {
                    return false;
                }
                break;
            default:
                return false;
        }
    }

    /**
     * Flush data to the stream
     *
     * @return 
     */
    public function stream_flush()
    {
        return wincache_ucache_set($this->key, $this->data, $this->ttl);
    }

    /**
     * Locks the stream
     *
     * @return 
     */
    public function stream_lock($operation)
    {
        if ($operation == LOCK_SH) {
            return wincache_lock($this->key);
        } elseif ($operation == LOCK_EX) {
            return wincache_lock($this->key, true);
        } else {
            return wincache_unlock($this->key);
        }
    }

    /**
     * Flush data to the stream
     *
     * @return 
     */
    public function rename($from, $to)
    {
        $url = parse_url($from);
        $key = $url['host'];

        $data = wincache_ucache_get($key);
        $meta = wincache_ucache_info(false, $key);
        wincache_ucache_delete($key);

        $url = parse_url($to);
        $key = $url['host'];

        return wincache_ucache_set($key, $data, $meta['ucache_entries'][1]['ttl_seconds']);
    }

    /**
     * deletes the cache entry
     *
     * @return 
     */
    public function unlink($path)
    {
        $url = parse_url($path);
        $key = $url['host'];

        return wincache_ucache_delete($key);
    }

    /**
     * Does a stat on the stream
     * used for is_readable checks
     * notice unlike eof and read, this
     * is called with no open_stream called
     * first, so we have to check this ourselves
     */
    public function url_stat($path)
    {

        $url = parse_url($path);
        $key = $url['host'];

        $data = wincache_ucache_info(false, $key);
        $time = time() - $data['ucache_entries'][1]['age_seconds'];

        $stat = array('dev' => 0,
                    'ino' => 0,
                    'mode' => 0100000 | 0777, // is a file plus anything readable
                    'nlink' => 0,
                    'uid' => 0,
                    'gid' => 0,
                    'rdev' => 0,
                    'size' => $data['ucache_entries'][1]['value_size'],
                    'atime' => $time,
                    'mtime' => $time,
                    'ctime' => $time,
                    'blksize' => -1,
                    'blocks' => -1);
        return $stat + array_values($stat);
    }

    /**
     * ick
     */
    public function stream_stat()
    {
        $this->stream_flush();

        $data = wincache_ucache_info(false, $this->key);
        $time = time() - $data['ucache_entries'][1]['age_seconds'];

        $stat = array('dev' => 0,
                    'ino' => 0,
                    'mode' => 0100000 | 0777, // is a file plus anything readable
                    'nlink' => 0,
                    'uid' => 0,
                    'gid' => 0,
                    'rdev' => 0,
                    'size' => $data['ucache_entries'][1]['value_size'],
                    'atime' => $time,
                    'mtime' => $time,
                    'ctime' => $time,
                    'blksize' => -1,
                    'blocks' => -1);
        return $stat + array_values($stat);
    }
}