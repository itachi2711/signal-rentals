export class BaseModel {
    id: string;
    created_by: string;
    updated_by: string;
    deleted_by: string;
    created_at: string | Date;
    updated_at: string | Date;
}
