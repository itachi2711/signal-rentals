import { BaseModel } from '../../../../shared/models/base-model';

export class LateFeeModel extends BaseModel {
    late_fee_name: string;
    late_fee_display_name: string;
    late_fee_description: string;
    grace_period: string;
}
