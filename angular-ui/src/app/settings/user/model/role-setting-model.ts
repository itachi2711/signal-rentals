import { BaseModel } from '../../../shared/models/base-model';

export class RoleSettingModel extends BaseModel {
    name: string;
    display_name: string;
    description: string;
    permissions: [];
}
