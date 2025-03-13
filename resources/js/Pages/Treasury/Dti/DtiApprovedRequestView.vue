<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import dayjs from 'dayjs';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    data: {
        data: {
            dti_num: number,
            dti_datereq: string,
        }
    },
    searchValue: string,
    title: string
}>();


const columns = ref([
    {
        title: 'RFSEGC#',
        dataIndex: 'dti_num',
    },
    {
        title: 'Date Requested',
        dataIndex: 'dti_datereq',
        customRender: ({ text }: { text: string }) => dayjs(text).format('MMMM D, YYYY'),
    },
    {
        title: 'Date Needed',
        dataIndex: 'dti_dateneed',
        customRender: ({ text }: { text: string }) => dayjs(text).format('YYYY-MM-DD'),
    },
    {
        title: 'Customer',
        dataIndex: 'dti_customer',
    },
    {
        title: 'Date Approved',
        dataIndex: 'dti_approveddate',
    },
    {
        title: 'Approved By',
        dataIndex: 'dti_approvedby',
    },
    {
        title: 'Action',
        dataIndex: 'action',
    }
])

const searchData = ref<string>(props.searchValue);

const search = () => {
    router.get(route('treasury.transactions.dti.dtiApprovedRequest'), {
        search: searchData.value
    }, {
        preserveState: true
    });
}

</script>

<template>
    <AuthenticatedLayout>

        <Head :title="title" />
        <a-breadcrumb>
            <a-breadcrumb-item><a :href="route('treasury.dashboard')">Home</a></a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-card class="mt-5">
            <div class="flex justify-end">
                <a-input-search class="w-1/4" allow-clear v-model:value="searchData" @change="search" enter-button />
            </div>
            <a-table class="mt-5" size="small" bordered :data-source="props.data.data" :columns="columns"
                :pagination="false">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex == 'action'">
                        <a-button type="primary" @click="() => {
                            $inertia.get(route('treasury.transactions.dti.dtiApprovedView'), { id: record.dti_num })
                        }">
                            <PicLeftOutlined />
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="props.data" class="mt-5" />
        </a-card>
    </AuthenticatedLayout>
</template>
