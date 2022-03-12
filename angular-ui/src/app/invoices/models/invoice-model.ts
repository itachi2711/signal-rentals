import { BaseModel } from '../../shared/models/base-model';

export class InvoiceModel extends BaseModel {
    agent_id: string;
    property_id: string;
    unit_id: string;

    utility_id: string;
    period_id: string;
    period: any;

    base_charge: string;
    previous_reading: string;
    current_reading: string;
    reading_date: string;
    rate_per_unit: string;
    units: string;
    total: string;

    lease_id: string;
    lease: any;
    payment_summary: any;
    waiver_summary: any;

    summary: {
        amount_due: string;
        amount_paid: string;
        count: number;
        invoice_amount: string;
        status: {
            status_text: string;
            status_icon: string;
            status_color: string;
        }
    };
    invoice_number: string;
    invoice_total: string;
    invoice_discount: string;
    invoice_tax: string;
    invoice_status: string;
    invoice_terms: string;
    invoice_date: string;
    due_date: string;
    note: string;


    created_by: string;
    updated_by: string;
}
