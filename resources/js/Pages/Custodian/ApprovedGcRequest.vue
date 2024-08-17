<template>
    <a-card>
        <a-table size="small" :data-source="record.data" :columns="columns" :pagination=false>

        </a-table>
        <pagination :datarecords="record" class="mt-5"/>
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
