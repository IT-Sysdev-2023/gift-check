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
                        <a-form-item label="Remarks:" name="remarks">
                            <a-textarea v-model:value="form.InputRemarks" />
                        </a-form-item>
                        <a-form-item label="Prepared By">
                            <a-input v-model:value="form.preparedBy" readonly />
                        </a-form-item>
                        <a-form-item label="Approved By:">
                            <a-input v-model:value="form.approvedBy" readonly />
                        </a-form-item>
                        <a-form-item label="Reviewed By:">
                            <a-input v-model:value="form.reviewedBy" readonly />
                        </a-form-item>

                    </div>
                    <div v-if="form.status == '2'">
                        <a-form-item label="Cancel Remarks">
                            <a-textarea v-model:value="form.cancelremarks"/>
                        </a-form-item>
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
                        <a-form-item label="Remarks">
                            <a-input v-model:value="form.remarks" readonly />
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
import axios from "axios";
import dayjs from "dayjs";

export default {
    layout: Authenticatedlayout,
    props: {
        data: Object,
        columns: Object,
        barcodes: Object,
        barcodeColumns: Object,
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
                cancelremarks: '',
                total: '',
                status: '1',
                checkedBy: '',
                approvedBy: '',
                approvedById: '',
                preparedById: '',
                preparedBy: '',
                dateApproved: dayjs(),
                dateCancelled: dayjs(),
                reviewedBy: this.$page.props.auth.user.full_name,
                reviewedById: this.$page.props.auth.user.user_id,
            }
        }
    },
    methods: {
        handleViewClick(record) {
            this.selectedRow(record);
        },
        selectedRow(data) {
            axios.get(route('marketing.pendingRequest.getSigners'), {
                params: {
                    id: this.data[0]?.pe_id,
                }
            }).then((response) => {
                this.form.approvedBy = response.data.response.approvedBy.employee_name;
                this.form.approvedById = response.data.response.approvedById;
            });



            this.open = true;
            this.form.pe_no = data.pe_num;
            this.form.department = data.title;
            this.form.dateRequested = data.dateReq;
            this.form.dateNeeded = data.dateneed;
            this.form.remarks = data.pe_remarks;
            this.form.preparedBy = data.requestedBy;
            this.form.preparedById = data.pe_requested_by;
            this.form.total = data.total;
            this.form.dateneed = data.dateneed;
            this.$inertia.get(route('marketing.pendingRequest.pending.request'), {
                id: data?.pe_id
            }, {
                preserveState: true
            })
        },
        submitReqForm() {
            this.$inertia.post(route('marketing.pendingRequest.submit.request'), {
                data: this.form,
                barcode: this.barcodes
            }, {
                onSuccess: (response) => {
                    if (response.props.flash.type == 'success') {
                        this.open = false;
                        this.stream = `data:application/pdf;base64,${response.props.flash.stream}`;
                        this.openIframe = true;

                    } else {
                        notification[response.props.flash.type]({
                            message: response.props.flash.msg,
                            description: response.props.flash.description,
                        });
                    }

                },
                preserveState: true
            })
        },
        closeModal() {
            this.open = false;
        },
    }
}
</script>
