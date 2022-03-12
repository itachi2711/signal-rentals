<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 30/03/2020
 * Time: 22:49
 */

use App\Models\AccountClass;
use App\Models\AccountType;
use App\Models\GeneralSetting;

if (!function_exists('format_money')) {

    /**
     * @param $amount
     * @return string
     */
    function format_money($amount)
    {
        return isset($amount) ? number_format($amount, amountDecimal(), amountDecimalSeparator(), amountThousandSeparator()) : null;
       // return number_format($amount, amountDecimal(), amountDecimalSeparator(), amountThousandSeparator());
    }
}

if (!function_exists('format_date')) {

    /**
     * @param $date
     * @return false|string
     */
    function format_date($date)
    {
        return isset($date) ? date(dateFormat(), strtotime($date)) : null;
       // return $new_date_format = date(dateFormat(), strtotime($date));
    }
}


if (!function_exists('transaction_amount')) {

    /**
     * @param $wallet
     * @param $balanceDue
     * @return false|string
     */
    function transaction_amount($wallet, $balanceDue)
    {
        switch ($wallet) {
            case  $wallet >= $balanceDue:
            {
                $transactionAmount = $balanceDue;
                break;
            }
            case  $wallet < $balanceDue:
            {
                $transactionAmount = $wallet;
                break;
            }
            default: {
                $transactionAmount = 0;
            }
        }
        return $transactionAmount;
    }
}


/**
 * @return string
 */
function amountThousandSeparator() {
    $separator = GeneralSetting::select('amount_thousand_separator')->first()->amount_thousand_separator;
    if(isset($separator))
        return $separator;
    return ',';
}

/**
 * @return string
 */
function amountDecimalSeparator() {
    $separator = GeneralSetting::select('amount_decimal_separator')->first()->amount_decimal_separator;
    if(isset($separator))
        return $separator;
    return '.';
}

/**
 * @return int
 */
function amountDecimal() {
    $separator = GeneralSetting::select('amount_decimal')->first()->amount_decimal;
    if(isset($separator))
        return (int)$separator;
    return 2;
}

/**
 * @return string
 */
function dateFormat(){
   $format = GeneralSetting::select('date_format')->first()->date_format;
    if(isset($format))
        return $format;
    return 'd-m-Y';
}

function getAccountTypeID($accountName) {
    return (isset($accountName)) ?  AccountType::where('name', $accountName)->select('id')->first()['id'] : null;
}

function getAccountClassID($accountName) {
    return (isset($accountName)) ?  AccountClass::where('name', $accountName)->select('id')->first()['id'] : null;
}
