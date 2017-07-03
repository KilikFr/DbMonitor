<?php

namespace Kilik\DbMonitorBundle\Twig;

class SignedExtenstion extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('signed', [$this, 'signedFilter']),
            new \Twig_SimpleFilter('sign_color', [$this, 'signColorFilter']),
            new \Twig_SimpleFilter('signed_color', [$this, 'signedColorFilter']),
        );
    }

    /**
     * Return signed number.
     *
     * @param        $number
     * @param int    $decimals
     * @param string $thousandsSep
     * @param string $decPoint
     *
     * @return string
     */
    public function signedFilter($number, $decimals = 0, $thousandsSep = ' ', $decPoint = '.')
    {
        $fNumber=number_format($number, $decimals, $decPoint, $thousandsSep);
        if((string)$fNumber == '0' || (string)$fNumber=='-0') {
            return '&plusmn;0';
        }
        if ($number > 0) {
            return '+'.$fNumber;
        } else {
            return $fNumber;
        }

        return $result;
    }

    /**
     * Return html to color text with (optional) number argument.
     *
     * @param      $text
     * @param null $number
     *
     * @return string
     */
    public function signColorFilter($text, $number = null)
    {
        if (is_null($number)) {
            $number = $text;
        }

        if ($number > 0) {
            $color = 'green';
        } elseif ($number < 0) {
            $color = 'red';
        } else {
            $color = 'lightgray';
        }

        return '<span style=\'color: '.$color.'\'>'.$text.'</span>';
    }

    /**
     * Return html signed color value.
     *
     * @param        $number
     * @param int    $decimals
     * @param string $thousandsSep
     * @param string $decPoint
     *
     * @return string
     */
    public function signedColorFilter($number, $decimals = 0, $thousandsSep = ' ', $decPoint = '.')
    {
        return $this->signColorFilter($this->signedFilter($number, $decimals, $thousandsSep, $decPoint), $number);
    }
}
