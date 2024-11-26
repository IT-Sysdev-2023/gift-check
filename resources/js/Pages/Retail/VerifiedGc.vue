<template>
    <AuthenticatedLayout>
        <a-card title="Verified GC">
            <div>
                <div class="flex justify-end mb-2">
                    <a-input-search
                        v-model:value="search"
                        placeholder="Search Barcode"
                        style="width: 200px"
                        @change="onSearch"
                    />
                </div>
            </div>
            <a-spin tip="Searching..." :spinning="isloading">
                <a-table
                    size="small"
                    :dataSource="data.data"
                    :columns="columns"
                >
                    <template v-slot:bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'customer'">
                            {{ record.cus_fname + " " }}{{ record.cus_lname }}
                        </template>
                        <template v-if="column.dataIndex === 'details'">
                            <a-button
                                @click="details(record)"
                                style="background-color: green; color: white"
                                ><ContainerOutlined
                            /></a-button>
                        </template>
                    </template>
                </a-table>
            </a-spin>
        </a-card>
        <a-modal
            @ok="handleok"
            v-model:open="open"
            title="GC Navision POS Transactions"
            width="90%"
        >
            <a-table
                :pagination="false"
                :dataSource="datafetched"
                :columns="detailcolumn"
            />
        </a-modal>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/core";
import axios from "axios";
import { ref } from "vue";

defineProps({
    data: Object,
});

const search = ref("");
const isloading = ref(false);
const open = ref(false);
const datafetched = ref(null);

const details = (data) => {
    axios
        .get(route("retail.verified-gc.gcdetails"), {
            params: {
                barcode: data.vs_barcode,
            },
        })
        .then((r) => {
            datafetched.value = r.data.data;
            open.value = true;
        });
};

const handleok = () => {
    open.value = false;
};

const onSearch = () => {
    router.get(
        route("retail.verified-gc.list"),
        {
            barcode: search.value,
        },
        {
            onStart: () => {
                isloading.value = true;
            },
            onSuccess: () => {
                isloading.value = false;
            },
            onError: () => {
                isloading.value = false;
            },
            preserveState: true,
        }
    );
};

const columns = [
    {
        title: "Barcode",
        dataIndex: "vs_barcode",
    },
    {
        title: "Denomination",
        dataIndex: "vs_tf_denomination",
    },
    {
        title: "GC Type",
        dataIndex: "gctype",
    },
    {
        title: "Pay To",
        dataIndex: "vs_payto",
    },
    {
        title: "Date Sold/Released",
        dataIndex: "institutr_date",
    },
    {
        title: "Verified Customer",
        dataIndex: "customer",
    },
    {
        title: "GC Details",
        dataIndex: "details",
    },
];

const detailcolumn = [
    {
        title: "Textfile Line",
        dataIndex: "seodtt_line",
    },
    {
        title: "Credit Limit",
        dataIndex: "seodtt_creditlimit",
    },
    {
        title: "Cred. Pur. Amt + Add-on",
        dataIndex: "seodtt_credpuramt",
    },
    {
        title: "Add-on Amt",
        dataIndex: "seodtt_addonamt",
    },
    {
        title: "Remaining Balance",
        dataIndex: "seodtt_balance",
    },
    {
        title: "Transaction #",
        dataIndex: "seodtt_transno",
    },
    {
        title: "Time of Cred Tranx",
        dataIndex: "time",
    },
    {
        title: "Bus. Unit",
        dataIndex: "seodtt_bu",
    },
    {
        title: "Terminal #",
        dataIndex: "seodtt_terminalno",
    },
    {
        title: "Ackslip #",
        dataIndex: "seodtt_ackslipno",
    },
];
</script>
