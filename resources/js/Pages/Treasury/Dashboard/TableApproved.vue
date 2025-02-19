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
                        allow-clear
                        class="mr-1"
                        v-model:value="form.search"
                        placeholder="Search here..."
                        style="width: 300px"
                    />
                </div>
            </div>
            <a-table
                :data-source="data.data"
                :columns="columns"
                bordered
                size="small"
                :pagination="false"
            >
                <template #title>
                    <a-typography-title :level="4">{{
                        title
                    }}</a-typography-title>
                </template>
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
                            {{
                                record[column.dataIndex[0]][column.dataIndex[1]]
                            }}
                        </span>
                    </template>
                    <template v-if="column.dataIndex === 'approved_by'">
                        <span>
                            {{ record.cancelled_request?.user.full_name }}
                        </span>
                    </template>

                    <template v-if="column.dataIndex === 'action'">
                        <a-button
                            type="primary"
                            size="small"
                            @click="viewRecord(record.br_id)"
                        >
                            <template #icon>
                                <FileSearchOutlined />
                            </template>
                            View
                        </a-button>
                    </template>
                </template>
            </a-table>
            <a-modal v-model:open="showModal" width="1000px" :footer="null">
                <Description :data="descriptionRecord" />
            </a-modal>

            <pagination-resource class="mt-5" :datarecords="data" />
        </a-card>
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import debounce from "lodash/debounce";
import pickBy from "lodash/pickBy";
import axios from "axios";
import { highlighten } from "@/Mixin/UiUtilities";
import Description from "./../Description.vue";
import { computed, ref, watch } from "vue";
import { BudgetRequest, ColumnTypes, FilterTypes } from "@/types/treasury";
import { router } from "@inertiajs/core";

const { highlightText } = highlighten();

const props = defineProps<{
    title: string;
    data: { data: BudgetRequest[] };
    columns: ColumnTypes[];
    filters: FilterTypes;
}>();

const descriptionRecord = ref([]);
const showModal = ref(false);

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

const viewRecord = async (id) => {
    try {
        const { data } = await axios.get(
            route("treasury.budget.request.view.approved", id),
        );
        descriptionRecord.value = data;
    } finally {
        showModal.value = true;
    }
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
