<template>
    <div>
        <a-table
            bordered
            :pagination="false"
            :dataSource="data.data"
            :columns="columns"
        >
            <template v-slot:bodyCell="{ column, record }">
                <template v-if="column.dataIndex === 'view'">
                    <a-button
                        size="small"
                        type="primary"
                        @click="opendrawer(record.pgcreq_id)"
                    >
                        <PicLeftOutlined />View
                    </a-button>
                </template>
            </template>
        </a-table>
        <pagination :datarecords="data" class="mt-5" />
    </div>
    <a-modal v-model:open="open" @ok="onClose" width="1000px" style="top:10px">
        <template #extra>
            <a-button
                size="small"
                danger
                type="primary"
                style="margin-right: 8px"
                @click="onClose"
                >Close</a-button
            >
        </template>
        <a-tabs v-model:activeKey="activeKey">
            <a-tab-pane
                key="1"
                :tab="'Promo GC Request # ' + selectedData.pgcreq_reqnum"
            >
            <a-row :gutter="[16, 16]">
                        <a-col :span="12">
                            <a-card>
                                <a-form-item label="Date Requested">
                                    <a-input
                                        v-model:value="selectedData.dateRequest"
                                        readonly
                                    />
                                </a-form-item>
                                <a-form-item label="Date Needed">
                                    <a-input
                                        v-model:value="selectedData.dateNeeded"
                                        readonly
                                    />
                                </a-form-item>
                                <a-form-item label="Promo GC Group">
                                    <a-input
                                        v-model:value="
                                            selectedData.pgcreq_group
                                        "
                                        readonly
                                    />
                                </a-form-item>
                                <a-form-item label="Remarks">
                                    <a-input
                                        v-model:value="
                                            selectedData.pgcreq_remarks
                                        "
                                        readonly
                                    />
                                </a-form-item>
                                <a-form-item label="Requested By">
                                    <a-input
                                        v-model:value="selectedData.requestedBy"
                                        readonly
                                    />
                                </a-form-item>
                                <a-form-item label="Uploaded Document">
                                    <a-image
                                        style="height: 100px"
                                        :src="
                                            '/storage/promoGcUpload/' +
                                            selectedData.pgcreq_doc
                                        "
                                    ></a-image>
                                </a-form-item>
                            </a-card>
                        </a-col>
                        <a-col :span="12">
                            <a-card>
                                <a-table
                                    :pagination="false"
                                    :dataSource="barcodes"
                                    :columns="barcodeCol"
                                />
                                <div>
                                    <div class="flex justify-end mt-5">
                                        <a-form-item label="Total">
                                            <a-input
                                                v-model:value="
                                                    selectedData.pgcreq_total
                                                "
                                                readonly
                                            ></a-input>
                                        </a-form-item>
                                    </div>
                                </div>
                            </a-card>
                        </a-col>
                    </a-row>
            </a-tab-pane>
            <a-tab-pane key="2" tab="Recommendation Details" force-render>
                <a-card style="height: 455px;">
                    <a-row :gutter="[16, 16]">
                        <a-col :span="12">
                            <a-form-item label="Date Recommended">
                                <a-input
                                    v-model:value="
                                        selectedData.requestApprovedDate
                                    "
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Remarks">
                                <a-input
                                    v-model:value="selectedData.pgcreq_remarks"
                                    readonly
                                />
                            </a-form-item>
                        </a-col>
                        <a-col :span="12">
                            <a-form-item label="Time Recommended">
                                <a-input
                                    v-model:value="
                                        selectedData.requestApprovedTime
                                    "
                                    readonly
                                />
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-card>
            </a-tab-pane>
            <a-tab-pane key="3" tab="Approved Details">
                <a-row :gutter="[16, 16]">
                    <a-col :span="8">
                        <a-card style="height: 455px;">
                            <a-form-item label="Date Approved">
                                <a-input
                                    v-model:value="
                                        approvedDetails.requestApprovedDate
                                    "
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Remarks">
                                <a-input
                                    v-model:value="selectedData.reqap_remarks"
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Checked By">
                                <a-input
                                    v-model:value="
                                        approvedDetails.reqap_checkedby
                                    "
                                    readonly
                                />
                            </a-form-item>
                        </a-card>
                    </a-col>
                    <a-col :span="8">
                        <a-card>
                            <a-form-item label="Time Approved">
                                <a-input
                                    v-model:value="
                                        approvedDetails.requestApprovedTime
                                    "
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Document">
                                <div v-if="approvedDetails.reqap_doc == ''">
                                    None
                                </div>
                                <div v-else>
                                    <a-image></a-image>
                                </div>
                            </a-form-item>
                            <a-form-item label="Approved By">
                                <a-input
                                    v-model:value="
                                        approvedDetails.reqap_approvedby
                                    "
                                    readonly
                                />
                            </a-form-item>
                        </a-card>
                    </a-col>
                    <a-col :span="8">
                        <a-form-item label="Prepared By">
                            <a-input
                                v-model:value="approvedDetails.prepby"
                                readonly
                            />
                        </a-form-item>
                    </a-col>
                </a-row>
            </a-tab-pane>
        </a-tabs>
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
            activeKey: "1",
            selectedData: [],
            approvedDetails: [],
            barcodes: [],
            open: false,
            columns: [
                {
                    title: "RFPROM #",
                    dataIndex: "pgcreq_reqnum",
                },
                {
                    title: "Date Requested",
                    dataIndex: "dateRequested",
                },
                {
                    title: "Date Needed",
                    dataIndex: "dateNeeded",
                },
                {
                    title: "Total GC",
                    dataIndex: "pgcreq_total",
                },
                {
                    title: "Retail Group",
                    dataIndex: "pgcreq_group",
                },
                {
                    title: "Requested By",
                    dataIndex: "requestedBy",
                },
                {
                    title: "Recommended By",
                    dataIndex: "recommendedBy",
                },
                {
                    title: "Approved By",
                    dataIndex: "approvedBy",
                },
                {
                    title: "View",
                    dataIndex: "view",
                },
            ],
            barcodeCol: [
                {
                    title: "Denomination",
                    dataIndex: "denomination",
                },
                {
                    title: "Quantity",
                    dataIndex: "pgcreqi_qty",
                },
            ],
        };
    },
    methods: {
        opendrawer(data) {
            axios
                .get(route("marketing.promoGcRequest.selected.approved"), {
                    params: {
                        id: data,
                    },
                })
                .then((response) => {
                    console.log(response.data.barcodes);
                    this.selectedData = response.data.data[0];
                    this.approvedDetails = response.data.approvedRequests[0];
                    this.barcodes = response.data.barcodes;
                    this.open = true;
                });
        },
        onClose() {
            this.open = false;
        },
    },
};
</script>
