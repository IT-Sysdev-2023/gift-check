<template>
    <a-card>
        <span style="margin-left: 50%; color: blue; font-weight: bold">
            <ExportOutlined />
            RELEASE GC REPORTS
        </span>
        <a-card style="width: 85%; margin-left: 16%; border: 1px solid #dcdcdc;">
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
                            <a-button @click="perCustumerPdf" style="background-color:#b22222; color:white ">
                                <FilePdfOutlined />
                                Generate PDF
                            </a-button>
                        </span>
                        <span style="font-weight: bold; margin-left: 3%;">
                            <a-button @click="perCustomerExcel" style="background-color:green; color:white ">
                                <FileExcelOutlined />
                                Generate EXCEL
                            </a-button>
                        </span>
                        <span style="font-weight: bold; margin-left: 30%;">
                            Search:
                            <a-input allow-clear v-model:value="spgcApprovedSearch" placeholder="Input search here!"
                                style="width: 20%; max-width: 30%; min-width: 30%; border: 1px solid #1e90ff" />
                        </span>
                        <div style="margin-left: 40%;">
                            <span style="color: red;">
                                Table showing per customer
                            </span>
                        </div>
                        <div style="margin-top: 20px;">
                            <a-table :columns="perCustomerReleaseTable" :data-source="data.dataCustomer.data"
                                :pagination="false" size="small">

                            </a-table>
                            <pagination :datarecords="data.dataCustomer" class="mt-5" />
                        </div>
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
                            <a-button @click="perCustumerPdf" style="background-color:#b22222; color:white ">
                                <FilePdfOutlined />
                                Generate PDF
                            </a-button>
                        </span>
                        <span style="font-weight: bold; margin-left: 3%;">
                            <a-button @click="perBarcodeExcel" style="background-color:green; color:white ">
                                <FileExcelOutlined />
                                Generate EXCEL
                            </a-button>
                        </span>
                        <span style="font-weight: bold; margin-left: 30%;">
                            Search:
                            <a-input allow-clear v-model:value="spgcApprovedSearchPerBarcode" placeholder="Input search here!"
                                style="width: 20%; max-width: 30%; min-width: 30%; border: 1px solid #1e90ff" />
                        </span>
                        <div style="margin-left: 40%;">
                            <span style="color: red;">
                                Table showing per barcode
                            </span>
                        </div>
                        <div style="margin-top: 20px;">
                            <a-table :columns="perBarcodeReleaseTable" :data-source="data.dataBarcode.data"
                                :pagination="false" size="small">

                            </a-table>
                            <pagination :datarecords="data.dataBarcode" class="mt-5" />
                        </div>
                    </a-card>
                </a-tab-pane>

            </a-tabs>
        </a-card>


        <a-card style="width: 15%; position: absolute; top: 45px; border: 1px solid #dcdcdc;">
            <div style="font-weight: bold; font-size: small;">
                <ExportOutlined />
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
                <div style="color:#1e90ff; font-weight: bold;">
                    Date Selected:
                </div>
                <div style="margin-top: 5px;">
                    <span style="color:red;">
                        <div>
                            <span style="color:green; font-weight: bold;">
                                FROM:
                            </span>
                            {{ data.fromDate }}
                        </div>
                        <span style="color:green; font-weight: bold;">
                            TO:
                        </span>
                        {{ data.endDate }}
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

export default {
    components: { Pagination },
    layout: AuthenticatedLayout,
    props: {
        data: Object
    },

    data() {
        return {
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
                    title: 'Approval #',
                    dataIndex: 'spexgc_num'
                },
                {
                    title: 'Total Amount',
                    dataIndex: 'totdenom'
                }
            ],
            perBarcodeReleaseTable: [
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
                    dataIndex: 'customer_name'
                },
                {
                    title: 'Approval #',
                    dataIndex: 'spexgc_num'
                },
                {
                    title: 'Date Approved',
                    dataIndex: 'daterel'
                },

            ],
            spgcData: {
                startDate: '',
                endDate: '',
                errors: {}
            }
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
                onOk() {
                    console.log('OK');
                },
                onCancel() {
                    console.log('Cancel');
                },
            });
        },

        perCustomerExcel() {
            Modal.confirm({
                title: 'Confirmation',
                icon: createVNode(ExclamationCircleOutlined),
                content: 'Are you sure you want to generate EXCEL?',
                okText: 'Yes',
                okType: 'danger',
                cancelText: 'No',
                onOk:()=> {
                    window.location.href = route('storeaccounting.releaseExcel', {
                        startDate: this.data.fromDate,
                        endDate: this.data.endDate
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
