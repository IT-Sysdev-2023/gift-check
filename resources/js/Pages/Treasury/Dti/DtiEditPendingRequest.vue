<template>
    <AuthenticatedLayout>
        <Head :title="title"/>
        <a-breadcrumb>
            <a-breadcrumb-item>
                <a :href="route('treasury.dashboard')">Home</a>
            </a-breadcrumb-item>
            <a-breadcrumb-item>
                <a :href="route('treasury.transactions.dti.dtiPendingRequest')">Dti Pending Request</a>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-row :gutter="[16, 16]" class="mt-5">
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
                            <a-button block type="dashed" @click="openDocumentButton">
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

                            <div class="text-center text-red-500">
                                {{ formState.errors.file }}
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
                                :value="page.auth.user.full_name" />
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
        <a-modal v-model:open="openDocument" style="width: 45%;" title="Preview Image">
            <a-row :gutter="[16, 16]">
                <a-col :span="12" v-for="image in props.docs" :key="image.id">
                    <div
                        style="box-shadow: rgba(0, 0, 0, 0.4) 0px 2px 4px, rgba(0, 0, 0, 0.3) 0px 7px 13px -3px, rgba(0, 0, 0, 0.2) 0px -3px 0px inset; padding: 10px; border-radius: 1rem;">
                        <a-image style="height: 300px; width: 100%;  border-radius: 1rem;"
                            :src="'/storage/' + image.dti_fullpath" />
                    </div>
                </a-col>
            </a-row>
        </a-modal>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { notification, SelectProps, UploadProps } from "ant-design-vue";
import dayjs from 'dayjs';
import { ref } from 'vue';

const page = usePage().props
interface Record {
    id: number,
    dti_num: number,
    dti_datereq: string,
    dti_dateneed: string,
    dti_remarks: string,
    dti_payment: number,
    dti_payment_arno: number,
    dti_company: number,
}

interface UseFormType {
    id: number,
    denomination: any,
    validity: any,
    payment: any,
    remarks: string,
    arNo: any,
    dtiNum: number,
    companyId: number,
    file: UploadProps["fileList"];
}
interface Document {
    id: any,
    dti_fullpath: string
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
    docs: Document,
    title: string
}>();

const formState = useForm<UseFormType>({
    id: props.record.id,
    dtiNum: props.record.dti_num,
    denomination: props.denom,
    validity: dayjs(props.record.dti_dateneed),
    arNo: props.record.dti_payment_arno,
    payment: props.record.dti_payment,
    remarks: props.record.dti_remarks,
    companyId: props.record.dti_company,
    file: [],
});
const openDocument = ref<boolean>(false);

const openDocumentButton = () => {
    openDocument.value = true;
}

const handleUploadChange = (file: any) => {
    formState.errors.file = '';
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
    })).post(route('treasury.transactions.dti.update-gc-request'), {
        onSuccess: (e: any) => {
            console.log(e.props.flash.type);
            if (e.props.flash.type == 'success') {
                notification[e.props.flash.type]({
                    message: e.props.flash.title,
                });
            }else{
                notification[e.props.flash.type]({
                    message: e.props.flash.title,
                });
            }
        }
    });
}

</script>
