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
                            <!-- <a-input :value="data.rel_num" readonly /> -->
                        </a-form-item>
                        <a-form-item label="Date Released:">
                            <a-input :value="today" readonly />
                        </a-form-item>
                        <a-form-item
                            label="Remarks:"
                            :validate-status="errorForm?.remarks ? 'error' : ''"
                            :help="errorForm?.remarks"
                        >
                            <a-textarea v-model:value="formState.remarks" />
                        </a-form-item>
                        <a-form-item
                            label="Checked By:"
                            :validate-status="
                                errorForm?.checkedBy ? 'error' : ''
                            "
                            :help="errorForm?.checkedBy"
                        >
                            <!-- <ant-select
                                :options="data.checkBy"
                                @handle-change="handleCheckedBy"
                            /> -->
                        </a-form-item>
                        <!-- <a-form-item label="Released By:">
                            <a-input
                                :value="$page.props.auth.user.full_name"
                                readonly
                            />
                        </a-form-item> -->

                        <a-form-item
                            label="Received By:"
                            :validate-status="
                                errorForm?.receivedBy ? 'error' : ''
                            "
                            :help="errorForm?.receivedBy"
                        >
                            <a-input v-model:value="formState.receivedBy" />
                        </a-form-item>
                        <check-cash-jv-payment
                            :formState="formState"
                            :errorForm="errorForm"
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
                            label="Promo GC Req #"
                            :labelStyle="{ fontWeight: 'bold' }"
                        >
                            {{ data.req_no }}
                        </a-descriptions-item>
                        <a-descriptions-item
                            label="Time & Date Requested"
                            :labelStyle="{ fontWeight: 'bold' }"
                            :span="2"
                        >
                            {{ data.date_req }}
                        </a-descriptions-item>
                        <a-descriptions-item
                            label="Date Needed"
                            :labelStyle="{ fontWeight: 'bold' }"
                        >
                            {{ data.date_needed }}
                        </a-descriptions-item>

                        <a-descriptions-item
                            v-if="data.document"
                            label="Document"
                            :labelStyle="{ fontWeight: 'bold' }"
                        >
                            {{ data.document }}
                        </a-descriptions-item>
                        <a-descriptions-item
                            label="Remarks"
                            :labelStyle="{ fontWeight: 'bold' }"
                        >
                            {{ data.remarks }}
                        </a-descriptions-item>
                        <a-descriptions-item
                            label="Requested by"
                            :labelStyle="{ fontWeight: 'bold' }"
                        >
                            {{ data.user }}
                        </a-descriptions-item>
                    </a-descriptions>
                    <!-- <div class="mb-8 pt-5 text-right space-x-5">
                        <a-button @click="viewAllocatedGc" type="primary" ghost
                            >View Allocated GC</a-button
                        >
                        <a-button @click="viewScannedGc" type="dashed"
                            >View Scanned Gc</a-button
                        >
                    </div> -->
                    <a-table
                        bordered
                        class="mt-8"
                        size="small"
                        :pagination="false"
                        :data-source="denominationTableData.data"
                        :columns="denominationColumns"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.key == 'subtotal'">
                                ₱{{ record.subtotal }}
                            </template>
                            <template v-if="column.key == 'reqgc'">
                                {{ record.pgcreqi_remaining }} pc/s
                            </template>
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

    <!-- Scan Modal  -->
    <ScanModalReleasing v-model:open="scanModal" :data="data" :scan-data="scanData" />

    <!-- View Scanned Gc -->
    <a-modal
        v-model:open="viewScannedModal"
        title="Scanned Gc"
        style="width: 800px"
        centered
        :footer="null"
    >
        <!-- <a-table
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
        > -->
        <!-- </a-table> -->
        <pagination-axios
            :datarecords="scannedGcData"
            @on-pagination="onScannedPagination"
        />
    </a-modal>
</template>

<script lang="ts" setup>
import dayjs from "dayjs";
import { usePage } from "@inertiajs/vue3";
import { ref, computed, reactive, watch } from "vue";
import {
    PageWithSharedProps,
    PaginationTypes,
} from "@/../../resources/js/types";
import { notification } from "ant-design-vue";
import axios from "axios";

import type { UploadChangeParam } from "ant-design-vue";

//Props
const props = defineProps<{
    open: boolean;
    data: any;
    denominations: PaginationTypes;
}>();
const page = usePage<PageWithSharedProps>().props;
const emit = defineEmits<{
    (e: "update:open", value: boolean): void;
}>();

//Data/Variables
const formState = reactive({
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

const today = dayjs().format("YYYY-MMM-DD HH:mm:ss a");
const denominationTableData = ref(props.denominations);
const allocatedGcData = ref(null);
const scanData = ref(null);
const scanModal = ref(false);
const allocatedModal = ref(false);
const viewScannedModal = ref(false);
const errorForm = ref({
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
const scannedGcData = ref(null);

//Computed
const totals = computed(() => {
    let totalBorrow = 0;
    denominationTableData.value.data.forEach(({ subtotal }) => {
        const floatAmount = subtotal;
        totalBorrow += floatAmount;
    });
    //format with sign
    return new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
    }).format(totalBorrow);
});

//Methods
const denominationColumns = [
    {
        title: "Denomination",
        dataIndex: "denomination",
    },
    {
        title: "Requested Gc",
        key: "reqgc",
    },
    {
        title: "Subtotal",
        key: "subtotal",
    },
    {
        title: "Action",
        key: "action",
    },
    {
        title: "Scanned Gc",
        key: "scan",
    },
];
const submitForm = () => {
    //     const rid = props.data.details.sgc_id;
    //     const store_id = props.data.details.store.store_id;
    //     //released = current user
    //     axios
    //         .post(route("treasury.store.gc.releasingEntrySubmission"), {
    //             rid: rid,
    //             store_id: store_id,
    //             file: formState.file,
    //             remarks: formState.remarks,
    //             receivedBy: formState.receivedBy,
    //             paymentType: formState.paymentType,
    //             checkedBy: formState.checkedBy,
    //         })
    //         .then((res) => {
    //             notification.success({
    //                 message: "Scan Success",
    //                 description: res.data,
    //             });
    //             location.reload();
    //         })
    //         .catch((err) => {
    //             if (err.response.status === 400) {
    //                 notification.error({
    //                     message: "Submission Failed",
    //                     description: err.response.data,
    //                 });
    //             } else {
    //                 // console.log(formState.errors)
    //                 console.log(err.response.data.errors);
    //                 errorForm.value = err.response.data.errors;
    //             }
    //         });
    // };
    // const viewScannedGc = async () => {
    //     const { data } = await axios.get(
    //         route("treasury.store.gc.viewScannedBarcode"),
    //         { params: { id: props.data.details.sgc_id } }
    //     );
    //     scannedGcData.value = data;
    //     viewScannedModal.value = true;
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
        denominationTableData.value = data;
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
            record.pgcreqi_denom == item.denomid &&
            item.reqid == props.data.req_id
        );
    }).length;
};
// const viewAllocatedGc = async () => {
//     const { data } = await axios.get(
//         route(
//             "treasury.store.gc.viewAllocatedList",
//             props.data.details.sgc_store
//         )
//     );
//     allocatedGcData.value = data;
//     allocatedModal.value = true;
// };
const handPaymentType = (value: string) => {
    formState.paymentType.type = value;
    errorForm.value["paymentType.type"] = null;
};
const handleCheckedBy = (value) => {
    formState.checkedBy = value;
    // errorForm.value.checkedBy = null;
};
const handleCustomerOption = (value) =>
    (formState.paymentType.customer = value);
const handleDocumentChange = (file: UploadChangeParam) => {
    // formState.file = file.file;
};

//Watchers
watch(
    () => props.denominations,
    (newValue) => {
        if (newValue) {
            denominationTableData.value = newValue;
        }
    },
    { immediate: true }
);
</script>
