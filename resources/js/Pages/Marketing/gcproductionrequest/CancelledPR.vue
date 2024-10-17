<template>
    <div>
        <a-card title="Cancelled Production Request">
            <a-table
                :pagination="false"
                size="small"
                bordered
                :dataSource="data.data"
                :columns="columns"
            >
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'view'">
                        <a-button
                            block
                            type="primary"
                            @click="viewdetails(record.pe_id)"
                        >
                            <PicRightOutlined />Details
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="data" class="mt-5" />
        </a-card>
    </div>

    <a-modal
    width="800px"
        v-model:open="open"
        title="Cancelled Production Request Details"
        @ok="onClose"
    >
        <a-row :gutter="[16, 16]">
            <a-col :span="12">
                <a-card title="Request Details">
                    <a-form-item label="Production Request Number">
                        <a-input :value="selectedData.pe_num" readonly />
                    </a-form-item>
                    <a-form-item label="Requested By">
                        <a-input :value="selectedData.requestedBy" readonly />
                    </a-form-item>
                    <a-form-item label="Date Requested">
                        <a-input :value="selectedData.Daterequested" readonly />
                    </a-form-item>
                    <a-form-item label="Request Remarks">
                        <a-textarea :value="selectedData.pe_remarks" readonly />
                    </a-form-item>
                </a-card>
            </a-col>
            <a-col :span="12">
                <a-card title="Cancellation Details">
                    <a-form-item label="Cancelled By">
                        <a-input :value="selectedData.cancelledBy" readonly />
                    </a-form-item>
                    <a-form-item label="Date Cancelled">
                        <a-input :value="selectedData.dateCancelled" readonly />
                    </a-form-item>
                    <a-form-item label="Cancellation Remarks">
                        <a-textarea :value="selectedData.remarks" readonly />
                    </a-form-item>
                </a-card>
            </a-col>
        </a-row>
    </a-modal>
</template>

<script>
import Pagination from "@/Components/Pagination.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import axios from "axios";
export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
    },
    data() {
        return {
            open: false,
            selectedData: [],
            columns: [
                {
                    title: "PR No",
                    dataIndex: "pe_num",
                    width: "5%",
                },
                {
                    title: "Date Request",
                    dataIndex: "Daterequested",
                    width: "16%",
                },
                {
                    title: "Requested By",
                    dataIndex: "requestedBy",
                    width: "16%",
                },
                {
                    title: "Date Cancelled",
                    dataIndex: "dateCancelled",
                    width: "16%",
                },
                {
                    title: "Cancelled By",
                    dataIndex: "cancelledBy",
                    width: "16%",
                },
                {
                    title: "View",
                    dataIndex: "view",
                    width: "10%",
                    align: "center",
                },
            ],
        };
    },
    methods: {
        viewdetails(e) {
            axios
                .get(route("marketing.cancelled.view.cancelled.request"), {
                    params: {
                        id: e,
                    },
                })
                .then((response) => {
                    this.open = true;
                    this.selectedData = response.data.response[0];
                });
        },
        onClose() {
            this.open = false;
        },
    },
};
</script>
