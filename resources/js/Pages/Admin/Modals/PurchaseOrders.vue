<template>
    <a-modal class="text-center" title="Add Purchase Order" style="width: 80%; top: 50px;" :footer="false">
        <a-card class="mt-4">
            <a-row :gutter="[16, 16]">
                <a-col :span="9">
                    <a-form layout="horizontal">
                        <a-form-item label="Requisition No." has-feedback
                            :validate-status="errors.req_no ? 'error' : ''" :help="errors.req_no">
                            <a-input allow-clear v-model:value="form.req_no" placeholder="Enter Here..." />
                        </a-form-item>
                        <a-form-item label="Supplier Name." :validate-status="errors.sup_name ? 'error' : ''"
                            :help="errors.sup_name">
                            <a-input allow-clear v-model:value="form.sup_name" placeholder="Enter Here..." />
                        </a-form-item>
                        <a-form-item label="Mode Of Payment." :validate-status="errors.mop ? 'error' : ''"
                            :help="errors.mop">
                            <a-input allow-clear v-model:value="form.mop" placeholder="Enter Here..." />
                        </a-form-item>
                        <a-form-item label="Receiving No." :validate-status="errors.rec_no ? 'error' : ''"
                            :help="errors.rec_no">
                            <a-input allow-clear v-model:value="form.rec_no" placeholder="Enter Here..." />
                        </a-form-item>
                        <a-form-item label="Transaction Date." :validate-status="errors.trans_date ? 'error' : ''"
                            :help="errors.trans_date">
                            <a-date-picker allow-clear style="width: 100%;" @change="transDate"
                                placeholder="Enter Here..." />
                        </a-form-item>
                        <a-form-item label="Reference No." :validate-status="errors.ref_no ? 'error' : ''"
                            :help="errors.ref_no">
                            <a-input allow-clear v-model:value="form.ref_no" placeholder="Enter Here..." />
                        </a-form-item>
                        <a-form-item label="Purchase Order No" :validate-status="errors.po_no ? 'error' : ''"
                            :help="errors.po_no">
                            <a-input allow-clear v-model:value="form.po_no" placeholder="Enter Here..." />
                        </a-form-item>
                        <a-form-item label="Pay Terms." :validate-status="errors.pay_terms ? 'error' : ''"
                            :help="errors.pay_terms">
                            <a-input allow-clear v-model:value="form.pay_terms" placeholder="Enter Here..." />
                        </a-form-item>
                    </a-form>
                </a-col>
                <a-col :span="8">
                    <a-form layout="horizontal">
                        <a-form-item label="Location Code." :validate-status="errors.loc_code ? 'error' : ''"
                            :help="errors.loc_code">
                            <a-input allow-clear v-model:value="form.loc_code" placeholder="Enter Here..." />
                        </a-form-item>
                        <a-form-item label="Purchase Date." :validate-status="errors.pur_date ? 'error' : ''"
                            :help="errors.pur_date">
                            <a-date-picker allow-clear style="width: 100%;" @change="purchaseDate"
                                placeholder="Enter Here..." />
                        </a-form-item>
                        <a-form-item label="Ref Purchase Order No."
                            :validate-status="errors.ref_no ? 'error' : ''" :help="errors.ref_no">
                            <a-input allow-clear v-model:value="form.ref_po_no" placeholder="Enter Here..." />
                        </a-form-item>
                        <a-form-item label="Deparment Code." :validate-status="errors.dep_code ? 'error' : ''"
                            :help="errors.dep_code">
                            <a-input allow-clear v-model:value="form.dep_code" placeholder="Enter Here..." />
                        </a-form-item>
                        <a-form-item label="Remarks" :validate-status="errors.remarks ? 'error' : ''"
                            :help="errors.remarks">
                            <a-input allow-clear v-model:value="form.remarks" placeholder="Enter Here..." />
                        </a-form-item>
                        <a-form-item label="Prepare By." :validate-status="errors.prep_by ? 'error' : ''"
                            :help="errors.prep_by">
                            <a-input allow-clear v-model:value="form.prep_by" placeholder="Enter Here..." />
                        </a-form-item>
                        <a-form-item label="Checked By." :validate-status="errors.check_by ? 'error' : ''"
                            :help="errors.check_by">
                            <a-input allow-clear v-model:value="form.check_by" placeholder="Enter Here..." />
                        </a-form-item>
                        <a-form-item label="Srr Type" :validate-status="errors.srr_type ? 'error' : ''"
                            :help="errors.srr_type">
                            <a-input allow-clear v-model:value="form.srr_type" placeholder="Enter Here..." />
                        </a-form-item>
                    </a-form>
                </a-col>
                <a-col :span="7">
                    <a-row :gutter="[16, 16]" v-for="(item, key) in denom" :key="item.denom_id">
                        <a-col :span="6">
                            <p class="mt-3 text-right">
                                {{ item.denomination }}
                            </p>
                        </a-col>
                        <a-col :span="18">
                            <a-form-item :validate-status="errors.loc_code ? 'error' : ''" :help="errors.denom">
                                <a-input allow-clear v-model:value="form.denom[item.denom_id]"
                                    placeholder="Enter Here..." />
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-button type="primary" ghost class="mt-5 " block @click="submit">
                        <template #icon>
                            <CloudDownloadOutlined />
                        </template>
                        Add Purchase Order
                    </a-button>
                </a-col>
            </a-row>

        </a-card>
    </a-modal>
</template>
<script>
import { useForm } from '@inertiajs/vue3';
import pickBy from "lodash/pickBy";
import { notification } from 'ant-design-vue';

export default {
    props: {
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
                    this.$emit('close-modal');
                    notification[response.props.flash.status]({
                        message: response.props.flash.title,
                        description: response.props.flash.msg,
                    });
                },
                onError: (errors) => {
                    this.errors = errors
                }
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
