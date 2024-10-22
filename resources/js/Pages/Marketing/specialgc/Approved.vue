<template>
    <AuthenticatedLayout>
        <div class="mb-3">
            <div class="flex justify-end">
                <a-input-search
                    class="w-96"
                    v-model:value="search"
                    placeholder="Search..."
                    enter-button
                    @search="onSearch"
                    @change="handlesearch"
                />
            </div>
        </div>

        <a-card title="Approved Special External GC Request">
            <a-spin tip="Searching..." :spinning="isloading">
                <a-table
                    :pagination="false"
                    bordered
                    size="small"
                    :dataSource="apexgcreq.data"
                    :columns="columns"
                >
                    <template #bodyCell="{ record, column }">
                        <div v-if="column.dataIndex === 'view'">
                            <a-button
                                type="primary"
                                @click="viewRecord(record.spexgc_id)"
                                ><EyeOutlined />View</a-button
                            >
                        </div>
                    </template>
                </a-table>
                <pagination class="mt-6" :datarecords="apexgcreq" />
            </a-spin>
        </a-card>
        <a-modal
            v-model:open="open"
            width="100%"
            style="top: 40px"
            @ok="closemodal"
        >
            <div>
                <a-tabs v-model:activeKey="activeKey">
                    <a-tab-pane
                        key="1"
                        :tab="
                            'Special External GC Request # ' +
                            selectedData.spexgc_num
                        "
                    >
                        <a-row :gutter="[16, 16]">
                            <a-col :span="6">
                                <a-form-item label="Date Requested">
                                    <a-input
                                        v-model:value="
                                            selectedData.datedRequested
                                        "
                                        readonly
                                    />
                                </a-form-item>
                                <a-form-item label="Requested By">
                                    <a-input
                                        v-model:value="selectedData.requestedby"
                                        readonly
                                    />
                                </a-form-item>
                                <a-form-item label="Document">
                                    <a-image
                                        style="height: 100px"
                                        :src="
                                            'storage/' + selectedData.reqap_doc
                                                ? selectedData.reqap_doc
                                                : ''
                                        "
                                        alt="No Document Uploaded"
                                    />
                                </a-form-item>
                            </a-col>
                            <a-col :span="6">
                                <a-form-item label="Date Validity">
                                    <a-input
                                        v-model:value="
                                            selectedData.datedValidity
                                        "
                                        readonly
                                    />
                                </a-form-item>
                                <a-form-item label="Remarks">
                                    <a-input
                                        v-model:value="
                                            selectedData.reqap_remarks
                                        "
                                        readonly
                                    />
                                </a-form-item>
                                <a-form-item label="AR #">
                                    <a-input
                                        v-model:value="
                                            selectedData.spexgc_payment_arnum
                                        "
                                        readonly
                                    />
                                </a-form-item>
                                <a-form-item label="Payment Type">
                                    <a-input
                                        v-model:value="paymentype"
                                        readonly
                                    />
                                </a-form-item>
                            </a-col>
                            <a-col :span="6" class="bg-blue-100">
                                <a-form-item label="Date Approved" class="mt-2">
                                    <a-input
                                        v-model:value="
                                            selectedData.datedApproved
                                        "
                                        readonly
                                    />
                                </a-form-item>
                                <a-form-item label="Document">
                                    <a-image
                                        style="height: 100px"
                                        :src="
                                            'storage/' + selectedData.reqap_doc
                                                ? selectedData.reqap_doc
                                                : ''
                                        "
                                        alt="No Document Uploaded"
                                    />
                                </a-form-item>
                                <a-form-item label="Checked By">
                                    <a-input
                                        v-model:value="selectedData.checkedby"
                                        readonly
                                    />
                                </a-form-item>
                                <a-form-item label="Prepared By">
                                    <a-input
                                        v-model:value="selectedData.approveby"
                                        readonly
                                    />
                                </a-form-item>
                            </a-col>
                            <a-col :span="6" class="bg-blue-100">
                                <a-form-item label="Remarks" class="mt-2">
                                    <a-input
                                        v-model:value="
                                            selectedData.spexgc_remarks
                                        "
                                        readonly
                                    />
                                </a-form-item>
                                <a-form-item label="Approved By">
                                    <a-input
                                        v-model:value="selectedData.approveby"
                                        readonly
                                    />
                                </a-form-item>
                            </a-col>
                        </a-row>
                    </a-tab-pane>
                    <a-tab-pane key="2" tab="Barcodes" force-render>
                        <a-table
                            size="small"
                            :dataSource="barcodes"
                            :columns="barcodecol"
                        />
                    </a-tab-pane>
                </a-tabs>
            </div>
        </a-modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { router } from "@inertiajs/core";
import axios from "axios";
import { ref } from "vue";

defineProps({
    apexgcreq: Object,
});
const search = ref("");
const isloading = ref(false);
const open = ref(false);
const selectedData = ref({});
const activeKey = ref("1");
const paymentype = ref("");
const barcodes = ref([]);

const onSearch = (search) => {
    router.get(
        route("marketing.special-gc.aexgcreq"),
        {
            search: search,
        },
        {
            onStart: () => {
                isloading.value = true;
            },
            onSuccess: () => {
                isloading.value = false;
            },
            preserveState: true,
        }
    );
};

const getPaymentType = (type) => {
    const paymentTypes = {
        1: "Cash",
        2: "Check",
        3: "JV",
        4: "AR",
        5: "On Account",
    };
    return paymentTypes[type] || "Unknown Payment Type";
};

const viewRecord = (e) => {
    axios
        .get(route("marketing.special-gc.selectedaexgcreq"), {
            params: {
                id: e,
            },
        })
        .then((res) => {
            open.value = true;
            selectedData.value = res.data.data[0];
            barcodes.value = res.data.barcodes;
            paymentype.value = getPaymentType(
                res.data.data[0]["spexgc_paymentype"]
            );
        });
};
const closemodal = () => {
    open.value = false;
};

const handlesearch = () => {
    router.get(
        route("marketing.special-gc.aexgcreq"),
        {
            search: search.value,
        },
        {
            onStart: () => {
                isloading.value = true;
            },
            onSuccess: () => {
                isloading.value = false;
            },
            preserveState: true,
        }
    );
};
const barcodecol = ref([
    {
        title: "Barcode",
        dataIndex: "spexgcemp_barcode",
    },
    {
        title: "Denomination",
        dataIndex: "spexgcemp_denom",
    },
    {
        title: "Holder",
        dataIndex: "holderfullname",
    },
]);

const columns = ref([
    {
        title: "RFSEGC #",
        dataIndex: "spexgc_num",
    },
    {
        title: "Date Requested",
        dataIndex: "dateReq",
    },
    {
        title: "Date Validity",
        dataIndex: "dateNeed",
    },
    {
        title: "Customer",
        dataIndex: "spcus_companyname",
        ellipsis: true,
    },
    {
        title: "Date Approved",
        dataIndex: "dateApproved",
    },
    {
        title: "Approved By",
        dataIndex: "appBy",
    },
    {
        title: "View Details",
        dataIndex: "view",
        align: "center",
    },
]);
</script>
