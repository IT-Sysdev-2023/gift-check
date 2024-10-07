<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table :data-source="record" :columns="column" size="small" bordered>
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'view'">
                        <a-button @click="view(record.pe_id)">
                            <template #icon>
                                <ArrowsAltOutlined />
                            </template>
                            View
                        </a-button>
                    </template>
                </template>
            </a-table>
        </a-card>
        
        <production-approved-modal :id="peId" :width="1300" style="top: 40px" v-model:open="open" :data="details"/>


    </AuthenticatedLayout>
</template>
<script setup>
import axios from 'axios';
import { ref } from 'vue';

defineProps({
    record: Object,
    column: Array,
});

const open = ref(false);
const details = ref({});
const peId = ref('');


const view = async (id) => {
    try {
        const { data } = await axios.get(route('custodian.production.details', id));
        open.value = true;
        peId.value = id;
        details.value = data;
    } catch (error) {

    }
}
</script>
