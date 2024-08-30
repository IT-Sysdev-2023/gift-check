<template>
    <AuthenticatedLayout>
        <a-card>
            <div class="flex justify-end mb-2">
                <a-button @click="() => $inertia.get(route('retailgroup.dashboard'))">
                    <template #icon>
                        <RollbackOutlined />
                    </template>
                    Back to Dashboard
                </a-button>
            </div>
            <a-table size="small" :data-source="record" bordered :columns="columns">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key == 'setup'">
                        <a-button @click="() => $inertia.get(route('retailgroup.recommendation.setup'), {id: record.pgcreq_id})">
                            <template #icon>
                                <FastForwardOutlined />
                            </template>
                            Setup Recommendation
                        </a-button>
                    </template>
                </template>
            </a-table>
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
