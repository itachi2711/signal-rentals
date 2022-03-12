<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 8/7/2021
 * Time: 3:07 PM
 */

namespace App\Http\Controllers\Api\Aggregate;

use App\Http\Controllers\Api\ApiController;
use App\Rental\Repositories\Contracts\ExtraChargeInterface;
use App\Rental\Repositories\Contracts\LateFeeInterface;
use App\Rental\Repositories\Contracts\LeaseSettingInterface;
use App\Rental\Repositories\Contracts\LeaseTypeInterface;
use App\Rental\Repositories\Contracts\PaymentMethodInterface;
use App\Rental\Repositories\Contracts\UtilityInterface;

class LeaseAggregateController extends ApiController
{
    protected $leaseSettingRepository, $leaseTypeRepository,
        $lateFeeRepository, $paymentMethodRepository,
        $utilityRepository, $extraChargeRepository;

    /**
     * LeaseAggregateController constructor.
     * @param LeaseSettingInterface $leaseSettingRepository
     * @param LeaseTypeInterface $leaseTypeRepository
     * @param LateFeeInterface $lateFeeRepository
     * @param PaymentMethodInterface $paymentMethodRepository
     * @param UtilityInterface $utilityRepository
     * @param ExtraChargeInterface $extraChargeRepository
     */
    public function __construct(LeaseSettingInterface $leaseSettingRepository,
                                LeaseTypeInterface $leaseTypeRepository,
                                LateFeeInterface $lateFeeRepository,
                                PaymentMethodInterface $paymentMethodRepository,
                                UtilityInterface $utilityRepository,
                                ExtraChargeInterface $extraChargeRepository)
    {
        $this->leaseSettingRepository = $leaseSettingRepository;
        $this->leaseTypeRepository = $leaseTypeRepository;
        $this->lateFeeRepository = $lateFeeRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->utilityRepository = $utilityRepository;
        $this->extraChargeRepository = $extraChargeRepository;
    }

    /**
     * @return array
     */
    public function leaseData()
    {
        return [
            'lease_settings'    => $this->leaseSettingRepository->getFirst(),
            'lease_types'       => $this->leaseTypeRepository->listAll(['lease_type_name', 'lease_type_display_name']),
            'late_fees'         => $this->lateFeeRepository->listAll(['late_fee_name', 'late_fee_display_name']),
            'payment_methods'   => $this->paymentMethodRepository->listAll(['payment_method_name', 'payment_method_display_name']),
            'utilities'         => $this->utilityRepository->listAll(['utility_name', 'utility_display_name']),
            'extra_charges'     => $this->extraChargeRepository->listAll(['extra_charge_name', 'extra_charge_display_name']),
        ];
    }
}
