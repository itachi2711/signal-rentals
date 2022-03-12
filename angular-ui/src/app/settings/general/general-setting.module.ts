import { NgModule } from '@angular/core';
import { GeneralSettingRoutingModule } from './general-setting-routing.module';
import { GeneralSettingComponent } from './general-setting.component';
import { SharedModule } from '../../shared/shared.module';
import { ImageCropperModule } from 'ngx-image-cropper';

@NgModule({
    imports: [
        SharedModule,
        GeneralSettingRoutingModule,
        ImageCropperModule
    ],
    declarations: [
        GeneralSettingComponent,
    ]
})

export class GeneralSettingModule {}
