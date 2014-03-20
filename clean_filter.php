<?php

class Clean_Filter extends PHP_User_Filter
{
    private $data;
    private $words;

    // Read in our dirty words, and set our data to nothing
    public function onCreate()
    {
        $this->data = '';
        $words = file(__DIR__ . DIRECTORY_SEPARATOR . 'badwords.txt');
        $words = array_map('trim', $words);
        $words = array_map('preg_quote', $words);
        $this->words = '/' . implode('|', $words) . '/i';
        return true;
    }

    // Actually do the filtering of the data
    public function filter($in, $out, &$consumed, $closing)
    {

        // We're going to grab any data that we can and buffer it in our data var
        while($bucket = stream_bucket_make_writeable($in)) {
            $this->data .= $bucket->data;
            $this->bucket = $bucket;
            $consumed = 0; // notice we're not eating any of it
        }
 
        // if we are closing the stream, we take all the data we stored
        // process it, and continue
        if($closing)
        {
            $consumed += strlen($this->data);
 
            $str = preg_replace($this->words,
                                '',
                                $this->data);
 
            $this->bucket->data = $str;
            $this->bucket->datalen = strlen($this->data);
 
            if(!empty($this->bucket->data))
                stream_bucket_append($out, $this->bucket);
 
            return PSFS_PASS_ON;
        }

        // normally all we do is say "feed me more" until we're ready to close
        return PSFS_FEED_ME;
    }
}