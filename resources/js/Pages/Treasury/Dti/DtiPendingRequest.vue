<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table size="small" :dataSource="props.record" :columns="columns" />
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
interface Record {
    
}
const props = defineProps<{

}>()
const columns = [
    {
        title: 'Name',
        dataIndex: 'name',
        key: 'name',
    },
    {
        title: 'Age',
        dataIndex: 'age',
        key: 'age',
    },
    {
        title: 'Address',
        dataIndex: 'address',
        key: 'address',
    },
];
</script>
