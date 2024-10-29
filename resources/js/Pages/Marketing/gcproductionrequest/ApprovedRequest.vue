<template>
    <a-card title="Approved Production Request">
        <div class="flex justify-end">
            <a-input-search
                class="w-96 mb-5"
                v-model:value="search"
                placeholder="Enter PR Number"
                enter-button
                @search="onSearch"
            />
        </div>

        <a-table :dataSource="data.data" :columns="columns" :pagination="false">
            <template v-slot:bodyCell="{ column, record }">
                <template v-if="column.dataIndex === 'View'">
                    <a-button
                        type="primary"
                        @click="getSelectedData(record.pe_id)"
                    >
                        <PicLeftOutlined />View
                    </a-button>
                </template>
            </template>
        </a-table>
        <pagination class="mt-5" :datarecords="data" />
    </a-card>

    <a-modal
        v-model:open="open"
        title="Production Details"
        width="95%"
        style="top: 65px"
    >
        <a-row :gutter="[16, 16]">
            <a-col :span="12">
                <a-card>
                    <a-form>
                        <a-form-item label="PR No">
                            <a-input :value="selectedData[0].pe_num" readonly />
                        </a-form-item>
                        <a-form-item label="Date Requested">
                            <a-input
                                :value="selectedData[0].DateRequested"
                                readonly
                            />
                        </a-form-item>
                        <a-form-item label="Request Remarks">
                            <a-input
                                :value="selectedData[0].pe_remarks"
                                readonly
                            />
                        </a-form-item>
                        <a-form-item label="Request Prepared by">
                            <a-input
                                :value="selectedData[0].RequestPreparedby"
                                readonly
                            />
                        </a-form-item>
                    </a-form>
                </a-card>
            </a-col>
            <a-col :span="12">
                <a-card>
                    <a-form>
                        <a-form-item label="Date Approved">
                            <a-input
                                :value="selectedData[0].DateApproved"
                                readonly
                            ></a-input>
                        </a-form-item>
                        <a-form-item label="Approved Remarks">
                            <a-input
                                :value="selectedData[0].ape_remarks"
                                readonly
                            ></a-input>
                        </a-form-item>
                        <a-form-item label="Approved by">
                            <a-input
                                :value="
                                    selectedData[0].approvedBy
                                        ? selectedData[0].approvedBy
                                        : ''
                                "
                                readonly
                            ></a-input>
                        </a-form-item>
                        <a-form-item label="Checked by">
                            <a-input
                                :value="
                                    selectedData[0].checkby
                                        ? selectedData[0].checkby
                                        : selectedData[0].ape_checked_by
                                "
                                readonly
                            ></a-input>
                        </a-form-item>
                    </a-form>
                </a-card>
            </a-col>
        </a-row>
        <a-card class="mt-5">
            <a-table
                :dataSource="barcodes"
                :columns="barcodeColumns"
                :pagination="false"
            />
        </a-card>
        <div>
            <a-button
                type="primary"
                class="m-5"
                @click="getRequisition(selectedData[0].pe_id)"
                >Requisition Created</a-button
            >
        </div>
        <a-modal
            v-model:open="requisitionmodal"
            style="top: 20px"
            @ok="closeRequisitionModal"
            width="60%"
        >
            <a-card title="Requisition Details">
                <a-row :gutter="[16, 16]">
                    <a-col :span="12">
                        <a-card>
                            <a-form-item label="Request No">
                                <a-input
                                    :value="requisitiondata.requis_erno"
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Date Request">
                                <a-input
                                    :value="requisitiondata.requis_req"
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Location">
                                <a-input
                                    :value="requisitiondata.requis_loc"
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Department">
                                <a-input
                                    :value="requisitiondata.requis_dept"
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Remarks">
                                <a-input
                                    :value="requisitiondata.requis_rem"
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Checked By">
                                <a-input
                                    :value="requisitiondata.requis_checked"
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Approved By">
                                <a-input
                                    :value="
                                        requisitiondata.requis_approved
                                            ? requisitiondata.requis_approved
                                            : ''
                                    "
                                    readonly
                                />
                            </a-form-item>
                        </a-card>
                    </a-col>
                    <a-col :span="12">
                        <a-card title="Supplier Information">
                            <a-form-item label="Company Name">
                                <a-input
                                    :value="requisitiondata.gcs_companyname"
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Contact Person">
                                <a-input
                                    :value="requisitiondata.gcs_contactperson"
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Contact Number">
                                <a-input
                                    :value="requisitiondata.gcs_contactnumber"
                                    readonly
                                />
                            </a-form-item>
                            <a-form-item label="Company Address">
                                <a-input
                                    :value="requisitiondata.gcs_address"
                                    readonly
                                />
                            </a-form-item>
                        </a-card>
                    </a-col>
                </a-row>
                <a-table
                    class="mt-2"
                    bordered
                    :dataSource="barcodes"
                    :columns="barcodeColumns"
                    :pagination="false"
                />
            </a-card>
            <template #footer>
                <a-button
                    key="submit"
                    type="primary"
                    :loading="loading"
                    @click="reprint(requisitiondata.requis_erno)"
                    >Reprint</a-button
                >
            </template>
        </a-modal>
        <template #footer>
            <a-button key="back" @click="handleCancel">Close</a-button>
        </template>
    </a-modal>

    <a-modal v-model:open="reprintmodal" width="900px" style="top: 10px">
        <div v-if="nopdf != null">
            <h4>{{ nopdf }}</h4>
        </div>
        <div v-else>
            <iframe :src="stream" width="100%" height="1000px"></iframe>
        </div>
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
            reprintmodal: false,
            search: "",
            id: "",
            requisitiondata: {},
            requisitionmodal: false,
            stream: null,
            nopdf: null,
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
                    this.open = false;
                    this.requisitionmodal = true;
                    this.requisitiondata = response.data.r;
                });
        },

        closeRequisitionModal() {
            this.requisitionmodal = false;
        },
        reprint(id) {
            axios
                .get(route("marketing.requisition.reprint"), {
                    params: {
                        id: id,
                    },
                })
                .then((response) => {
                    this.reprintmodal = true;
                    if (response.data.stream !== null) {
                        this.requisitionmodal = false;
                        this.stream = `data:application/pdf;base64,${response.data.stream}`;
                    } else {
                        this.nopdf = "No PDF Available";
                    }
                });
        },
    },
    watch: {
        reprintmodal(newValue) {
            if (!newValue) {
                this.$inertia.get(route('marketing.approvedRequest.approved.request'))
            }
        },
    },
};
</script>
