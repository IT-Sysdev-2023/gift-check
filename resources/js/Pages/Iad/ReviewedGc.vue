<template>
    <AuthenticatedLayout>
        <a-card>
            <a-input-search allow-clear enter-button placeholder="Input search here..." v-model:value="reviewGcSearch" style="width:25%; margin-left:75%"/>
            <a-table size="small" bordered :data-source="record.data" :columns="columns" :pagination="false" style="margin-top:10px">
                <template #bodyCell="{column, record}">
                    <template v-if="column.key === 'details'">
                        <a-button @click="() => $inertia.get(route('iad.reviewed.gc.details', {id: record.spexgc_id}))">
                            <template #icon>
                                <EyeFilled />
                           </template>
                    </a-button>
                    </template>
               </template>
            </a-table>
            <pagination :datarecords="record" class="mt-6"/>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { router } from '@inertiajs/core';


defineProps({
    record: Object,
    columns: Array,
})
const reviewGcSearch = ref ('');

watch(reviewGcSearch, debounce (async(search) => {
    router.get(route('iad.reviewed.gc.special.review'),{
    search: search

    },{
        preserveState: true
    });
}, 300));
</script>
