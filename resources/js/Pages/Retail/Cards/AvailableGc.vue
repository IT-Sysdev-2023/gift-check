<template>
    <div>
        <a-card title="Available GC">
            <a-table bordered :dataSource="availableGc" :columns="columns" :pagination="false" />
            <div>
                <div class="flex justify-end mt-2">
                    <a-button @click="() => $inertia.get(route('retail.availableGcList'))" type="primary">
                        <SearchOutlined />View Available GC
                    </a-button>
                </div>
            </div>
        </a-card>
    </div>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
export default {
    layout: AuthenticatedLayout,
    props: {
        availableGc: Object
    },
    data() {
        return {
            columns: [
                {
                    title: 'Denomination',
                    dataIndex: 'denomination',
                },
                {
                    title: 'Quantity',
                    dataIndex: 'count',
                },
            ],
        }
    }

}
</script>