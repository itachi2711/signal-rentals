import { BaseModel } from '../../../../shared/models/base-model';

export class LeaseTypeModel extends BaseModel {
    lease_type_name: string;
    lease_type_display_name: string;
    lease_type_description: string;

    created_by: string;
    updated_by: string;
}
