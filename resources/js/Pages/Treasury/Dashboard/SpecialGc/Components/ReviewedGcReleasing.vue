<template>
    <a-table
        :data-source="records.data"
        :columns="columns"
        bordered
        size="small"
        :pagination="false"
        :loading="loading"
    >
        <template #bodyCell="{ column, record }">
            <template v-if="column.key == 'approved'">
                {{ record.approvedRequest.reqap_approvedby }}
            </template>
            <template v-if="column.key == 'customer'">
                {{ record.specialExternalCustomer.spcus_acctname }}
            </template>
            <template v-if="column.key == 'reviewed'">
                {{ record.reviewed }}
            </template>
            <template v-if="column.key == 'denom'">
                {{ record.totalDenom.total }}
            </template>

            <template v-if="column.key === 'action'">
                <a-button
                    type="primary"
                    size="small"
                    @click="viewRecord(record.spexgc_id)"
                >
                    <template #icon>
                        <FileSearchOutlined />
                    </template>
                    View
                </a-button>
            </template>
        </template>
    </a-table>
    <pagination-resource class="mt-5" :datarecords="records" />
</template>

<script lang="ts" setup>
defineProps<{
    loading: boolean,
    records: {
        data: any[];
    };
    columns: any[];
}>();

const emit = defineEmits<{
    (e: 'viewRecord', id: number): void;
}>();

const viewRecord = (id) => {
    emit('viewRecord', id);
}
</script>

<style lang="scss" scoped></style>
