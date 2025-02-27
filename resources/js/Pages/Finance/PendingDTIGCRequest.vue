<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    columnsDti: Array,
    externalDti: Object,
})


</script>
<template>
    <AuthenticatedLayout>
        <a-card>
            <div class="flex items-center">
                <p class="text-lg">Pending DTI Special GC Request</p>
                <Link class="ml-auto text-black text-blue-700" :href="route('finance.dashboard')">
                <RollbackOutlined /> Back to Dashboard
                </Link>
            </div>
            <main class="mt-10">
                <section>
                    <a-table :columns="props.columnsDti" :data-source="props.externalDti">
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'View'">
                                <a-button type="primary"
                                    @click="() => $inertia.get(route('finance.pendingGc.ditPendingRequest'), { id: record.id, gcType: record.dti_promo })">
                                    <PicLeftOutlined /> View
                                </a-button>
                            </template>
                        </template>
                    </a-table>
                </section>
            </main>
        </a-card>
        <!-- {{ externalDti }} -->
    </AuthenticatedLayout>
</template>
