import { EmailTemplateService } from './email-template.service';
import { BaseDataSource } from '../../../../shared/base-data-source';

export class EmailTemplateDataSource extends BaseDataSource {
    constructor(service: EmailTemplateService) {
        super(service);
    }
}
