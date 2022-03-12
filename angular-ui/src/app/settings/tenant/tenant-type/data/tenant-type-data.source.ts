import { TenantTypeService } from './tenant-type.service';
import { BaseDataSource } from '../../../../shared/base-data-source';

export class TenantTypeDataSource extends BaseDataSource {
    constructor(service: TenantTypeService) {
        super(service);
    }
}
