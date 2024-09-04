<!-- <template>
    <div>
        <a-card title="Pending Promo GC Request">
            <a-table bordered size="small" :dataSource="data.data" :columns="columns" :pagination="false">
                <template v-slot:bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'view'">
                        <a-button size="small" type="primary">
                            <PicRightOutlined />View
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination class="mt-5" :datarecords="data" />
        </a-card>
    </div>
</template>
<script>
import Pagination from '@/Components/Pagination.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
export default {
    layout: AuthenticatedLayout,
    props: {
        data: Object,
        columns: Object
    }
}
</script> -->

<template>
    <AuthenticatedLayout>
        <a-card title="Pending Promo GC Request">
            <a-table bordered size="small" :dataSource="data.data" :columns="columns" :pagination="false">
                <template v-slot:bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'view'">
                        <a-button @click="view(record.pgcreq_id)" size="small" type="primary">
                            <PicRightOutlined />View
                        </a-button>
                    </template>
                </template>
            </a-table>
            <pagination class="mt-5" :datarecords="data" />
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/core';


const props = defineProps({ data: Object, columns: Object });

const view = (e) => {
    router.get(route('marketing.promoGcRequest.pending.selected'), {
        id: e
    })
}

</script>
