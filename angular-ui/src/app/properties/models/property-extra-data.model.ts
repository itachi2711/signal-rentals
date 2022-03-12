import { BaseModel } from '../../shared/models/base-model';

export class PropertyExtraDataModel extends BaseModel {
    property_settings: {};
    unit_types: [{}];
    amenities: [{}];
    utilities: [{}];
    property_types: [{}];
    extra_charges: [{}];
    late_fees: [{}];
    payment_methods: [{}];
}
