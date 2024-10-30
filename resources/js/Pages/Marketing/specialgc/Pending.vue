<template>
    <AuthenticatedLayout>
        <a-card>
            <a-tabs v-model:activeKey="activeKey">
                <a-tab-pane key="1" tab="Special Internal GC">
                    <a-table
                        size="small"
                        bordered
                        :dataSource="internal"
                        :columns="columns"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'view'">
                                <a-button
                                    size="small"
                                    block
                                    type="primary"
                                    @click="view(record.spexgc_id, 'internal')"
                                >
                                    <PicRightOutlined />Details
                                </a-button>
                            </template>
                        </template>
                    </a-table>
                </a-tab-pane>
                <a-tab-pane key="2" tab="Special External GC" force-render>
                    <a-table
                        bordered
                        size="small"
                        :dataSource="external"
                        :columns="columns"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'view'">
                                <a-button
                                    size="small"
                                    block
                                    type="primary"
                                    @click="view(record.spexgc_id, 'external')"
                                >
                                    <PicRightOutlined />Details
                                </a-button>
                            </template>
                        </template>
                    </a-table>
                </a-tab-pane>
            </a-tabs>
        </a-card>
        <a-modal
            v-model:open="open"
            :width="1000"
            @ok="closemodal"
            style="top: 20px"
        >
            <a-card>
                <a-row :gutter="[16, 16]">
                    <a-col :span="12" :gutter="[16, 16]">
                        <a-card>
                            <a-form-item label="GC Request #">
                                <a-input
                                    v-model:value="selectedData.spexgc_num"
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Date Requested">
                                <a-input
                                    v-model:value="selectedData.dateReq"
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Date Validity">
                                <a-input
                                    v-model:value="selectedData.dateNeed"
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Document Uploaded">
                                <a-image
                                    style="height: 100px"
                                    :src="'storage/' + doc"
                                ></a-image>
                            </a-form-item>
                            <a-form-item label="Remarks">
                                <a-textarea
                                    v-model:value="selectedData.spexgc_remarks"
                                    readonly
                                />
                            </a-form-item>
                        </a-card>
                    </a-col>
                    <a-col :span="12">
                        <a-card>
                            <a-form-item label="Company Name">
                                <a-textarea
                                    v-model:value="
                                        selectedData.spcus_companyname
                                    "
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Account Name">
                                <a-textarea
                                    v-model:value="selectedData.spcus_acctname"
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
                            <a-form-item label="Amount Paid">
                                <a-input
                                    v-model:value="selectedData.spexgc_balance"
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Amount Paid in Words">
                                <a-input
                                    v-model:value="selectedData.numbertowords"
                                    readonly
                                />
                            </a-form-item>
                        </a-card>
                    </a-col>
                </a-row>
                <a-row :gutter="[16, 16]" class="mt-5">
                    <a-col :span="12">
                        <a-form-item label="Denomination">
                            <a-input
                                v-for="item in denom"
                                v-model:value="item.specit_denoms"
                                readonly
                            />
                        </a-form-item>
                    </a-col>
                    <a-col :span="12">
                        <a-form-item label="Quantity">
                            <a-input
                                v-for="item in denom"
                                v-model:value="item.specit_qty"
                                readonly
                            />
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-row :gutter="[16, 16]">
                    <a-col :span="12">
                        <a-form-item label="# Holder">
                            <a-input v-for="item in denom" value="0" readonly />
                        </a-form-item>
                        <a-form-item label="Entry By">
                            <a-input
                                :value="$page.props.auth.user.full_name"
                                readonly
                            />
                        </a-form-item>
                    </a-col>
                    <a-col :span="12">
                        <a-form-item label="TOTAL">
                            <a-input
                                v-model:value="selectedData.totalDenom"
                                readonly
                            />
                        </a-form-item>
                        <a-form-item label="Total in Words">
                            <a-input
                                v-model:value="selectedData.totalnumbertowords"
                                readonly
                            />
                        </a-form-item>
                    </a-col>
                </a-row>
            </a-card>
        </a-modal>
    </AuthenticatedLayout>
</template>

<script setup>
import axios from "axios";
import { ref } from "vue";

const props = defineProps({
    internal: Object,
    external: Object,
});

const open = ref(false);
const selectedData = ref({});
const activeKey = ref("1");
const denom = ref({});
const doc = ref("");

const columns = [
    {
        title: "RFSEGC #",
        dataIndex: "spexgc_num",
        width: "14%",
    },
    {
        title: "Date Requested",
        dataIndex: "dateReq",
        width: "14%",
    },
    {
        title: "Date Validity",
        dataIndex: "dateNeed",
        width: "14%",
    },
    {
        title: "Total Denomination",
        dataIndex: "totalDenom",
        width: "14%",
    },
    {
        title: "Customer",
        dataIndex: "spcus_acctname",
        width: "14%",
        ellipsis: true,
    },
    {
        title: "Requested By",
        dataIndex: "requestedBy",
        width: "14%",
    },
    {
        title: "View",
        dataIndex: "view",
        width: "14%",
    },
];

const view = (id, type) => {
    axios
        .get(route("marketing.special-gc.pending.view"), {
            params: { id, type },
        })
        .then(({ data }) => {
            open.value = true;
            selectedData.value = data.data[0];
            denom.value = data.denom;
            doc.value = data.doc;
        });
};

const closemodal = () => {
    open.value = false;
};
</script>
