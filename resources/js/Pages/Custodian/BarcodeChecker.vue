<template>
    <a-row :gutter="[16, 16]">
        <a-col :span="8">
            <BarcodeForm :date="date"/>
            <Statistics :counts="count" class="mt-5" />
        </a-col>
        <a-col :span="16">
            <a-card>
                <a-table size="small" :data-source="data" :columns="columns" :pagination="false" />
            </a-card>
        </a-col>
    </a-row>
</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        columns: Array,
        count: Array,
        date: String
    }

}
</script>
