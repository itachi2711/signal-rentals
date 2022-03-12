import { BaseModel } from '../../../../shared/models/base-model';

export class SmsTemplateModel extends BaseModel {
    name: string;
    display_name: string;
    body: string;
    tags: string;
}
