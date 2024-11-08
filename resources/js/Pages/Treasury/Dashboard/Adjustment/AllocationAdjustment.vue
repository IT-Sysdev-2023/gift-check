<script setup>
import { highlighten } from "@/Mixin/UiUtilities";

const { highlightText } = highlighten();
</script>
<template>
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
            :loading="onLoading"
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
        <a-table
            size="small"
            :data-source="recordDetails.data"
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
            :datarecords="recordDetails"
            @on-pagination="onChangePagination"
        />
    </a-modal>
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { onLoading } from "@/Mixin/UiUtilities";
import axios from "axios";

export default {
    layout: AuthenticatedLayout,
    props: {
        title: String,
        records: Object,
        columns: Array,
    },
    data() {
        return {
            recordDetails: {},
            open: false,
        };
    },
    computed: {
        dashboardRoute() {
            const webRoute = route().current();
            const res = webRoute?.split(".")[0];
            return res + ".dashboard";
        },
    },

    methods: {
        getValue(record, dataIndex) {
            return dataIndex.reduce((acc, index) => acc[index], record);
        },
        viewHandler(id) {
            this.$inertia.get(route("treasury.special.gc.update.pending", id));
        },
        async onChangePagination(link) {
            if (link.url) {
                const { data } = await axios.get(link.url);
                this.recordDetails = data;
            }
        },
        async viewDetails(id) {
            const { data } = await axios.get(
                route("treasury.adjustment.viewAllocation", id)
            );
            this.recordDetails = data;
            this.open = true;
        },
    },
};
</script>
