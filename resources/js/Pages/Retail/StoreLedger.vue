<template>
    <AuthenticatedLayout>
        <a-tabs v-model:activeKey="activeKey">
            <a-tab-pane key="1" tab="Store GC Ledger">
                <a-table
                    :pagination="false"
                    :dataSource="ledger_data.data"
                    :columns="ledgercolumns"
                >
                    <template v-slot:bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'view'">
                            <a-button
                                type="primary"
                                @click="
                                    gcsalesdetails(
                                        record.sledger_ref,
                                        record.entry
                                    )
                                "
                            >
                                <FolderFilled />
                            </a-button>
                        </template>
                    </template>
                </a-table>
                <Pagination :datarecords="ledger_data" class="mt-2" />
            </a-tab-pane>
            <a-tab-pane key="2" tab="Store Revalidation" force-render>
                <a-table
                    :pagination="false"
                    :dataSource="reval_data.data"
                    :columns="revalidatecolumns"
                >
                    <template v-slot:bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'view'">
                            <a-button type="primary">
                                <FolderFilled />
                            </a-button>
                        </template>
                    </template>
                </a-table>
                <Pagination :datarecords="reval_data" class="mt-2" />
            </a-tab-pane>
        </a-tabs>

        <a-modal v-model:open="type1" :footer="false" width="900px">
            <a-card title="GC Entry Details">
                <a-row :gutter="[16, 16]">
                    <a-col :span="12">
                        <a-form-item label="GC Receiving No">
                            <a-input :value="type1data.srec_recid" readonly />
                        </a-form-item>
                        <a-form-item label="Date Received">
                            <a-input :value="type1data.dateReceived" readonly />
                        </a-form-item>
                        <a-form-item label="Received By">
                            <a-input :value="type1data.recBy" readonly />
                        </a-form-item>
                        <a-form-item label="Checked By">
                            <a-input :value="type1data.checkby" readonly />
                        </a-form-item>
                    </a-col>

                    <a-col :span="12">
                        <a-table
                            bordered
                            :dataSource="type1table"
                            :columns="type1columns"
                            size="small"
                        />
                    </a-col>
                </a-row>
            </a-card>
        </a-modal>
        <a-modal v-model:open="type2" width="900px" :footer="false">
            <a-card title="GC Sales Details">
                <a-row :gutter="[16, 16]">
                    <a-col :span="12">
                        <a-form-item label="Transaction #">
                            <a-input
                                :value="type2data.data.data.trans_number"
                                readonly
                            />
                        </a-form-item>
                        <a-form-item label="Subtotal">
                            <a-input
                                :value="type2data.data.tpayment"
                                readonly
                            />
                        </a-form-item>
                        <a-form-item label="Total Line Disc">
                            <a-input
                                :value="type2data.data.totlinedisc"
                                readonly
                            />
                        </a-form-item>
                        <a-form-item label="Subtotal Disc">
                            <a-input
                                :value="type2data.data.data.docdisc"
                                readonly
                            />
                        </a-form-item>
                        <a-form-item label="Total Payment">
                            <a-input
                                :value="type2data.data.tpayment"
                                readonly
                            />
                        </a-form-item>
                        <a-form-item label="Cashier">
                            <a-input
                                :value="type2data.data.data.cashier"
                                readonly
                            />
                        </a-form-item>
                        <a-form-item :label="type2data.data.typelabel">
                            <a-input
                                :value="type2data.data.type.payment_cash"
                                readonly
                            />
                        </a-form-item>
                        <a-form-item label="Change">
                            <a-input
                                :value="type2data.data.type.payment_change"
                                readonly
                            />
                        </a-form-item>
                    </a-col>
                    <a-col :span="12">
                        <a-card>
                            <a-table
                                size="small"
                                :dataSource="type2data.table"
                                :columns="type2columns"
                            >
                                <template v-slot:bodyCell="{ column, record }">
                                    <template
                                        v-if="column.dataIndex === 'dis'"
                                    >
                                        {{ record.discount ? record.discount : 0.00 }}
                                    </template>
                                </template>
                            </a-table>
                        </a-card>
                    </a-col>
                </a-row>
            </a-card>
        </a-modal>
    </AuthenticatedLayout>
</template>

<script setup>
import Pagination from "@/Components/Pagination.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import axios from "axios";
import { ref } from "vue";

defineProps({
    ledger_data: Object,
    reval_data: Object,
});
const activeKey = ref("1");
const type1 = ref(false);
const type2 = ref(false);

const type1data = ref({});
const type1table = ref([]);

const type2data = ref({});
const type2table = ref([]);

const gcsalesdetails = async (id, entry) => {
    await axios
        .get(route("retail.store_ledger.storeledgerdetails"), {
            params: {
                id: id,
                entry: entry,
            },
        })
        .then((r) => {
            if (r.data.type == "1") {
                type1data.value = r.data.data;
                type1table.value = r.data.table;
                type1.value = true;
            } else if (r.data.type == "2") {
                type2data.value = r.data;
                type2.value = true;
            }
        });
};

const type1columns = [
    {
        title: "Denomination",
        dataIndex: "denomination",
    },
    {
        title: "Qty",
        dataIndex: "cnt",
    },
    {
        title: "Total",
        dataIndex: "total",
    },
];
const type2columns = [
    {
        title: "Barcode",
        dataIndex: "sales_barcode",
    },
    {
        title: "Denomination",
        dataIndex: "denomination",
    },
    {
        title: "Discount",
        dataIndex: "dis",
    },
];

const ledgercolumns = [
    {
        title: "Document #",
        dataIndex: "sledger_no",
    },
    {
        title: "Date",
        dataIndex: "date",
    },
    {
        title: "Time",
        dataIndex: "time",
    },
    {
        title: "Description",
        dataIndex: "sledger_desc",
    },
    {
        title: "Debit",
        dataIndex: "debit",
    },
    {
        title: "Credit",
        dataIndex: "credit",
    },
    {
        title: "Sales Discount",
        dataIndex: "sledger_trans_disc",
    },
    {
        title: "Balance",
        dataIndex: "balance",
    },
    {
        title: "View",
        dataIndex: "view",
    },
];
const revalidatecolumns = [
    {
        title: "Transaction #",
        dataIndex: "trans_number",
    },
    {
        title: "Date",
        dataIndex: "date",
    },
    {
        title: "Time",
        dataIndex: "time",
    },
    {
        title: "# of GC",
        dataIndex: "totalGc",
    },
    {
        title: "Amount",
        dataIndex: "amount",
    },
    {
        title: "Cashier",
        dataIndex: "cashier",
    },
    {
        title: "View",
        dataIndex: "view",
    },
];
</script>
