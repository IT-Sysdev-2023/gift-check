<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route('treasury.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>

        <a-card :title="title" class="mt-10">
            <a-form
                layout="vertical"
                ref="formRef"
                :model="formState"
                :wrapper-col="{ span: 20 }"
                @finish="onSubmit"
            >
                <!-- @finish="onSubmit" -->
                <a-row>
                    <a-col :span="8">
                        <a-row>
                            <a-col :span="12">
                                <a-form-item label="Gc Releasing #:">
                                    <a-input :value="releasingNo" readonly />
                                </a-form-item>
                            </a-col>
                            <a-col :span="12">
                                <a-form-item label="Date Allocated:">
                                    <a-date-picker
                                        :value="currentDate"
                                        disabled
                                    />
                                </a-form-item>
                            </a-col>
                        </a-row>

                        <a-form-item
                            label="Received By:"
                            name="rec"
                            :validate-status="
                                getErrorStatus(formState, 'receivedBy')
                            "
                            :help="getErrorMessage(formState, 'receivedBy')"
                        >
                            <a-input
                                v-model:value="formState.receivedBy"
                                @input="
                                    () => formState.clearErrors('receivedBy')
                                "
                            />
                        </a-form-item>
                        <a-form-item
                            label="Check By:"
                            name="check"
                            :validate-status="
                                getErrorStatus(formState, 'checkedBy')
                            "
                            :help="getErrorMessage(formState, 'checkedBy')"
                        >
                            <ant-select
                                :options="checkBy"
                                @handle-change="handleCheckedBy"
                            />
                        </a-form-item>
                        <a-form-item
                            label="Remarks:"
                            name="re"
                            :validate-status="
                                getErrorStatus(formState, 'remarks')
                            "
                            :help="getErrorMessage(formState, 'remarks')"
                        >
                            <a-textarea
                                v-model:value="formState.remarks"
                                @input="() => formState.clearErrors('remarks')"
                            />
                        </a-form-item>
                    </a-col>
                    <a-col :span="16">
                        <a-row :gutter="16">
                            <a-col :span="10">
                                <a-form-item
                                    label="Customer:"
                                    name="cus"
                                    :validate-status="
                                        getErrorStatus(formState, 'customer')
                                    "
                                    :help="
                                        getErrorMessage(formState, 'customer')
                                    "
                                >
                                    <ant-select
                                        :options="customer"
                                        @handle-change="handleCustomer"
                                    />
                                </a-form-item>
                                <a-form-item
                                    label="Payment Fund:"
                                    name="fund"
                                    :validate-status="
                                        getErrorStatus(formState, 'paymentFund')
                                    "
                                    :help="
                                        getErrorMessage(formState, 'paymentFund')
                                    "
                                >
                                    <ant-select
                                        :options="paymentFund"
                                        @handle-change="handlePaymentFund"
                                    />
                                </a-form-item>

                                <institution-select
                                    :formState="formState"
                                    :total="totalScannedDenomination"
                                    @handPaymentType="handlePaymentType"
                                />
                                <a-form-item label="Upload Document:" name="up">
                                    <ant-upload-multi-image @handle-change="handleDocumentChange"/>
                                </a-form-item>
                            </a-col>
                            <a-col :span="14">
                                <a-flex justify="space-between" align="center">
                                    <a-button
                                        @click="scanBarcode"
                                        type="primary"
                                        ghost
                                        >Scan Barcode</a-button
                                    >
                                    <a-form-item
                                        label="Total Denomination:"
                                        name="den"
                                    >
                                        <a-input
                                            :value="
                                                currency(
                                                    totalScannedDenomination
                                                )
                                            "
                                            readonly
                                        />
                                    </a-form-item>
                                </a-flex>

                                <a-table
                                    bordered
                                    :pagination="false"
                                    :data-source="scannedBc.data"
                                    :columns="tableColumns"
                                >
                                    <template #bodyCell="{ record, column }">
                                        <template v-if="column.key == 'remove'">
                                            <a-button
                                                size="small"
                                                danger
                                                type="dashed"
                                                :loading="
                                                    barcodeRemoveLoading[
                                                        record.barcode
                                                    ]
                                                "
                                                @click="
                                                    removeBarcode(
                                                        record.barcode
                                                    )
                                                "
                                            >
                                                <template #icon>
                                                    <CloseOutlined />
                                                </template>
                                                Remove</a-button
                                            >
                                        </template>
                                    </template>
                                </a-table>
                                <pagination-axios
                                    :datarecords="scannedBc"
                                    @on-pagination="onPaginate"
                                />
                                <a-form-item class="mt-5">
                                    <a-button type="primary" html-type="submit"
                                        >Submit</a-button
                                    >
                                </a-form-item>
                            </a-col>
                        </a-row>
                    </a-col>
                </a-row>
            </a-form>
        </a-card>

        <scan-modal-institution v-model:open="openScanModal" />
        <a-modal
        v-model:open="openIframe"
        style="width: 70%; top: 50px"
        :footer="null"
        :afterClose="() => router.get(route('treasury.dashboard'))"
    >
        <iframe class="mt-7" :src="stream" width="100%" height="600px"></iframe>
    </a-modal>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import dayjs from "dayjs";
import { getError, onProgress, currency } from "@/Mixin/UiUtilities";

const props = defineProps<{
    title: string;
    customer: { label: string; value: number; date: string }[];
    paymentFund: { label: string; value: number; date: string }[];
    checkBy: { label: string; value: number; date: string }[];
    releasingNo: number;
    scannedBc: {
        data: any[];
    };
    totalScannedDenomination: number;
}>();

const barcodeRemoveLoading = ref({});
const currentDate = ref(dayjs());
const openIframe = ref(false);
const stream = ref(null);
const openScanModal = ref<boolean>(false);

const tableColumns = [
    {
        title: "Denomination",
        dataIndex: "denomination",
    },
    {
        title: "Barcode",
        dataIndex: "barcode",
    },
    {
        title: "Action",
        key: "remove",
    },
];
const formState = useForm({
    file: null,
    receivedBy: "",
    checkedBy: "",
    remarks: "",
    customer: "",
    paymentFund: "",
    paymentType: {
        type: "",
        customer: "",
        bankName: "",
        accountNumber: "",
        checkNumber: "",
        amount: "",
        cash: "",
        supDocu: "",
    },
});

const { openLeftNotification } = onProgress();

const onSubmit = () => {
    formState
        .transform((data) => ({
            ...data,
            releasingNo: props.releasingNo,
            file: data.file?.map((item) => item.originFileObj),
            totalDenomination: props.totalScannedDenomination,
        }))
        .post(route("treasury.transactions.institution.gc.sales.submission"), {
            onSuccess: ({ props }) => {
                openLeftNotification(props.flash);
                if(props.flash.success){
                    stream.value = `data:application/pdf;base64,${props.flash.stream}`;
                    openIframe.value = true;
                }
            },
        });
};
const removeBarcode = (bc) => {
    barcodeRemoveLoading.value = {
        ...barcodeRemoveLoading.value,
        [bc]: true,
    };
    router.put(
        route("treasury.transactions.institution.gc.sales.removeBarcode", bc),
        {},
        {
            preserveScroll: true,
            onSuccess: ({ props }) => {
                openLeftNotification(props.flash, "Barcode Deleted");
                barcodeRemoveLoading.value = {
                    ...barcodeRemoveLoading.value,
                    [bc]: false,
                };
            },
        }
    );
};
const scanBarcode = () => {
    openScanModal.value = true;
};

const handlePaymentType = (value) => {
    formState.paymentType.type = value;
    formState.errors['paymentType.type'] = null;
};

const onPaginate = async (link) => {
    if (link.url) {
        const baseUrl = window.location.origin + window.location.pathname;
        const fullUrl = baseUrl + link.url;

        router.visit(fullUrl, {
            preserveState: true,
            preserveScroll: true,
            only: ["scannedBc"], // Fetch only the necessary data
        });
    }
};
const handleDocumentChange = (file) => {
    formState.file = file.fileList;
};

const handleCustomer = (value) => {
    formState.customer = value;
    formState.clearErrors('customer');
};
const handleCheckedBy = (value) => {
    formState.checkedBy = value;
    formState.clearErrors('checkedBy');
};
const handlePaymentFund = (value) => {
    formState.paymentFund = value;
    formState.clearErrors('paymentFund');
};
const { getErrorMessage, getErrorStatus, clearError } = getError();
</script>
