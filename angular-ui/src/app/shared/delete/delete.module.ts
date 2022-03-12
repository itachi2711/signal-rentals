import { NgModule } from '@angular/core';

import { ConfirmationDialogComponent } from './confirmation-dialog-component';
import { MatDialogModule } from '@angular/material/dialog';
import { MatIconModule } from '@angular/material/icon';

@NgModule({
    imports: [
        MatDialogModule,
        MatIconModule
    ],
    declarations: [
       // ConfirmationDialogComponent
    ],
    entryComponents: [ConfirmationDialogComponent],
    exports: [
    ]
})

export class DeleteModule {}
