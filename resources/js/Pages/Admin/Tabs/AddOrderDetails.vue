<template>
    <a-card class="mt-4">
        <a-row :gutter="[16, 16]">
            <a-col :span="8">
                <strong>Requisition No.</strong>
                <a-form-item label="" has-feedback :validate-status="errors.req_no ? 'error' : ''"
                    :help="errors.req_no">
                    <a-input allow-clear @click="() => errors.req_no = null" v-model:value="form.req_no"
                        placeholder="Enter Here..." />
                </a-form-item>
                <strong>Supplier Name.</strong>
                <a-form-item has-feedback :validate-status="errors.sup_name ? 'error' : ''" :help="errors.sup_name">
                    <a-select allow-clear ref="select" placeholder="Select Supplier" v-model:value="form.sup_name"
                        @focus="focus" @click="() => errors.sup_name = null">
                        <a-select-option v-for="name in supplier" v-model:value="name.gcs_companyname">{{
                            name.gcs_companyname }}</a-select-option>
                    </a-select>
                </a-form-item>
                <strong>Mode Of Payment.</strong>
                <a-form-item has-feedback :validate-status="errors.mop ? 'error' : ''" :help="errors.mop">
                    <a-select ref="select" placeholder="Select Mode of Payment" allow-clear
                        @click="() => errors.mop = null" v-model:value="form.mop">
                        <a-select-option value="CASH">CASH</a-select-option>
                        <a-select-option value="CHECK">CHECK</a-select-option>
                        <a-select-option value="JV">JV</a-select-option>
                    </a-select>
                </a-form-item>
                <strong>Receiving No.</strong>
                <a-form-item has-feedback :validate-status="errors.rec_no ? 'error' : ''" :help="errors.rec_no">
                    <a-input allow-clear @click="() => errors.rec_no = null" v-model:value="form.rec_no"
                        placeholder="Enter Here..." />
                </a-form-item>
                <strong>Transaction Date.</strong>
                <a-form-item has-feedback :validate-status="errors.trans_date ? 'error' : ''" :help="errors.trans_date">
                    <a-date-picker allow-clear @click="() => errors.trans_date = null" style="width: 100%;"
                        @change="transDate" placeholder="Enter Here..." />
                </a-form-item>
                <strong>Reference No.</strong>
                <a-form-item has-feedback :validate-status="errors.ref_no ? 'error' : ''" :help="errors.ref_no">
                    <a-input allow-clear @click="() => errors.ref_no = null" v-model:value="form.ref_no"
                        placeholder="Enter Here..." />
                </a-form-item>
                <strong>Purchase Order No</strong>
                <a-form-item has-feedback :validate-status="errors.po_no ? 'error' : ''" :help="errors.po_no">
                    <a-input allow-clear @click="() => errors.po_no = null" v-model:value="form.po_no"
                        placeholder="Enter Here..." />
                </a-form-item>
                <strong>Pay Terms.</strong>
                <a-form-item has-feedback :validate-status="errors.pay_terms ? 'error' : ''" :help="errors.pay_terms">
                    <a-input allow-clear @click="() => errors.pay_terms = null" v-model:value="form.pay_terms"
                        placeholder="Enter Here..." />
                </a-form-item>

            </a-col>
            <a-col :span="8">
                <strong>Location Code.</strong>
                <a-form-item has-feedback :validate-status="errors.loc_code ? 'error' : ''" :help="errors.loc_code">
                    <a-input allow-clear @click="() => errors.loc_code = null" v-model:value="form.loc_code"
                        placeholder="Enter Here..." />
                </a-form-item>
                <strong>Purchase Date.</strong>
                <a-form-item has-feedback :validate-status="errors.pur_date ? 'error' : ''" :help="errors.pur_date">
                    <a-date-picker allow-clear @click="() => errors.pur_date = null" style="width: 100%;"
                        @change="purchaseDate" placeholder="Enter Here..." />
                </a-form-item>
                <strong>Ref Purchase Order No.</strong>
                <a-form-item has-feedback :validate-status="errors.ref_no ? 'error' : ''" :help="errors.ref_no">
                    <a-input allow-clear @click="() => errors.ref_no = null" v-model:value="form.ref_po_no"
                        placeholder="Enter Here..." />
                </a-form-item>
                <strong>Deparment Code.</strong>
                <a-form-item has-feedback :validate-status="errors.dep_code ? 'error' : ''" :help="errors.dep_code">
                    <a-input allow-clear @click="() => errors.dep_code = null" v-model:value="form.dep_code"
                        placeholder="Enter Here..." />
                </a-form-item>
                <strong>Prepare By.</strong>
                <a-form-item has-feedback :validate-status="errors.prep_by ? 'error' : ''" :help="errors.prep_by">
                    <a-input allow-clear @click="() => errors.prep_by = null" v-model:value="form.prep_by"
                        placeholder="Enter Here..." />
                </a-form-item>
                <strong>Checked By.</strong>
                <a-form-item has-feedback :validate-status="errors.check_by ? 'error' : ''" :help="errors.check_by">
                    <a-input allow-clear @click="() => errors.check_by = null" v-model:value="form.check_by"
                        placeholder="Enter Here..." />
                </a-form-item>
                <strong> Srr Type</strong>
                <a-form-item has-feedback :validate-status="errors.srr_type ? 'error' : ''" :help="errors.srr_type">
                    <a-select ref="select" allow-clear @click="() => errors.ssr_type = null"
                        placeholder="Select Srr Type" v-model:value="form.srr_type">
                        <a-select-option value="WHOLE">WHOLE</a-select-option>
                        <a-select-option value="PARTIAL">PARTIAL</a-select-option>
                        <a-select-option value="FINAL">FINAL</a-select-option>
                    </a-select>
                </a-form-item>
                <strong>Remarks</strong>
                <a-form-item has-feedback :validate-status="errors.remarks ? 'error' : ''" :help="errors.remarks">
                    <a-textarea allow-clear @click="() => errors.remarks = null" v-model:value="form.remarks"
                        placeholder="Remarks" :rows="3" />
                </a-form-item>
            </a-col>
            <a-col :span="8">
                <div class="text-center mt-3 animate-pulse">
                    <strong>
                        Denomation Needed
                    </strong>
                </div>
                <a-card>
                    <a-row v-for="(item, key) in denom" :key="item.denom_id">
                        <a-col :span="8">
                            <a-input type="text" class="text-center" :value="'₱' + item.denomination" readonly />
                        </a-col>
                        <a-col :span="16">
                            <a-form-item :validate-status="errors.denom ? 'error' : ''" :help="errors.denom">
                                <a-input allow-clear @click="() => errors.denom = null"
                                    v-model:value="form.denom[item.denom_id]" placeholder="Enter Here..." />
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-card>
                <a-button type="primary" class="mt-5 " block @click="submit">
                    <template #icon>
                        <CloudDownloadOutlined />
                    </template>
                    Add Purchase Order
                </a-button>
            </a-col>
        </a-row>
    </a-card>
</template>
<script>
import { useForm } from '@inertiajs/vue3';
import pickBy from "lodash/pickBy";
import { notification } from 'ant-design-vue';

export default {
    props: {
        supplier: Object,
        denom: Object,
    },
    data() {
        return {
            errors: {},
            form: useForm({
                req_no: null,
                sup_name: null,
                mop: null,
                rec_no: null,
                trans_date: null,
                ref_no: null,
                po_no: null,
                pay_terms: null,
                loc_code: null,
                ref_po_no: null,
                dep_code: null,
                remarks: null,
                prep_by: null,
                check_by: null,
                srr_type: null,
                denom: [],
                pur_date: null,
            }),

        }
    },
    methods: {
        submit() {
            this.form.transform((data) => ({
                ...pickBy(data)
            })).post(route('admin.submit.po'), {
                onSuccess: (response) => {
                    notification[response.props.flash.status]({
                        message: response.props.flash.title,
                        description: response.props.flash.msg,
                    });
                    this.form.reset();
                },
                onError: (errors) => {
                    this.errors = errors
                },
            })
        },
        purchaseDate(obj, str) {
            this.form.pur_date = str;
        },
        transDate(obj, str) {
            this.form.trans_date = str;
        }
    }
}
</script>
