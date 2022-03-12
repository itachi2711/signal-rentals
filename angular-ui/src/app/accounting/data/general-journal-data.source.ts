import { BaseDataSource } from '../../shared/base-data-source';
import { GeneralJournalService } from './general-journal.service';

export class GeneralJournalDataSource extends BaseDataSource {
    constructor(service: GeneralJournalService) {
        super(service);
    }
}
