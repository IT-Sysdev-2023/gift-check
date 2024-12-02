<template>
    <div style="font-weight: bold; margin-left: 70%;">
        Search:
        <a-input allow-clear v-model:value="salesSearchBox" placeholder="Input search here!"
            style="width:60%; border:1px solid #1e90ff" />
    </div>
    <a-tabs>
        <a-tab-pane>
            <div style="padding: 10px; font-weight: bold; background-color: #b0c4de;">
                Customer: {{ salesCustomer }}
            </div>
            <a-table :data-source="data.data" :columns="viewSalesColumns" :pagination="false" size="small">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'viewSales'">
                        <a-button @click="viewTreasurySales(record)" class="me-2 me-sm-5"
                            style="color:white; background-color: #1e90ff;">
                            <SearchOutlined />
                        </a-button>
                    </template>
                </template>


            </a-table>
            <pagination :datarecords="data" class="mt-5" />
        </a-tab-pane>
    </a-tabs>

    <a-modal v-model:open="salesViewModal" @ok="handleSalesView" style="width: 100%;">
        <div style="padding: 10px; font-weight: bold; background-color: #b0c4de; font-weight: bold; font-size: large;">
            Post Transaction: {{ selectedBarcode }}
        </div>
        <a-table :data-source="POStransactionData.data" :columns="salesViewColumns" size="small" :pagination="false" />
        <div style="margin-top: 20px;">
            <pagination :datarecords="POStransactionData" class="mt-5" />
        </div>
    </a-modal>
    <!-- {{ data }} -->
    <!-- {{ salesCustomerID }} -->
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import axios from 'axios';
export default {
    components: { Pagination },

    layout: AuthenticatedLayout,
    props: {
        salesCustomer: String,
        data: Array,
        search: String,
        salesCustomerID: String,
        viewSalesData: String


    },
    data() {
        return {
            salesSearchBox: this.search,

            selectedBarcode: {},

            salesViewModal: false,

            POStransactionData: {},

            salesViewColumns: [
                {
                    title: 'Textfile Line',
                    dataIndex: 'seodtt_line'
                },
                {
                    title: 'Credit Limit',
                    dataIndex: 'seodtt_creditlimit'
                },
                {
                    title: 'Cred.Pur.Amt + Add - on',
                    dataIndex: 'seodtt_credpuramt'
                },
                {
                    title: 'Add-on Amt',
                    dataIndex: 'seodtt_addonamt'
                },
                {
                    title: 'Remaining Balance',
                    dataIndex: 'seodtt_balance'
                },
                {
                    title: 'Transaction #',
                    dataIndex: 'seodtt_transno'
                },
                {
                    title: 'Time of Cred Tranx',
                    dataIndex: 'seodtt_timetrnx'
                },
                {
                    title: 'Bus. Unit',
                    dataIndex: 'seodtt_bu'
                },
                {
                    title: 'Terminal #',
                    dataIndex: 'seodtt_terminalno'
                },
                {
                    title: 'Ackslip #',
                    dataIndex: 'seodtt_ackslipno'
                },

            ],
            viewSalesColumns: [
                {
                    title: 'Barcode #',
                    dataIndex: 'barcode'
                },
                {
                    title: 'GC Type',
                    dataIndex: 'type'
                },
                {
                    title: 'Denomination',
                    dataIndex: 'denomination'
                },
                {
                    title: 'Date Verified',
                    dataIndex: 'dateverify'
                },
                {
                    title: 'Store Verified',
                    dataIndex: 'store'
                },
                {
                    title: 'Verified By',
                    dataIndex: 'verifyby'
                },
                {
                    title: 'Customer Name',
                    dataIndex: 'customer'
                },
                {
                    title: 'Balance',
                    dataIndex: 'balanced'
                },
                {
                    title: 'View',
                    dataIndex: 'viewSales'
                },
            ]
        }
    },
    watch: {
        salesSearchBox(salesViewSearch) {
            // alert(1)  
            console.log(salesViewSearch);
            this.$inertia.get(route('storeaccounting.storeAccountingViewSales', this.salesCustomerID), {
                search: salesViewSearch
            }, {
                preserveState: true
            })
        }
    },
    methods: {
        async viewTreasurySales(rec) {
            try {
                const response = await axios.get(route('storeaccounting.storeAccountingPOStransaction', rec.barcode));
                this.POStransactionData = response.data;
                this.selectedBarcode = rec.barcode
                this.salesViewModal = true
            }
            catch (error) {
                console.error("Error fetching sales transaction data:", error);
                this.errorMessage = "Failed to fetch sales transaction data.";
            }


        },
        handleSalesView() {
            this.salesViewModal = false
        },
    }
}

</script>
