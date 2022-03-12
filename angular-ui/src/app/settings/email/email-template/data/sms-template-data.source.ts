import { SmsTemplateService } from './sms-template.service';
import { BaseDataSource } from '../../../../shared/base-data-source';

export class SmsTemplateDataSource extends BaseDataSource {
    constructor(service: SmsTemplateService) {
        super(service);
    }
}
