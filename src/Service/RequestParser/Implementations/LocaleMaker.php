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

        if ($preferredLanguage = $request->getPreferredLanguage()) {
            $languages[] = $preferredLanguage;
        }

        if ($acceptLanguage = $request->headers->get('Accept-Language')) {
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
