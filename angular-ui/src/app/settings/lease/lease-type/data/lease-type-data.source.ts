import { LeaseTypeService } from './lease-type.service';
import { BaseDataSource } from '../../../../shared/base-data-source';

export class LeaseTypeDataSource extends BaseDataSource {
    constructor(service: LeaseTypeService) {
        super(service);
    }
}
