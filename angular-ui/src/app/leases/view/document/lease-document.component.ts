import { AfterViewInit, Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { MatPaginator } from '@angular/material/paginator';
import { MatSort } from '@angular/material/sort';
import { merge } from 'rxjs';
import { tap } from 'rxjs/operators';
import { NotificationService } from '../../../shared/notification.service';
import { DocumentDataSource } from '../../../documents/data/document-data.source';
import { DocumentService } from '../../../documents/data/document.service';
import { LeaseService } from '../../data/lease.service';

@Component({
    selector: 'robi-lease-document',
    templateUrl: './lease-document.component.html',
    styleUrls: ['./lease-document.component.css']
})
export class LeaseDocumentComponent implements OnInit, AfterViewInit {
    documentColumns = [
        'name',
        'title'
    ];

    // Data for the list table display
    documentDataSource: DocumentDataSource;

    // pagination
    @ViewChild(MatPaginator, {static: true }) paginator: MatPaginator;

    // Pagination
    length: number;
    pageIndex = 0;
    pageSizeOptions: number[] = [5, 10, 25, 50, 100];
    meta: any;
    @ViewChild(MatSort, {static: true}) sort: MatSort;
    // Search field
    @ViewChild('search', {static: true}) search: ElementRef;

    loader = false;

    leaseData: any;
    leaseID: string;

    constructor(private notification: NotificationService,
                private documentService: DocumentService,
                private leaseService: LeaseService) {}

    ngOnInit() {
        this.leaseService.selectedLeaseChanges$.subscribe(data => {
            if (data) {
                this.leaseData = data;
                this.leaseID = data.id;
            }
        });

        this.documentDataSource = new DocumentDataSource(this.documentService);
        this.documentDataSource.meta$.subscribe((res) => this.meta = res);
        this.documentDataSource.loadNested(
            this.leaseService.nestedDocumentsUrl(this.leaseID),
            '', 0, 0);
    }

    /**
     * Fetch data from data lead
     */
    loadData() {
        this.documentDataSource.loadNested(
            this.leaseService.nestedDocumentsUrl(this.leaseID),
            this.search.nativeElement.value,
            (this.paginator.pageIndex + 1),
            (this.paginator.pageSize),
            this.sort.active,
            this.sort.direction
        );
    }

    /**
     * Handle search and pagination
     */
    ngAfterViewInit() {
        this.paginator.page.pipe(
            tap(() => this.loadData() )
        ).subscribe();

        // reset the paginator after sorting
        this.sort.sortChange.subscribe(() => this.paginator.pageIndex = 0);

        merge(this.sort.sortChange, this.paginator.page)
            .pipe(
                tap(() => this.loadData())
            )
            .subscribe();
    }

    /**
     * Empty search box
     */
    clearSearch() {
        this.search.nativeElement.value = '';
        this.loadData()
    }
}
