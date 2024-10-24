<template>
    <a-card title="Approved Production Request">
        <div class="flex justify-end">
            <a-input-search class="w-96 mb-5" v-model:value="search" placeholder="Enter PR Number" enter-button
                @search="onSearch" />
        </div>

        <a-table :dataSource="data.data" :columns="columns" :pagination="false">
            <template v-slot:bodyCell="{ column, record }">
                <template v-if="column.dataIndex === 'View'">
                    <a-button type="primary" @click="getSelectedData(record.pe_id)">
                        <PicLeftOutlined />View
                    </a-button>
                </template>
            </template>
        </a-table>
        <pagination class="mt-5" :datarecords="data" />
    </a-card>

    <a-modal v-model:open="open" title="Production Details" width="95%" style="top: 65px">
        <a-row :gutter="[16, 16]">
            <a-col :span="12">
                <a-card>
                    <a-form>
                        <a-form-item label="PR No">
                            <a-input v-model:value="selectedData.pe_num" readonly />
                        </a-form-item>
                        <a-form-item label="Date Requested">
                            <a-input v-model:value="selectedData.DateRequested" readonly />
                        </a-form-item>
                        <a-form-item label="Date Needed">
                            <a-input v-model:value="selectedData.DateNeeded" readonly />
                        </a-form-item>
                        <a-form-item label="Request Remarks">
                            <a-input v-model:value="selectedData.pe_remarks" readonly />
                        </a-form-item>
                        <a-form-item label="Request Prepared by">
                            <a-input v-model:value="selectedData.RequestPreparedby" readonly />
                        </a-form-item>
                    </a-form>
                </a-card>
            </a-col>
            <a-col :span="12">
                <a-card>
                    <a-form>
                        <a-form-item label="Date Approved">
                            <a-input v-model:value="selectedData.DateApproved"></a-input>
                        </a-form-item>
                        <a-form-item label="Approved Remarks">
                            <a-input v-model:value="selectedData.ape_remarks"></a-input>
                        </a-form-item>
                        <a-form-item label="Approved by">
                            <a-input v-model:value="selectedData.ape_approved_by"></a-input>
                        </a-form-item>
                        <a-form-item label="Checked by">
                            <a-input v-model:value="selectedData.ape_checked_by"></a-input>
                        </a-form-item>
                        <a-form-item label="Prepared by">
                            <a-input v-model:value="selectedData.aprrovedPreparedBy"></a-input>
                        </a-form-item>
                    </a-form>
                </a-card>
            </a-col>
        </a-row>
        <a-card class="mt-5">
            <a-table :dataSource="barcodes" :columns="barcodeColumns" :pagination="false" />
        </a-card>
        <div>
            <a-button type="primary" class="m-5">Barcode Generated</a-button>
            <a-button type="primary" class="m-5" @click="getRequisition(selectedData.pe_id)">Requisition
                Created</a-button>
        </div>
        <a-modal v-model:open="requisitionmodal" @ok="closeRequisitionModal" width="60%">
            <a-card title="Requisition Details">
                <a-row :gutter="[16, 16]">
                    <a-col :span="12">
                        <a-form-item label="Request No">
                            <a-input v-model:value="requisitiondata.requis_erno" readonly />
                        </a-form-item>
                        <a-form-item label="Date Request">
                            <a-input v-model:value="requisitiondata.requis_req" readonly />
                        </a-form-item>
                        <a-form-item label="Location">
                            <a-input v-model:value="requisitiondata.requis_loc" readonly />
                        </a-form-item>
                        <a-form-item label="Department">
                            <a-input v-model:value="requisitiondata.requis_dept" readonly />
                        </a-form-item>
                        <a-form-item label="Remarks">
                            <a-input v-model:value="requisitiondata.requis_rem" readonly />
                        </a-form-item>
                        <a-form-item label="Checked By">
                            <a-input v-model:value="requisitiondata.requis_checked" readonly />
                        </a-form-item>
                        <a-form-item label="Approved By">
                            <a-input v-model:value="requisitiondata.requis_approved" readonly />
                        </a-form-item>
                    </a-col>
                    <a-col :span="12">
                        <a-card title="Supplier Information">
                            <a-form-item label="Company Name">
                                <a-input v-model:value="requisitiondata.gcs_companyname
                                    " readonly />
                            </a-form-item>
                            <a-form-item label="Contact Person">
                                <a-input v-model:value="requisitiondata.gcs_contactperson
                                    " readonly />
                            </a-form-item>
                            <a-form-item label="Contact Number">
                                <a-input v-model:value="requisitiondata.gcs_contactnumber
                                    " readonly />
                            </a-form-item>
                            <a-form-item label="Company Address">
                                <a-input v-model:value="requisitiondata.gcs_address" readonly />
                            </a-form-item>
                        </a-card>
                    </a-col>
                </a-row>
            </a-card>
        </a-modal>
        <template #footer>
            <a-button key="back" @click="handleCancel">Close</a-button>
        </template>
    </a-modal>
</template>

<script>
import Pagination from "@/Components/Pagination.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { PicLeftOutlined } from "@ant-design/icons-vue";
export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        columns: Object,
        barcodeColumns: Object,
        barcodes: Object,
        selectedData: Object,
    },
    data() {
        return {
            open: false,
            search: "",
            id: "",
            requisitiondata: {},
            requisitionmodal: false,
        };
    },
    methods: {
        getSelectedData(data) {
            this.$inertia.get(
                route("marketing.approvedRequest.approved.request"),
                {
                    id: data,
                },
                {
                    onSuccess: () => {
                        this.open = true;
                    },
                    preserveState: true,
                }
            );
        },
        handleCancel() {
            this.open = false;
        },
        onSearch(data) {
            this.$inertia.get(
                route("marketing.approvedRequest.approved.request"),
                {
                    search: data,
                },
                {
                    preserveState: true,
                }
            );
        },
        getRequisition(id) {
            axios
                .get(route("marketing.approvedRequest.getrequisition"), {
                    params: {
                        id: id,
                    },
                })
                .then((response) => {
                    this.requisitionmodal = true;
                    this.requisitiondata = response.data.r;
                });
        },

        closeRequisitionModal() {
            this.requisitionmodal = false;
        },
    },
};
</script>
