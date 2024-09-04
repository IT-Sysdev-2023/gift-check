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
                                    <a-input :value="currentDate" readonly />
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
                                :options="stores"
                                @handle-change="handleCheckedBy"
                            />
                        </a-form-item>
                        <a-form-item label="Remarks:" name="re">
                            <a-textarea v-model:value="formState.remarks" />
                        </a-form-item>

                        <a-form-item label="Upload Document:" name="up">
                            <ant-upload-image />
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
                                        :options="stores"
                                        @handle-change="handleCheckedBy"
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
                                        :options="stores"
                                        @handle-change="handleCheckedBy"
                                    />
                                </a-form-item>
                                <a-form-item
                                    label="Total Denomination:"
                                    name="den"
                                >
                                    <ant-input-number />
                                </a-form-item>
                                <institution-select :formState="formState" />
                            </a-col>
                            <a-col :span="12">
                                <a-button>Scan Gc By Range</a-button>
                                <a-button>Scan Gc By Barcode</a-button>
                                <a-table
                                    class="mt-5"
                                    bordered
                                    size="small"
                                    :columns="[
                                        {
                                            title: 'Denomination',
                                            dataIndex: 'denom',
                                        },
                                        {
                                            title: 'Barcode',
                                            dataIndex: 'barcode',
                                        },
                                        {
                                            title: 'Remove',
                                            dataIndex: 'remove',
                                        },
                                    ]"
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
        <ant-modal-table
            v-model:open="openModal"
            title="Allocated Gc"
            :columns="columns"
            :data="allocatedData"
            @handle-pagination="onChangePagination"
        />

        <a-modal
            v-model:open="gcAllocationModal"
            title="Scanned Gc"
            style="width: 1000px"
            centered
            :footer="null"
        >
            <a-table
                bordered
                size="small"
                :pagination="false"
                :columns="[
                    {
                        title: 'GC Barcode #',
                        dataIndex: 'barcode_no',
                    },
                    {
                        title: 'Denomination',
                        key: 'denom',
                    },
                    {
                        title: 'Date Validated',
                        key: 'date',
                    },
                    {
                        title: 'Validate By',
                        key: 'validate',
                    },
                ]"
                :data-source="forAllocationData.data"
            >
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key == 'denom'">
                        {{ record.denomination.denomination_format }}
                    </template>
                    <template v-if="column.key == 'date'">
                        {{ record.custodianSrrItems.custodiaSsr?.date_rec }}
                    </template>
                    <template v-if="column.key == 'validate'">
                        {{
                            record.custodianSrrItems.custodiaSsr?.user
                                ?.full_name
                        }}
                    </template>
                </template>
            </a-table>
            <pagination-axios
                :datarecords="forAllocationData"
                @on-pagination="forAllocationPagination"
            />
        </a-modal>
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
    stores: { value: number; label: string }[];
    gcTypes: { value: number; label: string }[];
    denoms: any[];
}>();

const allocatedData = ref([]);
const currentDate = ref(dayjs());
const openModal = ref(false);

const forAllocationData = ref<any>([]);
const gcAllocationModal = ref<boolean>(false);

const columns = [
    {
        title: "GC Barcode No",
        dataIndex: "loc_barcode_no",
    },
    {
        title: "Date Allocated",
        dataIndex: "loc_date",
    },
    {
        title: "Allocated By",
        key: "fullname",
    },
    {
        title: "GC Type",
        key: "gctype",
    },
    {
        title: "Production #",
        key: "productionrequest",
    },
    {
        title: "Denom",
        key: "denom",
    },
];
const formState = useForm({
    receivedBy: "",
    checkedBy: "",
    remarks: "",
    paymentType: {
        type: "",
        customer: "",
        bankName: "",
        accountNumber: "",
        checkNumber: "",
        checkAmount: "",
        amount: "",
    },
});

const forAllocationPagination = async (link) => {
    if (link.url) {
        const { data } = await axios.get(link.url);
        forAllocationData.value = data;
    }
};

const handleCheckedBy = (value) => {
    formState.checkedBy = value;
};
const onChangePagination = async (link) => {
    if (link.url) {
        const { data } = await axios.get(link.url);
        allocatedData.value = data;
    }
};
const { getErrorMessage, getErrorStatus, clearError } = getError();
</script>
