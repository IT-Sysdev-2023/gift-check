<template>
    <div>
        <a-spin tip="Loading..." :spinning="isLoading" size="large">
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
                            <a-input-search show-count type="number" placeholder="Search Barcode" style="width: 100%"
                                @search="onSearch" />
                        </div>
                    </div>
                </a-col>
            </a-row>
            <a-card>
                <a-table bordered :pagination="false" size="small" :dataSource="gc.data" :columns="columns">
                    <template v-slot:bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'strec_barcode'">
                            <BarcodeOutlined /> {{record.strec_barcode}}
                        </template>
                        <template v-if="column.dataIndex === 'denomination'">
                            <a-tag color="#87d068">{{ record.denomination }}</a-tag>
                        </template>
                    </template>
                </a-table>
                <Pagination :datarecords="gc" class="mt-4" />
            </a-card>
        </a-card>
    </a-spin>
    </div>
</template>
<script>
import Pagination from '@/Components/Pagination.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { onProgress } from '@/Mixin/UiUtilities';
export default {
    layout: AuthenticatedLayout,
    props: {
        denom: Object,
        gc: Object
    },
    data() {
        return {
            isLoading: false,
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
                    dataIndex: 'sgc_num',
                },
            ],
        }
    },
    methods: {
        denomId(id) {
            this.$inertia.get(route('retail.availableGcList'), {
                id: id
            }, {onStart:() => {
                this.isLoading = true;
            }, onSuccess:() => {
                this.isLoading = false;
            }});
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
