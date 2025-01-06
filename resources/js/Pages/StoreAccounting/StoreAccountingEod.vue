<template>
    <a-card>
        <a-card>
            <div style="font-weight: bold;">
                EOD Date: {{ selectedEODDate }}
            </div>
        </a-card>

        <!-- <div class="input-wrapper">
            <input type="search" placeholder="Input search here..." name="text" class="input" v-model="searchTerm" />
        </div> -->

        <div style="font-weight: bold; margin-left: 70%; margin-top: 10px;">
            <a-input-search allow-clear v-model:value="searchTerm" placeholder="Input search here!" enter-button
                style="width:90%;" />
        </div>

        <div style="margin-top: 10px;">
            <a-table :data-source="data.data" :columns="searchColumns" :pagination="false" size="small">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'search'">
                        <a-button title="search" @click="searchEODView(record)"
                            style="color:white; background-color: #1e90ff;" class=" me-2 me-sm-5">
                            <SearchOutlined />
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="data" :id="selectedEODDate" class="mt-5" />
        </div>
    </a-card>



    <!-- <span style="font-weight: bold;">
        Select
        <a-select id="select_entries" v-model:value="dataForSelectEntries.select_entries" placeholder="10"
            @change="dashboardViewSelectEntries" style="background-color: #1e90ff; border: 1px solid #1e90ff">
            <a-select-option value="10">10</a-select-option>
            <a-select-option value="20">20</a-select-option>
            <a-select-option value="50">50</a-select-option>
            <a-select-option value="100">100</a-select-option>

        </a-select>
        entries
    </span> -->





    <a-modal v-model:open="modalForGCNavisionPOSTransactions" style="width:100%;" @ok="modalBackButton">
        <a-card>
            <a-card>
                <div style="font-weight: bold;">
                    GC Navision POS Transactions - Barcode # {{ selectedBarcode }}
                </div>
            </a-card>
            <div style="margin-top: 10px;">
                <a-table size="small" :data-source="modalData.data" :columns="GCNavisionPOSTransactions"
                    :pagination="false" />
                <pagination :datarecords="modalData" class="mt-5" />
            </div>
        </a-card>
    </a-modal>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { notification } from 'ant-design-vue';
import axios from 'axios';
export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        pagination: String,
        search: String,
        eodDate: String,
        ideod: String
    },
    // components: {
    //     ErrorMessage,
    // },

    data() {
        return {
            errorMessage: null,
            dataForSelectEntries: {
                select_entries: this.pagination
            },

            modalData: {},
            selectedBarcode: {},
            searchTerm: this.search,
            selectedEODDate: this.eodDate,
            modalForGCNavisionPOSTransactions: false,
            errorModal: false,

            GCNavisionPOSTransactions: [
                {
                    title: 'Textfile Line',
                    dataIndex: 'seodtt_line',
                    key: 'seodtt_line'
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
                {
                    title: 'seodtt_crditpurchaseamt',
                    dataIndex: 'seodtt_crditpurchaseamt'
                }


            ],
            searchColumns: [
                {
                    title: 'Barcode #',
                    dataIndex: 'vs_barcode'

                },
                {
                    title: 'Denomination',
                    dataIndex: 'vs_tf_denomination'

                },
                {
                    title: 'Date/Time Verified',
                    dataIndex: 'storeEodDateTime'
                },
                {
                    title: 'Verified By',
                    dataIndex: 'users_fullname'
                },
                {
                    title: 'Customer Name',
                    dataIndex: 'cus_fullname'
                },
                {
                    title: 'Store',
                    dataIndex: 'store_name'
                },
                {
                    title: 'Balance',
                    dataIndex: 'vs_tf_balance'
                },
                {
                    title: 'Action',
                    dataIndex: 'search'
                }

            ],
        }

    },
    watch: {
        searchTerm(search) {
            const searchValidation = /[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{1F900}-\u{1F9FF}]/u;
            if (searchValidation.test(search)){
             const openNotificationWithIcon = (type) =>{
                    notification[type]({
                        message:'Invalid input',
                        description: 'Search contains invalid symbols or emojis',
                        placement: 'topRight'
                    });
                };
                    openNotificationWithIcon('warning');
                    return;
            }
            this.$inertia.get(route('storeaccounting.storeeod', this.ideod), {
                search: search,

                eodDate: this.selectedEODDate,

            }, {
                preserveState: true,

            })
        }
    },

    methods: {
        dashboardViewSelectEntries(value) {
            this.$inertia.get(route('storeaccounting.storeeod', this.ideod), {
                value: value,
                eodDate: this.selectedEODDate
            }, {
                preserveState: true,

            })

        },
        async searchEODView(rec) {
            try {
                const response = await axios.get(route('storeaccounting.GCNavisionPOSTransactions', rec.vs_barcode));
                this.modalData = response.data;
                this.selectedBarcode = rec.vs_barcode
                this.modalForGCNavisionPOSTransactions = true;
                this.errorMessage = null;

            } catch (error) {
                this.errorMessage = error.response?.data?.message || 'KUPAL KABA?';
                this.errorModal = true;
            }

        },

        modalBackButton() {
            this.modalForGCNavisionPOSTransactions = false
        },

    }

}

</script>
<style scoped>
/* From Uiverse.io by adamgiebl */
.input-wrapper input {
    background-color: whitesmoke;
    border: none;
    padding: 1rem;
    font-size: 1rem;
    width: 16em;
    border-radius: 2rem;
    color: black;
    box-shadow: 0 0.4rem #1e90ff;
    cursor: pointer;
    margin-top: 10px;
    margin-left: 70%;
}

.input-wrapper input:focus {
    outline-color: whitesmoke;
}
</style>
