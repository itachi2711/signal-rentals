<?php
/**
 * Created by PhpStorm.
 * User: Kevin G. Mungai
 * WhatsApp: +254724475357
 * Date: 8/7/2021
 * Time: 3:28 PM
 */

namespace App\Http\Controllers\Api\Aggregate;

use App\Http\Controllers\Api\ApiController;
use App\Rental\Repositories\Contracts\ExtraChargeInterface;
use App\Rental\Repositories\Contracts\LateFeeInterface;
use App\Rental\Repositories\Contracts\PaymentMethodInterface;
use App\Rental\Repositories\Contracts\PropertySettingInterface;
use App\Rental\Repositories\Contracts\PropertyTypeInterface;
use App\Rental\Repositories\Contracts\UnitTypeInterface;
use App\Rental\Repositories\Contracts\UtilityInterface;

class PropertyAggregateController extends ApiController
{
    protected $propertySettingRepository, $propertyTypeRepository,
        $lateFeeRepository, $paymentMethodRepository,
        $utilityRepository, $extraChargeRepository, $unitTypeRepository;

    /**
     * PropertyAggregateController constructor.
     * @param PropertySettingInterface $propertySettingRepository
     * @param PropertyTypeInterface $propertyTypeRepository
     * @param LateFeeInterface $lateFeeRepository
     * @param PaymentMethodInterface $paymentMethodRepository
     * @param UtilityInterface $utilityRepository
     * @param ExtraChargeInterface $extraChargeRepository
     * @param UnitTypeInterface $unitTypeRepository
     */
    public function __construct(PropertySettingInterface $propertySettingRepository,
                                PropertyTypeInterface $propertyTypeRepository,
                                LateFeeInterface $lateFeeRepository,
                                PaymentMethodInterface $paymentMethodRepository,
                                UtilityInterface $utilityRepository,
                                ExtraChargeInterface $extraChargeRepository,
                                UnitTypeInterface $unitTypeRepository)
    {
        $this->propertySettingRepository = $propertySettingRepository;
        $this->propertyTypeRepository = $propertyTypeRepository;
        $this->lateFeeRepository = $lateFeeRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->utilityRepository = $utilityRepository;
        $this->extraChargeRepository = $extraChargeRepository;
        $this->unitTypeRepository = $unitTypeRepository;
    }

    /**
     * @return array
     */
    public function propertyData()
    {
        return [
            'property_settings' => $this->propertySettingRepository->getFirst(),
            'property_types'    => $this->propertyTypeRepository->listAll(['name', 'display_name']),
            'late_fees'         => $this->lateFeeRepository->listAll(['late_fee_name', 'late_fee_display_name']),
            'payment_methods'   => $this->paymentMethodRepository->listAll(['payment_method_name', 'payment_method_display_name']),
            'utilities'         => $this->utilityRepository->listAll(['utility_name', 'utility_display_name']),
            'extra_charges'     => $this->extraChargeRepository->listAll(['extra_charge_name', 'extra_charge_display_name']),
            'unit_types'        => $this->unitTypeRepository->listAll(['unit_type_name', 'unit_type_display_name']),
        ];
    }
}
