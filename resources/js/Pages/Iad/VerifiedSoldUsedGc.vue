<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table :data-source="record.data" :columns="columns" size="small" bordered :pagination="false">
                <template #bodyCell="{ column, record }">

                    <template v-if="column.key === 'details'">
                        <a-button class="mx-1" @click="verified(record.vs_barcode)">
                            <AuditOutlined />
                        </a-button>
                        <span v-if="record.trans_datetime !== null">
                            <a-button>
                                <SearchOutlined />
                            </a-button>
                        </span>
                        <span v-if="record.vs_reverifydate !== null">
                            <a-button>
                                <SearchOutlined />
                            </a-button>
                        </span>
                        <span v-if="record.vs_tf_used === '*'">
                            <a-button>
                                <SearchOutlined />
                            </a-button>
                        </span>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="record" class="mt-5" />
            <verified-details-modal v-model:open="verifiedopen" :record="verdata"/>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup>
import axios from 'axios';
import { ref } from 'vue';

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
const verifiedopen = ref(false);

const verified = async (barcode) => {
    try {
        const { data } = await axios.get(route('iad.versoldused.verified', barcode));
        verifiedopen.value = true;
        verdata.value = data;

    } catch {
        alert('naay error ayaw sig galaw gaw diha');
    }
}

</script>
