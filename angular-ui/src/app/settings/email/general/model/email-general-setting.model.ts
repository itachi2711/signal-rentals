import { BaseModel } from '../../../../shared/models/base-model';

export class EmailGeneralSettingModel extends BaseModel {
    name: string;
    display_name: string;
    send_email: string;
    send_sms: string;
}
