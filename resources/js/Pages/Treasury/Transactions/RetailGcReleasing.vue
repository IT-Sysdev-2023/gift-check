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
                    <template v-if="column.key">
                        <span>
                            <!-- for the dynamic implementation of object properties, just add a key in column-->
                            {{ getValue(record, column.dataIndex) }}
                        </span>
                    </template>
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

                    <template v-if="column.dataIndex === 'action'">
                        <a-button
                            type="primary"
                            size="small"
                            :disabled="record.status === 'closed'"
                            @click="viewRecord(record)"
                        >
                            <template #icon>
                                <FileSearchOutlined />
                            </template>
                            View
                        </a-button>
                    </template>
                </template>
            </a-table>

            <pagination class="mt-5" :datarecords="data" />
        </a-card>
    </AuthenticatedLayout>
    <!-- <promo-gc-releasing-modal v-model:open="showModal" :data="modalData" :denominations="denominationList"/> -->
</template>
<script lang="ts" setup>
import AuthenticatedLayout from "@/../../resources/js/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import { highlighten } from "@/../../resources/js/Mixin/UiUtilities";
import { ColumnTypes, FilterTypes } from "@/types/treasury";
import { computed, ref, watch } from "vue";
import { router } from "@inertiajs/core";

const { highlightText } = highlighten();

const props = defineProps<{
    desc: string;
    title: string;
    data: any;
    columns: ColumnTypes[];
    filters: FilterTypes;
}>();

// const modalData = ref({});
// const denominationList = ref({});
// const descriptionRecord = ref([]);
// const showModal = ref(false);

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

// const  viewRecord = async(record) => {
//     this.modalData = record
//     const { data } = await axios.get(
//         route("treasury.transactions.promo.gc.releasing.denominationList", record.req_id)
//     );
//     // console.log(data);
//     this.denominationList = data;
//     this.showModal = true;
// },

const getValue = (record, dataIndex) => {
    return dataIndex.reduce((acc, index) => acc[index], record);
};

watch(
    () => form.value,
    throttle(function () {
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
    }, 150),
    { deep: true },
);
</script>
