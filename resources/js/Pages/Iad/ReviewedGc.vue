<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table size="small" bordered :data-source="record.data" :columns="columns" :pagination="false">
                <template #bodyCell="{column, record}">
                    <template v-if="column.key === 'details'">
                        <a-button @click="() => $inertia.get(route('iad.reviewed.gc.details', {id: record.spexgc_id}))">
                            <template #icon>
                                <EyeFilled />
                            </template>
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="record" class="mt-6"/>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    record: Object,
    columns: Array,
})
</script>
