<template>
    <a-card>
        <span style="margin-left: 45%; font-weight: bold">
            <span style="color:green">
                <ExportOutlined />
            </span>
            RELEASE GC REPORTS
        </span>
        <a-card style="width: 85%; margin-left: 16%; border: 1px solid #dcdcdc;">
            <a-tabs>
                <a-tab-pane key="1">
                    <template #tab>
                        <span style="font-weight: bold;">
                            <span style="color:red">
                                <FilePdfOutlined />
                            </span>
                            PDF Per Customer
                        </span>
                    </template>

                    <a-card style="margin-top: 5px;">

                        <span style="font-weight: bold; margin-left: 60%;">
                            Search:
                            <a-input allow-clear v-model:value="pdfPerCustomerSearch" placeholder="Input search here!"
                                style="width: 20%; max-width: 30%; min-width: 30%; border: 1px solid #1e90ff" />
                        </span>
                        <div style="margin-top: 10px;margin-left: 30px;">
                            <span style="color:red; font-style: oblique;">
                                <span v-if="message">
                                    <WarningOutlined />
                                </span>
                                {{ this.message }}
                            </span>
                        </div>
                        <div style="padding: 10px; background-color: #b0c4de;">
                            <span style="font-weight: bold; margin-left: 30%;">
                                <span style="color:red">
                                    <FilePdfOutlined />
                                </span>
                                Table showing PDF per customer
                            </span>
                        </div>
                        <div style="margin-top: 20px;">
                            <a-table :columns="pdfPerCustomer" :data-source="data.pdfPerCustomer.data"
                                :pagination="false" size="small">
                            </a-table>
                            <pagination :datarecords="data.pdfPerCustomer" class="mt-5" />
                        </div>

                    </a-card>
                    <span style="font-weight: bold;  margin-left: 80%;">
                        <a-button @click="perCustumerPdf" style="background-color:#b22222; color:white ">
                            <FilePdfOutlined />
                            Generate PDF
                        </a-button>
                    </span>
                </a-tab-pane>
                <!-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                <a-tab-pane key="2">
                    <template #tab>
                        <span style="font-weight: bold;">
                            <span style="color:red">
                                <FilePdfOutlined />
                            </span>
                            PDF Per Barcode
                        </span>
                    </template>

                    <a-card style="margin-top: 5px;">

                        <span style="font-weight: bold; margin-left: 60%;">
                            Search:
                            <a-input allow-clear v-model:value="pdfPerBarcodeSearch" placeholder="Input search here!"
                                style="width: 20%; max-width: 30%; min-width: 30%; border: 1px solid #1e90ff" />
                        </span>
                        <div style="margin-top: 10px;margin-left: 30px;">
                            <span style="color:red; font-style: oblique;">
                                <span v-if="message">
                                    <WarningOutlined />
                                </span>
                                {{ this.message }}
                            </span>
                        </div>
                        <div style="padding: 10px; background-color: #b0c4de;">
                            <span style="font-weight: bold; margin-left: 30%;">
                                <span style="color:red">
                                    <FilePdfOutlined />
                                </span>
                                Table showing PDF per barcode
                            </span>
                        </div>
                        <div style="margin-top: 20px;">
                            <a-table :columns="pdfPerBarcode" :data-source="data.pdfPerBarcode.data" :pagination="false"
                                size="small">

                            </a-table>
                            <pagination :datarecords="data.pdfPerBarcode" class="mt-5" />
                        </div>
                    </a-card>
                    <span style="font-weight: bold;  margin-left: 80%;">
                        <a-button @click="perCustumerPdf" style="background-color:#b22222; color:white ">
                            <FilePdfOutlined />
                            Generate PDF
                        </a-button>
                    </span>
                </a-tab-pane>
                <!-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                <a-tab-pane key="3">
                    <template #tab>
                        <span style="font-weight: bold;">
                            <span style="color: #006400">
                                <FileExcelOutlined />
                            </span>
                            EXCEL Per Customer
                        </span>
                    </template>
                    <a-card style="margin-top: 5px;">

                        <span style="font-weight: bold; margin-left: 60%;">
                            Search:
                            <a-input allow-clear v-model:value="spgcApprovedSearch" placeholder="Input search here!"
                                style="width: 20%; max-width: 30%; min-width: 30%; border: 1px solid #1e90ff" />
                        </span>
                        <div style="margin-top: 10px;margin-left: 30px;">
                            <span style="color:red; font-style: oblique;">
                                <span v-if="message">
                                    <WarningOutlined />
                                </span>
                                {{ this.message }}
                            </span>
                        </div>
                        <div style="padding: 10px; background-color: #b0c4de;">
                            <span style="font-weight: bold; margin-left: 30%;">
                                <span style="color:green">
                                    <FileExcelOutlined />
                                </span>
                                Table showing EXCEL per customer
                            </span>
                        </div>
                        <div style="margin-top: 20px;">
                            <a-table :columns=" perCustomerReleaseTable" :data-source="data.dataCustomer.data"
                                :pagination="false" size="small">

                            </a-table>
                            <pagination :datarecords="data.dataCustomer" class="mt-5" />
                        </div>
                    </a-card>
                    <span style="font-weight: bold; margin-left: 80%;">
                        <a-button @click="perCustomerExcel" style="background-color:green; color:white ">
                            <FileExcelOutlined />
                            Generate EXCEL
                        </a-button>
                    </span>
                </a-tab-pane>
                <!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                <a-tab-pane key="4">
                    <template #tab>
                        <span style="font-weight: bold;">
                            <span style="color: #006400">
                                <FileExcelOutlined />
                            </span>
                            EXCEL Per Barcode
                        </span>
                    </template>

                    <a-card style="margin-top: 5px; ">

                        <span style="font-weight: bold; margin-left: 60%;">
                            Search:
                            <a-input allow-clear v-model:value="spgcApprovedSearchPerBarcode"
                                placeholder="Input search here!"
                                style="width: 20%; max-width: 30%; min-width: 30%; border: 1px solid #1e90ff" />
                        </span>
                        <div style="margin-top: 10px;margin-left: 30px;">
                            <span style="color:red; font-style: oblique;">
                                <span v-if="message">
                                    <WarningOutlined />
                                </span>
                                {{ this.message }}
                            </span>
                        </div>
                        <div style="padding: 10px; background-color: #b0c4de;">
                            <span style="font-weight: bold; margin-left: 30%;">
                                <span style="color:green">
                                    <FileExcelOutlined />
                                </span>
                                Table showing EXCEL per barcode
                            </span>
                        </div>
                        <div style="margin-top: 20px;">
                            <a-table :columns="perBarcodeReleaseTable" :data-source="data.dataBarcode.data"
                                :pagination="false" size="small">

                            </a-table>
                            <pagination :datarecords="data.dataBarcode" class="mt-5" />
                        </div>
                    </a-card>
                    <span style="font-weight: bold;  margin-left: 80%;">
                        <a-button @click="perCustomerExcel" style="background-color:green; color:white ">
                            <FileExcelOutlined />
                            Generate EXCEL
                        </a-button>
                    </span>
                </a-tab-pane>

            </a-tabs>
        </a-card>


        <a-card style="width: 15%; position: absolute; top: 45px; border: 1px solid #dcdcdc;">
            <div style="font-weight: bold; font-size: small;">
                <span style="color:green">
                    <ExportOutlined />
                </span>
                Release GC Reports
            </div>
            <div style="font-weight: bold; margin-top: 30px;">
                Start Date:
            </div>
            <div>
                <a-form-item :validate-status="spgcData.errors.startDate ? 'error' : ''"
                    :help="spgcData.errors.startDate">
                    <a-date-picker allow-clear v-model:value="spgcData.startDate" style="width: 100%;">
                    </a-date-picker>
                </a-form-item>
            </div>
            <div style="font-weight: bold;">
                End Date:
            </div>
            <div>
                <a-form-item :validate-status="spgcData.errors.endDate ? 'error' : ''" :help="spgcData.errors.endDate">
                    <a-date-picker allow-clear v-model:value="spgcData.endDate" style="width: 100%;">

                    </a-date-picker>
                </a-form-item>
            </div>
            <div style="margin-top: 15px;">
                <a-button style="background-color: #1e90ff; color:white; width: 100%; font-size: 1em;"
                    @click="submitReleaseButton">
                    <SendOutlined /> Submit
                </a-button>
            </div>
            <div style="margin-top: 15%;">
                <div style=" font-weight: bold;">
                    Date Selected:
                </div>
                <div style="margin-top: 5px;">
                    <span style="color:red;">
                        <div>
                            <span style="color:green; font-weight: bold;">
                                FROM:
                            </span>
                            <span style="margin-left: 5px;">
                                {{ data.fromDate }}
                            </span>
                        </div>
                        <span style="color:green; font-weight: bold;">
                            TO:
                        </span>
                        <span style="margin-left: 25px;">
                            {{ data.endDate }}
                        </span>
                    </span>
                </div>

            </div>

        </a-card>
    </a-card>

    <!-- {{ dataCustomer }} -->
    <!-- {{ dataBarcode }} -->

</template>
<script>
// import { defineComponent } from '@vue/composition-api'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
// import dayjs from 'dayjs';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { createVNode } from 'vue';
import { Modal } from 'ant-design-vue';
import Pagination from '@/Components/Pagination.vue';
import { notification } from 'ant-design-vue';
import { message } from 'ant-design-vue';



export default {
    components: { Pagination },
    layout: AuthenticatedLayout,
    props: {
        data: Object
    },

    data() {
        return {
            message: '',
            pdfPerBarcodeSearch: this.pdfPerBarcode,
            pdfPerCustomerSearch: this.pdfPerCustomer,
            spgcApprovedSearchPerBarcode: '',
            spgcApprovedSearch: this.dataCustomer,
            perCustomerReleaseTable: [
                {
                    title: 'Date Requested',
                    dataIndex: 'datereq'
                },
                {
                    title: 'Company',
                    dataIndex: 'spcus_companyname'
                },
                {
                    title: 'Releasing #',
                    dataIndex: 'spexgc_num'
                },
                {
                    title: 'Total Denomination',
                    dataIndex: 'totdenom'
                }
            ],
            perBarcodeReleaseTable: [
                {
                    title: 'Date Requested',
                    dataIndex: 'dateRequest0'
                },
                {
                    title: 'Barcode',
                    dataIndex: 'spexgcemp_barcode'
                },
                {
                    title: 'Denom',
                    dataIndex: 'spexgcemp_denom'
                },
                {
                    title: 'Customer',
                    dataIndex: 'barCustomer'
                },
                {
                    title: 'Releasing #',
                    dataIndex: 'spexgc_num'
                },
                {
                    title: 'Date Approved',
                    dataIndex: 'dateRequest1'
                },

            ],
            spgcData: {
                startDate: '',
                endDate: '',
                errors: {}
            },
            isGenerating: false,
            pdfPerCustomer: [
                {
                    title: 'Date Requested ',
                    dataIndex: 'datereq'
                },
                {
                    title: 'Company',
                    dataIndex: 'spcus_companyname'
                },
                {
                    title: 'Releasing Number',
                    dataIndex: 'spexgc_num'
                },
                {
                    title: 'Denomination',
                    dataIndex: 'totdenom'
                },
                {
                    title: 'Date Release',
                    dataIndex: 'daterel'
                }
            ],
            pdfPerBarcode: [
                {
                    title: 'Date Requested',
                    dataIndex: 'datereq'
                },
                {
                    title: 'Barcode',
                    dataIndex: 'spexgcemp_barcode'
                },
                {
                    title: 'Denom',
                    dataIndex: 'spexgcemp_denom'
                },
                {
                    title: 'Customer',
                    dataIndex: 'spexgcemp_lname'
                },
                {
                    title: 'Releasing #',
                    dataIndex: 'spexgc_num'
                },
                {
                    title: 'Date Approved',
                    dataIndex: 'daterel'
                },
                
            ],


        }
    },
    watch: {
        spgcApprovedSearch(search) {
            // alert(1)
            console.log(search);
            const searchData = {
                perCustomer: search,
                startDate: this.data.fromDate,
                endDate: this.data.endDate
            };
            this.$inertia.get(route('storeaccounting.SPGCRelease', searchData), {

            }, {
                preserveState: true
            });
        },
        spgcApprovedSearchPerBarcode(search) {
            console.log(search);
            const searchData = {
                perBarcode: search,
                startDate: this.data.fromDate,
                endDate: this.data.endDate
            };
            this.$inertia.get(route('storeaccounting.SPGCRelease', searchData), {

            }, {
                preserveState: true
            })
        },
        pdfPerCustomerSearch(search) {
            console.log(search);
            const pdfPerCustomerData = {
                pdfPerCustomer: search,
                startDate: this.data.fromDate,
                endDate: this.data.endDate
            };
            this.$inertia.get(route('storeaccounting.SPGCRelease', pdfPerCustomerData),{

            }, {
                preserveState: true
            })
        },
        pdfPerBarcodeSearch(search) {
            console.log(search);
            const perBarcodeData = {
                pdfPerBarcode: search,
                startDate: this.data.fromDate,
                endDate: this.data.endDate
            }
            this.$inertia.get(route('storeaccounting.SPGCRelease', perBarcodeData), {
                
            }, {
                preserveState: true
            })
        }
    },
    methods: {
        submitReleaseButton() {
            this.spgcData.errors = {};
            const { startDate, endDate } = this.spgcData;

            const releasedStartDate = startDate ? startDate.format('YYYY-MM-DD') : '';
            const releasedEndDate = endDate ? endDate.format('YYYY-MM-DD') : '';

            if (!releasedStartDate) this.spgcData.errors.startDate = "Start Date field is required"
            if (!releasedEndDate) this.spgcData.errors.endDate = "End Date field is required"

            if (this.spgcData.errors.startDate || this.spgcData.errors.endDate) {
                console.error('Form validation failed');
                return;
            }

            const submitData = {
                startDate: releasedStartDate,
                endDate: releasedEndDate,
            };

            console.log('data', submitData);

            this.$inertia.get(route('storeaccounting.SPGCRelease', submitData));
        },
        perCustumerPdf() {
            Modal.confirm({
                title: 'Confirmation',
                content: 'Are you sure you want to generate PDF?',
                okText: 'Yes',
                okType: 'danger',
                cancelText: 'No',
                onOk: () => {
                    if (this.data.fromDate === null || this.data.endDate === null) {
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
                    const hide = message.loading('Generating in progress..', 0)

                    window.location.href = route('storeaccounting.releasePdf', {
                        startDate: this.data.fromDate,
                        endDate: this.data.endDate
                    });
                    setTimeout(hide, 16000);

                },
                onCancel() {
                    console.log('Cancel');
                },
            });
        },

        perCustomerExcel() {
            Modal.confirm({
                title: 'Confirmation',
                content: 'Are you sure you want to generate EXCEL?',
                okText: 'Yes',
                okType: 'danger',
                cancelText: 'No',
                onOk: () => {
                    if (this.data.fromDate === null || this.data.endDate === null) {
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
                    const hide = message.loading('Generating in progress..', 0)
                    window.location.href = route('storeaccounting.releaseExcel', {
                        startDate: this.data.fromDate,
                        endDate: this.data.endDate
                    });
                    setTimeout(hide, 2500);

                },
                onCancel() {
                    console.log('Cancel');
                },
            });
        }
    }

}
</script>
