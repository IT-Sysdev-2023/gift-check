<template>
    <a-row :gutter="[16, 16]">
        <a-col :span="8">
            <a-card title=" GC Production Request">
                <div v-if="gcProductionRequest.pendingRequest">
                    <a-badge :count=gcProductionRequest.pendingRequest>
                        <a-button @click="pendingRequestLink" block style="width: 340px"
                            class="mb-2 bg-red-500 text-white">
                            Pending Request
                        </a-button>
                    </a-badge>
                </div>
                <div v-else>
                    <a-button disabled block style="width: 340px" class="mb-2 bg-red-500 text-white">
                        Pending Request
                    </a-button>
                </div>
                <div v-if="gcProductionRequest.approvedRequest">
                    <a-badge :count=gcProductionRequest.approvedRequest :number-style="{
                        backgroundColor: '#3b82f6',
                        color: 'white',
                        boxShadow: '0 0 0 1px #d9d9d9 inset',
                    }">
                        <a-button @click="ApprovedRequestLink" block style="width: 340px"
                            class="mb-2 bg-blue-500 text-white">
                            Approved Request
                        </a-button>
                    </a-badge>
                </div>
                <div v-else>
                    <a-button disabled block style="width: 340px" class="mb-2 bg-blue-500 text-white">
                        Approved Request
                    </a-button>
                </div>
                <div v-if="gcProductionRequest.cancelledRequest">
                    <a-badge :count=gcProductionRequest.cancelledRequest :number-style="{
                        backgroundColor: '#6b7280',
                        color: 'white',
                        boxShadow: '0 0 0 1px #d9d9d9 inset',
                    }">
                        <a-button block style="width: 340px" class="mb-2 bg-gray-500 text-white">
                            Cancelled Request
                        </a-button>
                    </a-badge>
                </div>
                <div v-else>
                    <a-button disabled block style="width: 340px" class="mb-2 bg-gray-500 text-white">
                        Cancelled Request
                    </a-button>
                </div>


            </a-card>

        </a-col>
        <a-col :span="8">
            <a-card>
                yyy
            </a-card>
        </a-col>
        <a-col :span="8">
            <div class="mb-2">
                <a-card title="Current Budget">
                    <h2>₱ {{ currentBudget }}</h2>
                </a-card>
            </div>
            <div v-if="ReqNum">
                <div v-for="request in ReqNum" :key="pe_id">
                    <a-button class="mb-2" @click="openReqModal(request)">
                        Please fill up Requisition Form for Production Request # {{ request.pe_num }} P.O
                    </a-button>
                </div>
            </div>
        </a-col>
    </a-row>

    <a-modal v-model:open="open" width="95%" style="top: 65px;" title="Suggested E-Requisition Entry">
        <a-row :gutter="[16, 16]">
            <a-col :span="12">
                <a-card>
                    <a-form-item label="E-Request No:" :style="{ width: '70%' }">
                        <a-input v-model:value="form.requestNo" readonly />
                    </a-form-item>

                    <a-form-item label="Finalize:" :style="{ width: '70%' }">
                        <a-select v-model:value="form.finalize" placeholder="Select an option">
                            <a-select-option value="1">Approved</a-select-option>
                            <a-select-option value="3">Cancelled</a-select-option>
                        </a-select>
                    </a-form-item>

                    <div v-if="form.finalize == '1'">
                        <a-form-item label="Production Request Number:" :style="{ width: '70%' }">
                            <a-input v-model:value="form.productionReqNum" readonly />
                        </a-form-item>
                        <a-form-item label="Date Requested:" :style="{ width: '70%' }">
                            <a-input v-model:value="form.dateRequested" readonly />
                        </a-form-item>
                        <a-form-item label="Date Needed:" :style="{ width: '70%' }">
                            <a-input v-model:value="form.dateNeeded" readonly />
                        </a-form-item>
                        <a-form-item label="Location:" :style="{ width: '70%' }">
                            <a-input v-model:value="form.location" readonly />
                        </a-form-item>
                        <a-form-item label="Department:" :style="{ width: '70%' }">
                            <a-input v-model:value="form.department" readonly />
                        </a-form-item>
                        <a-form-item label="Remarks:" name="remarks" :style="{ width: '70%' }">
                            <a-textarea v-model:value="form.remarks" />
                        </a-form-item>

                        <a-form-item label="Checked By:" :style="{ width: '70%' }">
                            <a-select v-model:value="form.checkedBy" placeholder="Select an option">
                                <a-select-option v-for="item in checkBy" :key="item.assig_name"
                                    :value="item.assig_name">
                                    {{ item.assig_name }}
                                </a-select-option>
                            </a-select>
                        </a-form-item>
                    </div>



                    <a-card v-if="form.finalize == '3'" class="bg-red-400 text-white mb-4 text-center">
                        GC Barcode # of this requisition will be tag cancelled and cannot be use again.
                    </a-card>



                    <a-form-item :label="form.finalize == '1' ? 'Approved By' : 'Cancelled By'"
                        :style="{ width: '70%' }">
                        <a-input v-model:value="form.approvedBy" readonly />
                    </a-form-item>
                </a-card>
            </a-col>
            <a-col :span="12">
                <a-card v-if="form.finalize == '1'">
                    <a-form-item label="Select Supplier:" :style="{ width: '70%' }">
                        <a-select v-model:value="form.selectedSupplierId" placeholder="Select an option">
                            <a-select-option v-for="item in supplier" :key="item.gcs_id" :value="item.gcs_id">
                                {{ item.gcs_companyname }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                    <a-form-item label="Contact Person:" :style="{ width: '70%' }">
                        <a-input v-model:value="form.contactPerson" />
                    </a-form-item>
                    <a-form-item label="Contact No:" :style="{ width: '70%' }">
                        <a-input v-model:value="form.contactNum" />
                    </a-form-item>
                    <a-form-item label="Address:" :style="{ width: '70%' }">
                        <a-input v-model:value="form.address" />
                    </a-form-item>
                </a-card>

                <a-card style="margin: 10px;">
                    <a-card class="bg-gray-700 text-white mb-1 text-center">
                        Request for gift cheque printing as per breakdown provided below.
                    </a-card>
                    <a-table :pagination="false" :data-source="productionReqItems" :columns="columns" />
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
import dayjs, { Dayjs } from "dayjs";
import { notification } from 'ant-design-vue';

export default {
    layout: Authenticatedlayout,

    props: {
        getRequestNo: Array,
        ReqNum: Array,
        currentBudget: Number,
        checkBy: Array,
        supplier: Array,
        productionReqItems: Object,
        columns: Object,
        gcProductionRequest: Object
    },

    data() {
        return {
            open: false,
            form: {
                id: '',
                requestNo: '',
                finalize: '1',
                productionReqNum: '',
                dateRequested: dayjs(this.ReqNum[0]?.pe_date_request),
                dateNeeded: dayjs(this.ReqNum[0]?.pe_date_needed),
                location: 'AGC Head Office',
                department: 'Marketing',
                remarks: '',
                checkedBy: '',
                approvedById: this.$page.props.auth.user.user_id,
                approvedBy: this.$page.props.auth.user.full_name,
                selectedSupplierId: '',
                contactPerson: '',
                contactNum: '',
                address: '',

            }
        };
    },

    watch: {
        'form.selectedSupplierId': function (newVal) {
            const selected = this.supplier.find(item => item.gcs_id === newVal);
            if (selected) {
                this.form.contactPerson = selected.gcs_contactperson;
                this.form.contactNum = selected.gcs_contactnumber;
                this.form.address = selected.gcs_address;
            }
        }
    },

    methods: {
        openReqModal(data) {
            this.$inertia.get(route('marketing.dashboard'),
                {
                    data: data.pe_id,

                }, {
                onSuccess: () => {
                    this.open = true;
                    this.form.id = data.pe_id
                    this.form.requestNo = this.getRequestNo
                    this.form.productionReqNum = data.pe_num
                },
                preserveState: true
            })
        },
        submitReqForm() {
            this.$inertia.post(route('marketing.requisition.submit.form'), {
                data: this.form,
                denom: this.productionReqItems,
                supName: this.supplier.filter(data => data.gcs_id == this.form.selectedSupplierId)[0]?.gcs_companyname
            }, {
                onSuccess: (response) => {
                    if (response.props.flash.type == 'success') {
                        notification[response.props.flash.type]({
                            message: response.props.flash.msg,
                            description: response.props.flash.description,
                        });
                        this.$inertia.get(route('marketing.dashboard'))
                    } else {
                        notification[response.props.flash.type]({
                            message: response.props.flash.msg,
                            description: response.props.flash.description,
                        });
                    }
                },
            }
            )
        },
        closeModal() {
            this.open = false;
        },

        pendingRequestLink() {
            this.$inertia.get(route('marketing.pendingRequest.pending.request'))
        },
        ApprovedRequestLink() {
            this.$inertia.get(route('marketing.approvedRequest.approved.request'))
        }
    },
};
</script>
