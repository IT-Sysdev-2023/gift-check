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
                <template v-if="column.dataIndex === 'action'">
                    <a-button
                        type="primary"
                        size="small"
                        @click="viewHandler(record.pe_id)"
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
            width="1500px"
            :footer="false"
            centered
        >
            <a-row :gutter="[16, 0]" class="mt-8">
                <a-col :span="12">
                    <a-card
                        title="Approved Production Details"
                        style="width: 700px; text-align: center"
                    >
                        <a-form layout="horizontal" style="min-width: 600px">
                            <a-row :gutter="[16, 0]">
                                <a-col :span="12"
                                    ><FormItem
                                        label="PR No.:"
                                        :value="record.productionRequest.pe_num"
                                    />
                                    <FormItem
                                        label="Date Requested:"
                                        :value="
                                            record.productionRequest
                                                .pe_date_request
                                        "
                                    />
                                    <FormItem
                                        label="Time Requested:"
                                        :value="
                                            record.productionRequest
                                                .pe_date_request_time
                                        "
                                    />
                                    <FormItem
                                        label="Date Needed:"
                                        :value="
                                            record.productionRequest
                                                .pe_date_needed
                                        "
                                    />
                                    <FormItem
                                        v-if="
                                            record.productionRequest.pe_type ==
                                            2
                                        "
                                        label="Promo Group:"
                                        :value="
                                            record.productionRequest.pe_group
                                        "
                                    />
                                    <FormItem
                                        label="Request Remarks:"
                                        :value="
                                            record.productionRequest.pe_remarks
                                        "
                                    />
                                    <FormItem
                                        v-if="
                                            record.productionRequest
                                                .pe_file_docno
                                        "
                                        label="Request Document:"
                                        :value="
                                            record.productionRequest
                                                .pe_file_docno
                                        "
                                    />
                                    <FormItem
                                        label="Request Prepared by:"
                                        :value="
                                            record.productionRequest.user
                                                .full_name
                                        "
                                    />
                                </a-col>
                                <a-col :span="12">
                                    <FormItem
                                        label="Date Approved:"
                                        :value="
                                            record.productionRequest
                                                .approvedProductionRequest
                                                .ape_approved_at
                                        "
                                    />
                                    <FormItem
                                        label="Approved Remarks:"
                                        :value="
                                            record.productionRequest
                                                .approvedProductionRequest
                                                .ape_remarks
                                        "
                                    />
                                    <FormItem
                                        v-if="
                                            record.productionRequest
                                                ?.pe_file_docno
                                        "
                                        label="Approved Document:"
                                        :value="
                                            record.productionRequest
                                                .pe_file_docno
                                        "
                                    />
                                    <FormItem
                                        label="Approved by:"
                                        :value="
                                            record.productionRequest
                                                .approvedProductionRequest
                                                .ape_approved_by
                                        "
                                    />
                                    <FormItem
                                        label="Checked by:"
                                        :value="
                                            record.productionRequest
                                                .approvedProductionRequest
                                                .ape_checked_by
                                        "
                                    />
                                    <FormItem
                                        label="Prepared by:"
                                        :value="
                                            record.productionRequest
                                                .approvedProductionRequest.user
                                                .full_name
                                        "
                                    />
                                </a-col>
                            </a-row>
                            <a-button type="primary">
                                <template #icon>
                                    <DownloadOutlined />
                                </template>
                                Reprint this Request</a-button
                            >
                            <FormItem
                                class="mt-8"
                                v-if="
                                    !record.productionRequest.pe_generate_code
                                "
                                label="Total Gc:"
                                :value="record.items.total"
                            />
                        </a-form>
                    </a-card>
                </a-col>
                <a-col :span="12">
                    <a-card>
                        <a-tabs
                            v-model:activeKey="activeKey"
                            type="card"
                            @change="onTabChange"
                        >
                            <a-tab-pane key="1" tab="Denomination">
                                <a-table
                                    bordered
                                    :dataSource="record.items"
                                    :pagination="false"
                                    size="small"
                                    :columns="approvedRequestColumns"
                                    v-if="
                                        record.productionRequest
                                            .pe_generate_code
                                    "
                                >
                                    <template #bodyCell="{ column, record }">
                                        <template v-if="column.key === 'unit'">
                                            pc(s)
                                        </template>
                                    </template>
                                </a-table>

                                <a-table
                                    v-else
                                    bordered
                                    :dataSource="
                                        record.totalProductionRequest
                                            .denomination
                                    "
                                    :pagination="false"
                                    size="small"
                                    :columns="approvedRequestColumnsElse"
                                    v-if="
                                        record.productionRequest
                                            .pe_generate_code
                                    "
                                >
                                </a-table>
                                <a-form-item
                                    label="Total:"
                                    style="text-align: end; margin-top: 15px"
                                >
                                    <a-input
                                        :value="record.total"
                                        style="width: 100px; text-align: end"
                                        readonly
                                    />
                                </a-form-item>
                            </a-tab-pane>
                            <a-tab-pane
                                key="2"
                                tab="Barcode Generated"
                                v-if="record.productionRequest.pe_generate_code"
                            >
                                <a-tabs v-model:activeKey="activeKeyTab">
                                    <a-tab-pane key="1" tab="GC For Validation">
                                        <a-table
                                            bordered
                                            :loading="onLoading"
                                            :pagination="false"
                                            size="small"
                                            :columns="[{}]"
                                        >
                                        </a-table>
                                    </a-tab-pane>
                                    <a-tab-pane key="2" tab="Validated GC">
                                        <a-table
                                            bordered
                                            :loading="onLoading"
                                            :dataSource="
                                                barcodeGenerated.gcValidated
                                            "
                                            :pagination="false"
                                            size="small"
                                            :columns="[
                                                {
                                                    title: 'GC Barcode',
                                                    dataIndex: 'barcode_no',
                                                },
                                                {
                                                    title: 'Denomination',
                                                    key: 'denomination',
                                                },
                                                {
                                                    title: 'Date Validated',
                                                    key: 'date_validated',
                                                },
                                            ]"
                                        >
                                            <template
                                                #bodyCell="{ column, record }"
                                            >
                                                <template
                                                    v-if="
                                                        column.key ==
                                                        'date_validated'
                                                    "
                                                >
                                                    {{
                                                        record
                                                            .custodian_srr_items
                                                            ?.custodia_ssr
                                                            ?.csrr_datetime
                                                    }}
                                                </template>
                                                <template
                                                    v-if="
                                                        column.key ==
                                                        'denomination'
                                                    "
                                                >
                                                    {{
                                                        record.denomination
                                                            ?.denomination
                                                    }}
                                                </template>
                                            </template>
                                        </a-table>
                                    </a-tab-pane>
                                </a-tabs>
                            </a-tab-pane>
                            <a-tab-pane
                                key="3"
                                tab="Requisition Created"
                                v-if="record.productionRequest.pe_requisition"
                            >
                                <a-card :loading="onLoading">
                                    <a-row :gutter="[16, 0]">
                                        <a-col :span="12"
                                            ><FormItem
                                                label="Request No.:"
                                                :value="requisition.requis_erno"
                                            />
                                            <FormItem
                                                label="Date Requested:"
                                                :value="requisition.requis_req"
                                            />
                                            <FormItem
                                                label="Date Needed:"
                                                :value="
                                                    requisition
                                                        .production_request
                                                        ?.pe_date_needed
                                                "
                                            />
                                            <FormItem
                                                label="Location:"
                                                :value="requisition.requis_loc"
                                            />
                                            <FormItem
                                                label="Department:"
                                                :value="requisition.requis_dept"
                                            />
                                            <FormItem
                                                label="Remarks:"
                                                :value="requisition.requis_rem"
                                            />
                                            <FormItem
                                                label="Checked By:"
                                                :value="
                                                    requisition.requis_checked
                                                "
                                            />
                                            <FormItem
                                                label="Approved By:"
                                                :value="
                                                    requisition.requis_approved
                                                "
                                            />
                                            <FormItem
                                                label="Prepared By:"
                                                :value="
                                                    requisition.user?.full_name
                                                "
                                            />
                                        </a-col>
                                        <a-col :span="12">
                                            <span>Supplier Information</span>
                                            <FormItem
                                                label="Company Name:"
                                                :value="
                                                    requisition.supplier
                                                        ?.gcs_companyname
                                                "
                                            />
                                            <FormItem
                                                label="Contact Person:"
                                                :value="
                                                    requisition.supplier
                                                        ?.gcs_contactperson
                                                "
                                            />
                                            <FormItem
                                                label="Contact No:"
                                                :value="
                                                    requisition.supplier
                                                        ?.gcs_contactnumber
                                                "
                                            />
                                            <FormItem
                                                label="Company Address:"
                                                :value="
                                                    requisition.supplier
                                                        ?.gcs_address
                                                "
                                            />
                                        </a-col>
                                    </a-row>
                                </a-card>
                            </a-tab-pane>
                        </a-tabs>
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
import axios from "axios";
import { onLoading } from "@/Mixin/UiUtilities";

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
            activeKey: "1",
            activeKeyTab: "1",
            form: {
                search: this.filters.search,
                date: this.filters.date
                    ? [dayjs(this.filters.date[0]), dayjs(this.filters.date[1])]
                    : [],
            },
            record: {},
            requisition: {},
            barcodeGenerated: {},
        };
    },
    computed: {
        approvedRequestColumnsElse() {
            return [
                {
                    title: "Denomination",
                    dataIndex: "denomination",
                },
                {
                    title: "Quantity",
                    dataIndex: "pe_items_quantity",
                    width: "100px",
                },
            ];
        },
        approvedRequestColumns() {
            return [
                {
                    title: "Denomination",
                    dataIndex: "denomination",
                },
                {
                    title: "Quantity",
                    dataIndex: "pe_items_quantity",
                    width: "100px",
                },
                {
                    title: "Unit",
                    key: "unit",
                },
                {
                    title: "Barcode No. Start",
                    dataIndex: "barcode_start",
                },
                {
                    title: "Barcode No. End",
                    dataIndex: "barcode_end",
                },
                {
                    title: "Total",
                    dataIndex: "totalRow",
                    width: "100px",
                },
            ];
        },
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
        async onTabChange(e) {
            //Barcode Generated
            if (e === "2") {
                onLoading.value = true;
                try {
                    const { data } = await axios.get(
                        route(
                            "treasury.production.request.view.barcode",
                            this.record.productionRequest.pe_id
                        )
                    );
                    this.barcodeGenerated = data;
                } finally {
                    onLoading.value = false;
                }
                //Requisition Tab
            } else if (e === "3") {
                onLoading.value = true;
                try {
                    const { data } = await axios.get(
                        route(
                            "treasury.production.request.requisition",
                            this.record.productionRequest.pe_id
                        )
                    );
                    this.requisition = data;
                } finally {
                    onLoading.value = false;
                }
            }
        },
        async viewHandler(id) {
            onLoading.value = true;
            try {
                const { data } = await axios.get(
                    route("treasury.production.request.view.approved", id)
                );
                this.record = data;
                this.openModal = true;
            } finally {
                onLoading.value = false;
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
