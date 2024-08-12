<template>
    <div class="mb-4">
        <a-card>
            <p>Pending Production Request</p>
        </a-card>
    </div>
    <div>
        <a-card>
            <a-table :dataSource="data" :columns="columns" :pagination="false">
                <template #bodyCell="props">
                    <div v-if="props.column.key === 'View'">
                        <a-button @click="handleViewClick(props.record)" type="primary">
                            <PicRightOutlined />View Form
                        </a-button>
                    </div>
                </template>
            </a-table>
        </a-card>
    </div>

    <a-modal v-model:open="open" width="95%" style="top: 65px;">
        <a-row :gutter="[16, 16]">
            <a-col :span="12">
                <a-card title="Production Request Approval Form">
                    <a-form-item label="Request Status:">
                        <a-select v-model:value="form.status" placeholder="Select an option">
                            <a-select-option value="1">Approved</a-select-option>
                            <a-select-option value="3">Cancelled</a-select-option>
                        </a-select>
                    </a-form-item>
                    <div v-if="form.status == '1'">
                        <a-form-item label="Date Approved">
                            <a-input v-model:value="form.dateApproved" readonly />
                        </a-form-item>
                        <a-form-item label="Remarks:" name="remarks">
                            <a-textarea v-model:value="form.InputRemarks" />
                        </a-form-item>
                        <a-form-item label="Checked By:">
                            <a-select v-model:value="form.checkedBy" placeholder="Select an option">
                                <a-select-option v-for="item in checkedBy" :key="item.assig_name"
                                    :value="item.assig_name">
                                    {{ item.assig_name }}
                                </a-select-option>
                            </a-select>
                        </a-form-item>
                        <a-form-item label="Approved By:">
                            <a-select v-model:value="form.approvedBy" placeholder="Select an option">
                                <a-select-option v-for="item in checkedBy" :key="item.assig_name"
                                    :value="item.assig_name">
                                    {{ item.assig_name }}
                                </a-select-option>
                            </a-select>
                        </a-form-item>
                        <a-form-item label="Prepared By">
                            <a-input v-model:value="form.preparedBy" readonly />
                        </a-form-item>
                    </div>
                    <div v-if="form.status == '3'">
                        <a-form-item label="Date Cancelled">
                            <a-input v-model:value="form.dateApproved" readonly />
                        </a-form-item>
                        <a-form-item label="Cancelled By">
                            <a-input v-model:value="form.preparedBy" readonly />
                        </a-form-item>
                    </div>
                </a-card>
            </a-col>
            <a-col :span="12">
                <a-card title="Production Request Details">
                    <a-form>
                        <a-form-item label="PE no">
                            <a-input v-model:value="form.pe_no" readonly />
                        </a-form-item>
                        <a-form-item label="Department">
                            <a-input v-model:value="form.department" readonly />
                        </a-form-item>
                        <a-form-item label="Date Requested">
                            <a-input v-model:value="form.dateRequested" readonly />
                        </a-form-item>
                        <a-form-item label="Date Needed">
                            <a-input v-model:value="form.dateNeeded" readonly />
                        </a-form-item>
                        <a-form-item label="Remarks">
                            <a-input v-model:value="form.remarks" readonly />
                        </a-form-item>
                        <a-form-item label="Requested by">
                            <a-input v-model:value="form.requestedBy" readonly />
                        </a-form-item>
                    </a-form>
                    <a-table :dataSource="barcodes" :columns="barcodeColumns" :pagination="false" />
                    <div class="flex justify-end mt-3">
                        <h3>Total: {{ form.total }} </h3>
                    </div>
                </a-card>
            </a-col>
        </a-row>
        <template #footer>
            <a-button type="default" @click="closeModal">Cancel</a-button>
            <a-button type="primary" @click="submitReqForm" :disabled="isSubmitDisabled">
                Submit
            </a-button>
        </template>
    </a-modal>

</template>

<script>
import Authenticatedlayout from "@/Layouts/AuthenticatedLayout.vue";
import { PicRightOutlined } from '@ant-design/icons-vue';
import dayjs from "dayjs";

export default {
    layout: Authenticatedlayout,
    props: {
        data: Object,
        columns: Object,
        barcodes: Object,
        barcodeColumns: Object,
        checkedBy: Object,
    },
    data() {
        return {
            open: false,
            form: {
                pe_no: '',
                department: '',
                dateRequested: '',
                dateNeeded: '',
                InputRemarks: '',
                remarks: '',
                requestedBy: '',
                requestedById: '',
                total: '',
                status: '1',
                checkedBy: '',
                approvedBy: '',
                preparedById: this.$page.props.auth.user.user_id,
                preparedBy: this.$page.props.auth.user.full_name,
                dateApproved: dayjs(),
                dateCancelled: dayjs(),
            }
        }
    },
    methods: {
        handleViewClick(record) {
            this.selectedRow(record);
        },
        selectedRow(data) {
            this.open = true;
            this.form.pe_no = data.pe_num;
            this.form.department = data.title;
            this.form.dateRequested = data.dateReq;
            this.form.dateNeeded = data.dateneed;
            this.form.remarks = data.pe_remarks;
            this.form.requestedBy = data.requestedBy;
            this.form.requestedById = data.pe_requested_by;
            this.form.total = data.total;
            this.form.dateneed = data.dateneed;
            this.$inertia.get(route('marketing.pendingRequest.pending.request'), {
                id: data.pe_id
            }, {
                preserveState: true
            })
        },
        submitReqForm() {
            this.inertia.post(route('marketing.pendingRequest.submit.request'))
        },
        closeModal() {
            this.open = false;
        },

    }
}
</script>
