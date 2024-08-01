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
        <a-table
            :data-source="data.data"
            :columns="columns"
            :loading="onLoading"
            bordered
            size="small"
            :pagination="false"
        >
            <template #title>
                <a-typography-title :level="4">{{ title }}</a-typography-title>
            </template>
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex">
                    <span
                        v-html="
                            highlightText(record[column.dataIndex], form.search)
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

                <template v-if="column.dataIndex === 'reprint'">
                    <a-button
                        type="primary"
                        size="small"
                        @click="reprint(record.agcr_request_relnum)"
                    >
                        <template #icon>
                            <FileSearchOutlined />
                        </template>
                        Reprint
                    </a-button>
                </template>
                <template v-if="column.dataIndex === 'action'">
                    <a-button
                        type="primary"
                        size="small"
                        @click="viewHandler(record.sgc_id)"
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

        <a-modal
            v-model:open="openModal"
            width="1050px"
            :footer="false"
            centered
        >
            <a-row :gutter="[16, 0]" class="mt-8">
                <a-col :span="10">
                    <a-card title="Cancelled GC Request Details">
                        <a-form
                            layout="horizontal"
                            style="max-width: 600px; padding-top: 10px"
                        >
                            <a-form-item label="Request No.:">
                                <a-input
                                    :value="cancelledRecord.details.sgc_num"
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Date Requested:">
                                <a-input
                                    :value="
                                        cancelledRecord.details.sgc_date_request
                                    "
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Retail Store:">
                                <a-input
                                    :value="
                                        cancelledRecord.details.store.store_name
                                    "
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Date Needed:">
                                <a-input
                                    :value="
                                        cancelledRecord.details.sgc_date_needed
                                    "
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Request Remarks:">
                                <a-textarea
                                    :value="cancelledRecord.details.sgc_remarks"
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item
                                label="Document:"
                                v-if="cancelledRecord.details.sgc_file_docno"
                            >
                                <a-input
                                    :value="
                                        cancelledRecord.details.sgc_file_docno
                                    "
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Request Prepared by:">
                                <a-input
                                    :value="
                                        cancelledRecord.details.user.full_name
                                    "
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Date Cancelled:">
                                <a-input
                                    :value="
                                        cancelledRecord.details
                                            .cancelledStoreGcRequest.csgr_at
                                    "
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Cancelled by:">
                                <a-input
                                    :value="
                                        cancelledRecord.details
                                            .cancelledStoreGcRequest.user
                                            .full_name
                                    "
                                    readonly
                                />
                            </a-form-item>
                        </a-form>
                    </a-card>
                </a-col>
                <a-col :span="14">
                    <a-card>
                        <a-table
                            bordered
                            :dataSource="cancelledRecord.denomination"
                            :pagination="false"
                            size="small"
                            :columns="[
                                {
                                    title: 'Denomination',
                                    dataIndex: 'denomination',
                                },
                                {
                                    title: 'Quantity',
                                    dataIndex: 'sri_items_quantity',
                                    width: '100px',
                                },
                                {
                                    title: 'Total',
                                    dataIndex: 'total',
                                    width: '100px',
                                },
                            ]"
                        >
                        </a-table>

                        <a-form-item
                            label="Total:"
                            style="text-align: end; margin-top: 15px"
                        >
                            <a-input
                                :value="cancelledRecord.total"
                                style="width: 100px; text-align: end"
                                readonly
                            />
                        </a-form-item>
                    </a-card>
                </a-col>
            </a-row>
        </a-modal>
    </a-card>
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import debounce from "lodash/debounce";
import pickBy from "lodash/pickBy";
import _ from "lodash";

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
            openModal: false,
            onLoading: false,
            form: {
                search: this.filters.search,
                date: this.filters.date
                    ? [dayjs(this.filters.date[0]), dayjs(this.filters.date[1])]
                    : [],
            },
            cancelledRecord: {},
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
        reprint(id) {
            const url = route("treasury.store.gc.reprint", { id: id });

            axios
                .get(url, { responseType: "blob" })
                .then((response) => {
                    const file = new Blob([response.data], {
                        type: "application/pdf",
                    });
                    const fileURL = URL.createObjectURL(file);
                    window.open(fileURL, "_blank");
                })
                .catch((error) => {
                    if (error.response && error.response.status === 404) {
                        alert("Pdf Not available");
                    } else {
                        console.error(error);
                        alert("An error occurred while generating the PDF.");
                    }
                });
        },
        async viewHandler(id) {
            this.onLoading = true;
            try {
                const { data } = await axios.get(
                    route("treasury.store.gc.cancelled.gc", id)
                );
                this.cancelledRecord = data;
                this.openModal = true;
            } finally {
                this.onLoading = false;
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
