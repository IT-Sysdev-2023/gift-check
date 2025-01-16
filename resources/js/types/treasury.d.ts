import type { UploadFile } from "ant-design-vue";

export interface ColumnTypes {
    title: string;
    dataIndex?: string;
    key?: string;
}

export interface FilterTypes{
    search?: string,
    date?: string[]
}
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
export interface AllocatedGcTypes {
    user: User;
    gc_type: GcTypes;
    gc: Gc;
}

export interface AdjustmentAllocation {
    barcode_no: number;
    cutodianSrrItems: CustodianSrrItems;
    denom_id: number;
    denomination: {
        denomination: number;
        denomination_format: string;
        id: number;
    };
}

export interface BudgetAdjustmentForm<T> {
    adjustmentNo: string | null;
    file: T | null;
    budget: number;
    remarks: string;
    adjustmentType: string | null;
}

//Models
export interface CustodianSrrItems {
    cssitem_barcode: number;
    cssitem_recnum: number;
    custodiaSsr?: CustodianSsr;
}

export interface CustodianSsr {
    date_rec: string;
    e_reqno: null | string | number;
    rec_no: string;
    recby: string;
    rectype: null | string | number;
    supname: null | string | number;
    user?: User;
}
export interface User {
    user_id?: number;
    firstname: string;
    lastname: string;
    full_name: string;
    format_firstname?: string;
}
export interface GcTypes {
    gc_type_id: number;
    gctype: string;
}
export interface Gc {
    gc_id: number;
    denom_id: number;
    pe_entry_gc: number;
    barcode_no: number;
    denomination: Denomination;
    production_request: ProductionRequest;
}
export interface ProductionRequest {
    pe_id: number;
    pe_num: string;
}
export interface Denomination {
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

export interface ProductionRequest {
    pe_date_needed: null | string;
    pe_date_request: string;
    pe_date_request_time: string;
    pe_file_docno: string;
    pe_generate_code: null | number | string;
    pe_group: number;
    pe_id: number;
    pe_num: string;
    pe_remarks: string;
    pe_requested_by: number;
    pe_requisition: null | string;
    pe_type: null | string;
    user?: User;
}

export interface InstitutCustomer {
    ins_name: string;
    ins_custype: string | number;
    ins_date_created: string;
    user: User;
    gcType: GcTypes;
}
