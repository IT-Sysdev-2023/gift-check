<template>
    <AuthenticatedLayout>

        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route('treasury.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ titleGc }}</a-breadcrumb-item>
        </a-breadcrumb>

        <a-card :title="'Submit ' + titleGc" class="mt-10">
            <template #extra>
                <a-switch v-model:checked="formState.switchGc" checked-children="Special Int. GC"
                    un-checked-children="Special Ext. GC" />
            </template>
            <a-form ref="formRef" :model="formState" :label-col="{ span: 7 }" :wrapper-col="{ span: 15 }"
                @finish="onSubmit">
                <a-row :gutter="[16, 16]">
                    <a-col :span="12">
                        <a-card>
                            <a-form-item label="Transaction No." name="name">
                                <a-input :value="formState.trans" readonly />
                            </a-form-item>
                            <a-form-item label="Payment date:" name="dateRequested">
                                <a-input v-model:value="currentDate" readonly />
                            </a-form-item>
                            <a-form-item label="Date Needed:" name="dateNeeded" has-feedback
                                :validate-status="getErrorStatus('dateNeeded')" :help="getErrorMessage('dateNeeded')">
                                <span class="text-red-500">*Required</span>
                                <a-date-picker style="width: 100%;" :disabled-date="disabledDate"
                                    v-model:value="formState.dateNeeded" @change="clearError('dateNeeded')" />
                            </a-form-item>
                            <a-form-item label="Upload Scan Copy.:" name="upload"
                                :validate-status="getErrorStatus('file')" :help="getErrorMessage('file')">
                                <span class="text-red-500">*Required</span>
                                <ant-upload-multi-image @handle-change="handleChange" />
                            </a-form-item>
                            <a-form-item label="Account Name" name="account" v-if="accountName">
                                <a-input :value="accountName" />
                            </a-form-item>
                            <a-form-item label="Lookup Customer:" name="customer">
                                <span class="text-red-500">*Required</span>
                                <ant-select :options="props.options" @handle-change="handleCustomerChange" />
                                <span v-if="formState.errors.companyId" class="text-red-500">{{
                                    formState.errors.companyId
                                    }}</span>
                            </a-form-item>

                            <a-form-item label="Payment Type:" :validate-status="getErrorStatus('paymentType.type')
                                " :help="getErrorMessage('paymentType.type')">
                                <span class="text-red-500">*Required</span>
                                <ant-select :options="paymentType" @handle-change="handlePaymentChange" />
                            </a-form-item>
                            <a-form-item v-if="formState?.paymentType?.type != '4'" label="AR no." name="ar">
                                <span class="text-red-500">*Required</span>
                                <a-input v-model:value="formState.arNo" />
                            </a-form-item>
                            <PaymentType  :form="formState" v-if="formState.paymentType.type" />
                            <a-form-item label="Remarks:." name="name" has-feedback
                                :validate-status="getErrorStatus('remarks')" :help="getErrorMessage('remarks')">
                                <span class="text-red-500">*Required</span>
                                <a-textarea v-model:value="formState.remarks" @input="clearError('remarks')" />
                            </a-form-item>
                        </a-card>
                    </a-col>
                    <a-col :span="12">

                        <ant-form-nest-item :form="formState" />

                        <a-button class="mt-4" size="large" block type="primary" html-type="submit">
                            <FastForwardOutlined />Submit Special GC
                            Payment
                        </a-button>
                    </a-col>
                </a-row>
            </a-form>
        </a-card>
        <a-modal v-model:open="openIframe" style="width: 70%; top: 50px" :footer="null" :afterClose="routeToHome">
            <iframe class="mt-7" :src="stream" width="100%" height="600px"></iframe>
        </a-modal>
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import { ref, computed } from "vue";
import AuthenticatedLayout from "@/../../resources/js/Layouts/AuthenticatedLayout.vue";
import type { UploadChangeParam } from "ant-design-vue";
import dayjs from "dayjs";
import { router, useForm } from "@inertiajs/vue3";
import type { SelectProps } from "ant-design-vue";
import { onProgress } from "@/../../resources/js/Mixin/UiUtilities";
import type { UploadProps } from "ant-design-vue";
import { createVNode } from "vue";
import { ExclamationCircleOutlined } from "@ant-design/icons-vue";
import { Modal } from "ant-design-vue";

interface FormStateGc {
    trans: string;
    file: UploadProps["fileList"];
    companyId: string;
    arNo: number | string;

    denomination: any[];
    paymentType: any;
    remarks: string;
    dateNeeded: null;
    switchGc: boolean;
}

const props = defineProps<{
    title?: string;
    trans: string;
    options: {
        value: string | number;
        label: string;
        account_name: string;
    }[];
}>();

const titleGc = computed(() => {
    const t = formState.switchGc ? "Special Internal " : "Special External ";
    return t + props.title;
});
// const switchGc = reactive({ state: false });
const currentDate = dayjs().format("MMM DD, YYYY");
const formRef = ref();

const formState = useForm<FormStateGc>({
    trans: props.trans,
    companyId: "",
    arNo: "",
    dateNeeded: null,
    file: [],
    denomination: [],
    paymentType: {
        type: "",
        amount: 0,
    },
    remarks: "",
    switchGc: false,
});
const accountName = ref(null);
const stream = ref(null);
const openIframe = ref(false);
const paymentType = ref<SelectProps["options"]>([
    {
        value: "1",
        label: "Cash",
    },
    {
        value: "2",
        label: "Cheque",
    },
    {
        value: "3",
        label: "JV",
    },
    {
        value: "4",
        label: "AR",
    },
    {
        value: "5",
        label: "On Account",
    },
]);
const { openLeftNotification } = onProgress();
const handleChange = (file: UploadChangeParam) => {
    formState.file = file.fileList;
};

const handlePaymentChange = (value: string) => {
    clearError("paymentType.type");
    formState.paymentType.type = value;
};

const handleCustomerChange = (value: string, acc) => {
    clearError("companyId");
    accountName.value = acc.account_name;
    formState.companyId = value;
};

const onSubmit = () => {
    const t = formState.switchGc ? "INTERNAL " : "EXTERNAL ";
    Modal.confirm({
        title: "Confirm your Action!",
        icon: createVNode(ExclamationCircleOutlined),
        content: "You selected SPECIAL " + t + "GC",
        okText: "Yes",
        okType: "danger",
        cancelText: "No",
        onOk() {
            formState
                .transform((data) => ({
                    ...data,
                    dateNeeded: dayjs(data.dateNeeded).format("YYYY-MM-DD"),
                    denomination: data.denomination.filter(
                        (item) => item.denomination !== 0 && item.qty !== 0,
                    ),
                    file: data.file.map((item) => item.originFileObj),
                    total: data.denomination.reduce((acc, item) => {
                        return acc + item.denomination * item.qty;
                    }, 0),
                }))
                .post(
                    route("treasury.transactions.special.paymentSubmission"),
                    {
                        onSuccess: ({ props }) => {
                            openLeftNotification(props.flash);
                            if (props.flash.success) {
                                stream.value = `data:application/pdf;base64,${props.flash.stream}`;
                                openIframe.value = true;
                            }
                        },
                    },
                );
        },
        onCancel() {
            console.log("Cancel");
        },
    });
};
const routeToHome = () => {
    router.visit(route("treasury.dashboard"));
};
const getErrorStatus = (field: string) => {
    return formState.errors[field] ? "error" : "";
};
const getErrorMessage = (field: string) => {
    return formState.errors[field];
};
const clearError = (field: string) => {
    formState.errors[field] = null;
};

const disabledDate = (current) => {
    return current && current < new Date().setHours(0, 0, 0, 0);
};
</script>
