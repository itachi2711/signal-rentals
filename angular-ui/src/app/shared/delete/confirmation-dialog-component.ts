import { Component, Inject } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';

@Component({
    selector: 'robi-confirm-dialog',
    templateUrl: './confirmation-dialog-component.html',
})
export class ConfirmationDialogComponent {
    public confirmMessage: string;

    title: string;

    constructor(@Inject(MAT_DIALOG_DATA) public data: any, public dialogRef: MatDialogRef<ConfirmationDialogComponent>) {
        if (data?.title) {
            this.title = data?.title;
        } else {
            this.title = 'Confirm Permanent Action. This cannot be undone.';
        }
    }

}
