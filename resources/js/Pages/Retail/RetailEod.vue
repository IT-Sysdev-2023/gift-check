<template>
    <AuthenticatedLayout>
        <a-card title="Store EOD">
            <div>
                <div class="flex justify-end mb-3">
                    <a-input-search
                        v-model:value="search"
                        placeholder="Enter Barcode Here"
                        style="width: 200px"
                        @change="onSearch"
                    />
                </div>
            </div>
            <a-table
                size="small"
                bordered
                :dataSource="data"
                :columns="columns"
            >
                <template v-slot:bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'verify'">
                        {{ record.firstname }} {{ record.lastname }}
                    </template>
                    <template v-if="column.dataIndex === 'customer'">
                        {{ ucwords(record.cus_fname)
                        }}{{ ucwords(record.cus_lname) }}
                    </template>
                    <template v-if="column.dataIndex === 'status'">
                        <a-tag
                            :color="
                                record_vs_reverifydate === null
                                    ? '#87d068'
                                    : '#2db7f5'
                            "
                            >{{
                                record_vs_reverifydate === null
                                    ? "Verified"
                                    : "Reverified"
                            }}</a-tag
                        >
                    </template>
                </template>
            </a-table>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/core";
import { ref } from "vue";

defineProps({
    data: Object,
});

const search = ref(null);

const ucwords = (str) => {
    return str.toLowerCase().replace(/\b\w/g, (char) => char.toUpperCase());
};

const onSearch = () => {
    router.get(route("retail.storeEod"), {
        barcode: search.value,
    },{
        preserveState: true
    });
};

const columns = [
    {
        title: "Barcode #",
        dataIndex: "vs_barcode",
        width: "12.5%",
        align: "center",
    },
    {
        title: "Denomination",
        dataIndex: "vs_tf_denomination",
        width: "12.5%",
        align: "center",
    },
    {
        title: "GC Type",
        dataIndex: "gctype",
        width: "12.5%",
        align: "center",
    },
    {
        title: "Date",
        dataIndex: "date",
        width: "12.5%",
        align: "center",
    },
    {
        title: "Time",
        dataIndex: "vs_time",
        width: "12.5%",
        align: "center",
    },
    {
        title: "Verified By",
        dataIndex: "verify",
        width: "12.5%",
        align: "center",
    },
    {
        title: "Customer Name",
        dataIndex: "customer",
        width: "12.5%",
        align: "center",
    },
    {
        title: "Status",
        dataIndex: "status",
        width: "12.5%",
        align: "center",
    },
];
</script>
