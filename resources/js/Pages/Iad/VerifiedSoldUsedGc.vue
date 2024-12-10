<template>
    <AuthenticatedLayout>
        <a-card>
            <a-input-search allow-clear enter-button v-model:value="iadVerifiedSearch" placeholder="Input search here..." style="width: 25%; margin-left: 75%"/>
            <a-table :data-source="record.data" :columns="columns" size="small" bordered :pagination="false" style="margin-top: 10px;">
                <template #bodyCell="{ column, record }">

                    <template v-if="column.key === 'details'">
                        <a-button class="mx-1" @click="verified(record.vs_barcode)">
                            <AuditOutlined />
                        </a-button>
                        <span v-if="record.trans_datetime !== null">
                            <a-button @click="revalidation(record.vs_barcode)">
                                <SearchOutlined  />
                            </a-button>
                        </span>
                        <span v-if="record.vs_reverifydate !== null">
                            <a-button>
                                <SearchOutlined />
                            </a-button>
                        </span>
                        <span v-if="record.vs_tf_used === '*'">
                            <a-button @click="transactiontxt(record.vs_barcode)">
                                <SearchOutlined />
                            </a-button>
                        </span>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="record" class="mt-5" />
            <verified-details-modal v-model:open="verifiedopen" :record="verdata"/>
            <transaction-txt-modal v-model:open="transtxtopen" :record="transdata"/>
        </a-card>
    </AuthenticatedLayout>
    <!-- {{ record }} -->

</template>

<script setup>
import axios from 'axios';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { router } from '@inertiajs/core';

const props = defineProps({
    record: Object,
});

const columns = ref([
    {
        title: 'Barcode',
        dataIndex: 'vs_barcode',
        key: 'name',
    },
    {
        title: 'Denomination',
        dataIndex: 'vs_tf_denomination',
        key: 'name',
    },
    {
        title: 'Gc Type',
        dataIndex: 'gctype',
        key: 'name',
    },
    {
        title: 'Date Sold / Released',
        dataIndex: 'soldrel',
        key: 'name',
    },
    {
        title: 'Store',
        dataIndex: 'storename',
        key: 'name',
    },

    {
        title: 'Verified Customer',
        dataIndex: 'customername',
        key: 'name',
    },
    {
        title: 'Gc Details',
        key: 'details',
    },
]);


const verdata = ref({});
const transdata = ref({});

const verifiedopen = ref(false);
const transtxtopen = ref(false);
const iadVerifiedSearch = ref ('');



const verified = async (barcode) => {
    try {
        const { data } = await axios.get(route('iad.versoldused.verified', barcode));
        verifiedopen.value = true;
        verdata.value = data;

    } catch {
        alert('naay error ayaw sig galaw gaw diha');
    }
}

const revalidation = async (barcode) => {
    try{
        const { data } = await axios.get(route('iad.versoldused.verifieds', barcode));
        verdata.value = data;
    }catch{
        alert('naay error ayaw sig galaw gaw diha');
    }
}
const transactiontxt = async (barcode) => {
    try{
        const { data } = await axios.get(route('iad.versoldused.transaction', barcode));
        transdata.value = data;
        transtxtopen.value = true;
    }catch{
        alert('naay error ayaw sig galaw gaw diha');
    }
}

watch(iadVerifiedSearch, debounce (async (search) => {
    router.get(route('iad.versoldused.index'),{
        search: search
    },{
        preserveState: true
    });
}, 300));

</script>
