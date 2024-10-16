<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table bordered size="small" :data-source="records.data" :columns="columns" :pagination="false">
                <template #bodyCell="{column, record}">
                    <template v-if="column.key === 'view'">
                        <a-button @click="$inertia.get(route('retailgroup.details', record.pgcreq_id))">
                            <ArrowsAltOutlined />
                            Details
                        </a-button>
                    </template>
                </template>
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
