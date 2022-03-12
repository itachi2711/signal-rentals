import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { BaseService } from '../../shared/base-service';
import { BehaviorSubject } from 'rxjs';
import { TransactionModel } from '../models/transaction-model';

@Injectable({ providedIn: 'root' })
export class TransactionService extends BaseService<TransactionModel> {
    private selectedTransactionSource = new BehaviorSubject<TransactionModel | null>(null);
    selectedTransactionChanges$ = this.selectedTransactionSource.asObservable();

    private  localHttpClient: HttpClient;
    constructor(httpClient: HttpClient) {
        super( httpClient, 'transactions');
        this.localHttpClient = httpClient;
    }

    changeSelectedTransaction(selectedTransaction: TransactionModel | null ): void {
        this.selectedTransactionSource.next(selectedTransaction);
    }
}
