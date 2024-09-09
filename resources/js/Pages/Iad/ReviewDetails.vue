<template>
    <AuthenticatedLayout>
        <a-tabs v-model:activeKey="activeKey" type="card">
            <a-tab-pane key="1">
                <template #tab>
                    <span>
                        <apple-outlined />
                        Reviewed Details
                    </span>
                </template>
                <a-card>
                    <reviewed-details-card :record="record" :doc="document"/>
                </a-card>
            </a-tab-pane>
            <a-tab-pane key="2">
                <template #tab>
                    <span>
                        <android-outlined />
                        Barcodes
                    </span>
                </template>
                <a-card>

                </a-card>
            </a-tab-pane>
        </a-tabs>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
defineProps({
    record: Object,
    barcodes: Object,
    document: Object
})

const activeKey = ref('1');

</script>
