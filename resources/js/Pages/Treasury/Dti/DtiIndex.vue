<template>
    <AuthenticatedLayout>
        <a-card>
            <a-descriptions title="Department of Trade and Industry Special GC" size="small" bordered
                :column="{ xs: 1, sm: 1,  }" :labelStyle="{ fontWeight: 'bold', width: '150px' }">

                <a-descriptions-item label="Transaction No:">
                    <span>{{ transNo }}</span>
                </a-descriptions-item>

                <a-descriptions-item label="Payment Date:">
                    <span>{{ dayjs().format('MMMM D, YYYY') }}</span>
                </a-descriptions-item>

                <a-descriptions-item label="Customer:">
                    <span>{{ dti.label }}</span>
                </a-descriptions-item>
            </a-descriptions>

            <a-form ref="formRef" :model="formState" @finish="submitForm">
                <div class="mt-2">
                    <a-col>
                        <a-card class="w-1/2">
                            <p>Upload Image:</p>
                            <div class="flex w-full mt-1">
                                <ant-upload-multi-image @handle-change="handleImageChange" />
                            </div>
                            <p class="text-red-500 text-center w-full" v-if="formState.errors.file">
                                {{ formState.errors.file }}
                            </p>
                            <div class="mt-2">
                                <a-typography-text class="mt-5" style="color: black;">Select Date: </a-typography-text>
                                  <a-date-picker size="medium" @change="handleChangeDatePicker"
                                    :disabled-date="disabledDate" style="width: 85%;" />
                                <p class="text-red-500" v-if="formState.errors.date">
                                    {{ formState.errors.date }}
                                </p>
                            </div>
                            <div class="mt-2">
                                <a-typography-text class="mt-5" style="color:black" >Remarks:  </a-typography-text>
                                <a-textarea :rows="2" placeholder="Remarks" v-model:value="formState.remarks"
                                    class="mb-1" style="width: 89%;" />
                                <p class="text-red-500" v-if="formState.errors.remarks">
                                    {{ formState.errors.remarks }}
                                </p>
                            </div>

                            <ant-form-nest-item :form="formState" />
                            <div class="flex justify-end">
                                <a-button size="medium" class="mt-2" type="primary" block html-type="submit">
                                    SUBMIT REQUEST
                                </a-button>
                            </div>
                        </a-card>
                    </a-col>
                </div>
            </a-form>
            <a-modal v-model:open="openIframe" style="width: 70%; top: 50px" :footer="null" :afterClose="routeToHome">
                <iframe class="mt-7" :src="stream" width="100%" height="600px"></iframe>
            </a-modal>
        </a-card>
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

                notification[props.flash.success]({
                    message: 'Success',
                    description:
                        'Created Successfully',
                });
            }
            if (props.flash.error) {
                notification['error']({
                    message: 'Request Error',
                    description: props.flash.error,
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

//disabled the past date, past date can not be use


const disabledDate = (current) => {
    return current && current.isBefore(dayjs(), "day");
};
</script>
