<template>
    <a-card>
        <a-alert message="Available GC for Allocation / Sales" type="info" class="mb-1" show-icon />
        <a-descriptions class="mb-2" size="small" layout="horizontal" bordered v-for="item in denom">
            <a-descriptions-item style="width: 50%;" :label="item.denomination_format">
                <p class="text-center">{{ item.count.toLocaleString() }}</p>
            </a-descriptions-item>
        </a-descriptions>
        <a-button class="mt-2" @click="view" block>
            <FastForwardOutlined />View Available Gc
        </a-button>
    </a-card>

    <a-modal style="width: auto; top: 50px;" title="Gc For Allocation" v-model:open="openDrawer" @close="onClose">
        <template #extra>
            <a-button style="margin-right: 8px" @click="onClose">Exit</a-button>
        </template>
        <a-card>
            <a-table :pagination="false" bordered size="small" :data-source="dataValue.data" :columns="[
                {
                    title: 'GC Barcode#',
                    dataIndex: 'barcode_no',
                },
                {
                    title: 'Denom',
                    dataIndex: 'denom',
                },
                {
                    title: 'Date Validated',
                    dataIndex: 'date',
                },
                {
                    title: 'Validated By',
                    dataIndex: 'valBy',
                },
            ]" />

            <pagination-axios-small :datarecords="dataValue" @on-pagination="handleChangePagination" />
        </a-card>

    </a-modal>
</template>
<script setup>
import axios from 'axios';
import { ref } from 'vue';


const dataValue = ref([]);
const openDrawer = ref(false);

const props = defineProps({
    denom: Object,
});

const view = async () => {
    try {
        const { data } = await axios.get(route('custodian.available.gc'));
        dataValue.value = data;
        openDrawer.value = true;
    } catch {
        alert('keanding');
    }
}

const onClose = () => {
    openDrawer.value = false;
}

const handleChangePagination = async (link) => {
    if (link.url) {
        const { data } = await axios.get(link.url);
        dataValue.value = data;
    }
}

</script>
