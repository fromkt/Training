<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

require_once dirname(__FILE__) . '/_translation.php';
require_once dirname(__FILE__) . '/_merge.php';

class Gm_Translations extends ArrayObject
{
    public $HEADER_LANGUAGE = 'Language';
    public $HEADER_PLURAL = 'Plural-Forms';
    public $HEADER_DOMAIN = 'X-Domain';

    public $options = array(
        'defaultHeaders' => array(
            'Project-Id-Version' => '',
            'Report-Msgid-Bugs-To' => '',
            'Last-Translator' => '',
            'Language-Team' => '',
            'MIME-Version' => '1.0',
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Transfer-Encoding' => '8bit',
        ),
        'headersSorting' => false,
        'defaultDateHeaders' => array(
            'POT-Creation-Date',
            'PO-Revision-Date',
        ),
    );
    
    private $file_paths = array();
    private $headers;

    public function __construct($input = array(), $flags = 0, $iterator_class = 'ArrayIterator')
    {
        $this->headers = $this->options['defaultHeaders'];

        foreach ($this->options['defaultDateHeaders'] as $header) {
            $this->headers[$header] = date('c');
        }

        $this->headers[$this->HEADER_LANGUAGE] = '';

        parent::__construct($input, $flags, $iterator_class);
    }

    public function setHeader($name, $value)
    {
        $name = trim($name);
        $this->headers[$name] = trim($value);

        return $this;
    }

    public function getHeader($name)
    {
        return isset($this->headers[$name]) ? $this->headers[$name] : null;
    }

    public function getHeaders()
    {
        if ($this->options['headersSorting']) {
            ksort($this->headers);
        }

        return $this->headers;
    }

    public function deleteHeaders()
    {
        $this->headers = array();

        return $this;
    }

    public function getLanguage()
    {
        return $this->getHeader($this->HEADER_LANGUAGE);
    }

    public function setLanguage($language)
    {
        $this->setHeader($this->HEADER_LANGUAGE, trim($language));

        if (($info = Language::getById($language))) {
            return $this->setPluralForms(count($info->categories), $info->formula);
        }

        throw new Exception(sprintf('The language "%s" is not valid', $language));
    }

    public function hasLanguage()
    {
        $language = $this->getLanguage();

        return (is_string($language) && ($language !== '')) ? true : false;
    }

    public function getPluralForms()
    {
        $header = $this->getHeader($this->HEADER_PLURAL);

        if (!empty($header)
            && preg_match('/^nplurals\s*=\s*(\d+)\s*;\s*plural\s*=\s*([^;]+)\s*;$/', $header, $matches)
        ) {
            return array(intval($matches[1]), $matches[2]);
        }
    }

    public function setPluralForms($count, $rule)
    {
        if (preg_match('/[a-z]/i', str_replace('n', '', $rule))) {
            throw new Exception('Invalid Plural form: ' . $rule);
        }
        $this->setHeader($this->HEADER_PLURAL, "nplurals={$count}; plural={$rule};");

        return $this;
    }

    public function offsetSet($index, $value)
    {
        /*
        if (!($value instanceof Translation)) {
            throw new Exception(
                'Only instances of Gettext\\Translation must be added to a Gettext\\Translations'
            );
        }
        */

        $id = $value->getId();

        if ($this->offsetExists($id)) {
            $this[$id]->mergeWith($value);

            return $this[$id];
        }

        parent::offsetSet($id, $value);

        return $value;
    }

    public function set_filepath($path)
    {
        $this->file_paths[] = trim($path);
    }

    public function get_filepath()
    {
        return $this->file_paths;
    }

    public function setDomain($domain)
    {
        $this->setHeader($this->HEADER_DOMAIN, trim($domain));

        return $this;
    }

    public function getDomain()
    {
        return $this->getHeader($this->HEADER_DOMAIN);
    }

    /**
     * Checks whether the domain is empty or not.
     *
     * @return bool
     */
    public function hasDomain()
    {
        $domain = $this->getDomain();

        return (is_string($domain) && ($domain !== '')) ? true : false;
    }

    public function find($context, $original = '')
    {
        if ($context instanceof Translation) {
            $id = $context->getId();
        } else {
            $id = Translation::generateId($context, $original);
        }

        return $this->offsetExists($id) ? $this[$id] : false;
    }

    /**
     * Count all elements translated
     *
     * @return integer
     */
    public function countTranslated()
    {
        $callback = function (Translation $v) {
            return ($v->hasTranslation()) ? $v->getTranslation() : null;
        };

        return count(array_filter(get_object_vars($this), $callback));
    }

    public function insert($context, $original, $plural = '')
    {
        return $this->offsetSet(null, new Gm_Translation($context, $original, $plural));
    }

    /**
     * Merges this translations with other translations.
     *
     * @param Translations $translations The translations instance to merge with
     * @param int          $options
     *
     * @return self
     */
    public function mergeWith($translations, $options = Gm_Merge::DEFAULTS)
    {
        Gm_Merge::mergeHeaders($translations, $this, $options);
        Gm_Merge::mergeTranslations($translations, $this, $options);

        return $this;
    }
}
?>