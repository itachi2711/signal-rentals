import { BaseModel } from '../../shared/models/base-model';

export class VacateModel extends BaseModel {
    agent_id: string;
    tenant_id: string;
    lease_id: string;
    property_id: string;
    unit: string;
    date_received: string;
    vacating_date: string;
    vacating_date_display: string;
    vacating_reason: string;

    tenant: any;
    lease: {lease_number, unit_names};
    property: {property_name, property_code, location};
}
