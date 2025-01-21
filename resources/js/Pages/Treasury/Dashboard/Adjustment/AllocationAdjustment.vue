<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route(dashboardRoute)">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-card>
            <a-table
                :data-source="records.data"
                :columns="columns"
                bordered
                size="small"
                :pagination="false"
            >
                <template #title> </template>
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'store'">
                        {{ record.store.store_name }}
                    </template>
                    <template v-if="column.key === 'gctype'">
                        {{ record.gcType?.gctype }}
                    </template>
                    <template v-if="column.key === 'user'">
                        {{ record.user.full_name }}
                    </template>
                    <template v-if="column.key === 'adjustType'">
                        {{ record.aadj_type }}
                    </template>
                    <template v-if="column.key === 'action'">
                        <a-button
                            type="primary"
                            size="small"
                            @click="viewDetails(record.aadj_id)"
                        >
                            <template #icon>
                                <FileSearchOutlined />
                            </template>
                            View
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination-resource class="mt-5" :datarecords="records" />
        </a-card>
        <a-modal
            v-model:open="open"
            title="Allocation Adjustment Details"
            :footer="false"
            width="800px"
            centered
        >
            <a-tabs
                v-model:activeKey="activeScannedKey"
                @change="viewGcAllocationTab"
            >
                <a-tab-pane key="all" tab="All" force-render></a-tab-pane>
                <a-tab-pane
                    v-for="denom of recordDetails.denoms"
                    :key="denom.denomination"
                    :tab="denom.denomination_format"
                ></a-tab-pane>
            </a-tabs>
            <a-table
                size="small"
                :data-source="recordDetails.record.data"
                bordered
                :columns="[
                    {
                        title: 'Barcodes',
                        key: 'barcode',
                    },
                    {
                        title: 'Denomination',
                        key: 'denomination',
                    },
                ]"
                :pagination="false"
            >
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'barcode'">
                        {{ record.aadji_barcode }}
                    </template>
                    <template v-if="column.key === 'denomination'">
                        {{ record.gc.denomination.denomination_format }}
                    </template>
                </template>
            </a-table>

            <pagination-axios-small
                class="mt-5"
                :datarecords="recordDetails.record"
                @on-pagination="onChangePagination"
            />
        </a-modal>
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { PaginationTypes } from "@/types";
import { AllocationAdjustment, ColumnTypes } from "@/types/treasury";
import axios from "axios";
import { computed, ref } from "vue";

defineProps<{
    title: string;
    records: PaginationTypes<AllocationAdjustment>;
    columns: ColumnTypes[];
}>();

const activeScannedKey = ref("all");
const recordDetails = ref();
const open = ref(false);
const id = ref("");

const dashboardRoute = computed(() => {
    const webRoute = route().current();
    const res = webRoute?.split(".")[0];
    return res + ".dashboard";
});

// const getValue = (record, dataIndex) => {
//     return dataIndex.reduce((acc, index) => acc[index], record);
// };
// const viewHandler = (id) => {
//     router.get(route("treasury.special.gc.update.pending", id));
// };

const onChangePagination = async (link) => {
    if (link.url) {
        const { data } = await axios.get(link.url);
        recordDetails.value = data;
    }
};

const viewDetails = async (id) => {
    id.value = id;
    const { data } = await axios.get(
        route("treasury.adjustment.viewAllocation", id),
    );
    // console.log(data);
    recordDetails.value = data;
    open.value = true;
};

const viewGcAllocationTab = async (value) => {
    const text = value == "all" ? "" : value;
    const { data } = await axios.get(
        route("treasury.adjustment.viewAllocation", id.value),
        {
            params: {
                search: text,
            },
        },
    );
    recordDetails.value.record = data.record;
};
</script>
