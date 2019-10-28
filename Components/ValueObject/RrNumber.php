<?php

namespace EMS\FormBundle\Components\ValueObject;

class RrNumber
{
    /** @var integer */
    protected $base;

    /** @var integer */
    private $year;

    /** @var integer */
    private $month;

    /** @var integer */
    private $day;

    /** @var integer */
    private $dayCounter;

    /** @var integer */
    private $controlNumber;

    const RRN = '/(?<base>(?<year>\d\d)(?<month>\d\d)(?<day>\d\d)(?<dayCounter>\d\d\d))(?<controlNumber>\d\d)/m';

    /**
     * Rijksregisternummer
     *
     * Dit nummer wordt uitgereikt door het Rijksregister en bestaat uit 11 cijfers. Het heeft de volgende structuur:
     * •    de eerste 6 cijfers vormen de geboortedatum in het formaat YYMMDD met DD= # dagen, MM= # maanden, YY= # jaren;
     * •    de volgende 3 cijfers zijn de dagteller van de geboorten; dit getal is paar voor een vrouw en onpaar voor een man;
     * •    de laatste 2 cijfers vormen het controlegetal.
     * De geboortedatum kan onvolledig zijn. Dit betekent dat zowel de maand als de dag de waarde 0 kan hebben.
     * Het controlegetal wordt berekend op basis van de formule:
     * controlegetal = 97 – ((de eerste 9 cijfers van het INSZ) modulo 97)
     * Voor personen die in de 21e eeuw geboren zijn, wordt vóór de eerste 9 cijfers het getal 2 geplaatst waarna dezelfde formule wordt gebruikt.
     * Bij het wijzigen van de geboortedatum of het geslacht wordt een nieuw rijksregisternummer uitgereikt.
     *
     * valid rrn : 00/00/00-000.97
     */
    public function __construct(string $number)
    {
        $rrn = (new NumberValue($number))->getDigits();
        preg_match_all(self::RRN, $rrn, $matches, PREG_SET_ORDER, 0);

        $data = $matches[0];
        $this->base = $data['base'];
        $this->year = $data['year'];
        $this->month = $data['month'];
        $this->day = $data['day'];
        $this->dayCounter = $data['dayCounter'];
        $this->controlNumber = $data['controlNumber'];

        if (!$this->validate() || strlen($rrn) > 11) {
            throw new \Exception(sprintf('invalid rrn data: %s', $number));
        }
    }

    protected function validate(): bool
    {
        $valid = $this->controlNumber == (97 - ($this->base % 97));

        if (!$valid && $this->possiblyTwentyFirstCentury()) {
            $valid = $this->controlNumber == (97 - ((2 . $this->base) % 97));
        }

        return $valid;
    }

    private function possiblyTwentyFirstCentury(): bool
    {
        return date("y") <= $this->year;
    }
    
    public function transform(): string
    {
        return sprintf('%d%d', $this->base, $this->controlNumber);
    }
}
