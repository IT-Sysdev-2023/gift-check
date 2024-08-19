<template>
    <a-tabs v-model:activeKey="activeKey">
        <a-tab-pane key="1">
            <template #tab>
                <span>
                    {{ title }}
                </span>
            </template>
            <a-card>
                <div class="flex justify-end">
                    <h2 class="bg-gray-900 text-white p-2 rounded mb-3">
                        🤑 Current Budget: {{ form.formattedbudget }}
                    </h2>
                </div>
                <a-row :gutter="[16, 16]">
                    <a-col :span="12">
                        <a-card>
                            <a-form>
                                <a-form-item label="Request Status">
                                    <a-select ref="select" v-model:value="form.status" style="width: 120px"
                                        @focus="focus" @chaImagenge="handleChange">
                                        <a-select-option value="1">Approved</a-select-option>
                                        <a-select-option value="2">Cancelled</a-select-option>
                                    </a-select>
                                </a-form-item>
                                <div v-if="form.status == '1'">
                                    <a-form-item label="Date Approved">
                                        <a-input v-model:value="form.dateApproved" readonly></a-input>
                                    </a-form-item>
                                    <a-form-item label="Remarks">
                                        <a-textarea v-model:value="form.approveRemarks"></a-textarea>
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
                                        <a-input v-model:value="form.preparedBy" readonly></a-input>
                                    </a-form-item>
                                    <a-form-item label="Upload Document">
                                        <a-upload-dragger :before-upload="() => false" :max-count="1"
                                            @change="handleImageChange">
                                            <p class="ant-upload-drag-icon">
                                                <inbox-outlined></inbox-outlined>
                                            </p>
                                            <p class="ant-upload-text">Click or drag file to this area to upload</p>
                                        </a-upload-dragger>
                                    </a-form-item>
                                </div>
                                <div v-else>
                                    <a-form-item label="Date Cancelled">
                                        <a-input v-model:value="form.dateCancelled" readonly></a-input>
                                    </a-form-item>
                                    <a-form-item label="Remarks">
                                        <a-textarea v-model:value="form.approveRemarks"></a-textarea>
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
                                    <a-form-item label="Cancelled By">
                                        <a-input v-model:value="form.cancelledBy" readonly></a-input>
                                    </a-form-item>
                                    <a-form-item label="Upload Document">
                                        <a-upload name="file" :before-upload="() => false" :max-count="1"
                                            @change="handleImageChange" @drop="handleDrop">
                                            <p class="ant-upload-drag-icon">
                                                <inbox-outlined></inbox-outlined>
                                            </p>
                                            <p class="ant-upload-text">Click or drag file to this area to upload</p>
                                            <p class="ant-upload-hint">
                                                Support for a single or bulk upload. Strictly prohibit from uploading
                                                company data
                                                or other
                                                band files
                                            </p>
                                        </a-upload>
                                    </a-form-item>
                                </div>
                            </a-form>
                        </a-card>
                    </a-col>
                    <a-col :span="12">
                        <a-card :title="detailTitles">
                            <a-row :gutter="[16, 16]">
                                <a-col :span="12">
                                    <a-form>
                                        <a-form-item label="RFSEGC #">
                                            <a-input v-model:value="data[0].spexgc_num" readonly></a-input>
                                        </a-form-item>
                                        <a-form-item label="Department">
                                            <a-input v-model:value="data[0].title" readonly></a-input>
                                        </a-form-item>
                                        <a-form-item label="Date Requested">
                                            <a-input v-model:value="data[0].dateRequeted" readonly></a-input>
                                        </a-form-item>
                                        <a-form-item label="Date Needed">
                                            <a-input v-model:value="data[0].dateNeed" readonly></a-input>
                                        </a-form-item>
                                        <a-form-item label="Customer">
                                            <a-textarea v-model:value="data[0].spcus_companyname" readonly></a-textarea>
                                        </a-form-item>
                                    </a-form>
                                </a-col>
                                <a-col :span="12">
                                    <a-form>
                                        <a-form-item label="Total Denomination">
                                            <a-input
                                                v-model:value="data[0].special_external_gcrequest_items_has_many[0].specit_denoms"
                                                readonly></a-input>
                                        </a-form-item>
                                        <a-form-item label="Payment Type">
                                            <a-input v-model:value="paymentType" readonly></a-input>
                                        </a-form-item>
                                        <a-form-item label="Payment Amount">
                                            <a-input v-model:value="data[0].spexgc_payment" readonly></a-input>
                                        </a-form-item>
                                        <a-form-item label="AR #">
                                            <a-input v-model:value="data[0].spexgc_payment_arnum" readonly></a-input>
                                        </a-form-item>
                                        <a-form-item label="Remarks">
                                            <a-textarea v-model:value="data[0].spexgc_remarks" readonly></a-textarea>
                                        </a-form-item>

                                    </a-form>
                                </a-col>
                            </a-row>
                            <a-form-item label="Requested by">
                                <a-input v-model:value="data[0].fullname" readonly></a-input>
                            </a-form-item>
                        </a-card>
                    </a-col>
                </a-row>
                <div class="flex justify-end mt-5">
                    <a-button @click="submitForm" type="primary">Submit</a-button>
                </div>
            </a-card>
        </a-tab-pane>
        <a-tab-pane key="2">
            <template #tab>
                <span>
                    GC Holder
                </span>
            </template>
            <a-table :dataSource="gcHolder" :columns="columns" />
        </a-tab-pane>

    </a-tabs>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import { notification } from 'ant-design-vue';

export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        checkedBy: Object,
        type: String,
        currentBudget: String,
        gcHolder: Object,
        columns: Object
    },
    data() {
        return {
            activeKey: '1',

            title: this.formTitle(),
            detailTitles: this.gcDetailsTitle(),
            paymentType: this.payType(),
            file: null,
            form: {
                gcType: this.type,
                id: this.data[0].spexgc_id,
                status: '1',
                dateApproved: dayjs(),
                dateCancelled: dayjs(),
                approveRemarks: '',
                checkedBy: '',
                approvedBy: '',
                budget: this.currentBudget,
                formattedbudget: this.currentBudget.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                cancelledBy: this.data[0].fullname,
                preparedBy: this.data[0].fullname,

            }
        }
    },
    methods: {
        handleImageChange(document) {
            this.file = document.file;
        },
        formTitle() {
            return this.type === 'external'
                ? 'Special External GC Request Approval Form'
                : 'Special Internal GC Request Approval Form';
        },
        gcDetailsTitle() {
            return this.type === 'external'
                ? 'Special External GC Request Approval Details'
                : 'Special Internal GC Request Approval Details';
        },
        payType() {
            return this.data[0].spexgc_paymentype === '1'
                ? 'Cash'
                : 'Check';
        },
        submitForm() {
            this.$inertia.post(route('finance.pendingGc.approval.submit'), {
                data: this.data,
                formData: this.form,
                file: this.file,
                currentBudget: this.form.budget
            }, {
                onSuccess: (response) => {
                    if (response.props.flash.type != 'error') {
                        notification[response.props.flash.type]({
                            message: response.props.flash.msg,
                            description: response.props.flash.description
                        });

                        this.$inertia.get(route('finance.pendingGc.pending'));
                    } else {
                        notification[response.props.flash.type]({
                            message: response.props.flash.msg,
                            description: response.props.flash.description
                        });
                    }
                }
            });
        }
    }
}
</script>
