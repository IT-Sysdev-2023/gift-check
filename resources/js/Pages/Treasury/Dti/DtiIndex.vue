<template>
    <AuthenticatedLayout>
        <a-descriptions title="Debt-to-Income ratio" size="small" layout="vertical" bordered>
            <a-descriptions-item label="Transaction No">{{ transNo }}</a-descriptions-item>
            <a-descriptions-item label="Payment Date">{{ dayjs().format('MMM, DD, YYYY') }}</a-descriptions-item>
            <a-descriptions-item label="Customer">{{ dti.label }}</a-descriptions-item>
        </a-descriptions>
        <a-form ref="formRef" :model="formState" @finish="submitForm">
            <a-row :gutter="[16, 16]" class="mt-5">
                <a-col :span="12">
                    <a-card>
                        <strong class="ml-2">Date Needed:</strong>
                        <a-date-picker @change="handleChangeDatePicker" class="mb-2" style="width: 100%;" />
                        <p class="text-red-500" v-if="formState.errors.date">
                            {{ formState.errors.date }}
                        </p>
                        <p class="text-center w-full mt-10">Upload Image</p>
                        <div class="flex justify-center w-full">
                            <ant-upload-multi-image @handle-change="handleImageChange" />
                        </div>
                        <p class="text-red-500 text-center w-full" v-if="formState.errors.file">
                            {{ formState.errors.file }}
                        </p>

                    </a-card>
                </a-col>
                <a-col :span="12">
                    <strong class="ml-2">Remarks:</strong>
                    <a-textarea :rows="4" placeholder="Remarks" v-model:value="formState.remarks" class="mb-2" />
                    <p class="text-red-500" v-if="formState.errors.remarks">
                        {{ formState.errors.remarks }}
                    </p>

                    <ant-form-nest-item :form="formState" />
                    <div class="flex justify-end">
                        <a-button class="mt-4" type="primary" block html-type="submit">
                            Submit Form
                        </a-button>
                    </div>
                </a-col>
            </a-row>
        </a-form>
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
import { notification } from 'ant-design-vue';


interface UseFormType {
    trans: number,
    denomination: any[],
    date: string,
    remarks: string,
    file: UploadProps["fileList"];
}

const props = defineProps<{
    transNo: number,
    dti: {
        value: string | number;
        label: string;
        account_name: string;
    };
}>()

const formState = useForm<UseFormType>({
    trans: props.transNo,
    denomination: [],
    date: '',
    remarks: '',
    file: [],
});

const stream = ref(null);
const openIframe = ref<boolean>(false);


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
        file: data.file.map((item) => item.originFileObj),
        total: data.denomination.reduce((acc, item) => {
            return acc + item.denomination * item.qty;
        }, 0),
    })).post(route('treasury.transactions.dti.submit'), {
        onSuccess: ({ props }) => {
            if (props.flash.success) {
                stream.value = `data:application/pdf;base64,${props.flash.stream}`;
                openIframe.value = true;

                notification['success']({
                    message: 'Success',
                    description:
                        'Created Successfully',
                });
            }
        },
        onError: () => {
            notification['error']({
                message: 'Error',
                description:
                    'Check if the informations is correct!',
            });
        }
    });
}
</script>
