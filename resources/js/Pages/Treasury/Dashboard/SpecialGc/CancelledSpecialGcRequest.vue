<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route(dashboardRoute)">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-card :title="title">
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
                    <template v-if="column.key === 'store'">
                        {{ record.store?.store_name }}
                    </template>
                    <template v-if="column.key === 'user'">
                        {{ record.user?.full_name }}
                    </template>
                    <template v-if="column.key === 'dateCancelled'">
                        {{ record.cancelledStoreGcRequest?.csgr_at }}
                    </template>
                    <template v-if="column.key === 'cancelledBy'">
                        {{ record.cancelledStoreGcRequest.user.full_name }}
                    </template>

                    <template v-if="column.key === 'action'">
                        <a-space>
                            <a-button
                                type="primary"
                                size="small"
                                ghost
                                @click="viewRecord(record.sgc_id)"
                            >
                                <template #icon>
                                    <AuditOutlined />
                                </template>
                                View
                            </a-button>
                        </a-space>
                    </template>
                </template>
            </a-table>

            <pagination-resource class="mt-5" :datarecords="data" />
        </a-card>

        <a-modal
            v-model:open="showModal"
            style="width: 1000px"
            centered
            :footer="null"
        >
            <a-descriptions bordered title="More Details">
                <a-descriptions-item label="Request #">{{
                    moreDetails.info?.sgc_num
                }}</a-descriptions-item>
                <a-descriptions-item label="Date Requested">{{
                    moreDetails.info?.sgc_date_request
                }}</a-descriptions-item>
                <a-descriptions-item label="Retail Store">{{
                    moreDetails.info?.store.store_name
                }}</a-descriptions-item>
                <a-descriptions-item label="Date Needed">{{
                    moreDetails.info?.sgc_date_needed
                }}</a-descriptions-item>
                <a-descriptions-item label="Requested Remarks">{{
                    moreDetails.info?.sgc_remarks
                }}</a-descriptions-item>
                <a-descriptions-item label="Requested Prepared By">{{
                    moreDetails.info?.user.full_name
                }}</a-descriptions-item>
                <a-descriptions-item label="Date Cancelled">{{
                    moreDetails.info?.cancelledStoreGcRequest.csgr_at
                }}</a-descriptions-item>
                <a-descriptions-item label="Cancelled By">{{
                    moreDetails.info?.cancelledStoreGcRequest?.user.full_name
                }}</a-descriptions-item>
            </a-descriptions>
            <a-table
                class="mt-10"
                bordered
                size="small"
                :pagination="false"
                :columns="[
                    {
                        title: 'Denomination',
                        key: 'denom',
                    },
                    {
                        title: 'Quantity',
                        dataIndex: 'sri_items_quantity',
                    },
                ]"
                :data-source="moreDetails?.denomination.data"
            >
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key == 'denom'">
                        ₱{{ record.denomination }}
                    </template>
                </template>
                <template #summary>
                    <a-table-summary-row>
                        <a-table-summary-cell>Total</a-table-summary-cell>
                        <a-table-summary-cell>
                            <a-typography-text type="danger">{{
                                moreDetails?.total
                            }}</a-typography-text>
                        </a-table-summary-cell>
                    </a-table-summary-row>
                </template>
            </a-table>
            <pagination-axios
                :datarecords="moreDetails?.denomination"
                @on-pagination="paginationFun"
            />
        </a-modal>
    </AuthenticatedLayout>
</template>

<script lang="ts" setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import debounce from "lodash/debounce";
import pickBy from "lodash/pickBy";
import axios from "axios";
import { highlighten } from "@/Mixin/UiUtilities";
import { CancelledSpecialMoreDetails, ColumnTypes, FilterTypes, StoreGcRequest } from "@/types/treasury";
import { computed, ref, watch } from "vue";
import { router } from "@inertiajs/core";
import { route } from 'ziggy-js';

const { highlightText } = highlighten();

const props = defineProps<{
    title: string;
    data: { data: StoreGcRequest[] };
    columns: ColumnTypes[];
    filters: FilterTypes;
}>();

const showModal = ref(false);

const moreDetails = ref<CancelledSpecialMoreDetails | null>();
const total = ref(null);
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

// const totals = computed(() => {
//     let totalBorrow = 0;
//     moreDetails.value.denomination.data.forEach(({ denomination, qty }) => {
//         const floatAmount = denomination.denomination * qty;
//         totalBorrow += floatAmount;
//     });
//     //format with sign
//     return new Intl.NumberFormat("en-PH", {
//         style: "currency",
//         currency: "PHP",
//     }).format(totalBorrow);
// });

const viewRecord = async (id) => {
    const { data } = await axios.get(
        route("treasury.special.gc.viewCancelledRequest", id),
    );
    moreDetails.value = data;
    total.value = data.denomination;
    showModal.value = true;
};

const paginationFun = async (link) => {
    if (link.url) {
        const { data } = await axios.get(link.url);
        moreDetails.value.denomination = data.denomination;
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
