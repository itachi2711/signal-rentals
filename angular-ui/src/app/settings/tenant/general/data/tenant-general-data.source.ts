import { TenantGeneralService } from './tenant-general.service';
import { BaseDataSource } from '../../../../shared/base-data-source';

export class TenantGeneralDataSource extends BaseDataSource {
    constructor(service: TenantGeneralService) {
        super(service);
    }
}
