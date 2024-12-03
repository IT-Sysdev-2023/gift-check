<template>
    <a-card>
        <span style="margin-left: 45%; font-weight: bold; font-size: 15px;">
            APPROVED GC REPORTS
        </span>
        <div>

            <a-card style="width: 85%; margin-left: 16%; border: 1px solid #dcdcdc">
                <a-tabs>
                    <a-tab-pane key="1">
                        <template #tab>
                            <span style="font-weight: bold;">
                                <FilePdfOutlined />
                                PDF PER CUSTOMER
                            </span>
                        </template>
                        <a-card style="margin-top: 5px;">

                            <span style="font-weight: bold; margin-left: 60%;">
                                <a-input-search allow-clear v-model:value="pdfPerCustomerSearch"
                                    placeholder="Input search here!" style="width: 35%;" />
                            </span>
                            <div style="margin-top: 10px;margin-left: 30px;">
                                <span style="color:red; font-style: oblique;">
                                    <span v-if="message">
                                        <WarningOutlined />
                                    </span>
                                    {{ this.message }}
                                </span>
                            </div>
                            <div>
                                <span style="font-weight: bold; margin-left: 30%;">
                                    Table showing PDF per customer
                                </span>
                            </div>
                            <div style="margin-top: 20px;">

                                <a-table :columns="pdfPerCustomer" :data-source="records.pdfPerCustomer.data"
                                    :pagination="false" size="small">
                                </a-table>
                            </div>
                            <pagination :datarecords="records.pdfPerCustomer" class="mt-5" />
                        </a-card>
                        <div style="margin-left: 80%;">
                            <span style="font-weight: bold; margin-left: 3%;">
                                <a-button @click="generatePdf" style="background-color:#b22222; color:white ">
                                    <FilePdfOutlined />
                                    Generate PDF
                                </a-button>
                            </span>
                        </div>
                    </a-tab-pane>
                    <!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

                    <a-tab-pane key="2">
                        <template #tab>
                            <span style="font-weight: bold;">
                                <FilePdfOutlined />
                                PDF PER BARCODE
                            </span>
                        </template>
                        <a-card style="margin-top: 5px;">
                            <span style="font-weight: bold; margin-left: 60%;">
                                <a-input-search allow-clear v-model:value="pdfPerBarcodeSearch"
                                    placeholder="Input search here!" style="width: 35%;" />
                            </span>
                            <div style="margin-top: 10px;margin-left: 30px;">
                                <span style="color:red; font-style: oblique;">
                                    <span v-if="message">
                                        <WarningOutlined />
                                    </span>
                                    {{ this.message }}
                                </span>
                            </div>
                            <div>
                                <span style="font-weight: bold; margin-left: 30%;">

                                    Table showing PDF per barcode
                                </span>
                            </div>
                            <div style="margin-top: 20px;">
                                <!-- {{ dataCustomer }}  -->
                                <a-table :columns="pdfPerBarcode" :data-source="records.pdfPerBarcode.data"
                                    :pagination="false" size="small">
                                </a-table>
                            </div>
                            <pagination :datarecords="records.pdfPerBarcode" class="mt-5" />
                        </a-card>
                        <div style="margin-left: 80%;">
                            <span style="font-weight: bold; margin-left: 3%;">
                                <a-button @click="generatePdf" style="background-color:#b22222; color:white ">
                                    <FilePdfOutlined />
                                    Generate PDF
                                </a-button>
                            </span>
                        </div>
                    </a-tab-pane>
                    <!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

                    <a-tab-pane key="3">
                        <template #tab>
                            <span style="font-weight: bold;">
                                <FileExcelOutlined />
                                EXCEL Per Customer
                            </span>
                        </template>
                        <a-card style="margin-top: 5px;">

                            <span style="font-weight: bold; margin-left: 60%;">
                                <a-input-search allow-clear v-model:value="spgcApprovedSearchPerCustomer"
                                    placeholder="Input search here!" style="width: 35%;" />
                            </span>
                            <div style="margin-top: 10px;margin-left: 30px;">
                                <span style="color:red; font-style: oblique;">
                                    <span v-if="message">
                                        <WarningOutlined />
                                    </span>
                                    {{ this.message }}
                                </span>
                            </div>

                            <div>
                                <span style="font-weight: bold; margin-left: 30%;">

                                    Table showing EXCEL per customer
                                </span>
                            </div>
                            <div style="margin-top: 20px;">
                                <!-- {{ dataCustomer }}  -->
                                <a-table :columns="perCustomerTable" :data-source="records.dataCustomer.data"
                                    :pagination="false" size="small">
                                </a-table>
                            </div>
                            <pagination :datarecords="records.dataCustomer" class="mt-5" />

                        </a-card>
                        <span style="font-weight: bold; margin-left: 80%;">
                            <a-button @click="generateExcel" style="background-color:green; color:white ">
                                <FileExcelOutlined />
                                Generate EXCEL
                            </a-button>
                        </span>
                    </a-tab-pane>
                    <!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

                    <a-tab-pane key="4">
                        <template #tab>
                            <span style="font-weight: bold">
                                <FileExcelOutlined />
                                EXCEL Per Barcode
                            </span>
                        </template>
                        <a-card style="margin-top: 5px;">
                            <span style="font-weight: bold; margin-left: 60%;">
                                <a-input-search allow-clear v-model:value="spgcApprovedSearch" placeholder="Input search here!"
                                    style="width: 35%;" />
                            </span>
                            <div style="margin-top: 10px;margin-left: 30px;">
                                <span style="color:red; font-style: oblique;">
                                    <span v-if="message">
                                        <WarningOutlined />
                                    </span>
                                    {{ this.message }}
                                </span>
                            </div>


                            <div>
                                <span style="font-weight: bold; margin-left: 30%;">

                                    Table showing EXCEL per barcode
                                </span>
                            </div>
                            <div style="margin-top: 20px;">
                                <a-table :columns="perBarcodeTable" :data-source="records.dataBarcode.data"
                                    :pagination="false" size="small">
                                </a-table>
                            </div>
                            <pagination :datarecords="records.dataBarcode" class="mt-5" />
                        </a-card>
                        <span style="font-weight: bold; margin-left: 80%;">
                            <a-button @click="generateExcel" style="background-color:green; color:white ">
                                <FileExcelOutlined />
                                Generate EXCEL
                            </a-button>
                        </span>
                    </a-tab-pane>
                </a-tabs>
            </a-card>

        </div>

        <a-card style="width:15%; border: 1px solid #dcdcdc; position: absolute; top: 45px;">

            <div style="font-weight: bold; font-size: small;">
                <span>
                    <LikeOutlined />

                </span>
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
            <div v-if="records.fromDate" style="margin-top: 15%;">
                <div style="font-weight: bold;">
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
                        <span style="margin-left: 25px;">
                            {{ records.toDate }}
                        </span>

                        <!-- {{ finalDateSelectedExcel }} -->
                    </span>
                </div>
            </div>
            <div v-if="!records.fromDate" style="margin-top: 15%;">
                <span style="color:red;">
                    No Date Selected !
                </span>
            </div>
        </a-card>
        <!-- <a-button @click="sample">
            modal
        </a-button> -->

    </a-card>



    <!-- {{ records. }} -->
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
// import dayjs from 'dayjs';
import Pagination from '@/Components/Pagination.vue';
import { ExclamationCircleOutlined, WindowsFilled } from '@ant-design/icons-vue';
import { createVNode } from 'vue';
import { message, Modal } from 'ant-design-vue';
import { notification } from 'ant-design-vue';

export default {
    components: { Pagination },
    layout: AuthenticatedLayout,
    props: {
        records: Object,
    },
    data() {
        return {
            message: '',
            pdfPerBarcodeSearch: this.pdfPerBarcode,
            pdfPerCustomerSearch: this.pdfPerCustomer,
            messageModal: false,
            spgcApprovedSearch: this.dataBarcode,
            spgcApprovedSearchPerCustomer: this.dataCustomer,
            spgcform: {
                spgcStartDate: '',
                spgcEndDate: '',

                errors: {}
            },
            pdfPerCustomer: [
                {
                    title: 'Date Requested',
                    dataIndex: 'datereq',
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
            pdfPerBarcode: [
                {
                    title: 'Date Requested',
                    dataIndex: 'datereq'
                },
                {
                    title: 'Denomination',
                    dataIndex: 'spexgcemp_denom'
                },
                {
                    title: 'Customer',
                    dataIndex: 'spexgcemp_lname'
                },
                {
                    title: 'Barcode',
                    dataIndex: 'spexgcemp_barcode'
                },
                {
                    title: 'Approval #',
                    dataIndex: 'spexgc_num'
                },
                {
                    title: 'Date Release',
                    dataIndex: 'daterel'
                },
            ],

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
                preserveState: true
            });
        },
        pdfPerCustomerSearch(search) {
            console.log(search);
            const pdfPerCustomer = {
                pdfCustomerSearch: search,
                spgcStartDate: this.records.fromDate,
                spgcEndDate: this.records.toDate
            };
            this.$inertia.get(route('storeaccounting.SPGCApproved', pdfPerCustomer), {

            }, {
                preserveState: true
            })
        },
        pdfPerBarcodeSearch(search) {
            console.log(search);
            const pdfPerBarcode = {
                pdfPerBarcodeSearch: search,
                spgcStartDate: this.records.fromDate,
                spgcEndDate: this.records.toDate
            };
            this.$inertia.get(route('storeaccounting.SPGCApproved', pdfPerBarcode), {
                
            }, {
                preserveState: true
            })
        }

    },

    methods: {
        spgcSubmit() {
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

        generatePdf() {
            if (this.records.fromDate === null || this.records.toDate === null) {
                const openNotificationWithIcon = (type) => {
                    notification[type]({
                        message: 'File Selection Required',
                        description: 'Please select start and end date first',
                        placement: 'topRight'
                    });
                };
                openNotificationWithIcon('warning');
                return;
            }
            Modal.confirm({
                title: 'Notification',
                content: 'Are you sure you want to generate PDF?',
                okText: 'Yes',
                cancelText: 'No',
                onOk: () => {
                    const hide = message.loading('Generating in progress..', 0);
                    
                    window.location.href = route('storeaccounting.pdfApprovedSubmit', {
                        startDate: this.records.fromDate,
                        endDate: this.records.toDate
                    },);
                    setTimeout(hide, 16000);

                },
                onCancel() {
                    console.log('Cancel');
                },
            });
        },
        generateExcel() {
            if (this.records.fromDate === null || this.records.toDate === null) {
                const openNotificationWithIcon = (type) => {
                    notification[type]({
                        message: 'File Selection Required',
                        description: 'Please select start and end date first',
                        placement: 'topRight'
                    });
                };
                openNotificationWithIcon('warning');
                return;
            }
            Modal.confirm({
                title: 'Confirmation',
                content: 'Are you sure you want to generate EXCEL per CUSTOMER?',
                okText: 'Yes',
                cancelText: 'No',
                onOk: () => {
                    
                    const hide = message.loading('Generating in progress..', 0)

                    window.location.href = route('storeaccounting.SPGCApprovedExcel', {
                        startDate: this.records.fromDate,
                        endDate: this.records.toDate
                    });
                    setTimeout(hide, 2500);

                },
                onCancel() {
                    console.log('Cancel');
                },
            });
        },
    }
}
</script>
