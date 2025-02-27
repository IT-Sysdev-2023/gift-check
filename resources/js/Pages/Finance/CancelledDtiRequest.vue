<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    columns: Object,
    data: Object
})
</script>
<template>
    <AuthenticatedLayout>
        <a-card>
            <a-card>
                <div class="flex items-center">
                    <p class="text-lg">Cancelled DTI Request</p>
                    <Link :href="route('finance.dashboard')" class="ml-auto text-black text-blue-700">
                    <RollbackOutlined /> Back to Dashboard
                    </Link>
                </div>
            </a-card>
            <div class="mt-5">
                <a-table :columns="props.columns" :data-source="data">
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'View'">
                            <a-button type="primary" @click="cancelledButton(record)">
                                <PicLeftOutlined /> View
                            </a-button>
                        </template>
                    </template>
                </a-table>
            </div>
        </a-card>
        {{ data }}

    </AuthenticatedLayout>
</template>
