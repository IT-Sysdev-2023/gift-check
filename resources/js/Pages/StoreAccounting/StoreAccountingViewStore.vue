<template>
    <div style="font-weight: bold; margin-left: 70%;">
        Search:
        <a-input allow-clear v-model:value="storeSearchBox" placeholder="Input search here!"
            style="border:1px solid #1e90ff; width: 60%;" />
    </div>
    <a-tabs>
        <a-tab-pane key="StoreView">
            <div style="font-weight: bold; padding:10px; background-color: #b0c4de;">
                Barcode # {{ selectecTransNumber }}
            </div>
            <a-table :data-source="viewStoreSalesData.data" :columns="viewStoreColumns" size="small"
                :pagination="false">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'view'">
                        <a-button @click="storeModalButton(record)" style="background-color: #1e90ff; color: white;"
                            class=" me-2 me-sm-5">
                            <SearchOutlined />
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="viewStoreSalesData" class="mt-5" />
        </a-tab-pane>
    </a-tabs>
    <a-modal v-model:open="storeModal" style="width: 100%;" @ok="storeOkButton">
        <div style="background-color: #b0c4de; padding: 10px; font-weight: bold;">
            GC Barcode # {{ salesBarcode }}
        </div>
        <a-table :data-source="storeModalData.data" :columns="storeModalColumns" size="small" :pagination="false">
        </a-table>
        <div style="margin-top: 20px;"> 
            <pagination :datarecords="storeModalData" class="mt-t" />

        </div>
    </a-modal>
    <!-- {{ viewStoreSalesData }} -->
</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
export default {
  components: { Pagination },
    layout: AuthenticatedLayout,
    props: {
        transnumber: String,
        viewStoreData: Object,
        viewStoreSalesData: Object,
        modalBarcode: Number,
        storeID: String,
        search: String

    },
    data() {
        return {
            storeSearchBox: '',
            barcodeModal: this.modalBarcode,
            storeModalData: {},
            storeModal: false,
            salesBarcode: {},
            selectecTransNumber: this.transnumber,
            viewStoreColumns: [
                {
                    title: 'Barcode',
                    dataIndex: 'sales_barcode'
                },
                {
                    title: 'Denomination',
                    dataIndex: 'denomination'
                },
                {
                    title: 'Store Verified',
                    dataIndex: 'store_name'
                },
                {
                    title: 'Date Verified',
                    dataIndex: 'vs_date'
                },
                {
                    title: 'Verified By',
                    dataIndex: 'verby'
                },
                {
                    title: 'Customer',
                    dataIndex: 'customer'
                },
                {
                    title: 'Balance',
                    dataIndex: 'vs_tf_balance'
                },
                {
                    title: 'View',
                    dataIndex: 'view'
                },
            ],
            storeModalColumns: [
                {
                    title: 'Textfile Line',
                    dataIndex: 'seodtt_line'
                },
                {
                    title: 'Credit Limit',
                    dataIndex: 'seodtt_creditlimit'
                },
                {
                    title: 'Cred. Pur. Amt + Add-on',
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
            ]

        }

    },
    watch: {
        storeSearchBox(search) {
            console.log(search);
            this.$inertia.get(route('storeaccounting.storeAccountingViewStore', {id: this.storeID }), {
                search:search
            }, {
                preserveState: true
            })
        }  
    },
    methods: {
        async storeModalButton(rec) {

            // this.salesBarcode = rec.sales_barcode
            try {
                const { data } = await axios.get(route('storeaccounting.storeAccountingViewModal', { barcode: rec.sales_barcode }));
                this.storeModal = true
                this.salesBarcode = rec.sales_barcode
                this.storeModalData = data

            } catch (error) {
                console.error("Error fetching store transaction data", error);
                this.errorMessage = "Error fetching store transaction data";
            }
            // barcode: rec.sales_barcode

            // this.storeModalData = response.data;
            // this.salesBarcode = rec.sales_barcode

            // catch (error){
            //    
            // }
        },
        storeOkButton() {
            this.storeModal = false
        }

    }

}
</script>
