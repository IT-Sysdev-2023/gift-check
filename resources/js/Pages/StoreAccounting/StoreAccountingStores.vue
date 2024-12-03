<template>
    <a-card>
        <a-card title="STORE SALES">
        </a-card>
        <div style="margin-left: 70%; font-weight: bold; margin-top: 10px;">
            <a-input-search allow-clear style=" width: 90%;" v-model:value="storeSearchBox"
                placeholder="Input search here!" />
        </div>
        <div style="margin-top: 10px;">
            <a-table :data-source="data.data" :columns="storeColumns" :pagination="false" size="small">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'view'">
                        <a-button @click="viewStore(record)" class="me-2 me-sm-5"
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
        <a-select id="select_entries" style="background-color: #1e90ff; border: 1px solid #1e90ff"
            @change="storeSelectEntries" v-model:value="storeDataForSelectEntries.select_entries">
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
import Pagination from '@/Components/Pagination.vue';
export default {
    components: { Pagination },
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        search: String,
        pagination: Number
    },

    data() {
        return {
            storeDataForSelectEntries: {
                select_entries: this.pagination
            },
            storeSearchBox: this.search,
            storeTransNumber: '',
            storeColumns: [
                {
                    title: 'Transactions #',
                    dataIndex: 'trans_number',
                    sorter: (a, b) => (a.trans_number || '').localeCompare(b.trans_number || '', undefined, { sensitivity: 'base' }),
                },
                {
                    title: 'Store',
                    dataIndex: 'store_name',
                    filters: [
                        {
                            text: 'Alturas Mall',
                            value: 'Alturas Mall'
                        },
                        {
                            text: 'Alturas Talibon',
                            value:'Alturas Talibon'
                        },
                        {
                            text: 'Island City Mall',
                            value: 'Island City Mall'
                        },
                        {
                            text: 'Plaza Marcela',
                            value: 'Plaza Marcela'
                        },
                        {
                            text: 'Alturas Tubigon',
                            value: 'Alturas Tubigon'
                        },
                        {
                            text: 'Colonade Colon',
                            value: 'Colonade Colon'
                        },
                        {
                            text: 'Colonade Mandaue',
                            value: 'Colonade Mandaue'
                        },
                        {
                            text: 'Alta Citta',
                            value: 'Alta Citta'
                        },
                        {
                            text: 'Farmers Market',
                            value: 'Farmers Market'
                        },
                        {
                            text: 'Ubay Distribution Center',
                            value: 'Ubay Distribution Center'
                        },
                        {
                            text: 'Screenville',
                            value: 'Screenville'
                        },
                        {
                            text: 'Asc Tech',
                            value: 'Asc Tech'
                        },

                    ],
                    onFilter: (value, record) => record.store_name === value



                },
                {
                    title: 'Date',
                    dataIndex: 'trans_date',
                    sorter: (a, b) => (a.trans_date || '').localeCompare(b.trans_date || '', undefined, { sensitivity: 'base' }),
                },
                {
                    title: 'Time',
                    dataIndex: 'trans_time',
                    sorter: (a, b) => (a.trans_time || '').localeCompare(b.trans_time || '', undefined, {sensitivity: 'base'}),
                },
                {
                    title: 'GC pc(s)',
                    dataIndex: 'totalCount',
                    sorter: (a, b) => (a.totalCount || '').localeCompare(b.totalCount || '', undefined, { sensitivity: 'base' }),

                },
                {
                    title: 'Total denom',
                    dataIndex: 'totalAmount',
                    sorter: (a, b) => (a.totalAmount || '').localeCompare(b.totalAmount || '', undefined, { sensitivity: 'base' }),

                },
                {
                    title: 'Payment Type',
                    dataIndex: 'payment_type',
                    sorter: (a, b) => (a.payment_type || '').localeCompare(b.payment_type || '', undefined, { sensitivity: 'base' }),

                },
                {
                    title: 'View',
                    dataIndex: 'view'
                }
            ]
        }
    },
    watch: {
        storeSearchBox(searchData) {
            console.log(searchData);
            this.$inertia.get(route('storeaccounting.store'), {
                search: searchData
            }, {
                preserveState: true
            })
        }
    },
    methods: {
        viewStore(rec) {
            this.storeTransNumber = rec.trans_number
            this.$inertia.get(route('storeaccounting.storeAccountingViewStore', rec.trans_sid), {
                transNumber: this.storeTransNumber
            })
        },

        storeSelectEntries(entries) {
            console.log(entries);
            this.$inertia.get(route('storeaccounting.store'), {
                pagination: entries
            }, {
                preserveState: true
            })
        }

    }


}

</script>
