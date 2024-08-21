<template>
    <a-modal style="width: 80%; top: 40px" class="text-center" title="Create Entry Store" :footer="null">
        <a-card>
            <a-row :gutter="[16, 16]">
                <a-col :span="7">
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Gc Receiving No.">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Date Received">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Gc Request No">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Date Requested">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Gc Released No.">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Dated Released">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Released By">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Received Type">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Check By">Cloud Database</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions class="mb-2" size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Received by">Cloud
                            Database</a-descriptions-item>
                    </a-descriptions>
                </a-col>
                <a-col :span="17">
                    <a-table size="small" :data-source="record.release" :columns="columns" bordered>
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.key == 'action'">
                                <a-input-number allow-clear style="width: 100%" />
                            </template>
                        </template>
                    </a-table>
                    <a-row :gutter="[16, 16]">
                        <a-col :span="10">
                            <span>
                                Total
                            </span>
                            <a-input class="text-center" size="large" readonly :value="record.total" />
                        </a-col>
                        <a-col :span="14">
                        </a-col>
                    </a-row>

                </a-col>
            </a-row>
        </a-card>
    </a-modal>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
    record: Object,
})

const columns = ref([
    {
        title: 'Denomination',
        dataIndex: 'denom',
        width: '15%'
    },
    {
        title: 'Qty',
        dataIndex: 'quantity',
        width: '15%'
    },
    {
        title: 'Subtotal',
        dataIndex: 'sub',
        width: '15%'
    },
    {
        title: 'No. of Scanned',
        dataIndex: '',
        width: '20%'
    },
    {
        title: 'Action',
        key: 'action',
        align: 'center'
    },
]);

</script>
