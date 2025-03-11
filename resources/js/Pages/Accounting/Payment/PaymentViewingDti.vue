<template>
    <AuthenticatedLayout>
        <a-card class="text-center" title="Dti Gc Payment Viewing">
            <a-table :pagination="false" bordered size="small" :data-source="record.data" :columns="column">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'view'">
                        <a-button type="dashed" @click="view(record.dti_insp_paymentnum)">
                            View Records
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination class="mt-5" :datarecords="record" />
        </a-card>
        <a-modal title="Details" :footer="false" :width="1000" v-model:open="viewModal">
            <a-table  size="small" bordered :data-source="details" :columns="detailColumns">

            </a-table>
        </a-modal>
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import axios from 'axios';
import { ref } from 'vue';

defineProps<{
    record: any,
    column: any,
}>();
const details = ref();
const viewModal = ref<boolean>(false);

const detailColumns = ref([
    {
        title: 'Name',
        dataIndex: 'name',
    },
    {
        title: 'Barcode',
        dataIndex: 'dti_barcode',
    },
    {
        title: 'Denomination',
        dataIndex: 'dti_denom',
    },
])

const view = async (id: number) => {
    const { data } = await axios.get(route('accounting.payment.details.dti', id));
    details.value = data;
    viewModal.value = true;
}
</script>
