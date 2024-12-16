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
        <a-modal v-model:open="open" @ok="okay">
            <span style="color:red;">
            {{ searchMessage }}

            </span>
        </a-modal>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { router } from '@inertiajs/core';
import { notification } from 'ant-design-vue';


defineProps({
    record: Object,
    columns: Array,
})
const reviewGcSearch = ref ('');
const searchMessage = ref ('');
const open = ref (false);

const okay = () =>{
    open.value = false;
}

watch(reviewGcSearch, debounce (async(search) => {
     const searchValidation = /[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{1F900}-\u{1F9FF}]/u;
            if(searchValidation.test(search)){
                const openNotificationWithIcon = (type) =>{
                    notification[type]({
                        message: 'Invalid input',
                        description: 'Search contains invalid symbol or emojis',
                        placement: 'topRight'
                    });
                };
                openNotificationWithIcon('warning');
                return;
            }
    router.get(route('iad.reviewed.gc.special.review'),{
    search: search

    },{
        preserveState: true
    });
}, 300));
</script>
