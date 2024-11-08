<template>
    <div style="margin-left: 70%; font-weight: bold;">
        Search:
        <a-input allow-clear v-model:value="salesSearchBox" placeholder="Input search here!"
            style="width: 60%; border: 1px solid #1e90ff;" />
    </div>

    <span style="font-weight: bold;">
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
    </span>

    <div style="padding: 20px; font-weight: bold; background-color: #b0c4de; margin-top: 15px; font-size: large;">
        Treasury Sales
    </div>
    <div style="background-color: #b0c4de;">
        <a-table :data-source="data.data" :columns="columns" :pagination="false">
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

    <!-- {{ data }} -->

</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { setInputSelection } from 'ant-design-vue/es/vc-mentions/src/util';
export default {

    layout: AuthenticatedLayout,
    props: {
        data: Object,
        pagination: String,
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
                    sorter: (a, b) => (a.insp_paymentnum || '').localeCompare(b.insp_paymentnum || '', undefined, {sensitivity: 'base'}),
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
                    sorter: (a, b) => (a.timetr || '').localeCompare(b.timetr || '', undefined, {sensitivity: 'base'}),
                },
                {
                    title: 'GC pc(s)',
                    dataIndex: 'totgccnt',
                    sorter: (a, b) => (a.totgccnt || '').localeCompare(b.totgccnt || '', undefined, {sensitivity: 'base' }),
                },
                {
                    title: 'Total Denomination',
                    dataIndex: 'totdenom',
                    sorter: (a, b) => (a.totdenom || '').localeCompare(b.totdenom || '', undefined, {sensitivity: 'base'}),
                },
                {
                    title: 'Payment Type',
                    dataIndex: 'paymenttype',
                    filters: [
                        {
                            text: 'CASH',
                            value:'cash'
                        },
                        {
                            text: 'CHECK',
                            value:'check'
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