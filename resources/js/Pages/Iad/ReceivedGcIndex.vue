<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table :data-source="record.data" :columns="columns" :pagination="false" size="small" bordered>
                <template #bodyCell="{column, record}">
                    <template v-if="column.key === 'action'">
                        <a-button class="mr-1">
                            <template #icon>
                                <EyeFilled />
                            </template>
                        </a-button>
                        <a-button>
                            <template #icon>
                                <PrinterFilled />
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
