import { TenantService } from './tenant.service';
import { BaseDataSource } from '../../shared/base-data-source';

export class TenantDataSource extends BaseDataSource {
    constructor(service: TenantService) {
        super(service);
    }
}
