<template>
    <AuthenticatedLayout>
        <a-card>
            <a-input-search allow-clear enter-button placeholder="Input search here..." v-model:value="gcReceivedSearch" style="width:25%; margin-left: 75%;"/>
            <a-table :data-source="record.data" :columns="columns" :pagination="false" size="small" bordered style="margin-top: 10px;">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'action'">
                        <a-button class="mr-1" @click="retreivedData(record.csrr_id)">
                            <template #icon>
                                <EyeFilled />
                            </template>
                        </a-button>
                        <a-button>
                            <template #icon>
                                <PrinterFilled />
                            </template>
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="record" class="mt-6" />
            <!-- <a-button @click="retreivedData">
                kupal
            </a-button> -->
            <received-gc-details-drawer v-model:open="openDrawer" :data="data"/>
            <a-modal v-model:open="open" @ok="okay">
                <span style="color:red">
                {{ searchMessage }}
                </span>
            </a-modal>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { router } from '@inertiajs/core';

defineProps({
    record: Object,
    columns: Array,
});

const data = ref({});
const openDrawer = ref(false);
const gcReceivedSearch = ref('');
const searchMessage = ref ('');
const open = ref (false);

const okay = () =>{
    open.value = false
}

const retreivedData = async (id) => {
    await axios.get(route('iad.details.view', id)).then((res) => {
        data.value = res;
        openDrawer.value = true;
    })
}


watch(gcReceivedSearch, debounce(async (search) => {
    const searchValidation = /[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{1F900}-\u{1F9FF}\u20B1\$]/u.test(search);
    if (searchValidation){
        searchMessage.value = "Search contains invalid symbols and emojis";
        open.value = true;
        return;
    }

    router.get(route('iad.view.received'), {
            search: search
    }, {
            preserveState: true
        })
}, 300) )

</script>
