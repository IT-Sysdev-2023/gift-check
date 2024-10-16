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
                            <a-input
                                :value="denominations.promo_releasing_no"
                                readonly
                            />
                        </a-form-item>
                        <a-form-item label="Date Released:">
                            <a-input :value="today" readonly />
                        </a-form-item>
                        <a-form-item label="Upload Document">
                            <ant-upload-image
                                @handle-change="handleDocumentChange"
                            />
                        </a-form-item>
                        <a-form-item
                            label="Remarks:"
                            :validate-status="formState.errors?.remarks ? 'error' : ''"
                            :help="formState.errors?.remarks"
                        >
                            <a-textarea v-model:value="formState.remarks" @input="() => formState.errors.remarks = null"/>
                        </a-form-item>
                        <a-form-item
                            label="Checked By:"
                        >
                          <a-input :value="formState.checkedBy" readonly/>
                        </a-form-item>
                        <a-form-item
                            label="Approved By:"
                        >
                           <a-input :value="formState.approvedBy" readonly/>
                        </a-form-item>
                        <a-form-item label="Released By:">
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
                            <a-input v-model:value="formState.receivedBy" @input="() => formState.errors.receivedBy = null"/>
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
                    <div class="mb-8 pt-5 text-right space-x-5">
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
    <ScanModalReleasing
        v-model:open="scanModal"
        :data="data"
        :scan-data="scanData"
    />

    <!-- View Scanned Gc -->
    <a-modal
        v-model:open="viewScannedModal"
        title="Promo Scanned Gc"
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
                    dataIndex: 'productionnum',
                },
                {
                    title: 'Type',
                    dataIndex: 'promo',
                },
                {
                    title: 'Denomination',
                    dataIndex: 'denomination',
                },
            ]"
            :data-source="scannedGcData"
        >
        </a-table>
        <!-- <pagination-axios
            :datarecords="scannedGcData"
            @on-pagination="onScannedPagination"
        /> -->
    </a-modal>
</template>

<script lang="ts" setup>
import dayjs from "dayjs";
import { usePage } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import {
    PageWithSharedProps,
    PaginationTypes,
} from "@/../../resources/js/types";
import { notification } from "ant-design-vue";
import axios from "axios";
import { useForm } from "@inertiajs/vue3";
import type { UploadChangeParam } from "ant-design-vue";

//Props
const props = defineProps<{
    open: boolean;
    data: any;
    denominations: {
        assignatories: any[];
        promo_releasing_no: string;
        denomination: PaginationTypes;
    };
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
    checkedBy: props.data?.approved_request_user?.user?.full_name,
    approvedBy: props.data?.approved_by_type,
});
const releasingNo = ref("");
const today = dayjs().format("YYYY-MMM-DD HH:mm:ss a");
const denominationTableData = ref(props.denominations.denomination);
const scanData = ref(null);
const scanModal = ref(false);
const viewScannedModal = ref(false);
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
    formState
        .transform((data) => ({
            ...data,
            rid: props.data.req_id,
        }))
        .post(route("treasury.transactions.promo.gc.releasing.submission"), {
            onSuccess: (e) => {
                if(e.props.flash.success){
                    notification.success({
                        message: "Success mate!",
                        description: e.props.flash.success,
                    });
                    location.reload();
                }
                if(e.props.flash.error){
                    notification.error({
                        message: "Submission Failed",
                        description: e.props.flash.error,
                    });
                }
            },
        });
};
const viewScannedGc = () => {
    const data =  page.barcodeReviewScan?.promo?.filter((item) => {
        return (
            item.reqid == props.data.req_id
        );
    });
    scannedGcData.value = data;
    viewScannedModal.value = true;
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
    return page.barcodeReviewScan?.promo?.filter((item) => {
        return (
            record.pgcreqi_denom == item.denomid &&
            item.reqid == props.data.req_id
        );
    }).length;
};
const handPaymentType = (value: string) => {
    formState.paymentType.type = value;
    formState.errors["paymentType.type"] = null;
};
const handleCheckedBy = (value) => {
    formState.checkedBy = value;
    formState.errors.checkedBy = null;
};
const handleApprovedBy = (value) => {
    formState.approvedBy = value;
    formState.errors.approvedBy = null;
};
const handleCustomerOption = (value) =>
    (formState.paymentType.customer = value);
const handleDocumentChange = (file: UploadChangeParam) => {
    formState.file = file.file;
};

//Watchers
watch(
    () => props.denominations,
    (newValue) => {
        if (newValue) {
            releasingNo.value = newValue.promo_releasing_no;
            denominationTableData.value = newValue.denomination;
        }
    },
    { immediate: true }
);
</script>
