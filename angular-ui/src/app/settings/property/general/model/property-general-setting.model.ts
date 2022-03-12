import { BaseModel } from '../../../../shared/models/base-model';

export class PropertyGeneralSettingModel extends BaseModel {
    invoice_day: string;
    lease_number_prefix: string;
    next_lease_number: string;
    tenant_number_prefix: string;
    next_tenant_number: string;
}
