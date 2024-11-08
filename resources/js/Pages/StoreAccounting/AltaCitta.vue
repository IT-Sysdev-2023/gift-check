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
    <div style="background-color: #b0c4de; margin-top: 15px;">
        <div style="font-weight: bold; font-size: large; padding: 20px;">
            {{ storeName }} - Verified GC
        </div>
        <a-table :data-source="data.data" :columns="alturasMallColumns" :pagination="false">
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
                    dataIndex: 'vs_barcode'
                },
                {
                    title: 'Denomination',
                    dataIndex: 'vs_tf_denomination'
                },
                {
                    title: 'Date Verified/Reverified',
                    dataIndex: 'newDate'
                },
                {
                    title: 'Verified/Reverified By',
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
            ]
        }

    },
    watch: {
        alturasSearchBox(search) {
            // alert
            console.log(search);
            this.$inertia.get(route('storeaccounting.altaCitta', this.id), {
                search: search
            }, {
                preserveState: true
            })
        }

    },
    methods: {
        PaginationEntries(entries) {
            console.log(entries);
            this.$inertia.get(route('storeaccounting.altaCitta', this.id), {
                pagination: entries
            })
        },
        viewAlturasMall(rec) {
            // this.selectecBarcode = rec.vs_barcode
            this.$inertia.get(route('storeaccounting.altaCittaPosTransaction', rec.vs_barcode));
        }
    }

}
</script>
