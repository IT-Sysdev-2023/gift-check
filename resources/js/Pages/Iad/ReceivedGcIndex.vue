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
                        <a-button @click="reprint(record.recnumber)">
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
            <received-gc-details-modal v-model:open="openModal" :data="data"/>
            <!-- <a-modal v-model:open="open" @ok="okay">
                <span style="color:red">
                {{ searchMessage }}
                </span>
            </a-modal> -->
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import axios from 'axios';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { router } from '@inertiajs/core';
import {notification} from 'ant-design-vue';

defineProps({
    record: Object,
    columns: Array,
});

const data = ref({});
const openModal = ref(false);
const gcReceivedSearch = ref('');
const searchMessage = ref ('');

const retreivedData = async (id) => {
    await axios.get(route('iad.details.view', id)).then((res) => {
        data.value = res;
        openModal.value = true;
    })
}


watch(gcReceivedSearch, debounce(async (search) => {
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

    router.get(route('iad.view.received'), {
            search: search
    }, {
            preserveState: true
        })
}, 300) )

const reprint = (id) => {
    router.get(route('iad.reprint.from.marketing'), {
        id
    })
}
</script>
