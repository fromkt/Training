<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

require_once dirname(__FILE__) . '/_translations.php';
require_once dirname(__FILE__) . '/_stringreader.php';
require_once dirname(__FILE__) . '/_parser.php';
require_once dirname(__FILE__) . '/_stringreader.php';

class Gm_Mo_Parser extends Gm_Parser
{
    public $MAGIC1 = -1794895138;
    public $MAGIC2 = -569244523;
    public $MAGIC3 = 2500072158;

    public function __construct()
    {
    }

    public function fromString($string, $translations, $options = array())
    {
        $stream = new Gm_StringReader($string);
        $magic = $this->readInt($stream, 'V');

        if (($magic === $this->MAGIC1) || ($magic === $this->MAGIC3)) { //to make sure it works for 64-bit platforms
            $byteOrder = 'V'; //low endian
        } elseif ($magic === ($this->MAGIC2 & 0xFFFFFFFF)) {
            $byteOrder = 'N'; //big endian
        } else {
            throw new Exception('Not MO file');
        }

        $this->readInt($stream, $byteOrder);

        $total = $this->readInt($stream, $byteOrder); //total string count
        $originals = $this->readInt($stream, $byteOrder); //offset of original table
        $tran = $this->readInt($stream, $byteOrder); //offset of translation table

        $stream->seekto($originals);
        $table_originals = $this->readIntArray($stream, $byteOrder, $total * 2);

        $stream->seekto($tran);
        $table_translations = $this->readIntArray($stream, $byteOrder, $total * 2);

        for ($i = 0; $i < $total; ++$i) {
            $next = $i * 2;

            $stream->seekto($table_originals[$next + 2]);
            $original = $stream->read($table_originals[$next + 1]);

            $stream->seekto($table_translations[$next + 2]);
            $translated = $stream->read($table_translations[$next + 1]);

            if ($original === '') {
                // Headers
                foreach (explode("\n", $translated) as $headerLine) {
                    if ($headerLine === '') {
                        continue;
                    }

                    $headerChunks = preg_split('/:\s*/', $headerLine, 2);
                    $translations->setHeader($headerChunks[0], isset($headerChunks[1]) ? $headerChunks[1] : '');
                }

                continue;
            }

            $chunks = explode("\x04", $original, 2);

            if (isset($chunks[1])) {
                $context = $chunks[0];
                $original = $chunks[1];
            } else {
                $context = '';
            }

            $chunks = explode("\x00", $original, 2);

            if (isset($chunks[1])) {
                $original = $chunks[0];
                $plural = $chunks[1];
            } else {
                $plural = '';
            }

            $translation = $translations->insert($context, $original, $plural);

            if ($translated === '') {
                continue;
            }

            if ($plural === '') {
                $translation->setTranslation($translated);
                continue;
            }

            $v = explode("\x00", $translated);
            $translation->setTranslation(array_shift($v));
            $translation->setPluralTranslations($v);
        }
    }

    protected function readInt($stream, $byteOrder)
    {
        if (($read = $stream->read(4)) === false) {
            return false;
        }

        $read = unpack($byteOrder, $read);

        return array_shift($read);
    }

    protected function readIntArray($stream, $byteOrder, $count)
    {
        return unpack($byteOrder.$count, $stream->read(4 * $count));
    }

}   //end Class Gm_Mo_Parser
?>