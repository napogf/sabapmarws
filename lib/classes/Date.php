<?php

class Date extends DateTime
{
    private static $mappaGiorni = array(
        0 => 'domenica',
        1 => 'lunedì',
        2 => 'martedì',
        3 => 'mercoledì',
        4 => 'giovedì',
        5 => 'venerdì',
        6 => 'sabato',
    );

    private static $mappaMesi = array(
        1 => 'gennaio',
        2 => 'febbraio',
        3 => 'marzo',
        4 => 'aprile',
        5 => 'maggio',
        6 => 'giugno',
        7 => 'luglio',
        8 => 'agosto',
        9 => 'settembre',
        10 => 'ottobre',
        11 => 'novembre',
        12 => 'dicembre',
    );

    public function __construct($time = 'now', $timezone = null)
    {
        /**
         * Quando si usano gli slashes / come separatori, la classe DateTime
         * tratta la data come formato Americano MM/DD/YYYY invece che Europeo
         */
        if (preg_match('/^(\d+)\/(\d+)\/(\d+)$/', $time, $matches)) {
            $time = $matches[2] . '/' . $matches[1] . '/' . $matches[3];
        }

        if ($timezone === null) {
            return parent::__construct($time);
        }

        return parent::__construct($time, $timezone);
    }

    public function __toString()
    {
        return $this->toMysql();
    }

    public function toMysql()
    {
        $format = $this->format('Y-m-d');
        if (($ore = $this->format('H:i:s')) !== '00:00:00') {
            $format .= ' ' . $ore;
        }

        return $format;
    }

    public function toReadable()
    {
        $format = $this->format('d/m/Y');
        if ($this->format('H:i:s') !== '00:00:00') {
            $format .= ' alle ' . $this->format('H:i');
        }

        return $format;
    }

    public static function createFromFormat($format, $time, $timezone = null)
    {
        if ($format === 'Y-m-d') {
            $format .= ' H:i:s';
            $time .= ' 00:00:00';
        }

        if ($timezone === null) {
            $datetime = parent::createFromFormat($format, $time);
        } else {
            $datetime = parent::createFromFormat($format, $time, $timezone);
        }

        if (! $datetime) {
            return $datetime;
        }

        if ($timezone === null) {
            $date = new static($datetime->format('Y-m-d H:i:s'));
        } else {
            $date = new static($datetime->format('Y-m-d H:i:s'), $timezone);
        }

        return $date;
    }

    public function fulltext($tipo)
    {
        if ($tipo === 'd') {
            return self::$mappaGiorni[$this->format('w')];
        }

        return self::$mappaMesi[$this->format('n')];
    }
}
