<template>
    <AuthenticatedLayout>
        <a-card>
                <a-input-search allow-clear enter-button v-model:value="productionSearch" placeholder="Input search here..." style="width:25%; margin-left:75%"/>

            <a-table :data-source="record.data" :columns="column" size="small" bordered :pagination="false" style="margin-top:10px">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'view'">
                        <a-button @click="view(record.pe_id)">
                            <template #icon>
                                <ArrowsAltOutlined />
                            </template>
                            View Details
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="record" class="mt-5"/>
        </a-card>

        <production-approved-modal :id="peId" :width="1300" style="top: 40px" v-model:open="open" :data="details"/>

    </AuthenticatedLayout>
    <!-- {{ record }} -->
</template>
<script setup>
import axios from 'axios';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { router } from '@inertiajs/core';



defineProps({
    record: Object,
    column: Array,
});

const open = ref(false);
const details = ref({});
const peId = ref('');
const productionSearch = ref ('');


const view = async (id) => {
    try {
        const { data } = await axios.get(route('custodian.production.details', id));
        open.value = true;
        peId.value = id;
        details.value = data;
    } catch (error) {
        console.log('Error', error)
    }
}
watch(productionSearch, debounce(async(search) => {
    router.get(route('custodian.production.index'),{
        search:search
    },{
        preserveState: true
    })
}, 300))
</script>
