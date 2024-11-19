<template>
    <a-tabs>
        <a-tab-pane key="1">
            <template #tab>
                <span>
                    <CheckOutlined />
                    Check Variances
                </span>
            </template>
            <a-card>
                <div style="font-weight: bold;">
                    Customer Name:
                </div>

                <a-form-item :validate-status="varianceData.errors.customerName ? 'error' : ''"
                    :help="varianceData.errors.customerName">

                    <a-select v-model:value="varianceData.customerName" style="width: 40%;">
                        <a-select-option v-for="item in companyNameList" :key="item.spcus_id" :value="item.spcus_id">
                            {{ `${item.spcus_companyname} * ${item.spcus_acctname}` }}
                        </a-select-option>
                    </a-select>
                </a-form-item>
                <div v-if="formatted" style="margin-top: 20px;">
                    <span style="color:#1e90ff; font-weight: bold;">
                        Selected Customer Name:
                    </span>
                    <span style="color:red; margin-left: 5px; text-decoration: underline;">
                        {{ formatted }}
                    </span>
                </div>
                <a-button style="background-color: green; color:white; margin-top: 10px;" @click="SelectCustomerName">
                    <FileExcelOutlined />
                    Generate Excel
                </a-button>
            </a-card>
        </a-tab-pane>
    </a-tabs>
    <!-- {{ formatted }} -->
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { createVNode } from 'vue';
import { Modal, message } from 'ant-design-vue';
import { notification } from 'ant-design-vue';


export default {
    layout: AuthenticatedLayout,
    props: {
        companyNameList: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            varianceData: this.$inertia.form({
                customerName: '',
                errors: {}
            }),
            selectedFormat: '',
        }
    },
    computed: {
        formatted() {
            const selectedCustomer = this.companyNameList.find(
                (item) => item.spcus_id === this.varianceData.customerName
            );
            return selectedCustomer
                ? `${selectedCustomer.spcus_companyname} - ${selectedCustomer.spcus_acctname}`
                : '';
        },
    },
    watch: {
        'varianceData.customerName'() {
            this.selectedFormat = this.formatted;
        },
    },
    methods: {
        SelectCustomerName() {
            this.varianceData.errors = {};

            if (!this.varianceData.customerName) {
                this.varianceData.errors.customerName = "Customer Name field is required";
            }
            if (!this.varianceData.customerName) {
                const openNotificationWithIcon = (type) => {
                    notification[type]({
                        message: 'File Selection Required',
                        description: 'Please select customer name first before generating',
                        placement: 'topRight'
                    });
                };
                openNotificationWithIcon('warning');
                return;
            }
           
            const varianceData = {
                customerName: this.varianceData.customerName,
                formatCusName: this.selectedFormat,
            };

            Modal.confirm({
                title: 'Confirmation',
                icon: createVNode(ExclamationCircleOutlined),
                content: 'Are you sure you want to generate EXCEL?',
                okText: 'Yes',
                okType: 'danger',
                cancelText: 'No',
                onOk: () => {
                    const key = 'spgcSubmitMessage';
                    message.loading({
                        content: 'Generating...',
                        key,
                    });
                    setTimeout(() => {
                        message.success({
                            content: 'Generated successfully!',
                            key,
                            duration: 10,
                        });
                    }, 1000);
                    window.location.href = route('storeaccounting.varianceExcelExport', varianceData);
                },
                onCancel() {
                    console.log('Cancel');
                },
            });
        },
    },
};
</script>
