import type { UploadFile } from "ant-design-vue";
import { AxiosPagination } from ".";

export interface ColumnTypes {
    title: string;
    dataIndex?: string;
    key?: string;
}

export interface FilterTypes {
    search?: string;
    date?: string[];
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

export interface CancelledSpecialMoreDetails{
    info: StoreGcRequest,
    total: string,
    denomination: AxiosPagination<{
        denomination: number,
        sri_items_quantity: number,
        total: number
    }>
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

export interface PaymentFundTypes {
    pay_id: number;
    pay_desc: string;
    pay_status: string;
    pay_dateadded: string;
    user?: User;
}

export interface SpecialExternalCustomer {
    spcus_id: number;
    spcus_companyname: string;
    spcus_acctname: string;
    spcus_address: string;
    spcus_cperson: string;
    spcus_cnumber: string | number;
    spcus_at: string;
    user?: User;
}

export interface ApprovedBudgetRequest {
    id: number;
    approved_by: string;
    approved_at: string;
    file_doc_no: string;
    checked_by: string;
    budget_remark?: string;
    user_prepared_by?: User;
}

export interface BudgetRequest {
    br_id: number;
    br_request: string;
    br_requested_at: string;
    br_requested_at_time: string;
    br_no: string;
    br_file_docno: string;
    br_remarks?: string;
    br_requested_needed?: string;
    prepared_by?: User;
    abr: ApprovedBudgetRequest;
    cancelled_request?: any;
    cancelled_by: User;
}

export interface Store {
    store_id: number;
    store_name: string;
}

export interface CancelledStoreGcRequest {
    csgr_id: number;
    csgr_gc_id: string | null;
    csgr_by: number;
    csgr_at: string;
    user?: User;
}
export interface StoreGcRequest {
    sgc_id: number;
    sgc_num: string | number;
    sgc_date_needed: string;
    sgc_remarks: string;
    sgc_date_request: string;
    sgc_status: string;
    store?: Store;
    user?: User;
    cancelledStoreGcRequest: CancelledStoreGcRequest;
}
