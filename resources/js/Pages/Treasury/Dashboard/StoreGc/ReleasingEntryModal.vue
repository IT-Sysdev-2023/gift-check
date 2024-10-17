<template>
    <a-modal
        :open="open"
        width="1300px"
        centered
        @cancel="handleClose"
        @ok="submitForm"
        title="Releasing Entry"
    >
        <a-row :gutter="[16, 0]" class="mt-3">
            <a-col :span="10">
                <a-card>
                    <a-form
                        layout="horizontal"
                        style="max-width: 600px; padding-top: 10px"
                    >
                        <a-form-item label="GC Releasing No.:">
                            <a-input :value="data.rel_num" readonly />
                        </a-form-item>
                        <a-form-item label="Date Released:">
                            <a-input :value="today" readonly />
                        </a-form-item>
                        <!-- <a-form-item
                            label="Upload Document:"
                            :validate-status="errorForm?.file ? 'error' : ''"
                            :help="errorForm?.file"
                        >
                            <ant-upload-image
                                @handle-change="handleDocumentChange"
                            />
                        </a-form-item> -->
                        <a-form-item
                            label="Remarks:"
                            :validate-status="
                                formState.errors?.remarks ? 'error' : ''
                            "
                            :help="formState.errors?.remarks"
                        >
                            <a-textarea
                                v-model:value="formState.remarks"
                                @input="() => (formState.errors.remarks = null)"
                            />
                        </a-form-item>
                        <a-form-item
                            label="Checked By:"
                            :validate-status="
                                formState.errors?.checkedBy ? 'error' : ''
                            "
                            :help="formState.errors?.checkedBy"
                        >
                            <ant-select
                                :options="data.checkBy"
                                @handle-change="handleCheckedBy"
                            />
                        </a-form-item>
                        <a-form-item label="Approved By:">
                            <a-input
                                :value="$page.props.auth.user.full_name"
                                readonly
                            />
                        </a-form-item>

                        <a-form-item
                            label="Received By:"
                            :validate-status="
                                formState.errors?.receivedBy ? 'error' : ''
                            "
                            :help="formState.errors?.receivedBy"
                        >
                            <a-input v-model:value="formState.receivedBy"/>
                        </a-form-item>
                        <check-cash-jv-payment
                            :formState="formState"
                            :errorForm="formState.errors"
                            @handleCustomerOption="handleCustomerOption"
                            @handPaymentType="handPaymentType"
                        />
                    </a-form>
                </a-card>
            </a-col>
            <a-col :span="14">
                <a-card>
                    <a-descriptions title="More Details">
                        <a-descriptions-item
                            label="Store"
                            :labelStyle="{ fontWeight: 'bold' }"
                            >{{
                                data.details.store.store_name
                            }}</a-descriptions-item
                        >
                        <a-descriptions-item
                            label="Date Requested"
                            :labelStyle="{ fontWeight: 'bold' }"
                            >{{
                                dayjs(data.details.sgc_date_request).format(
                                    "MMM DD YYYY"
                                )
                            }}</a-descriptions-item
                        >
                        <a-descriptions-item
                            label="Date Needed"
                            :labelStyle="{ fontWeight: 'bold' }"
                            >{{
                                dayjs(data.details.sgc_date_needed).format(
                                    "MMM DD YYYY"
                                )
                            }}</a-descriptions-item
                        >
                        <a-descriptions-item
                            label="GC Request No"
                            :labelStyle="{ fontWeight: 'bold' }"
                        >
                            {{ data.details.sgc_num }}
                        </a-descriptions-item>
                        <a-descriptions-item
                            label="Document"
                            v-if="data.details.sgc_file_docno"
                        >
                            ...On Development
                        </a-descriptions-item>
                        <a-descriptions-item
                            label="Remarks"
                            :labelStyle="{ fontWeight: 'bold' }"
                            >{{ data.details.sgc_remarks }}</a-descriptions-item
                        >
                        <a-descriptions-item
                            label="Requested By"
                            :labelStyle="{ fontWeight: 'bold' }"
                        >
                            {{ data.details.user.full_name }}
                        </a-descriptions-item>
                        <a-descriptions-item
                            label="Time Requested"
                            :labelStyle="{ fontWeight: 'bold' }"
                        >
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
                    <div class="mb-8 pt-5 text-right space-x-5">
                        <a-button @click="viewAllocatedGc" type="primary" ghost
                            >View Allocated GC</a-button
                        >
                        <a-button @click="viewScannedGc" type="dashed"
                            >View Scanned Gc</a-button
                        >
                    </div>
                    <a-table
                        bordered
                        class="mt-8"
                        size="small"
                        :pagination="false"
                        :data-source="denominationTableData.data"
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
                            {
                                title: 'Scanned Gc',
                                key: 'scan',
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
                            <template v-if="column.key == 'scan'">
                                {{ countScannedBc(record) }}
                                <!-- {{ $page.props.barcodeReviewScan?.allocation }} -->
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

                    <pagination-axios
                        :datarecords="denominationTableData"
                        @on-pagination="onChangeDenominationPagination"
                    />
                </a-card>
            </a-col>
        </a-row>
    </a-modal>

    <!-- View Allocated Gc Modal -->
    <view-allocated-gc-modal
        v-model:open="allocatedModal"
        :allocated-gc-data="allocatedGcData"
        :store_id="data?.details?.sgc_store"
    />

    <!-- Scan Modal  sadasd-->
    <ScanModal v-model:open="scanModal" :data="data" :scan-data="scanData" />

    <!-- View Scanned Gc -->
    <a-modal
        v-model:open="viewScannedModal"
        title="Scanned Gc"
        style="width: 800px"
        centered
        :footer="null"
    >
        <a-table
            bordered
            size="small"
            :pagination="false"
            :columns="[
                {
                    title: 'Barcode #',
                    dataIndex: 'barcode',
                },
                {
                    title: 'Pro. No.',
                    dataIndex: 'pro',
                },
                {
                    title: 'Type',
                    dataIndex: 'type',
                },
                {
                    title: 'Denomination',
                    dataIndex: 'denomination',
                },
            ]"
            :data-source="scannedGcData?.data"
        >
        </a-table>
        <pagination-axios
            :datarecords="scannedGcData"
            @on-pagination="onScannedPagination"
        />
    </a-modal>

    <a-modal
        v-model:open="openIframe"
        style="width: 70%; top: 50px"
        :footer="null"
        :afterClose="routeToHome"
    >
        <iframe class="mt-7" :src="stream" width="100%" height="600px"></iframe>
    </a-modal>
</template>

<script lang="ts" setup>
import dayjs from "dayjs";
import { usePage, useForm, router } from "@inertiajs/vue3";
import { ref, computed, reactive, watch } from "vue";
import { PageWithSharedProps } from "@/types";
import { notification } from "ant-design-vue";
import axios from "axios";
import { onProgress } from "@/Mixin/UiUtilities";
import type { UploadChangeParam } from "ant-design-vue";

//Props
const props = defineProps<{
    open: boolean;
    data: { rel_num: number; details: any; checkBy: any; rgc: any };
}>();
const page = usePage<PageWithSharedProps>().props;
const emit = defineEmits<{
    (e: "update:open", value: boolean): void;
}>();

//Data/Variables
const formState = useForm({
    file: null,
    remarks: "",
    receivedBy: "",
    paymentType: {
        type: "",
        amount: 0,
        bankName: "",
        accountNumber: "",
        checkNumber: "",
        checkAmount: "",
        customer: "",
    },
    checkedBy: "",
});
const { openLeftNotification } = onProgress();
const today = dayjs().format("YYYY-MMM-DD HH:mm:ss a");

const stream = ref(null);
const openIframe = ref(false);
const denominationTableData = ref(props.data.rgc);
const allocatedGcData = ref(null);
const scanData = ref(null);
const scanModal = ref(false);
const allocatedModal = ref(false);
const viewScannedModal = ref(false);
const scannedGcData = ref(null);

//Computed
const totals = computed(() => {
    let totalBorrow = 0;

    props.data.rgc.data.forEach(({ subtotal }) => {
        const floatAmount = parseFloat(subtotal.replace(/[₱,]/g, ""));
        totalBorrow += floatAmount;
    });
    //format with sign
    return new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
    }).format(totalBorrow);
});

//Methods

const submitForm = () => {
    const rid = props.data.details.sgc_id;
    const store_id = props.data.details.store.store_id;
    //released = current user
    formState
        .transform((data) => ({
            ...data,
            rel_num: props.data.rel_num,
            rid: rid,
            store_id: store_id,
        }))
        .post(route("treasury.store.gc.releasingEntrySubmission"), {
            preserveState: true,
            onSuccess: ({ props }) => {
                openLeftNotification(props.flash);
                if (props.flash.success) {
                    handleClose();
                    stream.value = `data:application/pdf;base64,${props.flash.stream}`;
                    openIframe.value = true;
                }
            },
        });
};
const routeToHome = () => {
    router.visit(route("treasury.dashboard"));
};

const viewScannedGc = async () => {
    const { data } = await axios.get(
        route("treasury.store.gc.viewScannedBarcode"),
        { params: { id: props.data.details.sgc_id } }
    );
    scannedGcData.value = data;
    viewScannedModal.value = true;
};

const onScannedPagination = async (link) => {
    if (link.url) {
        const { data } = await axios.get(
            `${window.location.origin}/treasury/store-gc/view-scanned-barcode${link.url}`
        );
        //to handle single record in table pagination
        if (data && !Array.isArray(data.data)) {
            data.data = [Object.values(data.data)[0]];
        }
        scannedGcData.value = data;
    }
};
const onChangeDenominationPagination = async (link) => {
    if (link.url) {
        const { data } = await axios.get(link.url);
        denominationTableData.value = data.rgc;
    }
};
const handleClose = () => {
    emit("update:open", false);
};
const handleScanModal = (record) => {
    scanData.value = record;
    scanModal.value = true;
};
const countScannedBc = (record) => {
    return page.barcodeReviewScan?.allocation?.filter((item) => {
        return (
            record.sri_items_denomination == item.denid &&
            item.reqid == props.data.details.sgc_id
        );
    }).length;
};
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
    formState.paymentType.type = value;
    formState.errors["paymentType.type"] = null;
};
const handleCheckedBy = (value) => {
    formState.checkedBy = value;
    formState.clearErrors("checkedBy");
};
const handleReceivedBy = (value) => {
    formState.receivedBy = value;
    formState.clearErrors("receivedBy");
}
const handleCustomerOption = (value) =>
    (formState.paymentType.customer = value);
const handleDocumentChange = (file: UploadChangeParam) => {
    formState.file = file.file;
};

//Watchers
watch(
    () => props.data.rgc,
    (newValue) => {
        if (newValue) {
            denominationTableData.value = newValue;
        }
    },
    { immediate: true }
);
</script>
