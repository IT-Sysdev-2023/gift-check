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
                            <a-select-option value="2">Cancelled</a-select-option>
                        </a-select>
                    </a-form-item>
                    <div v-if="form.status == '1'">
                        <a-form-item label="Date Approved">
                            <a-input v-model:value="form.dateApproved" readonly />
                        </a-form-item>
                        <a-form-item label="Approved By">
                            <a-input v-model:value="form.checkedByName" readonly />
                        </a-form-item>
                    </div>
                    <div v-if="form.status == '2'">
                        <a-form-item label="Date Cancelled">
                            <a-input v-model:value="form.dateApproved" readonly />
                        </a-form-item>
                        <a-form-item label="Cancelled By">
                            <a-input v-model:value="form.checkedByName" readonly />
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
                        <a-form-item label="Remarks">
                            <a-input v-model:value="form.remarks" readonly />
                        </a-form-item>
                        <a-form-item label="Prepared by">
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

    <a-modal v-model:open="openIframe" style="width: 70%; top: 50px" :footer="null">
        <iframe class="mt-7" :src="stream" width="100%" height="600px"></iframe>
    </a-modal>


</template>

<script>
import Authenticatedlayout from "@/Layouts/AuthenticatedLayout.vue";
import { PicRightOutlined } from '@ant-design/icons-vue';
import { notification } from 'ant-design-vue';
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
            stream: null,
            openIframe: false,
            open: false,
            form: {
                id: this.data[0]?.pe_id,
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
                checkedBy: this.$page.props.auth.user.user_id,
                checkedByName: this.$page.props.auth.user.full_name,
                dateApproved: dayjs().format('MMMM DD, YYYY'),
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
            this.$inertia.get(route('marketing.pendingRequest.checker.pending.request'), {
                id: data?.pe_id
            }, {
                preserveState: true
            })
        },
        submitReqForm() {
            this.$inertia.post(route('marketing.pendingRequest.submit.request'), {
                data: this.form,
                barcode: this.barcodes
            },
                {
                    onSuccess: (response) => {
                        notification[response.props.flash.type]({
                            message: response.props.flash.msg,
                            description: response.props.flash.description,
                        });
                        this.$inertia.get(route('marketing.dashboard'))
                    },
                }
            )
        },
        closeModal() {
            this.open = false;
        },
    }
}
</script>
