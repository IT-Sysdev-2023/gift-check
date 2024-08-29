<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table size="small" :data-source="record.data" :columns="columns" :pagination="false">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key == 'setup'">
                        <a-button>
                            <template #icon>
                                <FastForwardOutlined />
                            </template>
                            Setup Budget
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="record" class="mt-2" />
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
