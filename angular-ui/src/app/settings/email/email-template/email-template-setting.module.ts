import { NgModule } from '@angular/core';

import { SharedModule } from '../../../shared/shared.module';
import { EmailTemplateSettingRoutingModule } from './email-template-setting-routing.module';
import { EmailTemplateSettingComponent } from './email-template-setting.component';
import { AngularEditorModule } from '@kolkov/angular-editor';


@NgModule({
    imports: [
        SharedModule,
        EmailTemplateSettingRoutingModule,
        AngularEditorModule
    ],
    declarations: [
        EmailTemplateSettingComponent
    ],
    entryComponents: [
    ]
})

export class EmailTemplateSettingModule {

}
