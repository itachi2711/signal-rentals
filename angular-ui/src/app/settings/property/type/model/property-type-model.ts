import { BaseModel } from '../../../../shared/models/base-model';

export class PropertyTypeModel extends BaseModel {
    name: string;
    display_name: string;
    description: string;

    created_by: string;
    updated_by: string;
}
