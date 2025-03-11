<template>
    <AuthenticatedLayout>
        <a-tabs v-model:activeKey="activeKey" type="card">
            <a-tab-pane key="1">
                <template #tab>
                    <span>
                        <FileSearchOutlined />
                        Reviewed Details
                    </span>
                </template>
                <a-card>
                    <reviewed-details-card :record="record" :doc="document" :approved="approved" />
                </a-card>
            </a-tab-pane>
            <a-tab-pane key="2">
                <template #tab>
                    <span>
                        <BarcodeOutlined />
                        Barcodes
                    </span>
                </template>
                <a-card>
                    <reviewed-barcodes :barcodes="barcodes" />
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
    document: Object,
    approved: Object
})

const activeKey = ref('1');

</script>
