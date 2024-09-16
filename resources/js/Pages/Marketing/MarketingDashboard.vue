<template>
    <a-row :gutter="[16, 16]">
        <a-col :span="8">
            <PromoGcReq class="mb-2" :countPromoGcRequest/>
            <GcProductionReq :gcProductionRequest />
        </a-col>
        <a-col :span="8">
            <SpecialExternalGc/>
            <PromoGcReceived/>
        </a-col>
        <a-col :span="8">
            <div class="mb-2">
                <a-card title="Current Budget">
                    <h2>₱ {{ currentBudget }}</h2>
                </a-card>
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
                    <a-table bordered :pagination="false" :data-source="productionReqItems" :columns="columns" />
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

    <a-modal v-model:open="requestListModal" title="List of Requisition Request">
        <div v-if="ReqNum && ReqNum.length">
            <div v-for="request in ReqNum" :key="request.pe_id">
                <a-button class="mb-2" @click="openReqModal(request)">
                    Please fill up Requisition Form for Production Request # {{ request.pe_num }} P.O
                </a-button>
            </div>
        </div>
        <div v-else>
            <a-empty />
        </div>
        <template #footer>
            <a-button type="default" @click="requestListModal = false">Cancel</a-button>
        </template>
    </a-modal>


    <a-float-button title="List of Requisition Request" @click="requisitionListModal"
        :badge="{ count: Object.keys(ReqNum).length, overflowCount: 999 }" />
</template>

<script>
import Authenticatedlayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs, { Dayjs } from "dayjs";
import { notification } from 'ant-design-vue';
import GcProductionReq from "./Card/GcProductionReq.vue";
import GcPromoReq from "./Card/PromoGcReq.vue";
import BudgetRequest from "../Treasury/Transactions/BudgetRequest.vue";
import BudgetReq from "./Card/PromoGcReq.vue";
import PromoGcReq from "./Card/PromoGcReq.vue";
import SpecialExternalGc from "./Card/SpecialExternalGc.vue";
import PromoGcReceived from "./Card/PromoGcReceived.vue";

export default {
    layout: Authenticatedlayout,

    props: {
        getRequestNo: String,
        ReqNum: Array,
        currentBudget: String,
        checkBy: Array,
        supplier: Array,
        productionReqItems: Object,
        columns: Object,
        gcProductionRequest: Object,
        countPromoGcRequest: Object
    },

    data() {
        return {
            requestListModal: false,
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
            },
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
        requisitionListModal() {
            this.requestListModal = true
        },
    },
};
</script>
