<template>
    <a-modal style="top: 10px; width: 70%;" size="small" title="GC Received GC" placement="left" :width="900"
        :body-style="{ paddingBottom: '80px' }" @close="onClose">
        <a-row :gutter="[16, 16]">
            <a-col :span="10">
                <a-card>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Denomination">Quantity</a-descriptions-item>
                    </a-descriptions>
                    <a-descriptions v-for="(item, key) in data.data.record.denom" size="small" layout="horizontal"
                        class="mb-1 mt-1" bordered>
                        <a-descriptions-item style="width: 50%;" :label="key">{{ item }}</a-descriptions-item>
                    </a-descriptions>

                    <a-descriptions size="small" layout="horizontal" label="" bordered>
                        <!-- <a-descriptions-item style="width: 50%;" label="Quantity">{{ record }}</a-descriptions-item> -->
                    </a-descriptions>
                    <a-descriptions size="small" layout="horizontal" bordered>
                        <a-descriptions-item style="width: 50%;" label="Total">{{ data.data.record.total
                            }}</a-descriptions-item>
                    </a-descriptions>
                </a-card>
            </a-col>
            <a-col :span="14">
                <a-card>
                    <a-table size="small" bordered :data-source="data.data.record.record" :columns="columns"></a-table>

                </a-card>
            </a-col>
        </a-row>
    </a-modal>
</template>
<script setup>
import { ref } from 'vue';

defineProps({
    data: Object,
});

const columns = ref([
    {
        title: 'Barcode',
        dataIndex: 'cssitem_barcode',
        key: 'name',
    },
    {
        title: 'Denomination',
        dataIndex: 'denomination',
        align: 'center',
        width: '10%'
    },
])
</script>
