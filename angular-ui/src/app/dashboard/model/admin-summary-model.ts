import { BaseModel } from '../../shared/models/base-model';

export class AdminSummaryModel extends BaseModel {
    pending_amount: string;
    period_data: {period_id, amount_billed, amount_due, amount_paid};
    period_data_as_currency: {period_id, amount_billed, amount_due, amount_paid};
    periodical_billing: any;
    total_leases: string;
    total_properties: string;
    total_tenants: string;
    total_units: string;
}
