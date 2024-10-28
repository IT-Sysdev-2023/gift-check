<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="7">
                <a-range-picker v-model:value="dateRange" size="large" @change="fetch" style="width: 100%;" />
                <a-button :disabled="record.addedgc.length === 0  && record.gcsold.length === 0 && record.unusedgc.length === 0 " class="mt-4" block type="primary" @click="generate">
                    <FastForwardOutlined />  Generate Report
                </a-button>
            </a-col>
            <a-col :span="17">
                <a-descriptions size="small" layout="horiontal" bordered>
                    <a-descriptions-item style="width: 50%;" label="Beginning Balance">{{ record.begbal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</a-descriptions-item>
                </a-descriptions>
                <a-card title="Additional Gift Checks " class="mt-3">
                    <a-table bordered :data-source="record.addedgc" :pagination="false" size="small"
                        :columns="columns"></a-table>
                </a-card>

                <a-card title="Sold Gift Checks " class="mt-3">
                    <a-table bordered :data-source="record.gcsold" :pagination="false" size="small"
                        :columns="columns"></a-table>
                </a-card>

                <a-card title="Sold Gift Checks " class="mt-3">
                    <a-table bordered :data-source="record.unusedgc" :pagination="false" size="small"
                        :columns="columns"></a-table>
                </a-card>
            </a-col>

        </a-row>

        <a-modal v-model:open="open" style="top: 20px;" title="Basic Modal" :width="1000" :footer="null">
            <iframe :src="stream" style="width: 100%; height: 600px;">
            </iframe>
        </a-modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { router } from '@inertiajs/core';
import axios from 'axios';
import dayjs from 'dayjs';
import { ref } from 'vue';


const props = defineProps({
    record: Object
});

const open = ref(false);

const dateRange = ref(props.record.datebackend ? [dayjs(props.record.datebackend[0]), dayjs(props.record.datebackend[1])] : []);

const stream = ref('');

const generate = async () => {
    try {
        const { data } = await axios.get(route('iad.audit.generate'), {
            params: {
                date: props.record.datebackend

            }
        });

        stream.value = `data:application/pdf;base64,${data.stream}`;
        open.value = true;
    } catch {
        alert('error kay ana mana')
    }

}

const fetch = (dateObj, dateStr) => {

    date.value = dateStr;

    router.get(route('iad.audit.store'), {
        date: dateStr
    }, {
        preserveState: true
    })
}

const columns = ref([
    {
        title: 'Denomination',
        dataIndex: 'denom',
        key: 'name',
    },
    {
        title: 'Barcode Start',
        dataIndex: 'barcodest',
        key: 'name',
    },
    {
        title: 'Barcode End',
        dataIndex: 'barcodelt',
        key: 'name',
    },
    {
        title: 'Qty',
        dataIndex: 'count',
        key: 'name',
    },
    {
        title: 'Subtotal',
        dataIndex: 'subtotal',
        key: 'name',
    },
])

</script>
