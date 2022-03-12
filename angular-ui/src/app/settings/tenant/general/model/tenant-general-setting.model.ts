import { BaseModel } from '../../../../shared/models/base-model';

export class TenantGeneralSettingModel extends BaseModel {
    tenant_number_prefix: string;
    next_tenant_number: string;
}
