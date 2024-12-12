<template>
    <AuthenticatedLayout>
        <a-card>
                <a-input-search allow-clear enter-button v-model:value="releaseSearch" placeholder="Input search here..." style="width:25%; margin-left:75%"/>
            <a-table :columns="columns" bordered :data-source="records.data" size="small" :pagination="false" style="margin-top:10px">
                <template #bodyCell="{column, record}">
                    <template v-if="column.key === 'details'" >
                       <a-button @click="details(record.id)">
                        <SnippetsOutlined />
                       </a-button>
                    </template>
                </template>
            </a-table>
            <pagination class="mt-4" :datarecords="records"/>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/core';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';


interface Records {
    id: number,
    num: number,
    datereq: string,
    dateneed: string,
    reqby: string,
    revby: string,
    acctname: string,
    reqappdate: string,
}

defineProps<{
    records: {
        data: Records
    }
}>();

const columns = ref([
    {
        title: 'RFSEGC',
        dataIndex: 'num',
        align: 'center',
    },
    {
        title: 'DATE REQUESTED',
        dataIndex: 'datereq',
    },
    {
        title: 'REQUESTED BY',
        dataIndex: 'reqby',
    },
    {
        title: 'CUSTOMER',
        dataIndex: 'acctname',
    },
    {
        title: 'DATE RELEASED',
        dataIndex: 'reqappdate',
    },
    {
        title: 'RELEASED BY',
        dataIndex: 'revby',
    },
    {
        title: 'Action',
        key: 'details',
    },
]);
const releaseSearch =  ref ('');

watch(releaseSearch, debounce(async(search) => {
//    const searchValidation = /[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{1F900}-\u{1F9FF}\u20B1\$]/u.test(search);
//     if (searchValidation){
//         return;
//     }
console.log(search);
    router.get(route('custodian.released'),{
        search:search
    },{
        preserveState: true
    })
}, 300))

const details = (id: number) => {
    router.get(route('custodian.detail', id))
}
</script>
