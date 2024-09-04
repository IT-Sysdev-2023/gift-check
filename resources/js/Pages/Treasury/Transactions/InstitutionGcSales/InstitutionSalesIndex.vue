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
            >
                <!-- @finish="onSubmit" -->
                <a-row>
                    <a-col :span="10">
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

                        <a-form-item label="Received By:" name="rec">
                            <a-input v-model:value="formState.receivedBy" />
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
                        <a-form-item label="Remarks:" name="re">
                            <a-textarea v-model:value="formState.remarks" />
                        </a-form-item>

                        <a-form-item label="Upload Document:" name="up">
                            <ant-upload-image
                                @handle-change="handleDocumentChange"
                            />
                        </a-form-item>
                    </a-col>
                    <a-col :span="14">
                        <a-row :gutter="16">
                            <a-col :span="12">
                                <a-form-item
                                    label="Customer:"
                                    name="cus"
                                    :validate-status="
                                        getErrorStatus(formState, 'checkedBy')
                                    "
                                    :help="
                                        getErrorMessage(formState, 'checkedBy')
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
                                        getErrorStatus(formState, 'checkedBy')
                                    "
                                    :help="
                                        getErrorMessage(formState, 'checkedBy')
                                    "
                                >
                                    <ant-select
                                        :options="paymentFund"
                                        @handle-change="handlePaymentFund"
                                    />
                                </a-form-item>
                                <a-form-item
                                    label="Total Denomination:"
                                    name="den"
                                >
                                    <ant-input-number :amount="0" disabled />
                                </a-form-item>
                                <institution-select
                                    :formState="formState"
                                    :errorForm="formState.errors"
                                    @handPaymentType="handlePaymentType"
                                />
                            </a-col>
                            <a-col :span="12">
                                <a-button @click="scanBarcode">Scan Barcode</a-button>
                                <a-table
                                    class="mt-5"
                                    bordered
                                    size="small"
                                    :columns="tableColumns"
                                ></a-table>
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

        <scan-modal-institution v-model:open="openScanModal"/>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from "@/../../resources/js/Layouts/AuthenticatedLayout.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import dayjs from "dayjs";
import axios from "axios";
import { getError, onProgress } from "@/../../resources/js/Mixin/UiUtilities";

const props = defineProps<{
    title: string;
    customer: { label: string; value: number; date: string }[];
    paymentFund: { label: string; value: number; date: string }[];
    checkBy: { label: string; value: number; date: string }[];
    releasingNo: number;
}>();

const allocatedData = ref([]);
const currentDate = ref(dayjs());

const forAllocationData = ref<any>([]);
const openScanModal = ref<boolean>(false);

const tableColumns = [
    {
        title: "Denomination",
        dataIndex: "denom",
    },
    {
        title: "Barcode",
        dataIndex: "barcode",
    },
    {
        title: "Remove",
        dataIndex: "remove",
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
        change: "",

        totalAmountReceived: "",
        cash: "",

        supDocu: "",
    },
});
const scanBarcode = () => {
    openScanModal.value = true;
}

const handlePaymentType = (value) => {
    formState.paymentType.type = value;
};

const forAllocationPagination = async (link) => {
    if (link.url) {
        const { data } = await axios.get(link.url);
        forAllocationData.value = data;
    }
};
const handleDocumentChange = (file) => {
    formState.file = file.file;
};

const handleCustomer = (value) => {
    formState.customer = value;
};
const handleCheckedBy = (value) => {
    formState.checkedBy = value;
};
const handlePaymentFund = (value) => {
    formState.paymentFund = value;
}
const onChangePagination = async (link) => {
    if (link.url) {
        const { data } = await axios.get(link.url);
        allocatedData.value = data;
    }
};
const { getErrorMessage, getErrorStatus, clearError } = getError();
</script>
