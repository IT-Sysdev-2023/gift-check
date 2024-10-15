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
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import { router } from "@inertiajs/vue3";
import debounce from "lodash/debounce";
import pickBy from "lodash/pickBy";
import _ from "lodash";
import { onLoading } from "@/Mixin/UiUtilities";

export default {
    layout: AuthenticatedLayout,
    props: {
        title: String,
        records: Object,
        columns: Array,
    },
    data() {
        return {
            activeKeyTab: "1",
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
    },
};
</script>
