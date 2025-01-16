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
            <!-- {{ externalData.data }} -->
            <a-tabs v-model:activeKey="activeKeyTab" type="card">
                <a-tab-pane key="1" tab="Pending Special External GC Request">
                    <a-table
                        :data-source="externalData.data"
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
                                            form.search,
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
                    <pagination-resource
                        class="mt-5"
                        :datarecords="externalData"
                    />
                </a-tab-pane>
                <a-tab-pane key="2" tab="Pending Special Internal GC Request">
                    <a-table
                        :data-source="internalData.data"
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
                                            form.search,
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
                    <pagination-resource
                        class="mt-5"
                        :datarecords="internalData"
                    />
                </a-tab-pane>
            </a-tabs>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import debounce from "lodash/debounce";
import pickBy from "lodash/pickBy";
import { onLoading } from "@/Mixin/UiUtilities";

import { highlighten } from "@/Mixin/UiUtilities";
import { ColumnTypes, FilterTypes } from "@/types/treasury";
import { computed, ref, watch } from "vue";
import { router } from "@inertiajs/core";

const { highlightText } = highlighten();

const props = defineProps<{
    title: string;
    externalData: any;
    columns: ColumnTypes[];
    filters: FilterTypes;
    internalData: any;
}>();

const activeKeyTab = ref("1");

const form = ref({
    search: props.filters.search,
    date: props.filters.date
        ? [dayjs(props.filters.date[0]), dayjs(props.filters.date[1])]
        : [],
});

const dashboardRoute = computed(() => {
    const webRoute = route().current();
    const res = webRoute?.split(".")[0];
    return res + ".dashboard";
});

const getValue = (record, dataIndex) => {
    return dataIndex.reduce((acc, index) => acc[index], record);
};

const viewHandler = (id) => {
    router.get(route("treasury.special.gc.update.pending", id));
};

watch(
    () => form.value,
    debounce(function () {
        const formattedDate = form.value.date
            ? form.value.date.map((date) => date.format("YYYY-MM-DD"))
            : [];

        router.get(
            route(route().current()),
            { ...pickBy(form.value), date: formattedDate },
            {
                preserveState: true,
            },
        );
    }, 600),
    { deep: true },
);
</script>
