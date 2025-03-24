<template>
    <div v-if="Object.keys(record).length">
        <div class="flex justify-end mb-3">
            <a-button @click="() => $inertia.get(route('iad.receiving'))">
                <RollbackOutlined />
                Back to the Table
            </a-button>
        </div>
        <a-row :gutter="[16, 16]">
            <a-col :span="10">
                <a-alert v-if="errors.scanned" :message="errors.scanned" type="error" show-icon class="mb-2" />

                <a-descriptions size="small" class="mb-0 text-center" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%;" label="Received No.">{{ recnum }}</a-descriptions-item>
                </a-descriptions>
                <a-descriptions size="small" class="mb-0 text-center" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%;" label="E-Requisition No   ">0{{ reqid
                    }}</a-descriptions-item>
                </a-descriptions>

                <a-descriptions size="small" class="mb-3 text-center" layout="horizontal" bordered>
                    <a-descriptions-item style="width: 50%;" label="FAD Receiving Type"> {{ record.srr_type
                    }}</a-descriptions-item>
                </a-descriptions>

                <a-typography-text keyboard>Select Type</a-typography-text><span class="text-red-500">*required</span>
                <a-form-item has-feedback :help="errors.select" :validate-status="errors.select ? 'error' : ''">
                    <a-select ref="select" placeholder="Select Type" v-model:value="select" style="width: 100%"
                        @focus="focus" @change="handleChange">
                        <a-select-option v-if="ifPartial()" value="whole">Whole</a-select-option>
                        <a-select-option v-if="!ifPartial()" value="partial">Partials</a-select-option>
                        <a-select-option v-if="ifPartial()" value="final">Final</a-select-option>
                    </a-select>
                </a-form-item>
                <a-card>
                    <a-tabs v-model:activeKey="denomKey" size=small>
                        <a-tab-pane key="1">
                            <template #tab>
                                <span>
                                    <apple-outlined />
                                    Denomination Available
                                </span>
                            </template>
                            <a-table :data-source="denomination" :columns="columns" size="small" :pagination="false">
                                <template #bodyCell="{ column, record }">
                                    <template v-if="column.key == 'qty'">
                                        <a-input class="text-center" :value="record.qty ?? 0" readonly />
                                    </template>
                                    <template v-if="column.key == 'valid'">
                                        <a-input class="text-center" :value="record.scanned ?? 0" readonly />
                                    </template>
                                </template>
                            </a-table>
                        </a-tab-pane>
                        <a-tab-pane key="2">
                            <template #tab>
                                <span>
                                    <android-outlined />
                                    Remaining Available Item
                                </span>
                            </template>
                            <a-descriptions v-for="item in denomination" size="small" class="mb-0 text-center"
                                layout="horizontal" bordered>
                                <a-descriptions-item style="width: 50%;" :label="item.denomination">{{ item.item_remain
                                    ?? 0 }}
                                </a-descriptions-item>
                            </a-descriptions>
                        </a-tab-pane>
                    </a-tabs>
                </a-card>
                <a-row cl ass="mt-6">
                    <a-col :span="7">
                        <div class="flex justify-end">
                            <a-button class="mb-2">
                                <template #icon>
                                    <BarcodeOutlined />
                                </template>
                            </a-button>
                        </div>
                        <div class="flex justify-end">
                            <a-button class="mb-2">
                                <template #icon>
                                    <BarcodeOutlined />
                                </template>
                            </a-button>
                        </div>
                    </a-col>
                    <a-col :span="17">
                        <a-popconfirm title="Managers Key" v-model:open="isManKey" @cancel="cancel"
                            :show-cancel="false">
                            <template #description>

                                <a-alert v-if="response.status" :message="response.msg" :type="response.status"
                                    show-icon />
                                <a-form name="basic" class="mt-5" autocomplete="off" @finish="onFinish"
                                    @finishFailed="onFinishFailed">
                                    <a-form-item label="Username" name="username"
                                        :validate-status="error.username ? 'error' : ''" has-feedback
                                        :help="error.username">
                                        <a-input v-model:value="form.username" @keyup.enter="submitKey" />
                                    </a-form-item>
                                    <a-form-item label="Password" name="password"
                                        :validate-status="error.password ? 'error' : ''" has-feedback
                                        :help="error.password">
                                        <a-input-password v-model:value="form.password" @keyup.enter="submitKey" />
                                    </a-form-item>
                                </a-form>
                            </template>
                            <template #okButton>
                                <a-button type="primary" size="small" @click="submitKey" :loading="isSubmitting">
                                    <template #icon>
                                        <LockOutlined />
                                    </template>
                                    Continue?
                                </a-button>
                            </template>
                            <a-button block class="mb-2" @click="validateRange">
                                Validate Barcode By Range
                            </a-button>
                        </a-popconfirm>

                        <a-button block class="mb-2" @click="validateBarcode">
                            Validate By Barcode
                        </a-button>
                        <a-button block class="mb-2" type="primary" @click="submit" :loading="isSubmittingReq">
                            <!-- :disabled="denomination.filter((data) => data.scanned).length === 0" -->
                            Submit
                        </a-button>

                    </a-col>

                    <validate-by-range v-model:open="openRangeBarcode" :recnum="recnum" :reqid="reqid" :date="date" />
                    <validate-barcode v-model:open="openBarcode" :recnum="recnum" :reqid="reqid" :date="date" />
                </a-row>
            </a-col>
            <a-col :span="14">
                <a-tabs size="small" type="card" v-model:activeKey="activeKey">
                    <a-tab-pane key="1">
                        <template #tab>
                            <span>
                                <PaperClipOutlined />
                                Requisition Details
                            </span>
                        </template>
                        <setup-details :record="record" />
                    </a-tab-pane>
                    <a-tab-pane key="2">
                        <template #tab>
                            <span>
                                <FileProtectOutlined />
                                Scanned Gift-Check
                            </span>
                        </template>
                        <scanned-gc :scannedGc="scannedGc" />
                    </a-tab-pane>
                </a-tabs>

            </a-col>
        </a-row>
    </div>
    <div v-else>
        <not-found-result :requis="reqid" />
    </div>
</template>

<script>
import { BarcodeOutlined, LockOutlined, LoginOutlined } from '@ant-design/icons-vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { notification } from 'ant-design-vue';
import pickBy from "lodash/pickBy";
import axios from 'axios';


export default {
    layout: AuthenticatedLayout,

    props: {
        denomination: Object,
        scannedGc: Object,
        columns: Array,
        record: Object,
        recnum: Number,
        reqid: Number,
        date: String,
    },
    data() {
        return {
            openRangeBarcode: false,
            isSubmittingReq: false,
            isSubmitting: false,
            openBarcode: false,
            activeKey: '1',
            denomKey: '1',
            isManKey: false,
            response: {},
            byRange: false,
            select: null,
            errors: {},
            error: {},
            form: {
                username: null,
                password: null,
            },

        }
    },
    methods: {
        validateBarcode() {
            this.openBarcode = true;
        },
        validateRange() {
            this.byRange = true;
        },
        submitKey() {
            this.isSubmitting = true;

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
                    this.openRangeBarcode = true;
                    this.isSubmitting = false;
                    this.isManKey = false;

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
        submit() {
            this.$inertia.post(route('iad.submit.setup'), {
                data: pickBy(this.record),
                denom: pickBy(this.denomination),
                select: this.select,
                recnum: this.recnum,
                scanned: pickBy(this.scannedGc),
            }, {
                onStart: () => {
                    this.isSubmittingReq = true;
                },
                onSuccess: (response) => {
                    notification[response.props.flash.status]({
                        message: response.props.flash.title,
                        description: response.props.flash.msg,
                    });
                    this.isSubmittingReq = false;
                },
                onError: (errors) => {
                    this.errors = errors;
                    this.isSubmittingReq = false;
                }
            })
        },
        ifPartial() {
            return this.denomination
                .filter((data) => data.scanned)
                .reduce((sum, data) => sum + data.scanned, 0)

                == this.denomination
                    .filter((data) => data.item_remain)
                    .reduce((sum, data) => sum + data.item_remain, 0)
        }
    }
}
</script>
