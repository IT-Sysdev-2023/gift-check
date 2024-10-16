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
                    bordered
                    size="small"
                    :dataSource="apexgcreq"
                    :columns="columns"
                />
            </a-spin>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup>
import { router } from "@inertiajs/core";
import { ref } from "vue";

defineProps({
    apexgcreq: Object,
});
const search = ref("");
const isloading = ref(false);

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
]);
</script>
