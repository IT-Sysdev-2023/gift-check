import type { InertiaPage } from "@inertiajs/core";
export interface User {
    user_id: number;
    name: string;
    email: string;
    full_name: string;
    email_verified_at: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    barcodeReviewScan: {
        allocation: any[];
        scannedRelease: {
            data: {
                success?: string;
                error?: string;
                status: number;
            }[];
        };
        scannedBarcodeResponse: any[];
    };
    flash?: {
        success: string;
        error: string;
        stream: string;
        countSession: number;
        denominationSession: number;
        scanGc: any[];
    };
};

interface SharedProps extends PageProps {
    auth: {
        user: {
            id: number;
            name: string;
            email: string;
            full_name: string;
        };
    };
}

export interface FormState {
    brno: string;
    dateRequested: Dayjs;
    createdBy: string;
    updatedBy: string;
    remarks: string;
    budget: string;
    group: number;
    dateNeeded: Dayjs;
    file: UploadFile | null;
}

export interface FormStateGc {
    file: UploadFile | null;
    prNo: string;
    denom: any[];
    remarks: string;
    dateNeeded: null;
}

export interface PaginationMeta {
    current_page: number;
    from: number;
    last_page: number;
    path: string;
    per_page: number;
    to: number;
    total: number;
}
export interface PaginationTypes<T = unknown> {
    data: T[];
    links: {
        first: string;
        last: string;
        prev: null;
        next: null;
    };
    meta: PaginationMeta;
}

export interface ForAllocationTypes {
    data: PaginationMeta;
}
export interface AxiosOnPaginationTypes {
    active: boolean;
    label: string;
    url: string;
}
export interface AxiosPagination<T = unknown> {
    links: AxiosOnPaginationTypes[];
    data: T[];
    from: number;
    to: number;
    total: number;
}

type DashboardStatus = Record<"pending" | "approved" | "cancelled", number>;

export interface TreasuryDashboardTypes {
    data?: {
        budgetRequest: DashboardStatus;
        storeGcRequest: {
            pending: number;
            released: number;
            cancelled: number;
        };
        gcProductionRequest: DashboardStatus;
        specialGcRequest: DashboardStatus & {
            released: number;
            internalReviewed: number;
        };
        adjustment: {
            budget: number;
            allocation: number;
        };
        promoGcReleased: number;
        eod: number;
        budget: {
            totalBudget: number;
            regularBudget: number;
            specialBudget: number;
        };
        institutionGcSales: number;
    };
}

export interface FlashProps {
    flash?: { success: string; error: string; stream?: string };
    [key: string]: unknown;
}

export type PageWithSharedProps = InertiaPage<SharedProps>;
