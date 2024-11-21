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
                            highlightText(record[column.dataIndex], form.search)
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
            <a-descriptions-item label="Request #"
                >{{ moreDetails.info?.sgc_num }}</a-descriptions-item
            >
            <a-descriptions-item label="Date Requested">{{ moreDetails.info?.sgc_date_request }}</a-descriptions-item>
            <a-descriptions-item label="Retail Store">{{ moreDetails.info?.store.store_name }}</a-descriptions-item>
            <a-descriptions-item label="Date Needed">{{ moreDetails.info?.sgc_date_needed }}</a-descriptions-item>
            <a-descriptions-item label="Requested Remarks">{{ moreDetails.info?.sgc_remarks }}</a-descriptions-item>
            <a-descriptions-item label="Requested Prepared By">{{ moreDetails.info?.user.full_name }}</a-descriptions-item>
            <a-descriptions-item label="Date Cancelled">{{ moreDetails.info?.cancelledStoreGcRequest.csgr_at }}</a-descriptions-item>
            <a-descriptions-item label="Cancelled By">{{ moreDetails.info?.cancelledStoreGcRequest?.user.full_name }}</a-descriptions-item>
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
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import debounce from "lodash/debounce";
import pickBy from "lodash/pickBy";
import _ from "lodash";
import axios from "axios";

export default {
    layout: AuthenticatedLayout,
    props: {
        title: String,
        data: Object,
        columns: Array,
        filters: Object,
    },
    data() {
        return {
            descriptionRecord: [],
            showModal: false,
            moreDetails: {},
            total: null,
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
        totals() {
            let totalBorrow = 0;
            this.moreDetails.denomination.data.forEach(
                ({ denomination, qty }) => {
                    const floatAmount = denomination.denomination * qty;
                    totalBorrow += floatAmount;
                }
            );
            //format with sign
            return new Intl.NumberFormat("en-PH", {
                style: "currency",
                currency: "PHP",
            }).format(totalBorrow);
        },
    },
    methods: {
        async viewRecord(id) {
            const { data } = await axios.get(
                route("treasury.special.gc.viewCancelledRequest", id)
            );
            this.moreDetails = data;
            this.total = data.denomination;
            this.showModal = true;
        },
        async paginationFun(link) {
            if (link.url) {
                const { data } = await axios.get(link.url);
                this.moreDetails.denomination = data.denomination;
            }
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
