import { BaseModel } from '../../shared/models/base-model';

export class GeneralJournalModel extends BaseModel {
    transaction_id: string;
    debit_account_id: string;
    credit_account_id: string;
    amount: string;
    prepared_by: string;
    narration: string;
    created_by: string;

    debitAccount: any;
    creditAccount: any;
    preparedBy: any;

}
