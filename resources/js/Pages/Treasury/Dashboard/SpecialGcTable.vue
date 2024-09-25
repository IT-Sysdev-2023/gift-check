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
        <div class="flex justify-between mb-5">
            <div>
                <a-range-picker v-model:value="form.date" />
            </div>
            <div>
                <a-input-search
                    class="mr-1"
                    v-model:value="form.search"
                    placeholder="Search here..."
                    style="width: 300px"
                />
            </div>
        </div>
        <a-tabs
            v-model:activeKey="activeKeyTab"
            type="card"
        >
            <a-tab-pane key="1" tab="Pending Special External GC Request">
                <a-table
                    :data-source="data.data"
                    :columns="columns"
                    :loading="onLoading"
                    bordered
                    size="small"
                    :pagination="false"
                >
                    <template #title> </template>
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex">
                            <span
                                v-html="
                                    highlightText(
                                        record[column.dataIndex],
                                        form.search
                                    )
                                "
                            >
                            </span>
                        </template>
                        <template v-if="column.key">
                            <span>
                                <!-- for the dynamic implementation of object properties, just add a key in column-->
                                {{ getValue(record, column.dataIndex) }}
                            </span>
                        </template>
                        <template v-if="column.dataIndex === 'action'">
                            <a-button
                                type="primary"
                                size="small"
                                @click="viewHandler(record.spexgc_id)"
                            >
                                <template #icon>
                                    <FileSearchOutlined />
                                </template>
                                Update
                            </a-button>
                        </template>
                    </template>
                </a-table>
                <pagination-resource class="mt-5" :datarecords="data" />
            </a-tab-pane>
            <a-tab-pane key="2" tab="Pending Special Internal GC Request">
                <a-table
                    :data-source="externalData.data"
                    :columns="columns"
                    bordered
                    size="small"
                    :pagination="false"
                >
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex">
                            <span
                                v-html="
                                    highlightText(
                                        record[column.dataIndex],
                                        form.search
                                    )
                                "
                            >
                            </span>
                        </template>
                        <template v-if="column.key">
                            <span>
                                <!-- for the dynamic implementation of object properties, just add a key in column-->
                                {{ getValue(record, column.dataIndex) }}
                            </span>
                        </template>
                        <template v-if="column.dataIndex === 'action'">
                            <a-button
                                type="primary"
                                size="small"
                                @click="viewHandler(record.spexgc_id)"
                            >
                                <template #icon>
                                    <FileSearchOutlined />
                                </template>
                                Update
                            </a-button>
                        </template>
                    </template>
                </a-table>
                <pagination-resource class="mt-5" :datarecords="externalData" />
            </a-tab-pane>
        </a-tabs>
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
        data: Object,
        columns: Array,
        filters: Object,
        externalData: Object,
    },
    data() {
        return {
            activeKeyTab: "1",
            form: {
                search: this.filters.search,
                date: this.filters.date
                    ? [dayjs(this.filters.date[0]), dayjs(this.filters.date[1])]
                    : [],
            },
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

    watch: {
        form: {
            deep: true,
            handler: debounce(function () {
                const formattedDate = this.form.date
                    ? this.form.date.map((date) => date.format("YYYY-MM-DD"))
                    : [];

                this.$inertia.get(
                    route(route().current()),
                    { ...pickBy(this.form), date: formattedDate },
                    {
                        preserveState: true,
                    }
                );
            }, 600),
        },
    },
};
</script>
