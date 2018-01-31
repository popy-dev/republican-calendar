<?php

namespace Popy\RepublicanCalendar;

use Popy\RepublicanCalendar\Locale\HardcodedFrench as DefaultLocale;

class SymbolFormater
{
    /**
     * Locale (used for day & month names)
     *
     * @var LocaleInterface
     */
    protected $locale;

    /**
     * Roman number convertor.
     *
     * @var RomanConverter
     */
    protected $converter;

    static protected $symbols = array(
        'y', 'Y', 'L',
        'F', 'm', 'n', 't',
        'd', 'j', 'l', 'S', 'w', 'z', 'N', 'D',
        'W'
    );

    /**
     * Class constructor.
     *
     * @param LocaleInterface|null $locale
     * @param RomanConverter|null  $converter
     */
    public function __construct(LocaleInterface $locale = null, RomanConverter $converter = null)
    {
        $this->locale = $locale ?: new DefaultLocale();
        $this->converter = $converter ?: new RomanConverter();
    }

    public function handles($symbol)
    {
        return in_array($symbol, static::$symbols);
    }

    /**
     * Get a date-format symbol formatted result.
     *
     * @param Date   $input  Input date.
     * @param string $symbol Symbol.
     *
     * @return string|null
     */
    public function format(Date $input, $symbol)
    {
        if ($symbol === 'y') {
            return $this->converter->decimalToRoman($input->getYear());
        }

        if ($symbol === 'Y') {
            return $input->getYear();
        }

        if ($symbol === 'L') {
            // Is leap year
            return $input->isLeap();
        }

        if ($symbol === 'F') {
            // Month name
            return $this->locale->getMonthName($input->getMonth());
        }

        if ($symbol === 'm') {
            // Mois avec les zéros initiaux
            return substr('0' . $input->getMonth(), -2);
        }

        if ($symbol === 'n') {
            // Mois sans les zéros initiaux
            return $input->getMonth();
        }

        if ($symbol === 't') {
            // Nombre de jours dans le mois
            if ($input->getMonth() < 13) {
                return 30;
            }
            
            return $this->format($input, 'L') ? 6 : 5;
        }

        if ($symbol === 'd') {
            // Jour du mois, sur deux chiffres (avec un zéro initial)
            return substr('0' . $input->getDay(), -2);;
        }

        if ($symbol === 'j') {
            // Jour du mois sans les zéros initiaux
            return $input->getDay();
        }

        if ($symbol === 'l') {
            // Jour de la semaine, textuel, version longue
            return $this->locale->getWeekDayName($this->format($input, 'w'));
        }

        if ($symbol === 'D') {
            // Jour de la semaine, textuel, version longue
            return $this->locale->getDayName($this->format($input, 'z'));
        }

        if ($symbol === 'S') {
            // Suffixe ordinal d'un nombre pour le jour du mois, en anglais, sur deux lettres
            return $this->locale->getNumberOrdinalSuffix($input->getDay());
        }

        if ($symbol === 'w') {
            // Jour de la semaine au format numérique
            return ($input->getDay() - 1) % 10;
        }

        if ($symbol === 'z') {
            // Jour de l'année
            return $input->getMonth() * 30 + $input->getDay() - 30 - 1;
        }

        if ($symbol === 'N') {
            // Représentation numérique ISO-8601 du jour de la semaine (ajouté en PHP 5.1.0)    
            return $this->format($input, 'w') + 1;
        }

        if ($symbol === 'W') {
            // Numéro de semaine dans l'année
            return $this->format($input, 'z') % 10;
        }

        return $symbol;
    }
}
