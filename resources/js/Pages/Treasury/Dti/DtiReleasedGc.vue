<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/core';
import dayjs from 'dayjs';
import { ref } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps<{
    data: {
        data: {
            data: object;
        }
    },
    search: string,
    title: string;
}>();

const columns = ([
    {
        title: 'RFSEGC #',
        dataIndex: 'dti_trid'
    },
    {
        title: 'Date Requested',
        dataIndex: 'dti_datereq',
        customRender: ({ text }) => dayjs(text).format('MMMM D, YYYY'),
    },
    {
        title: 'Date Validity',
        dataIndex: 'dti_dateneed',
        customRender: ({ text }) => dayjs(text).format('dddd, MMMM D YYYY, h:mm A')
    },
    {
        title: 'Requested By',
        dataIndex: 'dti_reqby'
    },
    {
        title: 'Customer',
        dataIndex: 'dti_customer'
    },
    {
        title: 'Date Released',
        dataIndex: 'dti_date',
        customRender: ({ text }) => dayjs(text).format('dddd, MMMM D YYYY, h:mm A')

    },
    {
        title: 'Released By',
        dataIndex: 'released_by'
    },
    {
        title: 'Actions',
        dataIndex: 'action'
    },
]);

const searchData = ref<string>(props.search);
const searchFunction = () => {
    router.get(route('treasury.transactions.dti.dtiReleasedGc'), {
        search: searchData.value
    }, {
        preserveState: true
    })
}
</script>
<template>
    <AuthenticatedLayout>

        <Head :title="title" />
        <a-breadcrumb>
            <a-breadcrumb-item><a :href="route('treasury.dashboard')">Home</a></a-breadcrumb-item>
            <a-breadcrumb-item>{{ props.title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-card class="mt-5">
            <div class="flex justify-end">
                <a-input-search v-model:value="searchData" @change="searchFunction" allow-clear enter-button
                    placeholder="Input search here..." class="w-1/4" />
            </div>

            <a-table :columns="columns" :data-source="props.data.data" size="small" class="mt-5" :pagination="false">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex == 'action'">
                        <a-button type="primary" @click="() => router.get(route('treasury.transactions.dti.dtiReleasedView'), {
                            id: record.dti_trid
                        })">
                            <PicLeftOutlined />
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination :datarecords="props.data" class="mt-5" />
        </a-card>
    </AuthenticatedLayout>
</template>
