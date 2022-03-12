import { BaseModel } from '../../shared/models/base-model';

export class StatementModel extends BaseModel {
    account_id: string;
    journal_id: string;
    created_at: string;
    amount: string;
    display_amount: string;
    is_cr: string;
    is_dr: string;
    narration: string;
    balance: string;
}
