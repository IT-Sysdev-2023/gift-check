<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table :pagination="false" size="small" :data-source="record.data" :columns="columns">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'action'">
                        <a-button @click="view(record.barcode)">
                            <template #icon>
                                <SearchOutlined />
                            </template>
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination-resource :datarecords="record" />
        </a-card>

        <a-modal v-model:open="openmodal" style="width: auto;" :footer="null">
            <template #title>
                <p class="text-center">Text File Details</p>
            </template>

            <a-card>
                <a-table :pagination="false" size="small" :data-source="datatxt.data" :columns="columnstxt">
                </a-table>
            </a-card>
        </a-modal>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import axios from 'axios';
import { ref } from 'vue';


interface Records {
    barcode: number,
    denom: string,
    reverdate: string,
    verby: string,
    cus: string,
    storename: string,
    balance: string,
}

interface TextFile {
    data: {
        seodtt_line: string,
        seodtt_creditlimit: string,
        seodtt_credpuramt: string,
        seodtt_addonamt: string,
        seodtt_balance: string,
        seodtt_transno: string,
        seodtt_timetrnx: string,
        seodtt_bu: string,
        seodtt_terminalno: string,
        seodtt_ackslipno: string,
        seodtt_crditpurchaseamt: string,
    }
}
const datatxt = ref<TextFile>();
const openmodal = ref<boolean>(false);

defineProps<{
    record: {
        data: Records[]
    }
}>();

interface Columns {
    title: string;
    dataIndex: string;
    key: string;
    align: string;
}

const columns: Columns[] = [
    {
        title: 'Barcode#',
        dataIndex: 'barcode',
        key: 'barcode',
        align: 'center',
    },
    {
        title: 'Denomination',
        dataIndex: 'denom',
        key: 'denom',
        align: 'center',
    },
    {
        title: 'Date / Time Verified',
        dataIndex: 'reverdate',
        key: 'rev',
        align: 'center',
    },
    {
        title: 'Verified By',
        dataIndex: 'verby',
        key: 'ver',
        align: 'center',
    },
    {
        title: 'Customer Name',
        dataIndex: 'cus',
        key: 'cus',
        align: 'center',
    },
    {
        title: 'Store',
        dataIndex: 'storename',
        key: 'store',
        align: 'center',
    },
    {
        title: 'Balance',
        dataIndex: 'balance',
        key: 'balance',
        align: 'center',
    },
    {
        title: 'Action',
        dataIndex: '',
        key: 'action',
        align: 'center',
    },
];

const columnstxt: Columns[] = [
    {
        title: 'Textfile Line#',
        dataIndex: 'seodtt_line',
        key: 'barcode',
        align: 'center',
    },
    {
        title: 'Credit Limit',
        dataIndex: 'seodtt_creditlimit',
        key: 'denom',
        align: 'center',
    },
    {
        title: 'Credit.Pur.Amt + Add-on',
        dataIndex: 'seodtt_credpuramt',
        key: 'rev',
        align: 'center',
    },
    {
        title: 'Add-on Amt',
        dataIndex: 'seodtt_addonamt',
        key: 'ver',
        align: 'center',
    },
    {
        title: 'Remaining Balance',
        dataIndex: 'seodtt_balance',
        key: 'cus',
        align: 'center',
    },
    {
        title: 'Transaction#',
        dataIndex: 'seodtt_transno',
        key: 'store',
        align: 'center',
    },
    {
        title: 'Time of Cred Tranx',
        dataIndex: 'seodtt_timetrnx',
        key: 'balance',
        align: 'center',
    },
    {
        title: 'Bus. Unit',
        dataIndex: 'seodtt_bu',
        key: 'action',
        align: 'center',
    },
    {
        title: 'Ackslip',
        dataIndex: 'seodtt_terminalno',
        key: 'action',
        align: 'center',
    },
];

const view = async (barcode: number) => {
    try {
        const { data } = await axios.get(route('eod.txt', barcode));
        datatxt.value = data;
        openmodal.value = true;

    } catch (error) {
        throw error;
    }
}

</script>
