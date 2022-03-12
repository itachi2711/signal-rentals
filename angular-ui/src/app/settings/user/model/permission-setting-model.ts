import { BaseModel } from '../../../shared/models/base-model';

export class PermissionSettingModel extends BaseModel {
    name: string;
    display_name: string;
    description: string;
}
