import { BaseModel } from '../../../../shared/models/base-model';

export class TenantTypeModel extends BaseModel {
    tenant_type_name: string;
    tenant_type_display_name: string;
    tenant_type_description: string;

    created_by: string;
    updated_by: string;
}
