<template>
    <AuthenticatedLayout>
        <a-card>
            <div class="flex justify-end">
                <a-button class="mb-3" @click="() => $inertia.get(route('eod.process'))">
                    <template #icon>
                        <SettingOutlined />
                    </template>
                    Start Process Eod
                </a-button>
            </div>
            <a-table size="small" :pagination="false" :data-source="record.data" :columns="columns" bordered></a-table>

            <pagination :datarecords="record" class="mt-5" />
        </a-card>

    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
const props = defineProps({
    record: Object,
    columns: Array,
})
</script>
