<template>
    <a-card class="mt-4">
        <a-row :gutter="[16, 16]">
            <a-col :span="8">
                <strong>Requisition No.</strong>
                <a-form-item
                    label=""
                    has-feedback
                    :validate-status="form.errors.req_no ? 'error' : ''"
                    :help="form.errors.req_no"
                >
                    <a-input
                        allow-clear
                        v-model:value="form.req_no"
                        placeholder="Enter Here..."
                        @change="form.validate('req_no')"
                    />
                </a-form-item>
                <strong>Supplier Name.</strong>
                <a-form-item
                    has-feedback
                    :validate-status="form.errors.sup_name ? 'error' : ''"
                    :help="form.errors.sup_name"
                >
                    <a-select
                        allow-clear
                        ref="select"
                        placeholder="Select Supplier"
                        v-model:value="form.sup_name"
                        @change="form.validate('sup_name')"
                    >
                        <a-select-option
                            v-for="name in supplier"
                            v-model:value="name.gcs_companyname"
                            >{{ name.gcs_companyname }}</a-select-option
                        >
                    </a-select>
                </a-form-item>
                <strong>Mode Of Payment.</strong>
                <a-form-item
                    has-feedback
                    :validate-status="form.errors.mop ? 'error' : ''"
                    :help="form.errors.mop"
                >
                    <a-select
                        ref="select"
                        placeholder="Select Mode of Payment"
                        allow-clear
                        @click="() => (errors.mop = null)"
                        v-model:value="form.mop"
                        @change="form.validate('mop')"
                    >
                        <a-select-option value="CASH">CASH</a-select-option>
                        <a-select-option value="CHECK">CHECK</a-select-option>
                        <a-select-option value="JV">JV</a-select-option>
                    </a-select>
                </a-form-item>
                <strong>Receiving No.</strong>
                <a-form-item
                    has-feedback
                    :validate-status="form.errors.rec_no ? 'error' : ''"
                    :help="form.errors.rec_no"
                >
                    <a-input
                        allow-clear
                        v-model:value="form.rec_no"
                        @change="form.validate('rec_no')"
                        placeholder="Enter Here..."
                    />
                </a-form-item>
                <strong>Transaction Date.</strong>
                <a-form-item
                    has-feedback
                    :validate-status="form.errors.trans_date ? 'error' : ''"
                    :help="form.errors.trans_date"
                >
                    <a-date-picker
                        allow-clear
                        style="width: 100%"
                        @change="transDate"
                        placeholder="Enter Here..."
                    />
                </a-form-item>
                <strong>Reference No.</strong>
                <a-form-item
                    has-feedback
                    :validate-status="form.errors.ref_no ? 'error' : ''"
                    :help="form.errors.ref_no"
                >
                    <a-input
                        allow-clear
                        @change="form.validate('ref_no')"
                        v-model:value="form.ref_no"
                        placeholder="Enter Here..."
                    />
                </a-form-item>
                <strong>Purchase Order No</strong>
                <a-form-item
                    has-feedback
                    :validate-status="form.errors.po_no ? 'error' : ''"
                    :help="form.errors.po_no"
                >
                    <a-input
                        allow-clear
                        @change="form.validate('po_no')"
                        v-model:value="form.po_no"
                        placeholder="Enter Here..."
                    />
                </a-form-item>
                <strong>Pay Terms.</strong>
                <a-form-item
                    has-feedback
                    :validate-status="form.errors.pay_terms ? 'error' : ''"
                    :help="form.errors.pay_terms"
                >
                    <a-input
                        allow-clear
                        @change="form.validate('pay_terms')"
                        v-model:value="form.pay_terms"
                        placeholder="Enter Here..."
                    />
                </a-form-item>
            </a-col>
            <a-col :span="8">
                <strong>Location Code.</strong>
                <a-form-item
                    has-feedback
                    :validate-status="form.errors.loc_code ? 'error' : ''"
                    :help="form.errors.loc_code"
                >
                    <a-input
                        allow-clear
                        @change="form.validate('loc_code')"
                        v-model:value="form.loc_code"
                        placeholder="Enter Here..."
                    />
                </a-form-item>
                <strong>Purchase Date.</strong>
                <a-form-item
                    has-feedback
                    :validate-status="form.errors.pur_date ? 'error' : ''"
                    :help="form.errors.pur_date"
                >
                    <a-date-picker
                        allow-clear
                        style="width: 100%"
                        @change="purchaseDate"
                        placeholder="Enter Here..."
                    />
                </a-form-item>
                <strong>Ref Purchase Order No.</strong>
                <a-form-item
                    has-feedback
                    :validate-status="form.errors.ref_no ? 'error' : ''"
                    :help="form.errors.ref_no"
                >
                    <a-input
                        allow-clear
                        @change="form.validate('ref_no')"
                        v-model:value="form.ref_po_no"
                        placeholder="Enter Here..."
                    />
                </a-form-item>
                <strong>Deparment Code.</strong>
                <a-form-item
                    has-feedback
                    :validate-status="form.errors.dep_code ? 'error' : ''"
                    :help="form.errors.dep_code"
                >
                    <a-input
                        allow-clear
                        @change="form.validate('dep_code')"
                        v-model:value="form.dep_code"
                        placeholder="Enter Here..."
                    />
                </a-form-item>
                <strong>Prepare By.</strong>
                <a-form-item
                    has-feedback
                    :validate-status="form.errors.prep_by ? 'error' : ''"
                    :help="form.errors.prep_by"
                >
                    <a-input
                        allow-clear
                        @change="form.validate('prep_by')"
                        v-model:value="form.prep_by"
                        placeholder="Enter Here..."
                    />
                </a-form-item>
                <strong>Checked By.</strong>
                <a-form-item
                    has-feedback
                    :validate-status="form.errors.check_by ? 'error' : ''"
                    :help="form.errors.check_by"
                >
                    <a-input
                        allow-clear
                        @change="form.validate('check_by')"
                        v-model:value="form.check_by"
                        placeholder="Enter Here..."
                    />
                </a-form-item>
                <strong> Srr Type</strong>
                <a-form-item
                    has-feedback
                    :validate-status="form.errors.srr_type ? 'error' : ''"
                    :help="form.errors.srr_type"
                >
                    <a-select
                        ref="select"
                        allow-clear
                        @change="form.validate('srr_type')"
                        placeholder="Select Srr Type"
                        v-model:value="form.srr_type"
                    >
                        <a-select-option value="WHOLE">WHOLE</a-select-option>
                        <a-select-option value="PARTIAL"
                            >PARTIAL</a-select-option
                        >
                        <a-select-option value="FINAL">FINAL</a-select-option>
                    </a-select>
                </a-form-item>
                <strong>Remarks</strong>
                <a-form-item
                    has-feedback
                    :validate-status="form.errors.remarks ? 'error' : ''"
                    :help="form.errors.remarks"
                >
                    <a-textarea
                        allow-clear
                        @change="form.validate('remarks')"
                        v-model:value="form.remarks"
                        placeholder="Remarks"
                        :rows="3"
                    />
                </a-form-item>
            </a-col>
            <a-col :span="8">
                <div class="text-center mt-3 animate-pulse">
                    <strong> Denomation Needed </strong>
                </div>
                <a-card>
                    <a-row v-for="(item, key) in denom" :key="item.denom_id">
                        <a-col :span="8">
                            <a-input
                                type="text"
                                class="text-center"
                                :value="'₱' + item.denomination"
                                readonly
                            />
                        </a-col>
                        <a-col :span="16">
                            <a-form-item :validate-status="form.errors.denom ? 'error' : ''" :help="form.errors.denom">
                                <a-input allow-clear @change="form.validate('denom')"
                                    v-model:value="form.denom[item.denom_id]" placeholder="Enter Here..." />
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-card>
                <a-button type="primary" class="mt-5" block @click="submit">
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
// import { useForm } from '@inertiajs/vue3';
import { useForm } from "laravel-precognition-vue";
import pickBy from "lodash/pickBy";
import { notification } from "ant-design-vue";

export default {
    props: {
        supplier: Object,
        denom: Object,
    },
    data() {
        return {
            errors: {},
            form: useForm("post", route("admin.submit.po"), {
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
        };
    },
    methods: {
        submit() {
            this.form.submit({onSuccess: (response) => {
                notification[response.props.flash.status]({
                        message: response.props.flash.title,
                        description: response.props.flash.msg,
                    });
                    this.form.reset();
            }});
            // this.form.transform((data) => ({
            //     ...pickBy(data)
            // })).post(route('admin.submit.po'), {
            //     onSuccess: (response) => {
            //         notification[response.props.flash.status]({
            //             message: response.props.flash.title,
            //             description: response.props.flash.msg,
            //         });
            //         this.form.reset();
            //     },
            //     onError: (errors) => {
            //         this.errors = errors
            //     },
            // })
        },
        purchaseDate(obj, str) {
            this.form.pur_date = str;
            this.form.validate('pur_date');
        },
        transDate(obj, str) {
            this.form.trans_date = str;
            this.form.validate("req_no");
        },
    },
};
</script>
