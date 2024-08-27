<template>
    <a-card>
        <a-table size="small" :data-source="record.data" :columns="columns" :pagination=false>
            <template #bodyCell="{ column, record }">
                <template v-if="column.key == 'setup'">
                    <a-button @click="() => $inertia.get(route('custodian.approved.setup'), { id: record.spexgc_id })">
                        <template #icon>
                            <FastForwardOutlined />
                        </template>
                        Setup Gc Request
                    </a-button>
                </template>
            </template>
        </a-table>
        <pagination :datarecords="record" class="mt-5" />
    </a-card>
</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    layout: AuthenticatedLayout,
    props: {
        record: Object,
        columns: Array,
    }
}
</script>
