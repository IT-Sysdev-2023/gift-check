<template>
    <a-card>
        <span style="margin-left: 50%; color:#1e90ff; font-weight: bold;">
            <LikeOutlined />

            APPROVED GC REPORTS
        </span>
        <div>

            <a-card style="width: 85%; margin-left: 16%; border: 1px solid #dcdcdc">
                <a-tabs>
                    <a-tab-pane key="1">
                        <template #tab>
                            <span style="font-weight: bold;">
                                <CustomerServiceOutlined />
                                Per Customer
                            </span>
                        </template>
                        <a-card style="margin-top: 5px;">


                            <span style="font-weight: bold;">
                                <a-button @click="generatePdf" style="background-color:#b22222; color:white ">
                                    <FilePdfOutlined />
                                    Generate PDF
                                </a-button>
                            </span>
                            <span style="font-weight: bold; margin-left: 3%;">
                                <a-button @click="generateExcel" style="background-color:green; color:white ">
                                    <FileExcelOutlined />
                                    Generate EXCEL
                                </a-button>
                            </span>
                            <span style="font-weight: bold; margin-left: 30%;">
                                Search:
                                <a-input allow-clear v-model:value="spgcApprovedSearchPerCustomer" placeholder="Input search here!"
                                    style="width: 20%; max-width: 30%; min-width: 30%; border: 1px solid #1e90ff" />
                            </span>
                            <!-- <span style="font-weight: bold; margin-left: 3%;">
                                <a-button @click="genEx" style="background-color:green; color:white ">
                                    <FileExcelOutlined />
                                    Generate EXCEL Dummy
                                </a-button>
                            </span> -->

                            <div style="color:red; margin-left: 40%;">
                                Table showing per customer
                            </div>
                            <div style="margin-top: 20px;">
                                <!-- {{ dataCustomer }}  -->
                                <a-table :columns="perCustomerTable" :data-source="records.dataCustomer.data"
                                    :pagination="false" size="small">
                                </a-table>
                            </div>
                            <pagination :datarecords="records.dataCustomer" class="mt-5" />

                        </a-card>
                    </a-tab-pane>
                    <a-tab-pane key="2">
                        <template #tab>
                            <span style="font-weight: bold;">
                                <BarcodeOutlined />
                                Per Barcode
                            </span>
                        </template>
                        <a-card style="margin-top: 5px;">
                            <span style="font-weight: bold;">
                                <a-button @click="generatePdf" style="background-color:#b22222; color:white ">
                                    <FilePdfOutlined />
                                    Generate PDF
                                </a-button>
                            </span>
                            <span style="font-weight: bold; margin-left: 3%;">
                                <a-button @click="generateExcelPerBarcode" style="background-color:green; color:white ">
                                    <FileExcelOutlined />
                                    Generate EXCEL
                                </a-button>
                            </span>
                            <span style="font-weight: bold; margin-left: 30%;">
                                Search:
                                <a-input allow-clear v-model:value="spgcApprovedSearch" placeholder="Input search here!"
                                    style="width: 20%; max-width: 30%; min-width: 30%; border: 1px solid #1e90ff" />
                            </span>
                            <div style="color:red; margin-left: 40%;">
                                Table showing per barcode
                            </div>
                            <div style="margin-top: 20px;">
                                <a-table :columns="perBarcodeTable" :data-source="records.dataBarcode.data"
                                    :pagination="false" size="small">
                                </a-table>
                            </div>
                            <pagination :datarecords="records.dataBarcode" class="mt-5" />


                        </a-card>
                    </a-tab-pane>
                </a-tabs>
            </a-card>

        </div>

        <a-card style="width:15%; border: 1px solid #dcdcdc; position: absolute; top: 45px;">

            <div style="font-weight: bold; font-size: small;">
                <LikeOutlined />
                Approved GC Reports

            </div>

            <div style="font-weight: bold; margin-top: 30px;">Start Date:</div>
            <div>
                <a-form-item for="spgcStartDate" :validate-status="spgcform.errors.spgcStartDate ? 'error' : ''"
                    :help="spgcform.errors.spgcStartDate">
                    <a-date-picker allow-clear v-model:value="spgcform.spgcStartDate" style=" width:100%;" />
                </a-form-item>
            </div>
            <div style="font-weight: bold;">End Date:</div>
            <div>
                <a-form-item for="spgcEndDate" :validate-status="spgcform.errors.spgcEndDate ? 'error' : ''"
                    :help="spgcform.errors.spgcEndDate">
                    <a-date-picker allow-clear v-model:value="spgcform.spgcEndDate" style=" width: 100%;" />
                </a-form-item>
            </div>

            <div style="margin-top: 15px;">
                <a-button style="background-color: #1e90ff; color: white; width: 100%; font-size: 1em;"
                    @click="spgcSubmit">
                    <SendOutlined /> Submit
                </a-button>
            </div>
            <div style="margin-top: 15%;">
                <div style="font-weight: bold; color:#1e90ff">
                    Date Selected:
                </div>
                <div style="margin-top: 5px;">
                    <span style="color:red;">
                        <div>
                            <span style="color:green; font-weight: bold;">
                                FROM:
                            </span>
                            <span style="margin-left: 5px;">
                                {{ records.fromDate }}
                            </span>
                        </div>
                        <span style="color:green; font-weight: bold;">
                            TO:
                        </span>
                        <span style="margin-left: 5px;">
                            {{ records.toDate }}
                        </span>
                        <!-- {{ finalDateSelectedExcel }} -->
                    </span>
                </div>
            </div>
        </a-card>

    </a-card>



    <!-- {{ dataBarcode }} -->
    <!-- {{ search }} -->
    <!-- {{ dataCustomer }} -->
    <!-- {{ periodcover }}  -->
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
// import dayjs from 'dayjs';
import Pagination from '@/Components/Pagination.vue';
import { ExclamationCircleOutlined, WindowsFilled } from '@ant-design/icons-vue';
import { createVNode } from 'vue';
import { Modal } from 'ant-design-vue';

export default {
    components: { Pagination },
    layout: AuthenticatedLayout,
    props: {
        records: Object,
    },
    data() {
        return {

            spgcApprovedSearch: this.dataBarcode,
            spgcApprovedSearchPerCustomer: this.dataCustomer,
            spgcform: {
                spgcStartDate: '',
                spgcEndDate: '',

                errors: {}
            },

            perCustomerTable: [
                {
                    title: 'Date Requested',
                    dataIndex: 'datereq'
                },
                {
                    title: 'Company',
                    dataIndex: 'spcus_companyname'
                },
                {
                    title: 'Approval #',
                    dataIndex: 'spexgc_num'
                },
                {
                    title: 'Total Amount',
                    dataIndex: 'totdenom'
                }

            ],
            perBarcodeTable: [
                {
                    title: 'Date Requested',
                    dataIndex: 'datereq'
                },
                {
                    title: 'Barcode',
                    dataIndex: 'spexgcemp_barcode'
                }, {
                    title: 'Denom',
                    dataIndex: 'spexgcemp_denom'
                }, {
                    title: 'Customer',
                    dataIndex: 'customer_name'
                },
                {
                    title: 'Approval #',
                    dataIndex: 'spexgc_num'
                }, {
                    title: 'Date Approved',
                    dataIndex: 'daterel'
                }
            ]
        };
    },
    watch: {
        spgcApprovedSearch(search) {
            console.log(search);
            const formData = {
                search: search,
                spgcStartDate: this.records.fromDate,
                spgcEndDate: this.records.toDate
            };

            this.$inertia.get(route('storeaccounting.SPGCApproved', formData), {
                
            }, {
                preserveState: true
            });
        },
        spgcApprovedSearchPerCustomer(search) {
            console.log(search);
            const perCustomerSearch = {
                customerSearch: search,
                spgcStartDate: this.records.fromDate,
                spgcEndDate: this.records.toDate
            };
            this.$inertia.get(route('storeaccounting.SPGCApproved', perCustomerSearch), {
                
            }, {
                preserveState:true
            });
        }

    },

    methods: {
        spgcSubmit() {
            // alert('kanding')
            this.spgcform.errors = {};
            const { spgcStartDate, spgcEndDate } = this.spgcform;

            const startDate = spgcStartDate ? spgcStartDate.format('YYYY-MM-DD') : '';
            const endDate = spgcEndDate ? spgcEndDate.format('YYYY-MM-DD') : '';

            if (!startDate) this.spgcform.errors.spgcStartDate = 'Start date field is required.';
            if (!endDate) this.spgcform.errors.spgcEndDate = 'End date field is required.';

            if (this.spgcform.errors.spgcStartDate || this.spgcform.errors.spgcEndDate) {
                return;
            }

            const submitData = {
                spgcStartDate: startDate,
                spgcEndDate: endDate,
            };


            console.log('Submitting with values:', submitData);
            this.$inertia.get(route('storeaccounting.SPGCApproved', submitData));


        },
        genEx() {
            window.location.href = route('storeaccounting.dummy', {
                datatype: 'sending',
            });
        },

        generatePdf() {
            Modal.confirm({
                title: 'Notification',
                icon: createVNode(ExclamationCircleOutlined),
                content: 'UNDER MAINTENANCE, STAY TUNE FOR UPCOMING UPDATES!',
                okText: 'Yes',
                okType: 'danger',
                cancelText: 'No',
                onOk() {
                    console.log('Okay');
                },
                onCancel() {
                    console.log('Cancel');
                },
            });
        },
        generateExcel() {
            Modal.confirm({
                title: 'Confirmation',
                icon: createVNode(ExclamationCircleOutlined),
                content: 'Are you sure you want to generate EXCEL per customer?',
                okText: 'Yes',
                okType: 'danger',
                cancelText: 'No',
                onOk: () => {
                    window.location.href = route('storeaccounting.SPGCApprovedExcel', {
                        startDate: this.records.fromDate,
                        endDate: this.records.toDate
                    })
                },
                onCancel() {
                    console.log('Cancel');
                },
            });
        },
        generateExcelPerBarcode() {
            Modal.confirm({
                title: 'Confirmation',
                icon: createVNode(ExclamationCircleOutlined),
                content: 'Are you sure you want to generate EXCEL per barcode?',
                okText: 'Yes',
                okType: 'danger',
                cancelText: 'No',
                onOk: () => {
                    window.location.href = route('storeaccounting.SPGCApprovedExcel', {
                        startDate: this.records.fromDate,
                        endDate: this.records.toDate
                    })
                },
                onCancel() {
                    console.log('Cancel');
                },
            });
        }

    }
}
</script>
