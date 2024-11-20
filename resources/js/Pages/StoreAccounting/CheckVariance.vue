<template>

    <div style="font-weight: bold; margin-left: 15%;">
        <span>CHECK VARIANCES</span>
    </div>
    <a-card style="width: 40%; margin-top: 10px;">
        <div style="font-weight: bold;">
            Customer Name:
        </div>

        <a-form-item :validate-status="varianceData.errors.customerName ? 'error' : ''"
            :help="varianceData.errors.customerName">

            <a-select v-model:value="varianceData.customerName">
                <a-select-option v-for="item in companyNameList" :key="item.spcus_id" :value="item.spcus_id">
                    {{ `${item.spcus_companyname} * ${item.spcus_acctname}` }}
                </a-select-option>
            </a-select>
        </a-form-item>
        <div style="margin-top: 20px;">
            <span style="color:#1e90ff; font-weight: bold;">
                Selected Customer Name:
            </span>
            <span style="color:red; margin-left: 5px; text-decoration: underline;">
                {{ this.selectedFormat }}
            </span>
        </div>
        <a-button style="background-color: green; color:white; margin-top: 10px;" @click="SelectCustomerName">
            <FileExcelOutlined />
            Generate
        </a-button>
    </a-card>
    <!-- <a-card style="width: 60%; margin-left: 37%; position: absolute; top: 24px;">
            <div>
                <a-button style="background-color: green; color:white; margin-top: 10px;" @click="generateExcelButton">
                    <FileExcelOutlined />
                    Generate Excel
                </a-button>
            </div>
            <a-table :columns="varianceTable" :data-source="customer.tagbilaranData" size="small" style="margin-top: 10px;">

            </a-table>
        </a-card> -->


    <!-- {{ tagbilaranData }} -->
    <!-- {{ formatted }} -->
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { createVNode } from 'vue';
import { Modal, message } from 'ant-design-vue';
import { notification } from 'ant-design-vue';
import Pagination from '@/Components/Pagination.vue';


export default {
    components: { Pagination },
    layout: AuthenticatedLayout,
    props: {
        companyNameList: {
            type: Array,
            required: true,
        },
        customer: Object
    },
    data() {
        return {
            varianceData: this.$inertia.form({
                customerName: '',
                errors: {}
            }),
            selectedFormat: '',
            varianceTable: [
                {
                    title: 'Barcode',
                    dataIndex: 'spexgcemp_barcode'
                },
                {
                    title: 'Denomination',
                    dataIndex: 'spexgcemp_denom'
                },
                {
                    title: 'Customer Name',
                    dataIndex: 'customerName'
                },
                {
                    title: 'Verify Date',
                    dataIndex: 'vs_date'
                },
                {
                    title: 'Store',
                    dataIndex: 'store_name'
                },
                {
                    title: 'Transaction No',
                    dataIndex: 'seodtt_transno'
                },
            ],
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
        // SelectCustomerName() {
        //     this.varianceData.errors = {};

        //     if (!this.varianceData.customerName) {
        //         this.varianceData.errors.customerName = "Customer Name field is required";
        //         return;
        //     }
        //     const varianceData = {
        //         customerName: this.varianceData.customerName,
        //         formatCusName: this.selectedFormat,
        //     };

        //     Modal.confirm({
        //         title: 'Confirmation',
        //         icon: createVNode(ExclamationCircleOutlined),
        //         content: 'Are you sure you want to generate EXCEL?',
        //         okText: 'Yes',
        //         okType: 'danger',
        //         cancelText: 'No',
        //         onOk: () => {
        //             window.location.href = route('storeaccounting.CheckVariance', varianceData);
        //         },
        //         onCancel() {
        //             console.log('Cancel');
        //         },
        //     });
        // },
        SelectCustomerName() {
            this.varianceData.errors = {};
            if (!this.varianceData.customerName) {
                this.varianceData.errors.customerName = "Customer Name field is required";
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
                    const hide = message.loading('Generating in progress..', 0)

                    window.location.href = route('storeaccounting.varianceExcelExport', varianceData, {

                    });
                    setTimeout(hide, 2000);
                },

                onCancel() {
                    console.log('Cancel');
                },
            });
        }
    },
};
</script>
