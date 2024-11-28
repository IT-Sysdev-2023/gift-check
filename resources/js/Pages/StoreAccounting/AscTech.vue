<template>
    <div style="font-weight: bold; margin-left: 70%;">
        Search:
        <a-input allow-clear v-model:value="alturasSearchBox" style="border: 1px solid #1e90ff; width:60%"
            placeholder="Input search here!" />
    </div>
    <div style="font-weight: bold;">
        Select
        <a-select id="select_entries" style="border:1px solid #1e90ff; background-color: #1e90ff;"
            v-model:value="alturasPagination.select_entries" @change="PaginationEntries">
            <a-select-option value="10">10</a-select-option>
            <a-select-option value="20">20</a-select-option>
            <a-select-option value="50">50</a-select-option>
            <a-select-option value="100">100</a-select-option>
        </a-select>
        entries
    </div>
    <div style=" margin-top: 15px;">
        <div style="font-weight: bold; padding: 10px; background-color: #b0c4de;">
            <span style="margin-left: 40%;">
                {{ storeName }} - Verified GC
            </span>
        </div>
        <a-table :data-source="data.data" :columns="alturasMallColumns" :pagination="false" size="small"
            style="margin-top: 10px;">
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex === 'view'">
                    <a-button @click="viewAlturasMall(record)" class="me-2 me-sm-5"
                        style="color:white; background-color: #1e90ff;">
                        <EyeOutlined />
                    </a-button>
                </template>
            </template>
        </a-table>
        <pagination :datarecords="data" class="mt-5" />
    </div>

    <!-- {{ storeName }} -->
</template>
<script>
// import { defineComponent } from '@vue/composition-api'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        id: String,
        search: String,
        pagination: String,
        storeName: String
    },

    data() {
        return {
            selectecBarcode: {},
            alturasPagination: {
                select_entries: this.pagination,
            },
            alturasSearchBox: this.search,
            alturasMallColumns: [
                {
                    title: 'Barcode #',
                    dataIndex: 'vs_barcode',
                    sorter: (a, b) => String(a.vs_barcode || '').localeCompare(String(b.vs_barcode || ''), undefined, { sensitivity: 'base' }),


                },
                {
                    title: 'Denomination',
                    dataIndex: 'vs_tf_denomination',
                    sorter: (a, b) => (a.vs_tf_denomination || '').localeCompare(b.vs_tf_denomination || '', undefined, { sensitivity: 'base' }),
                },
                {
                    title: 'Date Verified/Reverified',
                    dataIndex: 'newDate',
                    sorter: (a, b) => (a.newDate || '').localeCompare(b.newDate || '', undefined, { sensitivity: 'base' }),

                },
                {
                    title: 'Verified/Reverified By',
                    dataIndex: 'verby',
                    sorter: (a, b) => (a.verby || '').localeCompare(b.verby || '', undefined, { sensitivity: 'base' }),

                },
                {
                    title: 'Customer',
                    dataIndex: 'customer',
                    sorter: (a, b) => (a.customer || '').localeCompare(b.customer || '', undefined, { sensitivity: 'base' }),

                },
                {
                    title: 'Balance',
                    dataIndex: 'vs_tf_balance',
                    sorter: (a, b) => (a.vs_tf_balance || '').localeCompare(b.vs_tf_balance || '', undefined, { sensitivity: 'base' }),

                },
                {
                    title: 'View',
                    dataIndex: 'view'
                },
            ]
        }

    },
    watch: {
        alturasSearchBox(search) {
            // alert
            console.log(search);
            this.$inertia.get(route('storeaccounting.ascTech', this.id), {
                search: search
            }, {
                preserveState: true
            })
        }

    },
    methods: {
        PaginationEntries(entries) {
            console.log(entries);
            this.$inertia.get(route('storeaccounting.ascTech', this.id), {
                pagination: entries
            })
        },
        viewAlturasMall(rec) {
            // this.selectecBarcode = rec.vs_barcode
            this.$inertia.get(route('storeaccounting.ascTechPosTransaction', rec.vs_barcode));
        }
    }

}
</script>
