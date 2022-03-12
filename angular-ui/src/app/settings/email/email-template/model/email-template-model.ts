import { BaseModel } from '../../../../shared/models/base-model';

export class EmailTemplateModel extends BaseModel {
    name: string;
    template: string;
    display_name: string;
    body: string;
    tags: string;

    created_by: string;
    updated_by: string;
}
