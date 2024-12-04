<template>
    <a-card>
        <a-card style="width: 59%; margin-left: 42%;">
            <a-tabs>
                <a-tab-pane key="1">
                    <template #tab>
                        <span style="font-weight: bold;">
                            <!-- <span style="color: green;">
                                <DatabaseOutlined />
                            </span> -->
                            Tagbilaran
                        </span>
                    </template>

                    <span style="margin-left: 60%; font-weight: bold;">
                        <a-input-search allow-clear v-model:value="tagbilaranSearch" placeholder="Input search here!"
                            style="width: 40%;" />
                    </span>
                    <div style="font-weight: bold; margin-top: 10px;">
                        <span style="margin-left: 40%;">Table Showing Tagbilaran</span>
                    </div>
                    <div style="margin-top: 10px;">
                        <a-table :columns="varianceTable" :data-source="variance.tagbilaranData.data"
                            :pagination="false" size="small">
                        </a-table>
                        <pagination :datarecords="variance.tagbilaranData" class="mt-5" />
                    </div>

                </a-tab-pane>
                <a-tab-pane key="2">
                    <template #tab>
                        <span style="font-weight: bold;">
                            <!-- <span style="color: green;">
                                <DatabaseOutlined />
                            </span> -->
                            Talibon
                        </span>
                    </template>
                    <!-- <a-button style="background-color: green; color:white; margin-top: 10px;"
                        @click="SelectCustomerName">
                        <FileExcelOutlined />
                        Generate Excel
                    </a-button> -->
                    <span style="margin-left: 60%; font-weight: bold;">
                        <a-input-search allow-clear v-model:value="talibonSearch" placeholder="Input search here!"
                            style="width: 40%;" />
                    </span>
                    <div style="font-weight: bold; margin-top: 10px;">
                        <span style="margin-left: 40%;">Table Showing Tubigon</span>
                    </div>
                    <div style="margin-top: 10px;">
                        <a-table :columns="talibonData" :data-source="variance.talibonData.data" :pagination="false"
                            size="small">

                        </a-table>
                        <pagination :datarecords="variance.talibonData" class="mt-5" />
                    </div>

                </a-tab-pane>
            </a-tabs>
        </a-card>
        <a-button style="background-color: green; color:white; margin-top: 10px; margin-left: 85%;"
            @click="SelectCustomerName">
            <FileExcelOutlined />
            Generate Excel
        </a-button>

        <a-card style="width: 40%; position: absolute; top:25px;">
            <span style="margin-left: 35%; font-weight: bold; font-size: 15px;">
                <span>
                    <DatabaseOutlined />
                </span>
                CHECK VARIANCE
            </span>
            <div style="font-weight: bold; margin-top: 20px;">
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
            <div style="margin-top: 15px;">
                <span v-if="variance.formatCusName" style="color:green; font-weight: bold;">
                    Selected Customer Name:
                </span>
                <span style="color:red; margin-left: 5px; text-decoration: underline;">
                    {{ this.variance.formatCusName }}
                </span>
                <span v-if="!variance.formatCusName" style="color:red">
                    No Selected Customer Name !
                </span>
            </div>
            <a-button style="background-color:#1e90ff; color:white; margin-top: 10px;" @click="selectButton">
                <FileExcelOutlined />
                Select
            </a-button>
        </a-card>
    </a-card>
    <!-- {{ variance.selectedCustomer }} -->

</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { CloseSquareFilled, ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { createVNode } from 'vue';
import { Modal, message } from 'ant-design-vue';
import Pagination from '@/Components/Pagination.vue';
import { notification } from 'ant-design-vue';

export default {
    components: { Pagination },
    layout: AuthenticatedLayout,
    props: {
        companyNameList: {
            type: Array,
            required: true,
        },
        // customer: Object
        variance: Object
    },
    data() {
        return {
            talibonSearch: this.variance.talibonSearch,
            tagbilaranSearch: this.variance.tagbSearch,
            varianceData: this.$inertia.form({
                customerName: '',
                errors: {}
            }),
            selectedFormat: '',
            varianceTable: [
                {
                    title: 'Barcode',
                    dataIndex: 'barcode'
                },
                {
                    title: 'Denomination',
                    dataIndex: 'denom'
                },
                {
                    title: 'Customer Name',
                    dataIndex: 'cusname'
                },
                {
                    title: 'Verify Date',
                    dataIndex: 'verifydate'
                },
                {
                    title: 'Store',
                    dataIndex: 'store'
                },
                {
                    title: 'Transaction No',
                    dataIndex: 'transno'
                },
            ],
            talibonData: [
                {
                    title: 'Barcode',
                    dataIndex: 'barcode'
                },
                {
                    title: 'Denomination',
                    dataIndex: 'denom'
                },
                {
                    title: 'Customer Name',
                    dataIndex: 'cusname'
                },
                {
                    title: 'Verify Date',
                    dataIndex: 'verifydate'
                },
                {
                    title: 'Store',
                    dataIndex: 'store'
                },
                {
                    title: 'Transaction No',
                    dataIndex: 'transno'
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
        tagbilaranSearch(search) {
            console.log(search);
            const tagbSearch = {
                tagbSearch: search,
                customerName: this.variance.selectedCustomer,
                formatCusName: this.variance.formatCusName,
            }
            this.$inertia.get(route('storeaccounting.CheckVariance', tagbSearch), {
            }, {
                preserveState: true
            })
        },
        talibonSearch(search) {
            console.log(search);
            const talibonSearch = {
                talibonSearch: search,
                customerName: this.variance.selectedCustomer,
                formatCusName: this.variance.formatCusName,
            }
            this.$inertia.get(route('storeaccounting.CheckVariance', talibonSearch), {
                
            }, {
                preserveState: true
            })
        }
    },
    methods: {

        selectButton() {

            this.varianceData.errors = {};
            if (!this.varianceData.customerName) {
                this.varianceData.errors.customerName = "Customer Name field is required";
                return;
            }
            const data = {
                customerName: this.varianceData.customerName,
                formatCusName: this.selectedFormat
            }
            this.$inertia.get(route('storeaccounting.CheckVariance', data))
        },

        SelectCustomerName() {
            if (!this.variance.selectedCustomer && !this.variance.formatCusName) {
                notification.warning({
                    message: 'Customer field required',
                    description:
                        'Please select customer name first before generating',
                });
                return;
            }
            const varianceData = {
                customerName: this.variance.selectedCustomer,
                formatCusName: this.variance.formatCusName,
            };

            Modal.confirm({
                title: 'Confirmation',
                content: 'Are you sure you want to generate EXCEL?',
                okText: 'Yes',
                cancelText: 'No',
                onOk: () => {
                    const hide = message.loading('Generating in progress..', 0)

                    window.location.href = route('storeaccounting.varianceExcelExport', varianceData);

                    setTimeout(hide, 1500);
                },
                onCancel() {
                    console.log('Cancel');
                },
            });
        }
    }
};
</script>
