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
                            <a-button type="primary" @click="gcsalesdetails(record.sledger_ref)">
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

const gcsalesdetails = (id) =>{
    axios.get(route('retail.store_ledger.storeledgerdetails'),{
        params:{
            id: id
        }
    })
}


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
