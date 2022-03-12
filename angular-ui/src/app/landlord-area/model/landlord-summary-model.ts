import { BaseModel } from '../../shared/models/base-model';

export class LandlordSummaryModel extends BaseModel {
    pending_amount: string;
    pending_amount_as_currency: string;
    period_data: {period_id, amount_billed, amount_due, amount_paid};
    periodical_billing: any;
    total_leases: string;
    total_properties: string;
    total_units: string;
}
