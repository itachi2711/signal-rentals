import { BaseModel } from '../../../../shared/models/base-model';

export class UnitTypeModel extends BaseModel {
    unit_type_name: string;
    unit_type_display_name: string;
    unit_type_description: string;

    created_by: string;
    updated_by: string;
}
