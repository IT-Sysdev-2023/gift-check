<template>
    <a-table :data-source="barcodes" :columns="columns" size="small" bordered></a-table>
</template>
<script setup>
import { ref } from 'vue';

defineProps({
    barcodes: Object
});

const columns = ref(
    [
        {
            title: 'Barcode',
            dataIndex: 'spexgcemp_barcode',
        },
        {
            title: 'Denomination',
            dataIndex: 'spexgcemp_denom',
        },
        {
            title: 'Lastname',
            dataIndex: 'spexgcemp_lname',
        },
        {
            title: 'Firstname',
            dataIndex: 'spexgcemp_fname',
        },
        {
            title: 'Middlename',
            dataIndex: 'spexgcemp_mname',
        },
        {
            title: 'Suffix.',
            dataIndex: 'spexgcemp_extname',
        },
    ]);
</script>
