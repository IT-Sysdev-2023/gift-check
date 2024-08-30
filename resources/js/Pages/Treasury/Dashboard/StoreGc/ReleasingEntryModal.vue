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
                        <a-form-item
                            label="Upload Document:"
                            :validate-status="errorForm?.file ? 'error' : ''"
                            :help="errorForm?.file"
                        >
                            <ant-upload-image
                                @handle-change="handleDocumentChange"
                            />
                        </a-form-item>
                        <a-form-item
                            label="Remarks:"
                            :validate-status="errorForm?.remarks ? 'error' : ''"
                            :help="errorForm?.remarks"
                        >
                            <a-textarea
                                v-model:value="formState.remarks"
                                @input="() => (errorForm.remarks = null)"
                            />
                        </a-form-item>
                        <a-form-item
                            label="Checked By:"
                            :validate-status="
                                errorForm?.checkedBy ? 'error' : ''
                            "
                            :help="errorForm?.checkedBy"
                        >
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

                        <a-form-item
                            label="Received By:"
                            :validate-status="
                                errorForm?.receivedBy ? 'error' : ''
                            "
                            :help="errorForm?.receivedBy"
                        >
                            <a-input
                                v-model:value="formState.receivedBy"
                                @input="() => (errorForm.receivedBy = null)"
                            />
                        </a-form-item>
                        <a-form-item
                            label="Payment Type:"
                            :validate-status="
                                errorForm?.['paymentType.type'] ? 'error' : ''
                            "
                            :help="errorForm?.['paymentType.type']"
                        >
                            <ant-select
                                :options="paymentTypeOptions"
                                @handle-change="handPaymentType"
                            />
                        </a-form-item>
                        <div v-if="formState.paymentType.type === 'check'">
                            <a-form-item label="Bank Name:" name="bank">
                                <a-input
                                    v-model:value="
                                        formState.paymentType.bankName
                                    "
                                />
                            </a-form-item>
                            <a-form-item label="Account Number:" name="acc">
                                <a-input
                                    v-model:value="
                                        formState.paymentType.accountNumber
                                    "
                                />
                            </a-form-item>
                            <a-form-item label="Check Number:" name="check">
                                <a-input
                                    v-model:value="
                                        formState.paymentType.checkNumber
                                    "
                                />
                            </a-form-item>
                            <a-form-item label="Check Amount:" name="check">
                                <a-input
                                    v-model:value="
                                        formState.paymentType.checkAmount
                                    "
                                />
                            </a-form-item>
                        </div>
                        <a-form-item
                            label="Cash Amount:"
                            name="amount"
                            v-else-if="formState.paymentType.type === 'cash'"
                        >
                            <ant-input-number
                                v-model:amount="formState.paymentType.amount"
                            />
                        </a-form-item>
                        <a-form-item
                            label="Customer:"
                            :validate-status="
                                errorForm?.['paymentType.customer']
                                    ? 'error'
                                    : ''
                            "
                            :help="errorForm?.['paymentType.customer']"
                            v-else-if="formState.paymentType.type === 'jv'"
                        >
                            <ant-select
                                :options="customerOptions"
                                @handle-change="handleCustomerOption"
                            />
                        </a-form-item>
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
     <view-allocated-gc-modal v-model:open="allocatedModal" :allocated-gc-data="allocatedGcData" :store_id="data?.details?.sgc_store"/>

    <!-- Scan Modal -->
    <a-modal
        v-model:open="scanModal"
        title="Scan Gc"
        style="width: 600px"
        centered
        @ok="onSubmitBarcode"
    >
        <a-descriptions class="mt-5">
            <a-descriptions-item
                label="Release No"
                :span="2"
                :labelStyle="{ fontWeight: 'bold' }"
                >{{ data.rel_num }}</a-descriptions-item
            >
            <a-descriptions-item
                label="Date"
                :labelStyle="{ fontWeight: 'bold' }"
                >{{ dayjs().format("MMM DD, YYYY") }}</a-descriptions-item
            >
            <a-descriptions-item
                label="Store"
                :span="2"
                :labelStyle="{ fontWeight: 'bold' }"
                >{{ data.details.store.store_name }}</a-descriptions-item
            >
            <a-descriptions-item
                label="Denomination"
                :labelStyle="{ fontWeight: 'bold' }"
            >
                {{ scanData.denomination }}
            </a-descriptions-item>
            <a-descriptions-item
                label="Validated By"
                :span="2"
                :labelStyle="{ fontWeight: 'bold' }"
            >
                {{ $page.props.auth.user.full_name }}
            </a-descriptions-item>
            <a-descriptions-item
                label="Scan Mode"
                :labelStyle="{ fontWeight: 'bold' }"
            >
                <a-switch
                    @change="() => (errorBarcode = null)"
                    v-model:checked="scanSwitch"
                    checked-children="Range Scan"
                    un-checked-children="Single Scan"
                />
            </a-descriptions-item>
        </a-descriptions>
        <a-form :model="formBc" layout="vertical">
            <!-- //Single Scan -->
            <a-form-item
                v-if="!scanSwitch"
                label="Barcode"
                :validate-status="errorBarcode?.barcode ? 'error' : ''"
                :help="errorBarcode?.barcode"
            >
                <a-input-number
                    :maxlength="13"
                    v-model:value="formBc.barcode"
                    placeholder="Enter Barcode"
                    class="w-full h-16 text-3xl pt-4"
                    @input="() => (errorBarcode = null)"
                />
            </a-form-item>

            <!-- //Range Scan -->
            <a-row :gutter="[16, 0]" class="mt-8" v-else>
                <a-col :span="12"
                    ><a-form-item
                        label="Barcode Start"
                        :validate-status="errorBarcode?.bstart ? 'error' : ''"
                        :help="errorBarcode?.bstart"
                    >
                        <a-input-number
                            :maxlength="13"
                            placeholder="Barcode Start"
                            v-model:value="formBc.startBarcode"
                            class="w-full h-16 text-3xl pt-4"
                            @input="() => (errorBarcode = null)"
                        />
                    </a-form-item>
                </a-col>
                <a-col :span="12">
                    <a-form-item
                        label="Barcode End"
                        :validate-status="errorBarcode?.bend ? 'error' : ''"
                        :help="errorBarcode?.bend"
                    >
                        <a-input-number
                            :maxlength="13"
                            placeholder="Barcode End"
                            v-model:value="formBc.endBarcode"
                            class="w-full h-16 text-3xl pt-4"
                            @input="() => (errorBarcode = null)"
                        />
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
    </a-modal>

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
</template>

<script lang="ts" setup>
import dayjs from "dayjs";
import { usePage } from "@inertiajs/vue3";
import { ref, computed, reactive, watch } from "vue";
import { PageWithSharedProps } from "@/types";
import { notification } from "ant-design-vue";
import axios from "axios";
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

const formBc = reactive({
    barcode: null,
    startBarcode: null,
    endBarcode: null,
});

const customerOptions = [
    {
        value: "beam and go",
        label: "Beam and Go",
    },
];
const paymentTypeOptions = [
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
const today = dayjs().format("YYYY-MMM-DD HH:mm:ss a");
const scanSwitch = ref(false);
const denominationTableData = ref(props.data.rgc);
const allocatedGcData = ref(null);
const scanData = ref(null);
const scanModal = ref(false);
const allocatedModal = ref(false);
const viewScannedModal = ref(false);
const errorBarcode = ref(null);
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

    axios
        .post(route("treasury.store.gc.releasingEntrySubmission"), {
            rid: rid,
            store_id: store_id,
            file: formState.file,
            remarks: formState.remarks,
            receivedBy: formState.receivedBy,
            paymentType: formState.paymentType,
            checkedBy: formState.checkedBy,
        })
        .then((res) => {
            notification.success({
                message: "Scan Success",
                description: res.data,
            });
            location.reload();
        })
        .catch((err) => {
            if (err.response.status === 400) {
                notification.error({
                    message: "Submission Failed",
                    description: err.response.data,
                });
            } else {
                // console.log(formState.errors)
                console.log(err.response.data.errors);
                errorForm.value = err.response.data.errors;
            }
        });
};
const viewScannedGc = async () => {
    const { data } = await axios.get(
        route("treasury.store.gc.viewScannedBarcode"),
        { params: { id: props.data.details.sgc_id } }
    );
    scannedGcData.value = data;
    viewScannedModal.value = true;
};
const onSubmitBarcode = async () => {
    axios
        .post(route("treasury.store.gc.scanBarcode"), {
            scanMode: scanSwitch.value,
            bstart: formBc.startBarcode,
            bend: formBc.endBarcode,
            barcode: formBc.barcode,
            relno: props.data.rel_num,
            denid: scanData.value.sri_items_denomination,
            store_id: props.data.details.store.store_id,
            reqid: props.data.details.sgc_id,
        })
        .then((res) => {
            page.barcodeReviewScan.allocation = res.data.sessionData;

            for (let bc of res.data.barcodes) {
                if (bc.status === 200) {
                    notification.success({
                        message: "Scan Success",
                        description: bc.message,
                    });
                } else {
                    notification.error({
                        message: "Scan Failed",
                        description: bc.message,
                    });
                }
            }
            formBc.startBarcode = null;
            formBc.endBarcode = null;
            formBc.barcode = null;
            scanModal.value = false;
        })
        .catch((err) => {
            if (err.response.status === 400) {
                notification.error({
                    message: "Scan Failed",
                    description: err.response.data,
                });
            } else {
                errorBarcode.value = err.response.data.errors;
            }
        });
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
    errorForm.value["paymentType.type"] = null;
};
const handleCheckedBy = (value) => {
    formState.checkedBy = value;
    errorForm.value.checkedBy = null;
};
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
