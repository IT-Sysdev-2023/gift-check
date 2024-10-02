<template>
    <a-card title="Cancelled Request">
        <div>
            <div class="flex justify-end mb-2">
                <a-input-search :keyup.enter="onSearch" v-model:value="form.search" placeholder="input search text" style="width: 300px"
                    @search="onSearch" />
            </div>
        </div>
        <a-table :pagination="false" size="small" bordered :dataSource="data.data" :columns="columns">
            <template #bodyCell="{ column, record }">
                <div v-if="column.dataIndex === 'pgcreq_doc'">
                    <a-image style="height: 60px; width: 70px;" :src="'/storage/promoGcUpload/' + record.pgcreq_doc" />
                </div>
            </template>
        </a-table>
    </a-card>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object
    },
    data() {
        return {
            form: useForm({
                search: null
            }),
            columns: [
                {
                    title: 'Promo Request No.',
                    dataIndex: 'pgcreq_reqnum',
                    width: '10%'
                },
                {
                    title: 'Requested By',
                    dataIndex: 'requestedBy',
                    width: '15%'
                },
                {
                    title: 'Promo Group',
                    dataIndex: 'pgcreq_group',
                    width: '7%'
                },
                {
                    title: 'Promo Total',
                    dataIndex: 'pgcreq_total',
                    width: '10%'
                },
                {
                    title: 'Cancelled By',
                    dataIndex: 'cancelledBy',
                    width: '10%'
                },
                {
                    title: 'Cancellation Remarks',
                    dataIndex: 'cancellremarks',
                    width: '40%'
                },
                {
                    title: 'Document',
                    dataIndex: 'pgcreq_doc',
                    width: '10%'
                },
            ],
        }
    },
    methods: {
        onSearch() {
            this.form.get(route('marketing.promoGcRequest.cancelled.list'));
            
        },
    },
}
</script>