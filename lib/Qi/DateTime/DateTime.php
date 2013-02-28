<?php

namespace Qi\DateTime;

class DateTime extends \DateTime
{
    const US = 'Y-m-d H:i:s';
    const BR = 'd/m/Y H:i:s';

    public $format = self::US;

    public function __toString()
    {
        return $this->format($this->format);
    }

    public function getDayOfMonth()
    {
        return $this->format("d");
    }

    public function getMonth()
    {
        return $this->format("m");
    }

    public function getMonthBR()
    {
        switch( $this->getMonth() ) {
            case "01": return "janeiro";
            case "02": return "fevereiro";
            case "03": return "março";
            case "04": return "abril";
            case "05": return "maio";
            case "06": return "junho";
            case "07": return "julho";
            case "08": return "agosto";
            case "09": return "setembro";
            case "10": return "outubro";
            case "11": return "novembro";
            case "12": return "dezembro";
        }
        return null;
    }

    public function getMonthBrShort()
    {
        return substr($this->getMonth(), 3, 0);
    }

    public function getWeek()
    {
        return $this->format("w");
    }

    public function getWeekBR()
    {
        switch( $this->getWeek() ) {
            case "0": return "domingo";
            case "1": return "segunda";
            case "2": return "terça";
            case "3": return "quarta";
            case "4": return "quinta";
            case "5": return "sexta";
            case "6": return "sábado";
        }
        return null;
    }

    public function getWeekBrShort()
    {
        return substr($this->getWeekBR(), 0, 3);
    }

    public function getWeekBrLong()
    {
        $feira = $this->getWeekBR();
        if ($feira == 'domingo' || $feira == 'sábado') return $feira;
        return "$feira-feira";
    }
}
