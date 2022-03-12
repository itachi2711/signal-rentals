import { ReadingService } from './reading.service';
import { BaseDataSource } from '../../shared/base-data-source';

export class ReadingDataSource extends BaseDataSource {
    constructor(service: ReadingService) {
        super(service);
    }
}
