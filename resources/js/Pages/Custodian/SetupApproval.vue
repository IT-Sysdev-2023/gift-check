<template>
    <!-- {{ record.docs.doc_trid }} -->
    <a-tabs type="card" v-model:activeKey="activeKey1">
        <a-tab-pane key="1">
            <template #tab>
                <span>
                    <apple-outlined />
                    Special Gc Request Setup
                </span>
            </template>
            <a-row :gutter="[16, 16]">
                <a-col :span="10">
                    <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Date Requested">{{ record.special.datereq
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Requested By">{{ record.special.user.full_name
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Date Validity">{{ record.special.dateneeded
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;"
                            label="Ar#">{{ record.special.spexgc_payment_arnum }}</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Payment Type">{{ record.special.paymentStatus
                            }}</a-descriptions-item>
                    </a-descriptions>
                    <span v-if="record.special.spexgc_paymentype == 1">
                        <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
                            <a-descriptions-item style="width: 50%;" label="Amount">{{ record.special.spexgc_payment
                                }}</a-descriptions-item>
                        </a-descriptions>
                    </span>
                    <span v-else-if="record.special.spexgc_paymentype == 2">
                        <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
                            <a-descriptions-item style="width: 50%;" label="Bank Name">under
                                production</a-descriptions-item>
                        </a-descriptions>
                        <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
                            <a-descriptions-item style="width: 50%;" label="Bank Account Number">under
                                production</a-descriptions-item>
                        </a-descriptions>
                        <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
                            <a-descriptions-item style="width: 50%;" label="Check Number">under
                                production</a-descriptions-item>
                        </a-descriptions>
                    </span>
                    <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Remarks">
                            <a-textarea :rows="2" :value="record.special.spexgc_remarks" readonly />
                        </a-descriptions-item>
                    </a-descriptions>
                    <a-row :gutter="[16, 16]" class="mt-2">
                        <a-col :span="12">
                            <p class="flex justify-center items-center mt-7 font-bold " style="height: 90px;">
                                Documents
                            </p>
                        </a-col>
                        <a-col :span="12">
                            <a-card v-if="record.docs?.doc_fullpath != null">
                                <a-image width="100" style="border-radius: 10px;"
                                    :src="'/storage/' + record.docs.doc_fullpath" alt="image" />
                            </a-card>
                            <a-card v-else>
                                <a-empty />
                            </a-card>
                        </a-col>
                    </a-row>
                    <!-- {{ record.special.spexgc_id }} -->
                    <a-button class="mt-5" block style="background-color: #FC6736; color: white;"
                        @click="() => $inertia.get(route('custodian.approved.reprint.request', record.special.spexgc_id))">
                        <template #icon>
                            <FastForwardOutlined />
                        </template>
                        Reprint this Request
                    </a-button>
                </a-col>
                <a-col :span="14">
                    <a-card>
                        <a-row :gutter="[16, 16]">
                            <a-col :span="12">
                                <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
                                    <a-descriptions-item style="width: 50%;" label="Date Approved">{{
                                        record.special.dateapp
                                    }}</a-descriptions-item>
                                </a-descriptions>
                                <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
                                    <a-descriptions-item style="width: 50%;" label="Chekced By">{{
                                        record.special.approved_request.reqap_checkedby
                                    }}</a-descriptions-item>
                                </a-descriptions>
                                <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
                                    <a-descriptions-item style="width: 50%;" label="Prepared By">{{
                                        record.special.approved_request.reqap_approvedby
                                    }}</a-descriptions-item>
                                </a-descriptions>
                                <a-descriptions class="mt-1" size="small" layout="horizontal" bordered>
                                    <a-descriptions-item style="width: 50%;"
                                        label="Approved By">Approved</a-descriptions-item>
                                </a-descriptions>
                            </a-col>
                            <a-col :span="12">
                                <a-typography-text code>Documents:</a-typography-text>
                                <a-card class="mb-4" v-if="record.special.approved_request.reqap_doc == ''">
                                    <a-empty class="mt-5" />
                                </a-card>
                                <a-card v-else>
                                    <a-empty class="mt-5" />
                                </a-card>
                                <a-typography-text code>Remarks:</a-typography-text>
                                <a-textarea :rows="3" readonly :value="record.special.approved_request.reqap_remarks" />
                            </a-col>
                            <a-popconfirm title="Managers Key" v-model:open="isByBarcode" @cancel="cancel"
                                :show-cancel="false">
                                <template #description>

                                    <a-alert v-if="response?.status" :message="response?.msg" :type="response?.status"
                                        show-icon />
                                    <a-form name="basic" class="mt-5" autocomplete="off" @finish="onFinish"
                                        @finishFailed="onFinishFailed">
                                        <a-form-item label="Username" name="username"
                                            :validate-status="error.username ? 'error' : ''" has-feedback
                                            :help="error.username">
                                            <a-input placeholder="Username" autocomplete="new-password"
                                                v-model:value="form.username" @keyup.enter="managersKey('1')" />
                                        </a-form-item>
                                        <a-form-item label="Password" name="password"
                                            :validate-status="error.password ? 'error' : ''" has-feedback
                                            :help="error.password">
                                            <a-input-password placeholder="Password" autocomplete="off"
                                                v-model:value="form.password" @keyup.enter="managersKey('1')" />
                                        </a-form-item>
                                    </a-form>
                                </template>
                                <template #okButton>
                                    <a-button type="primary" size="small" @click="managersKey('1')"
                                        :loading="isSubmitting">
                                        <template #icon>
                                            <LockOutlined />
                                        </template>
                                        Continue?
                                    </a-button>
                                </template>
                                <a-button block class="mt-3" style="height: 50px;" type="dashed"
                                    @click="openManKey('1')">
                                    <PrinterOutlined />
                                    Print Request By Barcode
                                </a-button>
                            </a-popconfirm>

                            <a-popconfirm title="Managers Key" v-model:open="isByRange" @cancel="cancel"
                                :show-cancel="false">
                                <template #description>

                                    <a-alert v-if="response?.status" :message="response?.msg" :type="response?.status"
                                        show-icon />
                                    <a-form name="basic" class="mt-5" autocomplete="off" @finish="onFinish"
                                        @finishFailed="onFinishFailed">
                                        <a-form-item label="Username" name="username"
                                            :validate-status="error.username ? 'error' : ''" has-feedback
                                            :help="error.username">
                                            <a-input placeholder="Username" autocomplete="new-password"
                                                v-model:value="form.username" @keyup.enter="managersKey('2')" />
                                        </a-form-item>
                                        <a-form-item label="Password" name="password"
                                            :validate-status="error.password ? 'error' : ''" has-feedback
                                            :help="error.password">
                                            <a-input-password placeholder="Password" autocomplete="off"
                                                v-model:value="form.password" @keyup.enter="managersKey('2')" />
                                        </a-form-item>
                                    </a-form>
                                </template>
                                <template #okButton>
                                    <a-button type="primary" size="small" @click="managersKey('2')"
                                        :loading="isSubmitting">
                                        <template #icon>
                                            <LockOutlined />
                                        </template>
                                        Continue?
                                    </a-button>
                                </template>
                                <a-button type="dashed" style="height: 50px;" block @click="openManKey('2')">
                                    <PrinterOutlined />
                                    Print Request By Range
                                </a-button>
                            </a-popconfirm>
                        </a-row>
                    </a-card>
                    <a-card class="mt-3">
                        <a-card >
                            <a-alert class="text-center" message="Enter Barcode" type="info" show-icon />
                            <div class="mt-5">
                                <p class="ml-2">Enter Barcode</p>
                                <a-form>
                                    <a-form-item has-feedback :validate-status="errorBarcode.barcode ? 'error' : ''"
                                        :help="errorBarcode.barcode">
                                        <a-input @change="() => errorBarcode = []" allow-clear placeholder="Barcode"
                                            @keyup.enter="printBarcode" v-model:value="formBarcode.barcode"
                                            style="width: 100%; font-size: 20px; font-weight: bolder; height: 60px; color: white;" />

                                    </a-form-item>
                                </a-form>
                                <div class="flex justify-end">
                                    <a-button type="primary" class="mt-10" @click="printBarcode">
                                        <template #icon>
                                            <FastForwardOutlined />
                                        </template>
                                        Preview Gift Check Barcode
                                    </a-button>
                                </div>
                            </div>
                        </a-card>
                        <a-card v-if="byRange">
                            <a-alert class="text-center" message="Enter Barcode Range" type="info" show-icon />
                            <div class="mt-5">
                                <a-form>
                                    <a-form-item has-feedback
                                        :validate-status="errorByRange.barcodeStart ? 'error' : ''"
                                        :help="errorByRange.barcodeStart">
                                        <a-typography-text code>Barcode Start</a-typography-text>
                                        <a-input-number allow-clear size="large" placeholder="Barcode Start"
                                            @keyup.enter="printBarcodeRange" v-model:value="formByRange.barcodeStart"
                                            style="width: 100%" />
                                    </a-form-item>
                                    <a-form-item has-feedback :validate-status="errorByRange.barcodeEnd ? 'error' : ''"
                                        :help="errorByRange.barcodeEnd">
                                        <a-typography-text code>Barcode End</a-typography-text>
                                        <a-input-number allow-clear size="large" @keyup.enter="printBarcodeRange"
                                            placeholder="Barcode End" v-model:value="formByRange.barcodeEnd"
                                            style="width: 100%" />
                                    </a-form-item>
                                </a-form>
                                <div class="flex justify-end">
                                    <a-button type="primary" class="mt-10" @click="printBarcodeRange">
                                        <template #icon>
                                            <FastForwardOutlined />
                                        </template>
                                        Preview Gift Check Barcode Range
                                    </a-button>
                                </div>
                            </div>
                        </a-card>
                    </a-card>
                </a-col>

            </a-row>
        </a-tab-pane>
        <a-tab-pane key="2">
            <template #tab>
                <span>
                    <android-outlined />
                    Gc Barcode List
                </span>
            </template>
            <!-- {{ barcodes }} -->
            <a-table size="small" :data-source="barcodes" :columns="columns" bordered></a-table>
        </a-tab-pane>
    </a-tabs>

</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import pickBy from "lodash/pickBy";
import { notification } from 'ant-design-vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
export default {
    layout: AuthenticatedLayout,
    props: {
        record: Object,
        barcodes: Object,
        docs: Object,
        custname: String,
    },
    data() {
        return {
            formByRange: {
                barcodeEnd: null,
                barcodeStart: null,
                custname: this.custname
            },
            formBarcode: {
                barcode: null,
                custname: this.custname
            },

            form: useForm({
                username: null,
                password: null,
            }),

            activeKey: null,
            activeKey1: '1',
            errorBarcode: {},
            errorByRange: {},
            isByBarcode: false,
            isByRange: false,
            byBarcode: false,
            byRange: false,
            isSubmitting: false,
            response: {},
            error: {},

            columns: [
                {
                    title: 'Barcodes',
                    dataIndex: 'spexgcemp_barcode',
                },
                {
                    title: 'Denomination',
                    dataIndex: 'spexgcemp_denom',
                },
                {
                    title: 'Complete Name',
                    dataIndex: 'completename',
                },
            ]
        }
    },
    methods: {

        openManKey(key) {
            if (key === '1') {
                this.isByBarcode = true;
            } else {
                this.isByRange = true;

            }
        },
        managersKey(key) {
            axios.post(route('manager.managers.key'), {
                username: this.form.username,
                password: this.form.password
            }).then(response => {
                this.response = response.data;
                notification[response.data.status]({
                    message: response.data.title,
                    description: response.data.msg,
                });
                if (response.data.status == 'success') {
                    this.isSubmitting = false;

                    if (key === '1') {
                        this.byBarcode = true;
                        this.isByBarcode = false;
                        this.byRange = false;
                        this.form.reset();
                        this.response = [];
                        this.error = [];
                    } else {
                        this.isByRange = false;
                        this.byRange = true;
                        this.byBarcode = false;
                        this.form.reset();
                        this.response = [];
                        this.error = [];
                    }

                }
                if (response.data.status == 'error') {
                    this.isSubmitting = false;
                }
            }).catch(errors => {
                if (errors.response && errors.response.status === 422) {
                    this.error = errors.response.data.errors;
                } else {
                    console.error('Error:', errors.message);
                }

                this.isSubmitting = false;
            })
        },
        printBarcodeRange() {
            this.$inertia.get(route('custodian.check.print.barcode'), {
                ...pickBy(this.formByRange),
                status: '2',
                id: this.record.special.spexgc_id
            },

                {
                    onSuccess: (response) => {
                        console.log(response.props.flash.status)
                        notification[response.props.flash.status]({
                            message: response.props.flash.title,
                            description: response.props.flash.msg,
                        });
                    },
                    onError: (errors) => {
                        this.errorByRange = errors;
                    },
                    preserveState: true,
                })
        },
        printBarcode() {
            this.$inertia.get(route('custodian.check.print.barcode'), {
                ...pickBy(this.formBarcode),
                status: '1',
                id: this.record.special.spexgc_id
            }, {
                onSuccess: (response) => {
                    notification[response.props.flash.status]({
                        message: response.props.flash.title,
                        description: response.props.flash.msg,
                    })
                },
                onError: (errors) => {
                    this.errorBarcode = errors;
                },
                preserveState: true,
            })
        }
    }
}

</script>
