<template>
    <a-row>
        <a-col :span="8">col-8</a-col>
        <a-col :span="8">{{  checkBy }}</a-col>
        <a-col :span="8">
            <div class="mb-2">
                <a-card title="Current Budget">
                    <h2>₱ {{ currentBudget }}</h2>
                </a-card>
            </div>
            <div v-if="ReqNum">
                <div v-for="request in ReqNum" :key="pe_id">
                    <a-button class="mb-2" @click="openReqModal(request)">
                        Please fill up Requisition Form for Production Request # {{ request.pe_num }}
                    </a-button>
                </div>
            </div>
        </a-col>
    </a-row>

    <a-modal v-model:open="open" width="95%" style="top: 65px;" title="Suggested E-Requisition Entry">
        <a-row>
            <a-col :span="12">
                <a-card style="margin: 10px;">
                    <a-form-item label="E-Request No:">
                        <a-input v-model:value="form.requestNo" :style="{ width: '30%' }" readonly />
                    </a-form-item>

                    <a-form-item label="Finalize:" :style="{ width: '50%' }">
                        <a-select v-model:value="form.finalize" placeholder="Select an option">
                            <a-select-option value="1">Approve</a-select-option>
                            <a-select-option value="3">Cancel</a-select-option>
                        </a-select>
                    </a-form-item>

                    <a-form-item label="Production Request Number:">
                        <a-input v-model:value="form.productionReqNum" :style="{ width: '30%' }" readonly />
                    </a-form-item>
                    <a-form-item label="Date Requested:">
                        <a-input v-model:value="form.dateRequested" :style="{ width: '50%' }" readonly />
                    </a-form-item>
                    <a-form-item label="Date Needed:">
                        <a-input v-model:value="form.dateNeeded" :style="{ width: '50%' }" readonly />
                    </a-form-item>
                    <a-form-item label="Location:">
                        <a-input v-model:value="form.location" :style="{ width: '50%' }" readonly />
                    </a-form-item>
                    <a-form-item label="Department:">
                        <a-input v-model:value="form.department" :style="{ width: '50%' }" readonly />
                    </a-form-item>
                    <a-form-item label="Remarks:" name="remarks">
                        <a-textarea v-model:value="form.remarks" />
                    </a-form-item>

                    <a-form-item label="Checked By:">
                        <a-select v-model:value="form.checkedBy" placeholder="Select an option"
                            :style="{ width: '70%' }">
                            <a-select-option v-for="item in checkBy" :key="item.assig_name" :value="item.assig_name">
                                {{ item.assig_name }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>

                    <a-form-item label="Approve By:">
                        <a-input v-model:value="form.approvedBy" :style="{ width: '50%' }" readonly />
                    </a-form-item>
                </a-card>
            </a-col>
            <a-col :span="12">
                <a-card style="margin: 10px;">
                    <a-form-item label="Select Supplier:">
                        <a-select v-model:value="form.selectedSupplierId" placeholder="Select an option"
                            :style="{ width: '70%' }">
                            <a-select-option v-for="item in supplier" :key="item.gcs_id" :value="item.gcs_id">
                                {{ item.gcs_companyname }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                    <a-form-item label="Contact Person:">
                        <a-input v-model:value="form.contactPerson" :style="{ width: '50%' }" />
                    </a-form-item>
                    <a-form-item label="Contact No:">
                        <a-input v-model:value="form.contactNum" :style="{ width: '50%' }" />
                    </a-form-item>
                    <a-form-item label="Address:">
                        <a-input v-model:value="form.address" :style="{ width: '50%' }" />
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

        {{ this.form.id }}
    </a-modal>

</template>

<script>
import Authenticatedlayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs, { Dayjs } from "dayjs";

export default {
    layout: Authenticatedlayout,

    props: {
        getRequestNo:Array,
        ReqNum: Array,
        currentBudget: Number,
        checkBy: Array,
        supplier: Array,
        productionReqItems: Object,
        columns: Object
    },

    data() {
        return {
            open: false,
            form: {
                id: '',
                requestNo: '',
                finalize: '',
                productionReqNum: '',
                dateRequested: dayjs(this.ReqNum[0].pe_date_request),
                dateNeeded: dayjs(this.ReqNum[0].pe_date_needed),
                location: 'AGC Head Office',
                department: 'Marketing',
                remarks: '',
                checkedBy: '',
                approvedById: this.$page.props.auth.user.user_id,
                approvedBy: this.$page.props.auth.user.full_name,
                selectedSupplierId: '',
                contactPerson: '',
                contactNum: '',
                address: ''
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
                    data: data.pe_id
                }, {
                onSuccess: () => {
                    this.open = true;
                    this.form.id= data.pe_id
                    this.form.requestNo = this.getRequestNo
                    this.form.productionReqNum = data.pe_num
                },
                preserveState: true
            })
        },
        submitReqForm() {
            axios.post(route('marketing.requisition.submit.form'), {
                data: this.form
            })
        },
        closeModal() {
            this.open = false;
        }
    },
};
</script>
