<template>
    <AuthenticatedLayout>
        <a-tabs type="card" v-model:activeKey="activeKey">
            <a-tab-pane key="1">
                <template #tab>
                    <span>
                        <apple-outlined />
                        Special Gc Request Setup Dti
                    </span>
                </template>
                <special-gc-request-component :datarecords="props.records"/>
            </a-tab-pane>
            <a-tab-pane key="2">
                <template #tab>
                    <span>
                        <android-outlined />
                        Barcodes
                    </span>
                </template>
                Tab 2
            </a-tab-pane>
        </a-tabs>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { ref } from 'vue';
const activeKey = ref('1');

const props = defineProps<{
    records: Object
}>()
</script>
