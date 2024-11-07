<template>
    <AuthenticatedLayout>
        <a-card title="Released Special External GC">
            <a-table :dataSource="data.data" :columns="columns">
                <template v-slot:bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'view'">
                        <a-button @click="viewDetails(record.spexgc_id)"
                            ><EyeOutlined
                        /></a-button>
                    </template>
                </template>
            </a-table>
        </a-card>
        <a-modal
            v-model:open="viewdetailsmodal"
            width="80%"
            style="top: 20px"
            @ok="handleOk"
        >
            <a-tabs v-model:activeKey="activeKey">
                <a-tab-pane
                    key="1"
                    :tab="
                        'Special External GC Request # ' +
                        selecteddata.spexgc_num
                    "
                >
                    <div style="height: 570px">
                        <a-row :gutter="[16, 16]">
                            <a-col :span="6">
                                <a-form-item label="Date Requested">
                                    <a-input
                                        :value="selecteddata.datereq"
                                        readonly
                                    />
                                </a-form-item>
                                <a-form-item label="Requested By">
                                    <a-input
                                        :value="selecteddata.requestedBy"
                                        readonly
                                    />
                                </a-form-item>
                            </a-col>
                            <a-col :span="6">
                                <a-form-item label="Date Validity">
                                    <a-input
                                        :value="selecteddata.validity"
                                        readonly
                                    />
                                </a-form-item>
                                <a-form-item label="Remarks">
                                    <a-input
                                        :value="selecteddata.spexgc_remarks"
                                        readonly
                                    />
                                </a-form-item>
                                <a-form-item label="Payment Type">
                                    <a-input :value="paymentType" readonly />
                                </a-form-item>
                            </a-col>
                            <a-col :span="12">
                                <a-row :gutter="[16, 16]" class="bg-blue-200">
                                    <a-col :span="12" class="p-2">
                                        <a-form-item label="Date Approved">
                                            <a-input
                                                :value="selecteddata.reqap_date"
                                                readonly
                                            />
                                        </a-form-item>
                                        <a-form-item label="Checked by">
                                            <a-input
                                                :value="
                                                    selecteddata.reqap_checkedby
                                                "
                                                readonly
                                            />
                                        </a-form-item>
                                        <a-form-item label="Prepared By">
                                            <a-input
                                                :value="selecteddata.preparedBy"
                                                readonly
                                            />
                                        </a-form-item>
                                    </a-col>
                                    <a-col :span="12" class="p-2">
                                        <a-form-item label="Remarks">
                                            <a-textarea
                                                :value="
                                                    selecteddata.reqap_remarks
                                                "
                                                readonly
                                            />
                                        </a-form-item>
                                        <a-form-item label="Approved By">
                                            <a-input
                                                :value="
                                                    selecteddata.reqap_approvedby
                                                "
                                                readonly
                                            />
                                        </a-form-item>
                                    </a-col>
                                </a-row>
                                <a-row :gutter="[16, 16]" class="bg-blue-300">
                                    <a-col :span="12" class="p-2">
                                        <a-form-item label="Date Reviewed">
                                            <a-input
                                                :value="revdetails.reqap_date"
                                                readonly
                                            />
                                        </a-form-item>
                                        <a-form-item label="Remarks">
                                            <a-textarea
                                                :value="
                                                    revdetails.reqap_remarks
                                                "
                                                readonly
                                            />
                                        </a-form-item>
                                    </a-col>
                                    <a-col :span="12">
                                        <a-form-item label="Reviewed by">
                                            <a-input
                                                :value="revdetails.rev"
                                                readonly
                                            />
                                        </a-form-item>
                                    </a-col>
                                </a-row>
                                <a-row :gutter="[16, 16]" class="bg-blue-400">
                                    <a-col :span="12">
                                        <a-form-item label="Date Released">
                                            <a-input
                                                :value="
                                                    releaseDetails.releaseDate
                                                "
                                                readonly
                                            />
                                        </a-form-item>
                                        <a-form-item label="Remarks">
                                            <a-input
                                                :value="
                                                    releaseDetails.reqap_remarks
                                                "
                                                readonly
                                            />
                                        </a-form-item>
                                    </a-col>
                                    <a-col :span="12">
                                        <a-form-item label="Released by">
                                            <a-input
                                                :value="releaseDetails.relby"
                                                readonly
                                            />
                                        </a-form-item>
                                        <a-form-item label="Received by">
                                            <a-input
                                                :value="
                                                    selecteddata.spexgc_receviedby
                                                "
                                                readonly
                                            />
                                        </a-form-item>
                                    </a-col>
                                </a-row>
                            </a-col>
                        </a-row>
                    </div>
                </a-tab-pane>
                <a-tab-pane key="2" tab="Barcodes" force-render>
                    <div style="height: 700px">
                        <a-table :dataSource="gc.data" :columns="gccolumns" />
                    </div>
                </a-tab-pane>
            </a-tabs>
        </a-modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";

defineProps({
    data: Object,
});

const selecteddata = ref({});
const revdetails = ref({});
const releaseDetails = ref({});
const viewdetailsmodal = ref(false);
const activeKey = ref("1");
const paymentType = ref("");
const gc = ref([]);

const viewDetails = (id) => {
    axios
        .get(route("marketing.releasedspexgc.viewReleasedSpexGcdetails"), {
            params: {
                id: id,
            },
        })
        .then((r) => {
            viewdetailsmodal.value = true;
            selecteddata.value = r.data.data.data[0];
            revdetails.value = r.data.data.revdetails[0];
            releaseDetails.value = r.data.data.releaseDetails[0];
            gc.value = r.data.data.gc;
            if (r.data.data.data[0].spexgc_paymentype === "1") {
                paymentType.value = "Cash";
            } else if (r.data.data.data[0].spexgc_paymentype === "2") {
                paymentType.value = "Check";
            } else if (r.data.data.data[0].spexgc_paymentype === "3") {
                paymentType.value = "JV";
            } else if (r.data.data.data[0].spexgc_paymentype === "4") {
                paymentType.value = "AR";
            } else if (r.data.data.data[0].spexgc_paymentype === "5") {
                paymentType.value = "On Account";
            }
        });
};

const columns = ref([
    {
        title: "RFSEGC#",
        dataIndex: "spexgc_num",
    },
    {
        title: "Date Requested",
        dataIndex: "datereq",
    },
    {
        title: "Requested By",
        dataIndex: "requestedBy",
    },
    {
        title: "Customer",
        dataIndex: "spcus_companyname",
        ellipsis: "true",
    },
    {
        title: "Date Released",
        dataIndex: "reqap_date",
    },
    {
        title: "Released By",
        dataIndex: "releasedBy",
    },
    {
        title: "View",
        dataIndex: "view",
        align: "center",
    },
]);

const gccolumns = ref([
    {
        title: "Barcode",
        dataIndex: "spexgcemp_barcode",
        align: "center",
    },
    {
        title: "Denomination",
        dataIndex: "spexgcemp_denom",
        align: "center",
    },
    {
        title: "Lastname",
        dataIndex: "spexgcemp_lname",
        align: "center",
    },
    {
        title: "Firstname",
        dataIndex: "spexgcemp_fname",
        align: "center",
    },
    {
        title: "Middlename",
        dataIndex: "spexgcemp_mname",
        align: "center",
    },
    {
        title: "Name Extension",
        dataIndex: "spexgcemp_extname",
        align: "center",
    },
]);
</script>
