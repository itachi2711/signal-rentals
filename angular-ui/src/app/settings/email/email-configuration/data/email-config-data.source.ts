import { EmailConfigService } from './email-config.service';
import { BaseDataSource } from '../../../../shared/base-data-source';

export class EmailConfigDataSource extends BaseDataSource {
    constructor(service: EmailConfigService) {
        super(service);
    }
}
