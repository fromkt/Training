<?php

namespace Gettext\Generators;

use Gettext\Translations;
use Gettext\Utils\MultidimensionalArrayTrait;

class PhpArray_custom extends Generator implements GeneratorInterface
{
    use MultidimensionalArrayTrait;

    public static $options = [
        'includeHeaders' => true,
    ];

    /**
     * {@inheritdoc}
     */
    public static function toString(Translations $translations, array $options = [])
    {
        $array = self::generate($translations, $options);

        return '<?php return '.var_export($array, true).';';
    }

    /**
     * Generates an array with the translations.
     *
     * @param Translations $translations
     * @param array        $options
     *
     * @return array
     */
    public static function generate(Translations $translations, array $options = [])
    {
        $options += static::$options;

        $pluralForm = $translations->getPluralForms();
        $pluralSize = is_array($pluralForm) ? ($pluralForm[0] - 1) : null;
        $messages = [];

        $includeHeaders = $options['includeHeaders'];
        $forceArray = true;

        if ($includeHeaders) {
            $messages[''] = [
                '' => [self::generateHeaders($translations)],
            ];
        }
        
        $i = 0;
        foreach ($translations as $translation) {
            if ($translation->isDisabled()) {
                continue;
            }

            $context = $translation->getContext();
            $original = $translation->getOriginal();
            $references = $translation->getReferences();
            $plural = $translation->getPlural();

            if (!isset($messages[$i])) {
                $messages[$i] = [];
            }

            if ($translation->hasPluralTranslations(true)) {
                $messages[$i][$original]['msg'] = $translation->getPluralTranslations($pluralSize);
                array_unshift($messages[$i][$original]['msg'], $translation->getTranslation());
            } elseif ($forceArray) {
                $messages[$i][$original]['msg'] = [$translation->getTranslation()];
            } else {
                $messages[$i][$original]['msg'] = $translation->getTranslation();
            }

            $messages[$i][$original]['references'] = $references;
            $messages[$i][$original]['context'] = $context;
            $messages[$i][$original]['plural'] = $plural;
            $i++;
        }

        return [
            'domain' => $translations->getDomain(),
            'plural-forms' => $translations->getHeader('Plural-Forms'),
            'messages' => $messages,
        ];

    }
}
