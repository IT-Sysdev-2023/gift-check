<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table :data-source="records" :columns="columns" size="small" bordered>
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'view'">
                        <a-button @click="view(record.pe_id)">
                            <FastForwardOutlined /> View
                        </a-button>
                    </template>
                </template>
            </a-table>
        </a-card>
        <production-cancel-drawer @close-drawer="close" :open="array.openDrawer" :record="array.cancelledDetails"/>
    </AuthenticatedLayout>
</template>

<script setup>
import axios from 'axios';
import { ref } from 'vue';

const array = ref({
    openDrawer: false,
    cancelledDetails: {}
});

const props = defineProps({
    records: Object,
    columns: Array,
});

const close = () => {
    array.value.openDrawer = false;
}

const view = async (id) => {
    // array.value.openDrawer = true;
    try {
        const { data } = await axios.get(route('custodian.production.cancelled.details', id));
        array.value.cancelledDetails = data;
        array.value.openDrawer = true;
    } catch {
        alert('wala ma kuha');
    }
}
</script>
