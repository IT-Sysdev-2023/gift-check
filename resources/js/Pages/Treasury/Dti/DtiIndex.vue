<template>
    <AuthenticatedLayout>
        <a-descriptions title="Debt-to-Income ratio" size="small" bordered>
            <a-descriptions-item label="Transaction No">{{ transNo }}</a-descriptions-item>
            <a-descriptions-item label="Payment Date">{{ dayjs().format('MMM, DD, YYYY') }}</a-descriptions-item>
        </a-descriptions>
        <a-row :gutter="[16, 16]" class="mt-5">
            <a-col :span="12">
                <a-card>
                    <a-row :gutter="[16, 16]">
                        <a-col :span="12">
                            <strong class="ml-2">Select Date:</strong>
                            <a-date-picker @change="handleChangeDatePicker" class="mb-2" style="width: 100%;" />
                            <strong class="ml-2">Ar No:</strong>
                            <a-input placeholder="Enter Ar Number" class="mb-2" />
                        </a-col>
                        <a-col :span="12">
                            <strong class="ml-2">Lookup Customer:</strong>
                            <ant-select class="mb-2" placeholder="Select Customer" style="width: 100%;"
                                :options="props.options" @handle-change="handleCustomerChange" />
                            <strong class="ml-2 mt-2">Amount:</strong>
                            <a-input v-model:value="formState.amount"/>

                        </a-col>
                        <p class="text-center w-full">Upload Image</p>
                        <div class="flex justify-center w-full">
                            <ant-upload-multi-image @handle-change="handleImageChange" />
                        </div>

                    </a-row>
                </a-card>
            </a-col>
            <a-col :span="12">
                <strong class="ml-2">Remarks:</strong>
                <a-textarea :rows="4" placeholder="Remarks" v-model:value="formState.remarks" class="mb-2" />
                <ant-form-nest-item :form="formState" />
                <div class="flex justify-end">
                    <a-button class="mt-4" type="primary" block @click="submitForm">
                        Submit Form
                    </a-button>
                </div>
            </a-col>
        </a-row>
        <a-modal v-model:open="openIframe" style="width: 70%; top: 50px" :footer="null" :afterClose="routeToHome">
            <iframe class="mt-7" :src="stream" width="100%" height="600px"></iframe>
        </a-modal>
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import { router, useForm } from "@inertiajs/vue3";
import { SelectProps, UploadProps } from "ant-design-vue";
import dayjs from "dayjs";
import { ref } from "vue";



interface UseFormType {
    trans: number,
    denomination: any,
    date: string,
    customer: string,
    amount: number,
    remarks: string,
    file: UploadProps["fileList"];
}

const props = defineProps<{
    transNo: number,
    options: {
        value: string | number;
        label: string;
        account_name: string;
    }[];
}>()

const formState = useForm<UseFormType>({
    trans: props.transNo,
    denomination: [],
    date: '',
    customer: '',
    amount: 0,
    remarks: '',
    file: [],
});

const stream = ref(null);
const openIframe = ref<boolean>(false);

const handleCustomerChange = (str: string) => {
    formState.customer = str;
}

const handleImageChange = (file: any) => {
    formState.file = file.fileList;
}
const handleChangeDatePicker = (obj: object, str: string) => {
    formState.date = str;
    // alert('ello')
}
const routeToHome = () => {
    router.get(route('treasury.dashboard'));
}


const submitForm = () => {

    formState.transform((data: any) => ({
        ...data,
        denomination: data.denomination.filter(
            (item) => item.denomination !== 0 && item.qty !== 0,
        ),
        file: data.file .map((item) => item.originFileObj),
        total: data.denomination.reduce((acc, item) => {
            return acc + item.denomination * item.qty;
        }, 0),
    })).post(route('treasury.transactions.dti.submit'), {
        onSuccess: ({ props }) => {
            if (props.flash.success) {
                stream.value = `data:application/pdf;base64,${props.flash.stream}`;
                openIframe.value = true;
            }
        },
    });
}
</script>
