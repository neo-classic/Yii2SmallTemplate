<?php
namespace app\components\helpers;

class TimespanHelper
{
    public static function getRussianMonth($param, $time = 0)
    {
        if (intval($time) == 0) {
            $time = time();
        }
        $MonthNames = array(
            "января",
            "февраля",
            "марта",
            "апреля",
            "мая",
            "июня",
            "июля",
            "августа",
            "сентября",
            "октября",
            "ноября",
            "декабря"
        );
        if (strpos($param, 'M') === false) {
            return date($param, $time);
        } else {
            return date(str_replace('M', $MonthNames[date('n', $time) - 1], $param), $time);
        }
    }

    public static function getTime($seconds, $time = null)
    {
        if (is_null($time)) {
            $time = time();
        }

        $result = self::timespan($seconds, $time);

        $str = '';

        if (count($result) != 0) {
            for ($i = 0; $i < 1; $i++) {
                $str .= $result[$i] . ' ';
            }

            $str .= ' назад';
        } else {
            $str = 'только что';
        }

        return $str;
    }

    public static function timespan($seconds = 1, $time = '')
    {
        if (!is_numeric($seconds)) {
            $seconds = 1;
        }

        if (!is_numeric($time)) {
            $time = time();
        }
        if ($time <= $seconds) {
            $seconds = 1;
        } else {
            $seconds = $time - $seconds;
        }

        $str = array();
        $years = floor($seconds / 31536000);

        if ($years > 0) {
            $str[] = $years . ' ' . self::getDateFormat($years, 'год', 'лет', 'года');
        }

        $seconds -= $years * 31536000;
        $months = floor($seconds / 2628000);

        if ($years > 0 || $months > 0) {
            if ($months > 0) {
                $str[] = $months . ' ' . self::getDateFormat($months, 'месяц', 'месяцев', 'месяца');
            }

            $seconds -= $months * 2628000;
        }

        $weeks = floor($seconds / 604800);

        if ($years > 0 || $months > 0 || $weeks > 0) {
            if ($weeks > 0) {
                $str[] = $weeks . ' ' . self::getDateFormat($weeks, 'неделю', 'недель', 'недели');
            }

            $seconds -= $weeks * 604800;
        }

        $days = floor($seconds / 86400);

        if ($months > 0 || $weeks > 0 || $days > 0) {
            if ($days > 0) {
                $str[] = $days . ' ' . self::getDateFormat($days, 'день', 'дней', 'дня');
            }

            $seconds -= $days * 86400;
        }

        $hours = floor($seconds / 3600);

        if ($days > 0 || $hours > 0) {
            if ($hours > 0) {

                $str[] = $hours . ' ' . self::getDateFormat($hours, 'час', 'часов', 'часа');
            }

            $seconds -= $hours * 3600;
        }

        $minutes = floor($seconds / 60);

        if ($days > 0 || $hours > 0 || $minutes > 0) {
            if ($minutes > 0) {
                $str[] = $minutes . ' ' . self::getDateFormat($minutes, 'минута', 'минут', 'минуты');
            }

            $seconds -= $minutes * 60;
        }

        if ($str == '') {
            $str[] = $seconds . ' ' . self::getDateFormat($seconds, 'секунда', 'секунд', 'секунды');
        }

        return $str;
    }

    public static function getDateFormat($date, $first, $second, $third)
    {
        if ((($date % 10) > 4 && ($date % 10) < 10) || ($date > 10 && $date < 20)) {
            return $second;
        }
        if (($date % 10) > 1 && ($date % 10) < 5) {
            return $third;
        }
        if (($date % 10) == 1) {
            return $first;
        } else {
            return $second;
        }

    }
} 