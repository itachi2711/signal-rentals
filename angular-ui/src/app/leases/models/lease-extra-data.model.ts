import { BaseModel } from '../../shared/models/base-model';

export class LeaseExtraDataModel extends BaseModel {
    lease_settings: {};
    lease_types: [{}];
    late_fees: [{}];
    payment_methods: [{}];
    utilities: [{}];
    extra_charges: [{}];
}
