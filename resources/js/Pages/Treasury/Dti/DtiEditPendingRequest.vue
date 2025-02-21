<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="13">
                <a-card title="Update Dti Request Form">
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Dti Gc Request #">{{ props.record.dti_num
                        }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Date Requested">{{
                            dayjs(props.record.dti_datereq).format('MMM, DD, YYYY') }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Date Validity">
                            <a-date-picker v-model:value="formState.validity" @handle-change="handleChangeDate"
                                class="w-full">
                            </a-date-picker>
                        </a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Uploaded Document">
                            <a-button block type="dashed">
                                <template #icon>
                                    <FolderOpenOutlined />
                                </template>
                                Open Uploaded Document
                            </a-button>
                        </a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Upload Scanned Copy">
                            <div class="flex justify-center">
                                <div class="max-h-52 overflow-y-auto"> <!-- Adjust height as needed -->
                                    <ant-upload-multi-image @handle-change="handleUploadChange" />
                                </div>
                            </div>
                        </a-descriptions-item>
                    </a-descriptions>
                    <a-card class="mt-1 text-center" title="Denomination">
                        <ant-form-nest-denom :form="formState" />
                    </a-card>
                </a-card>
            </a-col>
            <a-col :span="11">
                <a-card>
                    <a-row>
                        <a-col :span="12">
                            <a-statistic class="text-center" title="Total Denomination" :value="total"
                                style="margin-right: 50px" />
                        </a-col>
                        <a-col :span="12">
                            <a-statistic class="text-center" title="Updated By"
                                :value="$page.props.auth.user.full_name" />
                        </a-col>
                    </a-row>
                </a-card>
                <a-descriptions size="small" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%;" label="Company Name">{{
                        props.dti.label }}</a-descriptions-item>
                </a-descriptions>
                <a-descriptions size="small" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%;" label="Account Name">{{
                        props.dti.account_name }}</a-descriptions-item>
                </a-descriptions>
                <a-descriptions size="small" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%;" label="Payment Type">AR</a-descriptions-item>
                </a-descriptions>
                <a-descriptions size="small" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%;" label="AR No">
                        <a-input class="text-center" v-model:value="formState.arNo" />
                    </a-descriptions-item>
                </a-descriptions>
                <a-descriptions size="small" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%;" label="Amount">
                        <a-input class="text-center" v-model:value="formState.payment" />
                    </a-descriptions-item>
                </a-descriptions>
                <a-divider>Remarks</a-divider>
                <a-textarea v-model:value="formState.remarks" :rows="4" placeholder="Enter Remarks" />
                <a-button class="mt-2" block type="primary" @click="submit">
                    Submit
                </a-button>
            </a-col>
        </a-row>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { SelectProps, UploadProps } from "ant-design-vue";
import dayjs from 'dayjs';

interface Record {
    dti_num: number,
    dti_datereq: string,
    dti_dateneed: string,
    dti_remarks: string,
    dti_payment: number,
    dti_payment_arno: number,
}

interface UseFormType {
    denomination: any,
    validity: any,
    payment: any,
    remarks: string,
    arNo: any,
    file: UploadProps["fileList"];
}

interface Dti {
    label: string,
    account_name: string,
}
const props = defineProps<{
    record: Record,
    dti: Dti,
    total: number,
    denom: any
}>();

const formState = useForm<UseFormType>({
    denomination: props.denom,
    validity: dayjs(props.record.dti_dateneed),
    arNo: props.record.dti_payment_arno,
    payment: props.record.dti_payment,
    remarks: props.record.dti_remarks,
    file: [],
});

const handleUploadChange = (file: any) => {
    formState.file = file.fileList;
}
const handleChangeDate = (obj: any, str: string) => {
    formState.validity = str;
}

const submit = () => {
    formState.transform((data: any) => ({
        ...data,
        file: data.file.map((item) => item.originFileObj),
        validity: dayjs(data.validity).format("YYYY-MM-DD"),
    })).post(route('treasury.transactions.dti.update-gc-request'));
}

</script>
