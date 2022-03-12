import { BaseModel } from '../../../../shared/models/base-model';

export class ExtraChargeModel extends BaseModel {
    name: string;
    display_name: string;
    description: string;
    permissions: [];
}
