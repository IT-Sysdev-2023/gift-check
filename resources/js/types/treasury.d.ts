import type { UploadFile } from "ant-design-vue";

export interface ReportsGeneratedTypes {
    files: {
        file: string;
        filename: string;
        extension: string;
        expiration: string;
    }[];
}

export interface BudgetRequestForm {
    file: UploadFile | null;
    br: string;
    budget: number;
    remarks: string;
    dateNeeded: null;
    category: string;
}

export interface BudgetRequestProps {
    title?: string;
    remainingBudget: string;
    regularBudget: string;
    specialBudget: string;
    br: string;
}

export interface GcAllocationForm {
    store: number;
    gcType: number;
    denomination: {
        denominationFormat: string;
        denomination: number;
        qty: number | null;
    }[];
}

export interface HandleSelectTypes {
    value: number;
    label: string;
}

export interface StoreDenomination {
    count: number;
    denom_id: number;
    denomination: number;
    denomination_format: string;
}
//Models

export interface denomination {
    denom_id: number;
    denom_code: number;
    denomination: number;
    denom_fad_item_number: string;
    denom_barcode_start: number;
    denom_type: string;
    denom_status: string;
    denom_createdby: number;
    denom_datecreated: string;
    denom_updatedby: null | string;
    denom_dateupdated: null | string;
    cnt?: number;
    denomination_format: string;
}
