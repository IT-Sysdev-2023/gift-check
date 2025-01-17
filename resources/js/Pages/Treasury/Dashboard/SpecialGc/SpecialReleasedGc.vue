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
            <a-tabs
                class="mt-5"
                v-model:activeKey="activeKeyTab"
                type="card"
                @change="onTabChange"
            >
                <a-tab-pane key="0" tab="Released Special External GC Request">
                </a-tab-pane>
                <a-tab-pane key="*" tab="Released Special Internal GC Request">
                </a-tab-pane>
            </a-tabs>
            <a-card>
                <a-table
                    :data-source="data.data"
                    :columns="columns"
                    bordered
                    size="small"
                    :pagination="false"
                    :loading="onLoading"
                >
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.key === 'requestedBy'">
                            {{ record.user }}
                        </template>
                        <template v-if="column.key === 'customer'">
                            {{ record.specialExternalCustomer?.spcus_acctname }}
                        </template>
                        <template v-if="column.key === 'dateReleased'">
                            {{ record.approvedRequest?.reqap_date }}
                        </template>
                        <template v-if="column.key === 'releasedBy'">
                            {{ record.approvedRequest?.user.full_name }}
                        </template>

                        <template v-if="column.key === 'action'">
                            <a-button
                                type="primary"
                                size="small"
                                @click="viewRecord(record.spexgc_id)"
                            >
                                <template #icon>
                                    <FileSearchOutlined />
                                </template>
                                View
                            </a-button>
                        </template>
                    </template>
                </a-table>

                <pagination-resource class="mt-5" :datarecords="data" />
            </a-card>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import { router } from "@inertiajs/core";
import { computed, ref, watch } from "vue";
import { ColumnTypes, FilterTypes } from "@/types/treasury";

const props = defineProps<{
    desc: string;
    title: string;
    data: any;
    columns: ColumnTypes[];
    remainingBudget: string;
    filters: FilterTypes;
    tab: string;
}>();

const activeKeyTab = ref(props.tab);
const onLoading = ref(false);

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
    router.get(route("treasury.special.gc.viewReleasedGc", id));
};

const onTabChange = (val) => {
    router.visit(route(route().current()), {
        data: { promo: val },
        only: ["data", "tab", "title"],
        preserveScroll: true,
        onStart: () => {
            onLoading.value = true;
        },
        onSuccess: () => {
            onLoading.value = false;
        },
    });
};

watch(
    () => form.value,
    throttle(function () {
        const formattedDate = form.value.date
            ? form.value.date.map((date) => date.format("YYYY-MM-DD"))
            : [];

        this.$inertia.get(
            route(route().current()),
            { ...pickBy(form.value), date: formattedDate },
            {
                preserveState: true,
            },
        );
    }, 150),
    { deep: true },
);
</script>
