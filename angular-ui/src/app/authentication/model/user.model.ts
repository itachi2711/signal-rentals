import { GeneralSettingModel } from '../../settings/general/model/general-setting.model';
import { BaseModel } from '../../shared/models/base-model';

export interface User extends BaseModel {
    first_name: string;
    middle_name: any;
    last_name: string;
    access_token: string;
    expires_in: any;
    scope: [];
    settings: GeneralSettingModel;
    g_settings: GeneralSettingModel;
}
