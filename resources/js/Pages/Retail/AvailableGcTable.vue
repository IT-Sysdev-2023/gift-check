<template>
    <div>
        <a-card title="Available GC">
            <a-row class="mb-2">
                <a-col :span="16">
                    <a-card size="small" title="Denomination">
                        <a-row>
                            <a-col :span="row" v-for="(item, index) in denom" :key="index">
                                <a-button type="primary" @click="denomId(item.denom_id)" class="mx-2">{{
                                    item.denomination
                                    }}</a-button>
                            </a-col>
                        </a-row>
                    </a-card>
                </a-col>
                <a-col :span="8">
                    <div>
                        <div class="flex justify-end ml-2">
                            <a-input-search placeholder="Search Barcode" style="width: 100%" @search="onSearch" />
                        </div>
                    </div>
                </a-col>
            </a-row>
            <a-card>
                <a-table :pagination="false" size="small" :dataSource="gc.data" :columns="columns" />
            </a-card>
        </a-card>
    </div>
</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
export default {
    layout: AuthenticatedLayout,
    props: {
        denom: Object,
        gc: Object
    },
    data() {
        return {
            row: 0,
            columns: [
                {
                    title: 'Barcode No.',
                    dataIndex: 'strec_barcode',
                    key: 'name',
                },
                {
                    title: 'Denomination',
                    dataIndex: 'denomination',
                    key: 'age',
                },
                {
                    title: 'Request No.',
                    dataIndex: 'strec_id',
                },
            ],
        }
    },
    methods: {
        denomId(id) {
            this.$inertia.get(route('retail.availableGcList'), {
                id: id
            });
        },
        onSearch(barcode) {
            this.$inertia.get(route('retail.availableGcList'), {
                barcode: barcode
            });
        }
    },
    mounted() {
        const itemCount = this.denom.length;
        this.row = (24 / itemCount)
    },



}
</script>
