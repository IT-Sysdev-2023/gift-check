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

        <a-table
            :data-source="records.data"
            :columns="columns"
            bordered
            size="small"
            :pagination="false"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'pr'">
                    {{ record.productionRequest.pe_num }}
                </template>
                <template v-if="column.key === 'dateRequested'">
                    {{ record.productionRequest.pe_date_request }}
                </template>
                <template v-if="column.key === 'dateNedded'">
                    {{ record.productionRequest.pe_date_needed }}
                </template>
                <template v-if="column.key === 'preparedBy'">
                    {{ record.productionRequest.user.full_name }}
                </template>
                <template v-if="column.key === 'cancelledBy'">
                    {{ record.user.full_name }}
                </template>

                <template v-if="column.key === 'action'">
                    <a-button
                        type="primary"
                        size="small"
                        @click="viewHandler(record.productionRequest.pe_id)"
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
        v-model:open="openModal"
        :title="'View ' + title"
        :footer="null"
        width="800px"
    >
        <a-descriptions title="User Info" bordered>
            <a-descriptions-item
                label="Pr No"
                :labelStyle="{ fontWeight: 'bold' }"
                >{{ viewCancelled.pe_num }}</a-descriptions-item
            >
            <a-descriptions-item
                label="Date & Time Requested"
                :labelStyle="{ fontWeight: 'bold' }"
                :span="2"
                >{{
                    dayjs(viewCancelled.pe_date_request).format(
                        "MMM DD, YYYY HH:mm:ss",
                    )
                }}</a-descriptions-item
            >
            <a-descriptions-item
                label="Date Needed"
                :labelStyle="{ fontWeight: 'bold' }"
                >{{ viewCancelled.pe_date_needed }}</a-descriptions-item
            >
            <a-descriptions-item
                label="Request Remarks"
                :span="2"
                :labelStyle="{ fontWeight: 'bold' }"
            >
                {{ viewCancelled.pe_remarks }}
            </a-descriptions-item>

            <a-descriptions-item
                label="Document"
                :labelStyle="{ fontWeight: 'bold' }"
                v-if="viewCancelled.pe_file_docno"
            >
                {{ viewCancelled.pe_file_docno }}
            </a-descriptions-item>

            <a-descriptions-item
                label="Request Prepared By"
                :labelStyle="{ fontWeight: 'bold' }"
                :span="4"
            >
                {{ viewCancelled.user.full_name }}
            </a-descriptions-item>
            <a-descriptions-item
                label="Date Cancelled"
                :labelStyle="{ fontWeight: 'bold' }"
            >
                {{
                    dayjs(
                        viewCancelled.cancelled_production_request.cpr_at,
                    ).format("MMM DD, YYYY HH:mm:ss")
                }}
            </a-descriptions-item>

            <a-descriptions-item
                label="Cancelled By"
                :labelStyle="{ fontWeight: 'bold' }"
            >
                {{ viewCancelled.cancelled_production_request.user.full_name }}
            </a-descriptions-item>
        </a-descriptions>
        <a-table
            bordered
            :columns="[
                {
                    title: 'Denomination',
                    key: 'denomination',
                },
                {
                    title: 'Barcode Start',
                    key: 'start',
                },
                {
                    title: 'Barcode End',
                    key: 'end',
                },
                {
                    title: 'Quantity',
                    key: 'qty',
                },
            ]"
            :data-source="barcodes.data"
            :pagination="false"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'denomination'">
                    {{ record.denomination.denomination_format }}
                </template>
                <template v-if="column.key === 'start'">
                    {{ record.barcodeStartEnd?.max_barcode_no ?? "null" }}
                </template>
                <template v-if="column.key === 'end'">
                    {{ record.barcodeStartEnd?.min_barcode_no ?? "null" }}
                </template>
                <template v-if="column.key === 'qty'">
                    {{ record.pe_items_quantity }}
                </template>
            </template>
            <template #summary>
                <a-table-summary-row>
                    <a-table-summary-cell>Total</a-table-summary-cell>
                    <a-table-summary-cell>
                        <a-typography-text type="danger">{{
                            totals
                        }}</a-typography-text>
                    </a-table-summary-cell>
                </a-table-summary-row>
            </template>
        </a-table>
    </a-modal>
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import debounce from "lodash/debounce";
import pickBy from "lodash/pickBy";
import axios from "axios";

export default {
    layout: AuthenticatedLayout,
    props: {
        title: String,
        records: Object,
        columns: Array,
        filters: Object,
    },
    data() {
        return {
            barcodes: {},
            viewCancelled: {},
            openModal: false,
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

            this.barcodes?.data.forEach(
                ({ denomination, pe_items_quantity }) => {
                    const floatAmount =
                        denomination.denomination * pe_items_quantity;
                    totalBorrow += floatAmount;
                },
            );

            //format with sign
            return new Intl.NumberFormat("en-PH", {
                style: "currency",
                currency: "PHP",
            }).format(totalBorrow);
        },
    },
    methods: {
        async viewHandler(id) {
            const { data } = await axios.get(
                route("treasury.production.request.viewCancelled", id),
            );
            this.viewCancelled = data.productionRequest;
            this.barcodes = data.barcodes;
            this.openModal = true;
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
                    },
                );
            }, 600),
        },
    },
};
</script>
