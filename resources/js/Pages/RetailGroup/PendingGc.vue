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
            <a-table size="small" :data-source="record.data" bordered :columns="columns" :pagination="false">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key == 'status'">
                        <span v-if="record.pgcreq_group_status == ''">
                            <a-tag color="processing">
                                <template #icon>
                                    <sync-outlined :spin="true" />
                                </template>
                                for setup
                            </a-tag>
                        </span>
                        <span v-else>
                            <a-tag color="default">
                                <template #icon>
                                    <clock-circle-outlined />
                                </template>
                                waiting
                            </a-tag>
                        </span>
                    </template>
                    <template v-if="column.key == 'setup'">
                        <span v-if="record.pgcreq_group_status == ''">
                            <a-button block type="primary"
                                @click="() => $inertia.get(route('retailgroup.recommendation.setup'), { id: record.pgcreq_id })">
                                <template #icon>
                                    <FastForwardOutlined />
                                </template>
                                Setup
                            </a-button>
                        </span>
                        <span v-else>
                            <a-button block
                                @click="() => $inertia.get(route('retailgroup.recommendation.setup'), { id: record.pgcreq_id })">
                                <template #icon>
                                    <LikeOutlined />
                                </template>
                                Waiting
                            </a-button>
                        </span>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="record" class="mt-4"/>
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
