<template>
    <a-modal
        :open="open"
        width="1300px"
        centered
        @cancel="handleClose"
        title="Releasing Entry"
    >
        <a-row :gutter="[16, 0]" class="mt-8">
            <a-col :span="10">
                <a-card>
                    <a-form
                        :model="formState"
                        layout="horizontal"
                        style="max-width: 600px; padding-top: 10px"
                    >
                        <a-form-item label="GC Releasing No.:">
                            <a-input :value="data.rel_num" readonly />
                        </a-form-item>
                        <a-form-item label="Date Released:">
                            <a-input :value="today" readonly />
                        </a-form-item>
                        <a-form-item label="Upload Document:">
                            <ant-upload-image
                                @handle-change="handleDocumentChange"
                            />
                        </a-form-item>
                        <a-form-item label="Remarks:">
                            <a-textarea :value="formState.remarks" />
                        </a-form-item>
                        <a-form-item label="Checked By:">
                            <ant-select
                                :options="data.checkBy"
                                @handle-change="handleCheckedBy"
                            />
                        </a-form-item>
                        <a-form-item label="Released By:">
                            <a-input
                                :value="$page.props.auth.user.full_name"
                                readonly
                            />
                        </a-form-item>

                        <a-form-item label="Received By:">
                            <a-input :value="formState.receivedBy" />
                        </a-form-item>
                        <a-form-item label="Payment Type:">
                            <ant-select
                                :options="paymentType"
                                @handle-change="handPaymentType"
                            />
                        </a-form-item>
                    </a-form>
                </a-card>
            </a-col>
            <a-col :span="14">
                <a-card>
                    <a-descriptions title="More Details">
                        <a-descriptions-item label="Store">{{
                            data.details.store.store_name
                        }}</a-descriptions-item>
                        <a-descriptions-item label="Date Requested">{{
                            dayjs(data.details.sgc_date_request).format(
                                "MMM DD YYYY"
                            )
                        }}</a-descriptions-item>
                        <a-descriptions-item label="Date Needed">{{
                            dayjs(data.details.sgc_date_needed).format(
                                "MMM DD YYYY"
                            )
                        }}</a-descriptions-item>
                        <a-descriptions-item label="GC Request No">
                            {{ data.details.sgc_num }}
                        </a-descriptions-item>
                        <a-descriptions-item
                            label="Document"
                            v-if="data.details.sgc_file_docno"
                        >
                            ...On Development
                        </a-descriptions-item>
                        <a-descriptions-item label="Remarks">{{
                            data.details.sgc_remarks
                        }}</a-descriptions-item>
                        <a-descriptions-item label="Requested By">
                            {{ data.details.user.full_name }}
                        </a-descriptions-item>
                        <a-descriptions-item label="Time Requested">
                            {{
                                dayjs(data.details.sgc_date_request).format(
                                    "HH:mm:ss a"
                                )
                            }}
                        </a-descriptions-item>
                        <a-descriptions-item
                            label="Company Req.:"
                            v-if="data.details.sgc_type === 'special internal'"
                        >
                            ...On Development
                        </a-descriptions-item>
                    </a-descriptions>
                    <a-button type="primary" @click="viewAllocatedGc"
                        >View Allocated GC</a-button
                    >
                    <a-table
                        bordered
                        class="mt-8"
                        size="small"
                        :data-source="data.rgc.data"
                        :columns="[
                            {
                                title: 'Denomination',
                                dataIndex: 'denomination',
                            },
                            {
                                title: 'Requested Gc',
                                dataIndex: 'sri_items_remain',
                            },
                            {
                                title: 'Subtotal',
                                dataIndex: 'subtotal',
                            },
                            {
                                title: 'Allocated Gc',
                                dataIndex: 'count',
                            },
                            {
                                title: 'Action',
                                key: 'action',
                            },
                        ]"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.key == 'action'">
                                <a-button
                                    type="primary"
                                    size="small"
                                    @click="handleScanModal(record)"
                                    >Scan</a-button
                                >
                            </template>
                        </template>
                        <template #summary>
                            <a-table-summary-row>
                                <a-table-summary-cell
                                    >Total</a-table-summary-cell
                                >
                                <a-table-summary-cell>
                                    <a-typography-text type="danger">{{
                                        totals
                                    }}</a-typography-text>
                                </a-table-summary-cell>
                            </a-table-summary-row>
                        </template>
                    </a-table>
                </a-card>
            </a-col>
        </a-row>
    </a-modal>

    <!-- View Allocated Gc Modal -->
    <a-modal
        v-model:open="allocatedModal"
        title="Allocated Gc"
        style="width: 1000px"
        centered
        :footer="null"
    >
        <div>
            <a-input-search
                class="mr-1"
                v-model:value="searchValue"
                @change="filterSearch"
                placeholder="Search here..."
                style="width: 300px"
            />
        </div>
        <a-table
            bordered
            :pagination="false"
            :columns="allocatedGcColumn"
            :data-source="allocatedGcData.data"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.key == 'pro'">
                    {{ record.gc.pe_entry_gc }}</template
                >
                <template v-if="column.key == 'type'">
                    {{
                        record.loc_gc_type == 1 ? "Regular" : "Special"
                    }}</template
                >
                <template v-if="column.key == 'denom'">
                    {{ record.gc.denomination.denomination }}
                </template>
            </template>
        </a-table>
        <pagination-axios
            :datarecords="allocatedGcData"
            @on-pagination="onChangePagination"
        />
    </a-modal>

    <!-- Scan Modal -->
    <a-modal
        v-model:open="scanModal"
        title="Scan Gc"
        style="width: 600px"
        centered
        @ok="onSubmitBarcode"
    >
        <a-descriptions class="mt-5">
            <a-descriptions-item label="Release No" :span="2">{{
                data.rel_num
            }}</a-descriptions-item>
            <a-descriptions-item label="Date">{{
                dayjs().format("MMM DD, YYYY")
            }}</a-descriptions-item>
            <a-descriptions-item label="Store" :span="2">{{
                data.details.store.store_name
            }}</a-descriptions-item>
            <a-descriptions-item label="Denomination">
                {{ scanSingleData.denomination }}
            </a-descriptions-item>
        </a-descriptions>
        <a-form :model="formBc">
            <a-form-item
                label="Barcode"
                :validate-status="errorBarcode ? 'error' : ''"
                :help="errorBarcode"
            >
                <a-input-number
                    v-model:value="formBc.barcode"
                    style="width: 100%"
                    @input="()=> errorBarcode = null"
                />
            </a-form-item>
            <a-form-item label="Validated By:">
                <a-input
                    :value="$page.props.auth.user.full_name"
                    style="width: 100%"
                    readonly
                />
            </a-form-item>
        </a-form>
    </a-modal>
</template>

<script lang="ts" setup>
import dayjs from "dayjs";
import { useForm } from "@inertiajs/vue3";
import { ref, computed,reactive  } from "vue";
import axios from "axios";
import type { UploadChangeParam } from "ant-design-vue";

const props = defineProps<{
    open: boolean;
    data: { rel_num: number; details: any; checkBy: any; rgc: any };
}>();
const emit = defineEmits<{
    (e: "update:open", value: boolean): void;
}>();

const formState = useForm({
    file: null,
    remarks: "",
    receivedBy: "",
    paymentType: "",
    checkedBy: "",
});

const formBc = reactive({
    barcode: 0,
});
const searchValue = ref<string>("");

const paymentType = [
    {
        value: "cash",
        label: "Cash",
    },
    {
        value: "check",
        label: "Check",
    },
    {
        value: "jv",
        label: "JV",
    },
];

const allocatedGcColumn = [
    {
        title: "Barcode #.",
        dataIndex: "loc_barcode_no",
    },
    {
        title: "Pro #.",
        key: "pro",
    },
    {
        title: "Type",
        key: "type",
    },
    {
        title: "Denomination",
        key: "denom",
    },
];
const allocatedGcData = ref(null);
const scanSingleData = ref(null);
const scanModal = ref(false);
const allocatedModal = ref(false);
const errorBarcode = ref(null);

const filterSearch = async () => {
    const { data } = await axios.get(
        route(
            "treasury.store.gc.viewAllocatedList",
            props.data.details.sgc_store
        ),
        {
            params: {
                search: searchValue.value,
            },
        }
    );
    allocatedGcData.value = data;
};

const onSubmitBarcode = async () => {
    axios
        .post(route("treasury.store.gc.scanSingleBarcode"), {
            relno: props.data.rel_num,
            barcode: formBc.barcode,
            denid: scanSingleData.value.sri_items_denomination,
            store_id: props.data.details.store.store_id,
            reqid: props.data.details.sgc_id,
        })
        .then((res) => {
            console.log(res);
        })
        .catch((err) => {
            if (err.response) {
                errorBarcode.value = err.response.data.message;
            }
        });

    // console.log(data);
};
const onChangePagination = async (link) => {
    if (link.url) {
        const { data } = await axios.get(link.url);
        allocatedGcData.value = data;
    }
};
const handleClose = () => {
    emit("update:open", false);
};
const handleScanModal = (record) => {
    scanSingleData.value = record;
    scanModal.value = true;
};
const totals = computed(() => {
    let totalBorrow = 0;

    props.data.rgc.data.forEach(({ subtotal }) => {
        totalBorrow += subtotal;
    });
    return totalBorrow;
});

const viewAllocatedGc = async () => {
    const { data } = await axios.get(
        route(
            "treasury.store.gc.viewAllocatedList",
            props.data.details.sgc_store
        )
    );
    allocatedGcData.value = data;
    allocatedModal.value = true;
};
const handPaymentType = (value: string) => {
    formState.paymentType = value;
};

const handleCheckedBy = (value) => {
    formState.checkedBy = value;
};
const handleDocumentChange = (file: UploadChangeParam) => {
    formState.file = file.file;
};
const today = dayjs().format("YYYY-MMM-DD HH:mm:ss a");
</script>
