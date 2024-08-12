<template>
    <div v-if="Object.keys(record).length">

        <a-row :gutter="[16, 16]">
            <a-col :span="10">
                <a-card>
                    <a-table :data-source="denomination" :columns="columns" size="small" :pagination="false">
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.key == 'qty'">
                                <a-input class="text-center" :value="record.qty ?? 0" readonly />
                            </template>
                            <template v-if="column.key == 'valid'">
                                <a-input class="text-center" value="0" readonly />
                            </template>
                        </template>
                    </a-table>
                </a-card>
                <a-row class="mt-6">
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
                        <div class="flex justify-end">
                            <a-button class="mb-2">
                                <template #icon>
                                    <UnorderedListOutlined />
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

                        <a-button block class="mb-2">
                            Validate By Barcode
                        </a-button>
                        <a-button block class="mb-2">
                            Scanned Gc List
                        </a-button>
                    </a-col>

                    <validate-by-range v-model:open="openRangeBarcode" :recnum="recnum"  :date="date"/>
                </a-row>
            </a-col>
            <a-col :span="14">
                <setup-details :record="record"/>
            </a-col>
        </a-row>
    </div>
    <div v-else>
        <not-found-result :requis="reqid" />
    </div>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { BarcodeOutlined, LockOutlined, LoginOutlined } from '@ant-design/icons-vue';
import { notification } from 'ant-design-vue';
import axios from 'axios';

export default {
    layout: AuthenticatedLayout,

    props: {
        denomination: Object,
        columns: Array,
        record: Object,
        recnum: Number,
        reqid: Number,
        date: String,
    },
    data() {
        return {
            activeKey: null,
            byRange: false,
            form: {
                username: null,
                password: null,
            },
            isSubmitting: false,
            error: {},
            response: {},
            openRangeBarcode: false,
            isManKey: false,
        }
    },
    methods: {

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

        }
    }
}
</script>
