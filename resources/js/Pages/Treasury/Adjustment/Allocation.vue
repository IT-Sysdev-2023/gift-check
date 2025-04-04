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
                ref="formRef"
                :model="formState"
                :label-col="{ span: 8 }"
                :wrapper-col="{ span: 12 }"
                @finish="onSubmit"
            >
                <a-row>
                    <a-col :span="10">
                        <a-form-item label="Date Allocated:" name="name">
                            <a-input :value="currentDate" readonly />
                        </a-form-item>
                        <a-form-item
                            label="Store:"
                            name="store"
                            :validate-status="getErrorStatus('store')"
                            :help="getErrorMessage('store')"
                        >
                            <ant-select
                                :options="stores"
                                @handle-change="handleStoreChange"
                            />
                        </a-form-item>
                        <a-form-item
                            label="GC Type:"
                            name="gctype"
                            :validate-status="getErrorStatus('gcType')"
                            :help="getErrorMessage('gcType')"
                        >
                            <ant-select
                                :value="1"
                                :options="gcTypes"
                                @handle-change="handleGcTypeChange"
                            />
                        </a-form-item>
                        <a-form-item
                            label="Adj Type:"
                            name="gctype"
                            :validate-status="getErrorStatus('adjType')"
                            :help="getErrorMessage('adjType')"
                        >
                            <ant-select
                                :options="[
                                    { label: 'Negative', value: 'n' },
                                    { label: 'Positive', value: 'p' },
                                ]"
                                @handle-change="handleAdjTypeChange"
                            />
                        </a-form-item>
                        <a-form-item
                            label="Remarks"
                            name="name"
                            has-feedback
                            :validate-status="getErrorStatus('remarks')"
                            :help="getErrorMessage('remarks')"
                        >
                            <a-textarea
                                v-model:value="formState.remarks"
                                @input="clearError('remarks')"
                            />
                        </a-form-item>
                        <a-card>
                            <a-row :gutter="16" class="text-center">
                                <a-col :span="12">
                                    <span>Denomination</span>
                                </a-col>
                                <a-col :span="12">
                                    <span>Quantity</span>
                                </a-col>
                            </a-row>
                            <a-row
                                :gutter="16"
                                class="mt-5"
                                v-for="(item, index) of formState.denomination"
                                :key="index"
                            >
                                <a-col :span="12">
                                    <a-input
                                        :value="item.denominationFormat"
                                        readonly
                                        class="text-end"
                                    />
                                </a-col>
                                <a-col :span="12" style="text-align: center">
                                    <a-input-number
                                        id="inputNumber"
                                        v-model:value="item.qty"
                                        placeholder="0"
                                        :min="0"
                                    >
                                        <template #upIcon>
                                            <ArrowUpOutlined />
                                        </template>
                                        <template #downIcon>
                                            <ArrowDownOutlined />
                                        </template>
                                    </a-input-number>
                                </a-col>
                            </a-row>
                            <span
                                v-if="formState.errors.denomination"
                                class="text-red-500"
                                >{{ formState.errors.denomination }}</span
                            >
                        </a-card>
                        <a-form-item
                            label="Allocated By:"
                            name="name"
                            class="mt-5"
                        >
                            <a-input
                                :value="page.auth.user.full_name"
                                readonly
                            />
                        </a-form-item>

                        <div>
                            <div
                                class="flex justify-end"
                                style="margin-right: 80px"
                            >
                                <a-form-item class="text-end">
                                    <a-button type="primary" html-type="submit"
                                        >Submit</a-button
                                    >
                                </a-form-item>
                            </div>
                        </div>
                    </a-col>
                    <a-col :span="14">
                        <a-row :gutter="16">
                            <a-col :span="12">
                                <a-card title="Validated GC for Allocation">
                                    <a-alert
                                        v-for="(item, index) of denoms"
                                        :key="index"
                                        class="mt-5"
                                        :message="item.denomination_format"
                                        type="success"
                                    >
                                        <template #action>
                                            <a-space>
                                                <span>
                                                    {{ item.cnt }}
                                                </span>
                                            </a-space>
                                        </template>
                                    </a-alert>
                                    <a-button
                                        class="mt-5 float-right"
                                        @click="viewGcAllocation"
                                    >
                                        View GC For Allocation
                                    </a-button>
                                </a-card>
                            </a-col>

                            <a-col :span="12">
                                <a-card
                                    :title="allocatedGc + ' (Allocated GC)'"
                                    v-if="allDenoms"
                                >
                                    <a-alert
                                        v-for="(item, index) of allDenoms"
                                        :key="index"
                                        class="mt-5"
                                        :message="item.denomination_format"
                                        type="warning"
                                    >
                                        <template #action>
                                            <a-space>
                                                <span>
                                                    {{ item.count }}
                                                </span>
                                            </a-space>
                                        </template>
                                    </a-alert>
                                    <a-button
                                        class="float-right mt-5"
                                        @click="viewAllocatedGc"
                                    >
                                        View Allocated GC
                                    </a-button>
                                </a-card>
                            </a-col>
                        </a-row>
                    </a-col>
                </a-row>
            </a-form>
        </a-card>
        <ant-modal-table
            v-model:open="openModal"
            title="Allocated GC"
            :columns="columns"
            :data="allocatedData"
            :denoms="allDenoms"
            @handle-tab-change="handleTabChange"
            @handle-pagination="onChangePagination"
        />

        <a-modal
            v-model:open="gcAllocationModal"
            title="Scanned GC"
            style="width: 1000px"
            centered
            :footer="null"
        >
            <a-tabs
                v-model:activeKey="activeScannedKey"
                @change="viewGcAllocationTab"
            >
                <a-tab-pane key="all" tab="All" force-render></a-tab-pane>
                <a-tab-pane
                    v-for="denom of denoms"
                    :key="denom.denomination"
                    :tab="denom.denomination_format"
                ></a-tab-pane>
            </a-tabs>
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
                        title: 'Validated By',
                        key: 'validate',
                    },
                ]"
                :data-source="forAllocationData?.data"
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
import { router, useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import dayjs from "dayjs";
import axios from "axios";
import { getError, onProgress } from "@/Mixin/UiUtilities";
import { PageWithSharedProps } from "@/types";
import { route } from 'ziggy-js';
import {
    Denomination,
    AdjustmentAllocation,
    StoreDenomination,
    HandleSelectTypes,
} from "@/types/treasury";
import { AxiosOnPaginationTypes, AxiosPagination } from "@/types";

const props = defineProps<{
    title: string;
    stores: HandleSelectTypes[];
    gcTypes: HandleSelectTypes[];
    denoms: Denomination[];
}>();

const activeScannedKey = ref("all");
const allocatedData = ref([]);
const allocatedGc = ref("");
const allDenoms = ref<StoreDenomination[]>();
const currentDate = dayjs().format("MMM DD, YYYY");
const openModal = ref(false);
const forAllocationData = ref<AxiosPagination<AdjustmentAllocation>>();
const gcAllocationModal = ref<boolean>(false);
    const page = usePage<PageWithSharedProps>().props;
const formState = useForm({
    store: 0,
    gcType: 1,
    adjType: "",
    remarks: "",
    denomination: props.denoms.map((item) => ({
        denominationFormat: item.denomination_format,
        denomination: item.denomination,
        qty: null,
        denom_id: item.denom_id,
    })),
});

const handleAdjTypeChange = (val: string) => {
    formState.adjType = val;
};

const viewGcAllocation = async () => {
    const { data } = await axios.get(
        route("treasury.transactions.gcallocation.forallocation"),
    );
    forAllocationData.value = data;
    gcAllocationModal.value = true;
};

const handleStoreChange = async (_: number, obj: HandleSelectTypes) => {
    clearError("store");
    allocatedGc.value = obj.label;
    formState.store = obj.value;

    const { data } = await axios.get(
        route("treasury.transactions.gcallocation.storeAllocation"),
        { params: { store: formState.store, type: formState.gcType } },
    );
    allDenoms.value = data;
};

const forAllocationPagination = async (link: AxiosOnPaginationTypes) => {
    if (link.url) {
        const { data } = await axios.get(link.url);
        forAllocationData.value = data;
    }
};

const handleGcTypeChange = async (value: number) => {
    clearError("gcType");
    formState.gcType = value;

    const { data } = await axios.get(
        route("treasury.transactions.gcallocation.storeAllocation"),
        { params: { store: formState.store, type: formState.gcType } },
    );
    allDenoms.value = data;
};

const { openLeftNotification } = onProgress();
const onSubmit = () => {
    formState
        .transform((data) => ({
            ...data,
            denomination: data.denomination.filter(
                (item) =>
                    item.denomination !== 0 &&
                    item.qty !== 0 &&
                    item.qty !== null,
            ),
        }))
        .post(route("treasury.adjustment.allocation.setupSubmission"), {
            onSuccess: ({ props }) => {
                openLeftNotification(props.flash);
                if (props.flash?.success) {
                    router.get(route("treasury.dashboard"));
                }
            },
        });
};

const viewAllocatedGc = async () => {
    const { data } = await axios.get(
        route("treasury.transactions.gcallocation.viewAllocatedGc"),
        {
            params: {
                store: formState.store,
                type: formState.gcType,
            },
        },
    );
    allocatedData.value = data;
    openModal.value = true;
};
const handleTabChange = async (value: string) => {
    const text = value == "all" ? "" : value;
    const { data } = await axios.get(
        route("treasury.transactions.gcallocation.viewAllocatedGc"),
        {
            params: {
                store: formState.store,
                type: formState.gcType,
                search: text,
            },
        },
    );
    allocatedData.value = data;
};
const viewGcAllocationTab = async (value: string) => {
    const text = value == "all" ? "" : value;
    const { data } = await axios.get(
        route("treasury.transactions.gcallocation.forallocation"),
        {
            params: {
                search: text,
            },
        },
    );
    forAllocationData.value = data;
};
const onChangePagination = async (link: AxiosOnPaginationTypes) => {
    if (link.url) {
        const { data } = await axios.get(link.url);
        allocatedData.value = data;
    }
};
const { getErrorMessage, getErrorStatus, clearError } = getError(formState);

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
</script>
