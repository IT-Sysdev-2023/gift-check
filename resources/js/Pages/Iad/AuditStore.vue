<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="7">
                <a-range-picker size="large" @change="fetch" style="width: 100%;" />
            </a-col>
            <a-col :span="17">
                <p>{{ record.addedgcTotal }}</p>
                <a-card title="Additional Gift Checks ">
                    <a-table bordered :data-source="record.addedgc" :pagination="false" size="small"
                        :columns="addedgccolumn"></a-table>
                </a-card>
            </a-col>
        </a-row>
    </AuthenticatedLayout>
</template>

<script setup>
import { router } from '@inertiajs/core';
import { ref } from 'vue';


const props = defineProps({
    record: Object
});


const fetch = (dateObj, dateStr) => {
    router.get(route('iad.audit.store'), {
        date: dateStr
    }, {
        preserveState: true
    })
}

const addedgccolumn = ref([
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
