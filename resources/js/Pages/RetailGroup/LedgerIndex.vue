<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table bordered size="small" :data-source="records" :columns="columns">
            </a-table>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup>
import { ref } from 'vue';

const props = defineProps({
    records: Array,
});

const columns = ref([
    {
        title: 'Legder #',
        dataIndex: 'count',
    },
    {
        title: 'Date',
        dataIndex: 'date',
    },
    {
        title: 'Transaction',
        dataIndex: 'desc',
    },
    {
        title: 'Debit',
        dataIndex: 'debit',
    },
    {
        title: 'Credit',
        dataIndex: '',
    },
    {
        title: 'Balance',
        dataIndex: 'total',
    },
]);
</script>
