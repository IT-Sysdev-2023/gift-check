<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';

const columns = ref([
    {
        title: 'RFSEGC #',
        dataIndex: ''
    },
    {
        title: 'Date Requested',
        dataIndex: ''
    },
    {
        title: 'Date Needed',
        dataIndex: ''
    },
    {
        title: 'Total Denomination',
        dataIndex: ''
    },
    {
        title: 'Customer',
        dataIndex: ''
    },
    {
        title: 'Request By',
        dataIndex: ''
    },
    {
        title: 'View',
        dataIndex: 'action'
    },
])
</script>
<template>
    <AuthenticatedLayout>
        <header>
            <div class="flex items-center">
                <p class="text-lg">Approved DTI Special GC Request</p>
                <Link :href="route('finance.dashboard')" class="ml-auto text-black">
                <RollbackOutlined /> Back to Dashboard
                </Link>
            </div>
        </header>
        <main class="mt-10">
            <section>
                <a-table :columns="columns">

                </a-table>
            </section>
        </main>
    </AuthenticatedLayout>
</template>
