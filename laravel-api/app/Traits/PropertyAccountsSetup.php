<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * Email: robisignals@gmail.com
 * WhatsApp: +254724475357
 * Date: 12/30/2020
 * Time: 8:40 AM
 */

namespace App\Traits;

use App\Models\Account;
use App\Models\AccountType;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

trait PropertyAccountsSetup
{
    static function bootPropertyAccountsSetup()
    {
        static::created(function ($model) {
            $propertyId = $model->id;
            $propertyCode = $model->property_code;

            $data = [
                [
                    'id'                => Uuid::uuid4(),
                    'property_id'       => $propertyId,
                    'account_class_id'  => getAccountClassID(LIABILITY),
                    'account_type'      => RENT_DEPOSIT_ACCOUNT,
                    'account_name'      => RENT_DEPOSIT_ACCOUNT,
                    'account_number'    => $propertyCode.'-1000'
                ],
                [
                    'id'                => Uuid::uuid4(),
                    'property_id'       => $propertyId,
                    'account_class_id'  => getAccountClassID(LIABILITY),
                    'account_type'      => UTILITY_DEPOSIT_ACCOUNT,
                    'account_name'      => UTILITY_DEPOSIT_ACCOUNT,
                    'account_number'    => $propertyCode.'-2000'
                ],
                [
                    'id'                => Uuid::uuid4(),
                    'property_id'       => $propertyId,
                    'account_class_id'  => getAccountClassID(ASSET),
                    'account_type'      => EXTRA_CHARGE_ACCOUNT,
                    'account_name'      => EXTRA_CHARGE_ACCOUNT,
                    'account_number'    => $propertyCode.'-3000'
                ],
                [
                    'id'                => Uuid::uuid4(),
                    'property_id'       => $propertyId,
                    'account_class_id'  => getAccountClassID(INCOME),
                    'account_type'      => RENT_INCOME_ACCOUNT,
                    'account_name'      => RENT_INCOME_ACCOUNT,
                    'account_number'    => $propertyCode.'-4000'
                ],
                [
                    'id'                => Uuid::uuid4(),
                    'property_id'       => $propertyId,
                    'account_class_id'  => getAccountClassID(INCOME),
                    'account_type'      => UTILITY_INCOME_ACCOUNT,
                    'account_name'      => UTILITY_INCOME_ACCOUNT,
                    'account_number'    => $propertyCode.'-4000'
                ],
                [
                    'id'                => Uuid::uuid4(),
                    'property_id'       => $propertyId,
                    'account_class_id'  => getAccountClassID(INCOME),
                    'account_type'      => PENALTY_INCOME_ACCOUNT,
                    'account_name'      => PENALTY_INCOME_ACCOUNT,
                    'account_number'    => $propertyCode.'-5000'
                ],
                [
                    'id'                => Uuid::uuid4(),
                    'property_id'       => $propertyId,
                    'account_class_id'  => getAccountClassID(ASSET),
                    'account_type'      => SERVICE_FEE_INCOME_ACCOUNT,
                    'account_name'      => SERVICE_FEE_INCOME_ACCOUNT,
                    'account_number'    => $propertyCode.'-6000'
                ],
                [
                    'id'                => Uuid::uuid4(),
                    'property_id'       => $propertyId,
                    'account_class_id'  => getAccountClassID(LIABILITY),
                    'account_type'      => VAT_ACCOUNT,
                    'account_name'      => VAT_ACCOUNT,
                    'account_number'    => $propertyCode.'-7000'
                ],
                [
                    'id'                => Uuid::uuid4(),
                    'property_id'       => $propertyId,
                    'account_class_id'  => getAccountClassID(ASSET),
                    'account_type'      => BANK_ACCOUNT,
                    'account_name'      => BANK_ACCOUNT,
                    'account_number'    => $propertyCode.'-8000'
                ],
                [
                    'id'                => Uuid::uuid4(),
                    'property_id'       => $propertyId,
                    'account_class_id'  => getAccountClassID(ASSET),
                    'account_type'      => MPESA_ACCOUNT,
                    'account_name'      => MPESA_ACCOUNT,
                    'account_number'    => $propertyCode.'-9000'
                ],
                [
                    'id'                => Uuid::uuid4(),
                    'property_id'       => $propertyId,
                    'account_class_id'  => getAccountClassID(ASSET),
                    'account_type'      => CASH_ACCOUNT,
                    'account_name'      => CASH_ACCOUNT,
                    'account_number'    => $propertyCode.'-10000'
                ],
                [
                    'id'                => Uuid::uuid4(),
                    'property_id'       => $propertyId,
                    'account_class_id'  => getAccountClassID(ASSET),
                    'account_type'      => WAIVER_ACCOUNT,
                    'account_name'      => WAIVER_ACCOUNT,
                    'account_number'    => $propertyCode.'-'.WAIVER_ACCOUNT_CODE
                ],
            ];
            foreach ($data as $key => $value) {
                Account::create($value);
            }
        });
    }
}
