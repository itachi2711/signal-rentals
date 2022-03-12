import { LeaseService } from './lease.service';
import { BaseDataSource } from '../../shared/base-data-source';

export class LeaseDataSource extends BaseDataSource {
    constructor(service: LeaseService) {
        super(service);
    }
}
