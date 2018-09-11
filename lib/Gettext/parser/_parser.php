<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

Class Gm_Parser
{
    public function __construct()
    {
    }

    public function fromString($string, $translations, $options=array())
    {
    }

    public function fromFile($file, $translations, $options=array())
    {
        foreach ($this->getFiles($file) as $file) {

            $options['file'] = $file;

            $this->fromString($this->read_File($file), $translations, $options);

        }
    }

    protected function read_File($file)
    {
        $length = filesize($file);

        if (!($fd = fopen($file, 'rb'))) {
            throw new Exception("Cannot read the file '$file', probably permissions");
        }

        $content = $length ? fread($fd, $length) : '';
        fclose($fd);

        return $content;
    }

    protected function getFiles($file)
    {
        if (empty($file)) {
            throw new InvalidArgumentException('There is not any file defined');
        }

        if (is_string($file)) {
            if (!is_file($file)) {
                throw new InvalidArgumentException("'$file' is not a valid file");
            }

            if (!is_readable($file)) {
                throw new InvalidArgumentException("'$file' is not a readable file");
            }

            return array($file);
        }

        if (is_array($file)) {
            $files = array();

            foreach ($file as $f) {
                $files = array_merge($files, $this->getFiles($f));
            }

            return $files;
        }

        throw new InvalidArgumentException('The first argument must be string or array');
    }

    public function generateHeaders($translations){
        $headers = array();

        foreach ($translations->getHeaders() as $name => $value) {
            $headers[$name] = $value;
        }

        return $headers;
    }

    public function return_array($translations, $includeHeaders, $forceArray = false)
    {
        $pluralForm = $translations->getPluralForms();
        $pluralSize = is_array($pluralForm) ? ($pluralForm[0] - 1) : null;
        $messages = array();

        foreach ($translations as $translation) {
            if ($translation->isDisabled()) {
                continue;
            }
            
            /*
            $context = $translation->getContext();
            $original = $translation->getOriginal();

            if (!isset($messages[$context])) {
                $messages[$context] = array();
            }

            if ($translation->hasPluralTranslations(true)) {
                $messages[$context][$original] = $translation->getPluralTranslations($pluralSize);
                array_unshift($messages[$context][$original], $translation->getTranslation());
            } elseif ($forceArray) {
                $messages[$context][$original] = array($translation->getTranslation());
            } else {
                $messages[$context][$original] = $translation->getTranslation();
            }
            */

            $original = $translation->getOriginal();

            if ($translation->hasPluralTranslations(true)) {
                $messages[$original] = $translation->getPluralTranslations($pluralSize);
                array_unshift($messages[$original], $translation->getTranslation());
            } elseif ($forceArray) {
                $messages[$original] = array($translation->getTranslation());
            } else {
                $messages[$original] = $translation->getTranslation();
            }
        }
        
        $parsers = array(
            'domain'        => $translations->getDomain(),
            'file_paths'    => $translations->get_filepath(),
            'plural-forms'  => $translations->getHeader('Plural-Forms'),
            'messages'      => $messages,
        );

        if ($includeHeaders) {
            $parsers['header'] = array($this->generateHeaders($translations));
        }

        return $parsers;
    }
}
?>