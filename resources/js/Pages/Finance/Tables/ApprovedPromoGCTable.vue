<template>
    <a-card>
        <a-table size="small" bordered :data-source="records.data" :columns="columns" :pagination="false"></a-table>
        <pagination-resource class="mt-4" :datarecords="records"/>
    </a-card>
</template>
<script>
export default {
    props: {
        records: Object,
        columns: Object,
    }
}
</script>
