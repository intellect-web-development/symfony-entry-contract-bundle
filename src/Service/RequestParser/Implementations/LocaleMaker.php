<?php

declare(strict_types=1);

namespace IWD\SymfonyEntryContract\Service\RequestParser\Implementations;

use Symfony\Component\HttpFoundation\Request;
use IWD\SymfonyEntryContract\Service\RequestParser\Interfaces\LocaleMakerInterface;
use IWD\SymfonyEntryContract\Dto\Input\Locale;

class LocaleMaker implements LocaleMakerInterface
{
    public const LOCALE_QUERY_PARAM = 'lang';

    public static function make(Request $request): Locale
    {
        $languages = [];
        if ($request->query->has(self::LOCALE_QUERY_PARAM)) {
            $languages[] = $request->query->get(self::LOCALE_QUERY_PARAM);
        }

        $preferredLanguage = $request->getPreferredLanguage();
        if (null !== $preferredLanguage) {
            $languages[] = $preferredLanguage;
        }

        $acceptLanguage = $request->headers->get('Accept-Language');
        if (null !== $acceptLanguage) {
            foreach (explode(',', $acceptLanguage) as $langItem) {
                $result = strstr($langItem, ';', true);
                $languages[] = (false === $result) ? $langItem : $result;
            }
        }
        $languages[] = $request->getDefaultLocale();

        /** @var array<int, string> $languages */
        foreach ($languages as $i => $language) {
            $languages[$i] = str_replace('_', '-', $language);
        }
        $languages = array_unique($languages);

        return new Locale($languages);
    }
}
