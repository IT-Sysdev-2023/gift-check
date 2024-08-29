export interface User {
    user_id: number;
    name: string;
    email: string;
    full_name: string;
    email_verified_at: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>
> = T & {
    auth: {
        user: User;
    };
    barcodeReviewScan: {
        allocation: any[];
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

export interface FlashProps {
    flash?: { success: string; error: string };
    [key: string]: unknown;
}

export type PageWithSharedProps = InertiaPage<SharedProps>;
