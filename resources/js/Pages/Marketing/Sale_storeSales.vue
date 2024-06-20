<template>

    <Head title="Store Sales" />
    <a-card>
        <a-card class="mb-2" title="Store Sales"></a-card>
        <div class="flex justify-end">
            <a-input-search class="mt-5 mb-5" v-model:value="search" placeholder="input search text here."
                style="width: 300px" @search="onSearch" />
        </div>

        <a-table :dataSource="data.data" size="small" bordered :columns="columns" :pagination="false">
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex == 'View'">
                 
                    <a-button @click="viewDetails(record)">
                        <template #icon>
                            <EyeOutlined />
                        </template>
                    </a-button>
                </template>
            </template>
        </a-table>
        <pagination class="mt-5" :datarecords="data" />
    </a-card>

    <a-modal v-model:open="openViewModal" width="80%" style="top: 65px" :title="title" :confirm-loading="confirmLoading"
        @ok="handleOk">
        <a-table :data-source="selectedData" :columns="selectedDataColumns">
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex == 'View'">
                    <a-button @click="viewBarcodeDetails(record)" v-if="record.vs_tf_used !== null">
                        <template #icon>
                            <EyeOutlined />
                        </template>
                    </a-button>
                </template>
            </template>
        </a-table>
    </a-modal>
    <a-modal v-model:open="selectedBarcodeModal" width="80%" style="top: 65px" :title="selectedBarcodeTitle"
        :confirm-loading="confirmLoading" @ok="handleOkselectedBarcode" @cancel="handleCancelSelectedBarcode">
        <a-table :data-source="selectedBarcodeDetails" :columns="selectedBarcodeColumns">
            <template #bodyCell="{ column, record }"></template>
        </a-table>
    </a-modal>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import debounce from "lodash/debounce";
import { PlusOutlined } from '@ant-design/icons-vue';

export default {
    layout: AuthenticatedLayout,
    PlusOutlined,
    props: {
        data: Array,
        columns: Array,
    },
    data() {
        return {
            search: '',
            openViewModal: false,
            selectedBarcodeModal: false,
            open: false,
            selectedData: [],
            title: '',
            selectedBarcodeTitle: '',
            selectedBarcodeColumns: '',
            selectedDataColumns: [],
            selectedBarcodeDetails: [],
        }
    },
    methods: {
        handleCancel() {
            this.open = false;
        },
        handleOk() {
            this.openViewModal = false;
            this.selectedBarcodeModal = false;
        },
        handleOkselectedBarcode() {
            this.selectedBarcodeModal = false;
            this.openViewModal = true;

        },
        handleCancelSelectedBarcode() {

            this.openViewModal = true;
        },
        viewDetails(data) {
            axios.get(route('get.store.sale.details'), {
                params: {
                    id: data.trans_sid,
                }
            }).then(response => {
                this.openViewModal = true;
                this.selectedData = response.data.dataTransSales;
                this.title = response.data.dataTransStore[0].store_name + ' - Transaction # ' + response.data.dataTransStore[0].trans_number;
                this.selectedDataColumns = response.data.selectedDataColumns
            });
        },
        viewBarcodeDetails(data) {
            axios.get(route('get.transaction.pos.detail'), {
                params: {
                    id: data.sales_barcode,
                }
            }).then(response => {
                this.selectedBarcodeModal = true;
                this.openViewModal = false;
                this.selectedBarcodeDetails = response.data.barcodeDetails;
                this.selectedBarcodeTitle = 'GC Barcode # ' + response.data.title
                this.selectedBarcodeColumns = response.data.selectedBarcodeColumns;
            });
        }
    },


    watch: {
        search: {
            deep: true,
            handler: debounce(function () {
                this.$inertia.get(route("verified.gc.alturas.mall"), {
                    search: this.search
                }, {
                    preserveState: true,
                });
            }, 600),
        },
    },
}
</script>
