<template>

    <a-card>
        <a-card title=" TREASURY SALES"></a-card>

        <!-- <div class="input-wrapper">
            <input type="search" placeholder="Input search here..." name="text" class="input" v-model="salesSearchBox" />
        </div> -->

        <div style="margin-top: 10px; margin-left: 70%;">
            <a-input-search allow-clear v-model:value="salesSearchBox" placeholder="Input search here!" enter-button
                style="width: 90%;" />
        </div>


        <div style="margin-top: 10px;">
            <a-table :data-source="data.data" :columns="columns" :pagination="false" size="small">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'view'">
                        <a-button title="view" @click="viewSales(record)" class="me-2 me-sm-5"
                            style="color:white; background-color: #1e90ff;">
                            <EyeOutlined />
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="data" class="mt-5" />
        </div>
    </a-card>

    <!-- <span style="font-weight: bold;">
        Select
        <a-select id="select_entries" v-model:value="dataForSelectEntries.select_entries"
            @change="salesSelectEntriesButton" style="background-color: #1e90ff; border: 1px solid #1e90ff;"
            placeholder="10">
            <a-select-option value="10">10</a-select-option>
            <a-select-option value="20">20</a-select-option>
            <a-select-option value="50">50</a-select-option>
            <a-select-option value="100">100</a-select-option>
        </a-select>
        entries
    </span> -->


    <!-- {{ data }} -->

</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { notification } from 'ant-design-vue'
export default {

    layout: AuthenticatedLayout,
    props: {
        data: Object,
        pagination: Number,
        searchData: String
    },
    data() {
        return {
            dataForSelectEntries: {
                select_entries: this.pagination
            },
            salesSearchBox: this.searchData,
            salesCustomerName: '',
            columns: [
                {
                    title: 'Transaction #',
                    dataIndex: 'insp_paymentnum',
                    sorter: (a, b) => (a.insp_paymentnum || '').localeCompare(b.insp_paymentnum || '', undefined, { sensitivity: 'base' }),
                },
                {
                    title: 'GC Type',
                    dataIndex: 'insp_paymentcustomer',
                    filters: [
                        {
                            text: 'Institution GC',
                            value: 'Institution GC'
                        },
                        {
                            text: 'Regular GC',
                            value: 'Regular GC'
                        },
                        {
                            text: 'Special External GC',
                            value: 'Special External GC'
                        },
                        {
                            text: 'Promo GC',
                            value: 'promo'
                        }
                    ],
                    onFilter: (value, record) => record.insp_paymentcustomer === value

                },
                {
                    title: 'Customer',
                    dataIndex: 'customer',
                    sorter: (a, b) => (a.customer || '').localeCompare(b.customer || '', undefined, { sensitivity: 'base' }),
                },

                {
                    title: 'Date',
                    dataIndex: 'datetr',
                    sorter: (a, b) => (a.datetr || '').localeCompare(b.datetr || '', undefined, { sensitivity: 'base' }),
                },
                {
                    title: 'Time',
                    dataIndex: 'timetr',
                    sorter: (a, b) => (a.timetr || '').localeCompare(b.timetr || '', undefined, { sensitivity: 'base' }),
                },
                {
                    title: 'GC pc(s)',
                    dataIndex: 'totgccnt',
                    sorter: (a, b) => (a.totgccnt || '').localeCompare(b.totgccnt || '', undefined, { sensitivity: 'base' }),
                },
                {
                    title: 'Total Denomination',
                    dataIndex: 'totdenom',
                    sorter: (a, b) => (a.totdenom || '').localeCompare(b.totdenom || '', undefined, { sensitivity: 'base' }),
                },
                {
                    title: 'Payment Type',
                    dataIndex: 'paymenttype',
                    filters: [
                        {
                            text: 'CASH',
                            value: 'cash'
                        },
                        {
                            text: 'CHECK',
                            value: 'check'
                        },
                    ],
                    onFilter: (value, record) => record.paymenttype === value
                },
                {
                    title: 'View',
                    dataIndex: 'view'
                },
            ]
        }
    },
    watch: {
        salesSearchBox(search) {
            const searchValidation = /[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{1F900}-\u{1F9FF}]/u;
            if (searchValidation.test(search)){
                const openNotificationWithIcon = (type)=>{
                    notification[type]({
                        message: 'Invalid input',
                        description: 'Search contain invalid symbols or emojis',
                        placement: 'topRight'
                    });
                };
                openNotificationWithIcon('warning');
                return;
            }
            console.log(search);
            this.$inertia.get(route('storeaccounting.sales'), {
                search: search
            }, {
                preserveState: true
            })
        }
    },
    methods: {
        salesSelectEntriesButton(entries) {
            console.log(entries);
            this.$inertia.get(route('storeaccounting.sales'), {
                entries: entries
            }, {
                preserveState: true
            })
        },
        viewSales(rec) {
            console.log(rec.customer);
            this.salesCustomerName = rec.customer
            this.$inertia.get(route('storeaccounting.storeAccountingViewSales', rec.insp_id), {
                salesCustomer: this.salesCustomerName
            })
        }

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
