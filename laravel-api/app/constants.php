<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 20/02/2020
 * Time: 23:11
 */

// Account Classes
define('ASSET','ASSET');
define('LIABILITY','LIABILITY');
define('INCOME','INCOME');
define('EXPENDITURE','EXPENDITURE');

// Real Accounts
define('RENT_DEPOSIT_ACCOUNT','RENT_DEPOSIT_ACCOUNT'); // liability
define('LEASE_ACCOUNT','LEASE_ACCOUNT'); // Asset  // create when new lease is created
define('LEASE_ACCOUNT_CODE','111');

define('UTILITY_DEPOSIT_ACCOUNT','UTILITY_DEPOSIT_ACCOUNT'); // liability
define('EXTRA_CHARGE_ACCOUNT','EXTRA_CHARGE_ACCOUNT'); // Asset
define('RENT_INCOME_ACCOUNT','RENT_INCOME_ACCOUNT'); // income
define('UTILITY_INCOME_ACCOUNT','UTILITY_INCOME_ACCOUNT'); // income
define('PENALTY_INCOME_ACCOUNT','PENALTY_INCOME_ACCOUNT'); // income
define('SERVICE_FEE_INCOME_ACCOUNT','SERVICE_FEE_INCOME_ACCOUNT'); // Asset

define('LEASE_SUSPENSE', 'LEASE_SUSPENSE'); // Asset
define('LEASE_SUSPENSE_CODE', '999');
define('PREPAYMENT_ACCOUNT', 'PREPAYMENT_ACCOUNT'); // liability
define('PREPAYMENT_CODE', '888');

define('VAT_ACCOUNT','VAT_ACCOUNT'); // liability // create when new tax is added

define('BANK_ACCOUNT','BANK_ACCOUNT'); // Asset
define('MPESA_ACCOUNT','MPESA_ACCOUNT'); // Asset
define('CASH_ACCOUNT','CASH_ACCOUNT'); // Asset

define('WAIVER_ACCOUNT', 'WAIVER_ACCOUNT'); // liability
define('WAIVER_ACCOUNT_CODE', '000');

/// Invoice Item Types
define('ITEM_RENT', 'RENT');
define('ITEM_RENT_DEPOSIT', 'RENT_DEPOSIT');
define('ITEM_UTILITY', 'UTILITY');
define('ITEM_UTILITY_DEPOSIT', 'UTILITY_DEPOSIT');
define('ITEM_EXTRA_CHARGE', 'EXTRA_CHARGE');
define('ITEM_PENALTY', 'PENALTY');

// Communication events
define('NEW_LANDLORD', 'New Landlord');
define('NEW_TENANT', 'New Tenant');
define('NEW_PROPERTY', 'New Property');
define('NEW_LEASE', 'New Lease');

define('NEW_INVOICE', 'New Invoice');
define('DUE_INVOICE', 'Due Invoice');
define('OVER_DUE_INVOICE', 'Over Due Invoice');

define('RESET_PASSWORD', 'Reset Password');

define('TERMINATE_LEASE', 'Terminate Lease');
define('RECEIVE_PAYMENT', 'Receive Payment');
define('NEW_VACATE_NOTICE', 'New Vacate Notice');

// transaction types
define('WAIVER', 'WAIVER');
define('PAYMENT', 'PAYMENT');
