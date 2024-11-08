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
            <a-card
                title="Approved Production Details"
                style="text-align: center"
            >
                <a-form layout="horizontal" style="min-width: 600px">
                    <a-row :gutter="[16, 0]">
                        <a-col :span="12"
                            ><FormItem
                                label="PR No.:"
                                :value="record.productionRequest.pe_num"
                            />
                            <a-flex align="center" class="space-x-10">
                                <FormItem
                                    label="Date Requested:"
                                    :value="
                                        record.productionRequest.pe_date_request
                                    "
                                />
                                <FormItem
                                    label="Time Requested:"
                                    :value="
                                        record.productionRequest
                                            .pe_date_request_time
                                    "
                                />
                            </a-flex>
                            <a-flex align="center" class="space-x-10">
                                <FormItem
                                    label="Date Needed:"
                                    :value="
                                        record.productionRequest.pe_date_needed
                                    "
                                />
                                <FormItem
                                    label="Request Prepared by:"
                                    :value="
                                        record.productionRequest.user.full_name
                                    "
                                />
                            </a-flex>
                            <a-flex>
                                <a-form-item
                                    label="Request Document:"
                                    v-if="
                                        record.productionRequest.pe_file_docno
                                    "
                                >
                                    <a-button
                                        type="primary"
                                        ghost
                                        @click="
                                            download(
                                                record.productionRequest
                                                    .pe_file_docno
                                            )
                                        "
                                    >
                                        <template #icon>
                                            <DownloadOutlined />
                                        </template>
                                        Download</a-button
                                    >
                                </a-form-item>
                            </a-flex>

                            <FormItem
                                v-if="record.productionRequest.pe_type == 2"
                                label="Promo Group:"
                                :value="record.productionRequest.pe_group"
                            />
                        </a-col>
                        <a-col :span="12">
                            <a-flex align="center" class="space-x-10">
                                <FormItem
                                    label="Date Approved:"
                                    :value="
                                        record.productionRequest
                                            .approvedProductionRequest
                                            .ape_approved_at
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
                            </a-flex>
                            <a-flex align="center" class="space-x-10">
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
                            </a-flex>
                            <a-form-item label="Request Remarks:">
                                <a-textarea
                                    :value="record.productionRequest.pe_remarks"
                                    readonly
                                />
                            </a-form-item>

                            <a-button
                                type="primary"
                                class="float-right"
                                @click="
                                    reprint(record.productionRequest.pe_num)
                                "
                            >
                                <template #icon>
                                    <FileSyncOutlined />
                                </template>
                                Reprint this Request</a-button
                            >
                        </a-col>
                    </a-row>

                    <FormItem
                        class="mt-8"
                        v-if="!record.productionRequest.pe_generate_code"
                        label="Total Gc:"
                        :value="record.items.total"
                    />
                </a-form>
            </a-card>

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
                            v-if="record.productionRequest.pe_generate_code"
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
                                record.totalProductionRequest.denomination
                            "
                            :pagination="false"
                            size="small"
                            :columns="approvedRequestColumnsElse"
                            v-if="record.productionRequest.pe_generate_code"
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
                                    :data-source="
                                        barcodeGenerated.gcForValidation
                                    "
                                    :columns="[
                                        {
                                            title: 'GC Barcode #',
                                            dataIndex: 'barcode_no',
                                        },
                                        {
                                            title: 'denomination',
                                            key: 'denom',
                                        },
                                    ]"
                                >
                                    <template #bodyCell="{ column, record }">
                                        <template v-if="column.key == 'denom'">
                                            {{
                                                record.denomination.denomination
                                            }}
                                        </template>
                                    </template>
                                </a-table>
                            </a-tab-pane>
                            <a-tab-pane key="2" tab="Validated GC">
                                <a-table
                                    bordered
                                    :loading="onLoading"
                                    :dataSource="barcodeGenerated.gcValidated"
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
                                    <template #bodyCell="{ column, record }">
                                        <template
                                            v-if="
                                                column.key == 'date_validated'
                                            "
                                        >
                                            {{
                                                record.custodian_srr_items
                                                    ?.custodia_ssr
                                                    ?.csrr_datetime
                                            }}
                                        </template>
                                        <template
                                            v-if="column.key == 'denomination'"
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
                                            requisition.production_request
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
                                        :value="requisition.requis_checked"
                                    />
                                    <FormItem
                                        label="Approved By:"
                                        :value="requisition.requis_approved"
                                    />
                                    <FormItem
                                        label="Prepared By:"
                                        :value="requisition.user?.full_name"
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
                                            requisition.supplier?.gcs_address
                                        "
                                    />
                                </a-col>
                            </a-row>
                        </a-card>
                    </a-tab-pane>
                </a-tabs>
            </a-card>
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
import { notification } from "ant-design-vue";
import { message } from "ant-design-vue";

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
        download(file) {
            const url = route("treasury.production.request.download.document", {
                file: file,
            });
            axios
                .get(url, { responseType: "blob" })
                .then((response) => {
                    location.href = url;
                })
                .catch((error) => {
                    if (error.response && error.response.status === 404) {
                        notification.error({
                            message: "File not Found",
                            description:
                                "The file is missing on our server",
                        });
                    } else {
                        message.error("Error downloading the file");
                    }
                });
        },
        getValue(record, dataIndex) {
            return dataIndex.reduce((acc, index) => acc[index], record);
        },
        reprint(prNo) {
            const url = route("treasury.production.request.reprint", {
                id: prNo,
            });

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
                        notification.error({
                            message: "Pdf not Found",
                            description:
                                "Pdf Not available in Marketing.",
                        });
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
                    console.log(data);
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
