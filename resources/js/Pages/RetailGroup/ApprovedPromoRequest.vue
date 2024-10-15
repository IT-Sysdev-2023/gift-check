<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table bordered size="small" :data-source="records.data" :columns="columns" :pagination="false">

            </a-table>
            <pagination class="mt-4" :datarecords="records"/>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup>
const props = defineProps({
    records: Object,
    columns: Array,
});
</script>
