<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table :data-source="record.data" :columns="columns" :pagination="false" size="small" bordered>
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'action'">
                        <a-button class="mr-1" @click="retreivedData(record.csrr_id)">
                            <template #icon>
                                <EyeFilled />
                            </template>
                        </a-button>
                        <a-button>
                            <template #icon>
                                <PrinterFilled />
                            </template>
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="record" class="mt-6" />
            <a-button @click="retreivedData">

            </a-button>
            <received-gc-details-drawer v-model:open="openDrawer" :data="data"/>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';
import { ref } from 'vue';

defineProps({
    record: Object,
    columns: Array,
});

const data = ref({});
const openDrawer = ref(false);

const retreivedData = async (id) => {
    await axios.get(route('iad.details.view', id)).then((res) => {
        data.value = res;
        openDrawer.value = true;
    })
}

</script>
