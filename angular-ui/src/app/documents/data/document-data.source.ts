import { DocumentService } from './document.service';
import { BaseDataSource } from '../../shared/base-data-source';

export class DocumentDataSource extends BaseDataSource {
    constructor(service: DocumentService) {
        super(service);
    }
}
