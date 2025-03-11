<template>
    <AuthenticatedLayout>
        <a-tabs type="card" v-model:activeKey="activeKey">
            <a-tab-pane key="1">
                <template #tab>
                    <span>
                        <apple-outlined />
                        Special Gc Request Setup Dti
                    </span>
                </template>
                <special-gc-request-component :datarecords="props.records" />
            </a-tab-pane>
            <a-tab-pane key="2">
                <template #tab>
                    <span>
                        <android-outlined />
                        Barcodes
                    </span>
                </template>
                <a-card title="Dti Barcodes For Printing">
                    <a-table bordered :pagination="false" :data-source="barcodes.data" size="small" :columns="columns"/>
                    <pagination :datarecords="barcodes" class="mt-5"/>
                </a-card>
            </a-tab-pane>
        </a-tabs>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { ref } from 'vue';

const activeKey = ref('1');

interface Barcodes {
    data: {
        dti_barcode: number,
        dti_denom: number,
        completename: string,
    }
}

const props = defineProps<{
    records: Object,
    barcodes: Barcodes
}>();

const columns = ref([
    {
        title: 'Barcodes',
        dataIndex: 'dti_barcode',
        key: 'name',
    },
    {
        title: 'Denomination',
        dataIndex: 'dti_denom',
        key: 'age',
    },
    {
        title: 'Complete Name',
        dataIndex: 'completename',
        key: 'address',
    },
]);
</script>
