<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

class Gm_StringReader
{
    public $pos;
    public $str;
    public $strlen;

    /**
     * Constructor.
     *
     * @param string $str The string to read
     */
    public function __construct($str)
    {
        $this->str = $str;
        $this->strlen = strlen($this->str);
    }

    /**
     * Read and returns a part of the string.
     *
     * @param int $bytes The number of bytes to read
     *
     * @return string
     */
    public function read($bytes)
    {
        $data = substr($this->str, $this->pos, $bytes);

        $this->seekto($this->pos + $bytes);

        return $data;
    }

    /**
     * Move the cursor to a specific position.
     *
     * @param int $pos The amount of bytes to move
     *
     * @return int The new position
     */
    public function seekto($pos)
    {
        $this->pos = ($this->strlen < $pos) ? $this->strlen : $pos;

        return $this->pos;
    }
}
?>