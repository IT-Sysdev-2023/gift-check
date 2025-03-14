<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table bordered :data-source="record.data" :columns="column" size="small" :pagination="false">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'view'">
                        <a-button type="dashed" @click="view(record.insp_paymentnum)">
                            <template #icon>
                                <FileSearchOutlined />
                            </template>
                            View
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="record" class="mt-5" />
        </a-card>

        <a-modal :footer="false" :width="900" title="Payment Details" :placement="'left'" v-model:open="open">
            <template #extra>
                <a-button style="margin-right: 8px" @click="onClose">
                    <ShrinkOutlined /> Closed
                </a-button>
            </template>

            <a-table :data-source="details" size="small" bordered :columns="[
                {
                    title: 'Name',
                    dataIndex: 'name',
                },
                {
                    title: 'Barcode',
                    dataIndex: 'spexgcemp_barcode',
                    width: '25%',
                },
                {
                    title: 'Denomination',
                    dataIndex: 'spexgcemp_denom',
                    width: '25%',
                },
            ]" />
        </a-modal>
    </AuthenticatedLayout>
</template>
<script setup>
import axios from 'axios';
import { ref } from 'vue';

defineProps({
    record: Object,
    column: Object,
});

const details = ref({});
const open = ref(false);

const view = async (id) => {
    try {

        const { data } = await axios.get(route('accounting.payment.details', id));

        details.value = data;

        open.value = true;


    } catch (error) {
        console.error("Error fetching details:", error); // Handle error

    }
};


</script>
