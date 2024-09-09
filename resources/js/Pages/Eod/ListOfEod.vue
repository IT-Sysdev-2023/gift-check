<template>
    <AuthenticatedLayout>
        <a-card>
            <a-table size="small" bordered :data-source="record.data" :columns="columns" :pagination="false">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.key === 'storename'">
                        <span v-if="record.storename == null">
                            All Stores
                        </span>
                    </template>
                    <template v-if="column.key === 'view'">
                        <a-button>
                            <template #icon>
                                <EyeOutlined />
                            </template>
                            View
                        </a-button>
                    </template>
                </template>
            </a-table>

            <pagination :datarecords="record" class="mt-5"/>
        </a-card>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';

defineProps({
    record: Object
})

const columns = ref([
    {
        title: 'Stores',
        dataIndex: 'storename',
        key: 'storename',
    },
    {
        title: 'Eod By',
        dataIndex: 'fullname',
    },
    {
        title: 'Date',
        dataIndex: 'date',
    },
    {
        title: 'Time',
        dataIndex: 'time',
    },
    {
        title: 'View',
        key: 'view',
        width: '12%',
        align: 'center'
    },
])
</script>
