import { EmailGeneralService } from './email-general.service';
import { BaseDataSource } from '../../../../shared/base-data-source';

export class EmailGeneralDataSource extends BaseDataSource {
    constructor(service: EmailGeneralService) {
        super(service);
    }
}
