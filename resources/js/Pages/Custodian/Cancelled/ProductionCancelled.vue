<template>
    <AuthenticatedLayout>
        <a-card>
                <a-input-search allow-clear enter-button v-model:value="cancelSearch" placeholder="Input search here..." style="width:25%; margin-left:75%"/>
            <a-table :data-source="records.data" :columns="columns" size="small" bordered :pagination="false" style="margin-top:10px">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'view'">
                        <a-button @click="view(record.pe_id)">
                            <FastForwardOutlined /> View
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="records" class="mt-5"/>
        </a-card>
        <production-cancel-drawer @close-drawer="close" :open="array.openDrawer" :record="array.cancelledDetails"/>
    </AuthenticatedLayout>
</template>

<script setup>
import axios from 'axios';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { router } from '@inertiajs/core';

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
const cancelSearch =  ref ('');

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
watch(cancelSearch, debounce(async(search) => {
    console.log(search)
    router.get(route('custodian.production.pro.cancelled'),{
        search:search
    },{
        preserveState: true
    });
}, 300))
</script>
