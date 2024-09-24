<template>
    <!-- <Head :title="title" /> -->
    <a-breadcrumb style="margin: 15px 0">
        <a-breadcrumb-item>
            <Link :href="route('treasury.dashboard')">Home</Link>
        </a-breadcrumb-item>
        <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
    </a-breadcrumb>
    <a-card>
        <div class="flex justify-between mb-5">
            <div>
                <p>{{ date }}</p>
                <a-range-picker
                    v-model:value="dateRange"
                    @change="handleChangeDateRange"
                />
            </div>
            <div>
                <a-input-search
                    class="mr-1"
                    v-model:value="value"
                    placeholder="Search here..."
                    style="width: 300px"
                    @search="onSearch"
                />
                <a-button type="primary">
                    <template #icon>
                        <FileExcelOutlined />
                    </template>
                    Generate Excel Report
                </a-button>
            </div>
        </div>
        <a-table
            :data-source="data.data"
            :columns="columns"
            bordered
            size="small"
            :pagination="false"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'customer'">
                    {{ record.institutCustomer?.ins_name }}
                </template>
                <template v-if="column.key === 'totalDenom'">
                    {{ record.denomTotal }}
                </template>
                <template v-if="column.key === 'action'">
                    <a-space>
                        <a-button
                            v-if="record.institutr_paymenttype == 'ar'"
                            type="primary"
                            size="small"
                            @click="printAr(record.institutrId)"
                        >
                            <template #icon>
                                <FileSearchOutlined />
                            </template>
                            Print Ar
                        </a-button>
                        <a-button
                            v-else
                            type="primary"
                            size="small"
                            @click="viewRecord(record.institutrId)"
                        >
                            <template #icon>
                                <FileSearchOutlined />
                            </template>
                            View
                        </a-button>

                        <a-button
                            type="primary"
                            size="small"
                            ghost
                            @click="viewRecord(record)"
                        >
                            <template #icon>
                                <AuditOutlined />
                            </template>
                            Reprint
                        </a-button>
                        <a-button
                            type="primary"
                            size="small"
                            ghost
                            @click="viewRecord(record)"
                        >
                            <template #icon>
                                <AuditOutlined />
                            </template>
                            Excel
                        </a-button>
                    </a-space>
                </template>
            </template>
        </a-table>
        <pagination-resource class="mt-5" :datarecords="data" />
    </a-card>
    <a-modal v-model:open="viewModal" width="1000px">
        <a-descriptions
            title="Institution Gc Sales"
            layout="vertical"
            size="small"
            bordered
        >
            <a-descriptions-item label="Released #">{{
                viewRecordData.details?.institutrId
            }}</a-descriptions-item>
            <a-descriptions-item label="Customer">{{
                viewRecordData.details?.institutrTrnum == "338" ||
                viewRecordData.details?.institutrTrnum == "339"
                    ? "Island City Mall"
                    : viewRecordData.details?.institutCustomer.ins_name
            }}</a-descriptions-item>
            <a-descriptions-item label="Date Released">{{
                viewRecordData.details?.date
            }}</a-descriptions-item>
            <a-descriptions-item label="Received By">{{
                viewRecordData.details?.institutrReceivedby
            }}</a-descriptions-item>
            <a-descriptions-item label="Remarks" :span="2">{{
                viewRecordData.details?.institutrRemarks
            }}</a-descriptions-item>
            <a-descriptions-item label="Total Denomination">{{
                viewRecordData.details?.denomTotal
            }}</a-descriptions-item>
            <a-descriptions-item label="Documents Uploaded">{{
                viewRecordData.details?.document
            }}</a-descriptions-item>
            <a-descriptions-item label="Payment Type">{{
                viewRecordData.details?.institutr_paymenttype
            }}</a-descriptions-item>
        </a-descriptions>
        <a-table
            class="mt-10"
            :data-source="viewRecordData.denominationTable?.data"
            bordered
            :columns="[
                { title: 'GC Barcode #', dataIndex: 'instituttritems_barcode' },
                { title: 'Denomination', key: 'denomination' },
            ]"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'denomination'">
                    {{ record.gc.denomination.denomination_format }}
                </template>
            </template>
        </a-table>
    </a-modal>
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import { notification } from "ant-design-vue";
import axios from "axios";

export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        columns: Array,
        remainingBudget: Number,
        date: Array,
        title: String,
    },
    data() {
        return {
            viewModal: false,
            viewRecordData: {},
            dateRange: this.dateRange
                ? [dayjs(this.date[0] ?? null), dayjs(this.date[1] ?? null)]
                : null,
        };
    },
    methods: {
        printAr(id) {
            const url = route("treasury.transactions.institution.gc.sales.printAr", {
                id: id,
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
                            message: "File Not Found",
                            description: "Pdf is missing on the server!",
                        });
                    } else {
                        notification.error({
                            message: "Error Occured!",
                            description:
                                "An error occurred while generating the PDF.",
                        });
                    }
                });
        },

        async viewRecord(id) {
            const { data } = await axios.get(
                route(
                    "treasury.transactions.institution.gc.sales.transactionDetails",
                    id
                )
            );
            this.viewRecordData = data;
            this.viewModal = true;
        },
        handleChangeDateRange(_, dateRange) {
            this.$inertia.get(
                route("budget.ledger"),
                { date: dateRange },
                {
                    preserveState: true,
                }
            );
        },
    },
};
</script>
